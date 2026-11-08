<?php

namespace System\Tests\Twig;

use Backend\Models\User;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use InvalidArgumentException;
use System\Classes\MarkupManager;
use System\Tests\Bootstrap\TestCase;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\SandboxExtension;
use Twig\Sandbox\SecurityError;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;

/**
 * Covers the 1.2.15 sandbox posture: nothing core renders is sandboxed, because every template
 * core renders is managed by a developer-level role, and the sandbox a plugin can opt into for a
 * template its own users wrote is an allowlist.
 */
class SandboxPolicyTest extends TestCase
{
    /**
     * @var \Illuminate\Support\Collection Rows shaped like a theme template's query result.
     */
    protected $rows;

    public function setUp(): void
    {
        parent::setUp();

        $this->rows = collect([
            ['date' => 'b', 'title' => 'B'],
            ['date' => 'a', 'title' => 'A'],
        ]);
    }

    public function testNoCoreTwigEnvironmentIsSandboxed(): void
    {
        Config::set('cms.twigNoCache', true);

        $environments = [
            'twig.environment' => 'backs the .htm view engine, which renders module and plugin views',
            'twig.environment.cms' => 'renders theme templates, which only a code-level role can edit',
            'twig.environment.mailer' => 'renders mail content, which only a code-level role can write',
        ];

        foreach ($environments as $environment => $why) {
            $this->assertFalse(
                $this->app->make($environment)->hasExtension(SandboxExtension::class),
                $environment . ' ' . $why
            );
        }

        $this->assertTrue(
            MarkupManager::makeSandboxedTwigEnvironment()->hasExtension(SandboxExtension::class),
            'the factory is the only sandboxed environment, and a plugin has to ask for it'
        );
    }

    /**
     * Theme templates are trusted code, so the things a theme legitimately does have to work.
     * Every case here was either refused or silently given the wrong answer while the sandbox
     * was unconditional: SafeCollection dropped the arguments of a Collection call, so
     * `rows.where('date', 'a')` matched none of the rows and `rows.filter(...)` returned the
     * unfiltered set.
     */
    public function testAThemeTemplateReachesWhatItNeeds(): void
    {
        $twig = $this->app->make('twig.environment');

        $cases = [
            "{{ array_sort(rows, 'date')|column('title')|join(',') }}" => 'A,B',
            "{{ rows.where('date', 'a').count }}" => '1',
            "{{ rows.filter(r => r.title == 'A').count }}" => '1',
            "{{ rows.first(r => r.title == 'A').date }}" => 'a',
            '{{ rows.map(r => r.title)|join(",") }}' => 'B,A',
            "{{ rows.sortBy('date').first.title }}" => 'A',
            "{{ str_limit('abcdefgh', 4) }}" => 'abcd...',
        ];

        foreach ($cases as $template => $expected) {
            $this->assertSame(
                $expected,
                trim($twig->createTemplate($template)->render(['rows' => $this->rows])),
                $template
            );
        }
    }

    public function testTheSandboxedFactoryReturnsASandboxedEnvironment(): void
    {
        $twig = MarkupManager::makeSandboxedTwigEnvironment();

        $this->assertTrue($twig->hasExtension(SandboxExtension::class));

        $refused = [
            "{{ source('.env') }}",
            "{{ include('system::mail.layout-default') }}",
            "{{ constant('PHP_VERSION') }}",
            '{{ str_random(8) }}',
            "{{ array_sort(rows, 'date')|json_encode }}",
            '{{ rows.map(r => r.title)|join(",") }}',
            "{% extends 'system::mail.layout-default' %}",
            '{% macro m() %}x{% endmacro %}',
        ];

        foreach ($refused as $template) {
            try {
                $twig->createTemplate($template)->render(['rows' => $this->rows]);
                $this->fail('Sandbox allowed: ' . $template);
            } catch (SecurityError | SyntaxError $ex) {
                $this->assertTrue(true);
            }
        }
    }

