<?php

namespace System\Tests\Twig;

use InvalidArgumentException;
use System\Classes\MarkupManager;
use System\Tests\Bootstrap\TestCase;
use Twig\Error\SyntaxError;
use Twig\Extension\SandboxExtension;
use Twig\Sandbox\SecurityError;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;

/**
 * Covers the 1.2.15 sandbox posture: only a template that a developer did not write is
 * sandboxed, and the sandbox that applies to one is an allowlist.
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

    public function testOnlyTheMailerEnvironmentIsSandboxed(): void
    {
        Config::set('cms.twigNoCache', true);

        $this->assertFalse(
            $this->app->make('twig.environment')->hasExtension(SandboxExtension::class),
            'twig.environment backs the .htm view engine, which renders module and plugin views'
        );
        $this->assertFalse(
            $this->app->make('twig.environment.cms')->hasExtension(SandboxExtension::class),
            'twig.environment.cms renders theme templates, which only a code-level role can edit'
        );
        $this->assertTrue(
            $this->app->make('twig.environment.mailer')->hasExtension(SandboxExtension::class),
            'twig.environment.mailer renders templates a non-developer edits in the backend'
        );
    }

    /**
     * Theme templates are trusted code, so the things a theme legitimately does have to work.
     * Every case here was either refused or silently given the wrong answer while the sandbox
     * was unconditional: SafeCollection dropped the arguments of a Collection call, so
     * `rows.where(...)` and `rows.filter(...)` returned the unfiltered set.
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
        $data = ['m' => $model, 'rows' => $this->rows, 'when' => now()];

        $reads = [
            '{{ m.title }}' => 'Alice',
            '[{{ m.nickname }}]' => '[]',
            '[{{ m.derived }}]' => '[]',
            '{{ m.table }}' => 'sandbox_policy_test',
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
     * The mailer is the one core environment rendering a template a backend user edits, so it is
     * the one that has to keep refusing an escape. Each of these worked while the policy was a
     * blocklist, either because the method was not on the list or because the wildcard markup
     * extensions were never on it at all.
     */
    public function testTheMailerEnvironmentRefusesAnEscape(): void
    {
        $twig = $this->app->make('twig.environment.mailer');
        $model = new SandboxPolicyTestModel(['title' => 'Alice']);

        $escapes = [
            // Arbitrary static call on a facade, through a wildcard markup extension
            '{{ str_random(8) }}',
            "{{ url_to('/') }}",
            // Arbitrary function call: Arr::first() invokes a callable argument, and a template
            // can write the [class, method] array form
            "{{ array_first(['/etc/passwd'], ['Winter\\\\Storm\\\\Support\\\\Facades\\\\File', 'get']) }}",
            // Reading another template off disk
            "{{ source('.env') }}",
            "{% extends 'system::mail.layout-default' %}",
            // The database layer, reached through a model the sending plugin passed in
            '{{ m.getConnection() }}',
            '{{ m.newQuery() }}',
        ];

        foreach ($escapes as $template) {
            try {
                $twig->createTemplate($template)->render(['m' => $model]);
                $this->fail('Mailer sandbox allowed: ' . $template);
            } catch (SecurityError | SyntaxError $ex) {
                $this->assertTrue(true);
            }
        }
    }

    /**
     * ... while every layout and partial the module ships still renders.
     */
    public function testTheMailerEnvironmentStillRendersTheCoreMailViews(): void
    {
        $twig = $this->app->make('twig.environment.mailer');
        $data = [
            'appName' => 'Winter',
            'body' => 'Hello',
            'brandCss' => '',
            'content' => '<p>Hello</p>',
            'css' => '',
            'link' => 'https://example.com',
            'login' => 'admin',
            'name' => 'Alice',
            'subject' => 'Subject',
            'type' => 'primary',
            'url' => 'https://example.com',
        ];

        foreach (glob(base_path('modules/system/views/mail/*.php')) as $view) {
            $this->assertIsString(
                $twig->createTemplate(file_get_contents($view))->render($data),
                basename($view)
            );
        }

        // The partial tag, which only this environment registers and which the mailer therefore
        // has to allow by name. Resolving the partial itself needs the database, so all this
        // asserts is that the tag was not refused.
        $this->assertIsString(
            $twig->createTemplate("{% partial 'button' url=link body %}Sign in{% endpartial %}")->render($data)
        );
    }
}

/**
 * Carries a null column, an accessor that resolves to null, a column named after a query builder
 * method, a getter and a scope, so one fixture covers every branch of the read/behaviour split.
 */
class SandboxPolicyTestModel extends Model
{
    public $table = 'sandbox_policy_test';

    protected $guarded = [];

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
        return $query;
    }
}