    /**
     * Reading the data the caller passed in works, calling behaviour on it does not. A column
     * that holds null is a read even though Eloquent reports it as absent, which is what sends
     * it through the method check.
     */
    public function testASandboxedTemplateReadsDataButNotBehaviour(): void
    {
        $twig = MarkupManager::makeSandboxedTwigEnvironment();
        $model = new SandboxPolicyTestModel([
            'title' => 'Alice',
            'nickname' => null,
            'truncate' => null,
        ]);
        $model->setRelation('loaded', collect(['a', 'b']));
        $data = ['m' => $model, 'rows' => $this->rows, 'when' => now()];

        $reads = [
            '{{ m.title }}' => 'Alice',
            '[{{ m.nickname }}]' => '[]',
            '[{{ m.derived }}]' => '[]',
            '[{{ m.parent.title }}]' => '[]',
            '{{ m.loaded.count }}' => '2',
            '{{ rows.count }}' => '2',
            "{{ rows|column('title')|join(',') }}" => 'B,A',
            '{{ when|date("Y") }}' => now()->format('Y'),
            '{{ when ? "stringable" : "" }}' => 'stringable',
        ];

        foreach ($reads as $template => $expected) {
            $this->assertSame(
                $expected,
                trim($twig->createTemplate($template)->render($data)),
                $template
            );
        }

        $behaviour = [
            '{{ m.secretThing }}',
            '{{ m.active }}',
            '{{ m.delete() }}',
            '{{ m.truncate }}',
            '{{ m.getConnection() }}',
            "{{ rows.where('date', 'a').count }}",
            // Winter's models declare these public, so Twig would read them as properties
            '{{ m.table }}',
            '{{ m.belongsTo|json_encode }}',
            '{{ m.implement|json_encode }}',
            '{{ m.sessionKey }}',
        ];

        foreach ($behaviour as $template) {
            try {
                $twig->createTemplate($template)->render($data);
                $this->fail('Sandbox allowed: ' . $template);
            } catch (SecurityError $ex) {
                $this->assertTrue(true);
            }
        }
    }

    /**
     * A model's raw attribute bag is a property read - `$attributes` is public on Winter's models
     * - so without a check on it a sandboxed template reads every column of every model passed
     * in, including the ones the model hides. The same read by name has to go too, or the bag
     * check is cosmetic.
     */
    public function testASandboxedTemplateCannotReadAHiddenAttribute(): void
    {
        $twig = MarkupManager::makeSandboxedTwigEnvironment();
        $user = new User();
        $user->forceFill([
            'login' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'password-hash',
            'persist_code' => 'persist-code',
            'activation_code' => 'activation-code',
            'reset_password_code' => 'reset-code',
        ]);
        $data = ['user' => $user, 'users' => collect([$user])];

        $this->assertContains('password', $user->getHidden(), 'the fixture only means anything while the model hides these');

        $refused = [
            '{{ user.attributes|json_encode }}',
            '{{ user.attributes.password }}',
            '{{ user.password }}',
            '{{ user.persist_code }}',
            '{{ user.activation_code }}',
            '{{ user.reset_password_code }}',
            '{{ users.0.attributes.password }}',
        ];

        foreach ($refused as $template) {
            try {
                $twig->createTemplate($template)->render($data);
                $this->fail('Sandbox allowed: ' . $template);
            } catch (SecurityError $ex) {
                $this->assertTrue(true);
            }
        }

        // What the model does put in its output is still readable, and still leaves the rest out.
        $this->assertSame('admin', trim($twig->createTemplate('{{ user.login }}')->render($data)));
        $this->assertSame(
            '{"login":"admin","email":"admin@example.com"}',
            trim($twig->createTemplate('{{ user|json_encode|raw }}')->render($data))
        );
    }

    /**
     * The null-column read above is a real method call that Winter's Model::__call() is expected
     * to refuse. Each of these is a name its dispatch chain answers instead - a scope, a macro on
     * either builder, an extension's dynamic method, a dynamic `where<Column>()`, and Winter's
     * own `extend()`, which invokes any callable the template names. A column holding null is all
     * it takes to reach them, so the policy has to refuse the read rather than make the call.
     */
    public function testASandboxedTemplateCannotCallBehaviourNamedAfterANullColumn(): void
    {
        EloquentBuilder::macro('sandboxPolicyTestMacro', function () {
            SandboxPolicyTestModel::$called[] = 'eloquent builder macro';
            return 'macro';
        });
        QueryBuilder::macro('sandboxPolicyTestQueryMacro', function () {
            SandboxPolicyTestModel::$called[] = 'query builder macro';
            return 'macro';
        });

        $twig = MarkupManager::makeSandboxedTwigEnvironment();
        $model = new SandboxPolicyTestModel([
            'active' => null,
            'dynamic' => null,
            'extend' => null,
            'sandboxPolicyTestMacro' => null,
            'sandboxPolicyTestQueryMacro' => null,
            'whereTitle' => null,
        ]);
        $model->addDynamicMethod('dynamic', function () {
            SandboxPolicyTestModel::$called[] = 'dynamic method';
            return 'dynamic';
        });

        $refused = [
            '{{ m.active }}',
            '{{ m.dynamic }}',
            '{{ m.sandboxPolicyTestMacro }}',
            '{{ m.sandboxPolicyTestQueryMacro }}',
            '{{ m.whereTitle }}',
            "{{ m.extend('getenv', 'HOME') }}",
        ];

        SandboxPolicyTestModel::$called = [];

        foreach ($refused as $template) {
            try {
                $twig->createTemplate($template)->render(['m' => $model]);
                $this->fail('Sandbox allowed: ' . $template);
            } catch (SecurityError $ex) {
                $this->assertTrue(true);
            }
        }

        $this->assertSame([], SandboxPolicyTestModel::$called, 'nothing was allowed to run');
    }

    public function testTheAllowlistCanBeWidened(): void
    {
        $twig = MarkupManager::makeSandboxedTwigEnvironment([
            'tags' => ['do'],
            'functions' => ['str_limit'],
            'methods' => [SandboxPolicyTestModel::class => ['getSecretThing']],
        ]);
        $model = new SandboxPolicyTestModel(['title' => 'Alice']);

        $this->assertSame('', trim($twig->createTemplate('{% do 1 + 1 %}')->render()));
        $this->assertSame('abcd...', trim($twig->createTemplate("{{ str_limit('abcdefgh', 4) }}")->render()));
        $this->assertSame('kept', trim($twig->createTemplate('{{ m.secretThing }}')->render(['m' => $model])));

        // Widening one list leaves the rest of the baseline where it was.
        $this->expectException(SecurityError::class);
        $twig->createTemplate('{{ str_random(8) }}')->render();
    }

    public function testAnUnknownAllowanceKeyIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown Twig sandbox allowance "filter"');

        MarkupManager::makeSandboxedTwigEnvironment(['filter' => ['md']]);
    }

    /**
     * The factory's environment is the only sandbox left anywhere, so it is the one that has to
     * refuse an escape. Every one of these worked while the policy was a blocklist, because the
     * wildcard markup extensions were never on that list at all.
     */
    public function testTheSandboxedEnvironmentRefusesAnEscape(): void
    {
        $twig = MarkupManager::makeSandboxedTwigEnvironment();
        $model = new SandboxPolicyTestModel(['title' => 'Alice']);

        $escapes = [
            // Arbitrary static call on a facade, through a wildcard markup extension
            '{{ str_random(8) }}',
            "{{ url_to('/') }}",
            "{{ html_decode('x') }}",
            '{{ form_open() }}',
            "{{ array_pluck([{'a': 1}], 'a')|json_encode }}",
            // Arbitrary PHP function call with template-supplied arguments: Str::macro() binds
            // any callable under a name the next tag then invokes
            "{{ str_macro('sx', 'getenv') }}{{ str_sx('HOME') }}",
            "{{ form_macro('fm', 'getenv') }}",
            // Reading another template off disk
            "{{ source('.env') }}",
            "{% extends 'system::mail.layout-default' %}",
            "{% include 'system::mail.layout-default' %}",
            // Reflection and request surface
            "{{ constant('PHP_VERSION') }}",
            "{{ attribute(m, 'getConnection') }}",
            "{{ input('x') }}",
            // The database layer, reached through a model the sending plugin passed in
            '{{ m.getConnection() }}',
            '{{ m.newQuery() }}',
            '{{ m.attributes|json_encode }}',
            // A PHP function name smuggled into a higher-order filter
            "{{ ['b', 'a']|sort('strcmp')|join }}",
            "{{ ['a']|map('getenv')|join }}",
            // Writing a template through the datasource, if a plugin passes a Halcyon model in
            "{{ p.insert({'fileName': 'x.htm'}) }}",
            "{{ p.offsetSet('fileName', 'x.htm') }}",
            "{{ attribute(p, '__set', ['fileName', 'x.htm']) }}",
            '{{ p.newQuery() }}',
        ];

        $data = ['m' => $model, 'p' => Page::inTheme(Theme::load('test'))];

        foreach ($escapes as $template) {
            try {
                $twig->createTemplate($template)->render($data);
                $this->fail('Sandbox allowed: ' . $template);
            } catch (SecurityError | SyntaxError | RuntimeError $ex) {
                $this->assertTrue(true);
            }
        }
    }

    /**
     * ... while mail renders outside one. Every mail view the modules ship goes through the plain
     * mailer environment, and these would be the first things to break if it were ever sandboxed
     * again: between them they use the wildcard markup families, the `{% partial %}` tag and the
     * filters a message needs.
     */
    public function testTheMailerEnvironmentIsNotSandboxedAndRendersTheCoreMailViews(): void
    {
        $twig = $this->app->make('twig.environment.mailer');

        foreach ($this->coreMailViews() as $name => $content) {
            $this->assertIsString($twig->createTemplate($content)->render($this->mailViewData()), $name);
        }

        // The partial tag, which only the mailer environment registers. Resolving the partial
        // itself needs the database, so all this asserts is that the tag was not refused.
        $this->assertIsString(
            $twig->createTemplate("{% partial 'button' url=link body %}Sign in{% endpartial %}")
                ->render($this->mailViewData())
        );

        // And the markup extensions a mail template is free to use, which a sandbox would refuse.
        $this->assertSame(
            'abcd...',
            trim($twig->createTemplate("{{ str_limit('abcdefgh', 4) }}")->render())
        );
    }

    protected function coreMailViews(): array
    {
        $views = [];

        foreach (glob(base_path('modules/system/views/mail/*.php')) as $view) {
            $views[basename($view)] = file_get_contents($view);
        }

        foreach (glob(base_path('modules/backend/views/mail/*.php')) as $view) {
            $views[basename($view)] = file_get_contents($view);
        }

        return $views;
    }

    protected function mailViewData(): array
    {
        return [
            'appName' => 'Winter',
            'body' => 'Hello',
            'brandCss' => '',
            'content' => '<p>Hello</p>',
            'css' => '',
            'link' => 'https://example.com',
            'login' => 'admin',
            'name' => 'Alice',
            'password' => 'hunter2',
            'subject' => 'Subject',
            'type' => 'primary',
            'url' => 'https://example.com',
        ];
    }
}

/**
 * Carries a null column, an accessor that resolves to null, a relation whose foreign key is empty,
 * a column named after a query builder method, a getter and a scope, so one fixture covers every
 * branch of the read/behaviour split.
 */
class SandboxPolicyTestModel extends Model
{
    /**
     * @var string[] Behaviour the sandbox let run, which must stay empty.
     */
    public static $called = [];

    public $table = 'sandbox_policy_test';

    protected $guarded = [];

    public $belongsTo = [
        'parent' => [self::class, 'key' => 'parent_id'],
    ];

    public function getDerivedAttribute()
    {
        return null;
    }

    public function getSecretThing()
    {
        return 'kept';
    }

    public function scopeActive($query)
    {
        static::$called[] = 'scope';
        return $query;
    }
}
