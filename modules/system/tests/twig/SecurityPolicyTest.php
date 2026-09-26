<?php

namespace System\Tests\Twig;

use Cache;
use Cms\Classes\ComponentBase;
use Cms\Classes\Controller;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Config;
use Event;
use System\Tests\Bootstrap\TestCase;
use System\Tests\Fixtures\Twig\SandboxCanary;
use Twig\Environment;
use Winter\Storm\Filesystem\Filesystem;
use Winter\Storm\Halcyon\Datasource\FileDatasource;

/**
 * Stand-in for a plugin component; components are exposed to the page's Twig by their alias.
 */
class SecurityPolicyTestComponent extends ComponentBase
{
    public function componentDetails()
    {
        return ['name' => 'Security policy test', 'description' => ''];
    }

    public function items()
    {
        return ['first', 'second'];
    }
}

class SecurityPolicyTest extends TestCase
{
    protected Environment $twig;

    public function testCannotGetTwigInstanceFromCmsController()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set twig = this.controller.getTwig() %}
            {{ this.controller.getTwig() }}
        ');
    }

    public function testAllowedMethods()
    {
        // put, get
        $value = trim($this->renderTwigInCmsController('
            {{ this.session.put("test", "value") }}
            {{ this.session.get("test", "default") }}
        '));
        $this->assertEquals("value", $value);

        // has
        $value = trim($this->renderTwigInCmsController('
            {{ this.session.put("test", "value") }}
            {% if this.session.has("test") %}success{% else %}failure{% endif %}
        '));
        $this->assertEquals("success", $value);

        // forget
        $value = trim($this->renderTwigInCmsController('
            {{ this.session.put("test", "value") }}
            {{ this.session.forget("test") }}
            {% if this.session.has("test") %}failure{% else %}success{% endif %}
        '));
        $this->assertEquals("success", $value);

        // flush
        $value = trim($this->renderTwigInCmsController('
            {{ this.session.put("test", "value") }}
            {{ this.session.flush() }}
            {% if this.session.has("test") %}failure{% else %}success{% endif %}
        '));
        $this->assertEquals("success", $value);

        // Test all other methods blocked
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ this.session.driver }}
        ');
    }

    public function testCannotGetTwigLoaderFromCmsController()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set loader = this.controller.getLoader() %}
            {{ loader.load(\'/\') }}
        ');
    }

    public function testCannotRunAPageObjectFromWithinTwig()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {{ this.controller.runPage() }}
        ');
    }

    public function testCannotExtendAPageWithADynamicMethod()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set page = this.page.addDynamicMethod("test") %}
        ');
    }

    public function testCannotExtendAPageWithADynamicProperty()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set page = this.page.addDynamicProperty("test", "value") %}
        ');
    }

    public function testCannotWriteToAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set modelTest = model.setAttribute("test", "value") %}
        ', [
            'model' => new \Winter\Storm\Database\Model(),
        ]);
    }

    public function testCanReadFromAModel()
    {
        $model = new \Winter\Storm\Database\Model();
        $model->test = 'value';

        $result = trim($this->renderTwigInCmsController('
            {% set modelTest = model.getAttribute("test") %}
            {{- modelTest -}}
        ', [
            'model' => $model,
        ]));
        $this->assertEquals('value', $result);
    }

    public function testCannotAccessModelQuery()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {{ dump(model.getQuery) }}
        ', [
            'model' => new \Winter\Storm\Database\Model(),
        ]);
    }

    public function testCannotFillAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        try {
            $model = new \Winter\Storm\Database\Model();
            $model->addFillable('test');
            $model->test = 'value';

            $this->renderTwigInCmsController('
                {% set modelTest = model.fill({ test: \'value2\' }) %}
            ', [
                'model' => new \Winter\Storm\Database\Model(),
            ]);
        } catch (\Twig\Sandbox\SecurityNotAllowedMethodError $e) {
            // Ensure value hasn't changed
            $this->assertEquals('value', $model->test);
            throw $e;
        }
    }

    public function testCannotSaveAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set modelTest = model.save() %}
        ', [
            'model' => new \Winter\Storm\Database\Model(),
        ]);
    }

    public function testCannotPushAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set modelTest = model.push() %}
        ', [
            'model' => new \Winter\Storm\Database\Model(),
        ]);
    }

    public function testCannotUpdateAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $model = new \Winter\Storm\Database\Model();
        $model->addFillable('test');
        $model->test = 'value';

        $this->renderTwigInCmsController('
            {% set modelTest = model.update({ test: \'value2\' }) %}
        ', [
            'model' => $model,
        ]);
    }

    public function testCannotDeleteAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set modelTest = model.delete() %}
        ', [
            'model' => new \Winter\Storm\Database\Model(),
        ]);
    }

    public function testCannotForceDeleteAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set modelTest = model.forceDelete() %}
        ', [
            'model' => new \Winter\Storm\Database\Model(),
        ]);
    }

    public function testCannotExtendAModelWithABehaviour()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set model = model.extendClassWith("Winter\Storm\Database\Behaviors\Encryptable") %}
        ', [
            'model' => new \Winter\Storm\Database\Model(),
        ]);
    }

    public function testExtendingModelBeforePassingIntoTwigShouldStillWork()
    {
        $model = new \Winter\Storm\Database\Model();
        $model->addDynamicMethod('foo', function () {
            return 'foo';
        });

        $result = trim($this->renderTwigInCmsController('
            {{- model.foo() -}}
        ', [
            'model' => $model,
        ]));
        $this->assertEquals('foo', $result);
    }

    public function testCannotGetDatasourceFromTheme()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set datasource = this.theme.getDatasource() %}
        ');
    }

    // Even if someone decides to be clever and make the datasource available, you shouldn't be able to insert/delete/update
    public function testCannotDeleteInDatasource()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set datasource = datasource.delete() %}
        ', [
            'datasource' => new FileDatasource(
                base_path('modules/system/tests/fixtures/themes/test'),
                new Filesystem()
            ),
        ]);
    }

    public function testCannotInsertInDatasource()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set datasource = datasource.insert() %}
        ', [
            'datasource' => new FileDatasource(
                base_path('modules/system/tests/fixtures/themes/test'),
                new Filesystem()
            ),
        ]);
    }

    public function testCannotUpdateInDatasource()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set datasource = datasource.update() %}
        ', [
            'datasource' => new FileDatasource(
                base_path('modules/system/tests/fixtures/themes/test'),
                new Filesystem()
            ),
        ]);
    }

    public function testCannotChangeThemeDirectory()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        $this->renderTwigInCmsController('
            {% set theme = this.theme.setDirName("test") %}
        ');
    }

    //
    // GHSA-8cfw-pcwh-v63w — bypasses of the CVE-2024-54149 patch, and adjacent vectors
    //

    public function testCannotSaveQuietlyAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.saveQuietly() %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    public function testCannotForceFillAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.forceFill({ is_admin: 1 }) %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    public function testCannotDestroyAModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.destroy(1) %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    // Reaches the Query Builder through the Model's __call forwarding (blocked via the chain)
    public function testCannotIncrementAModelViaForwarderChain()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.increment("price", 99999) %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    // callable-typed builder method reached via the chain — would execute a string callable
    public function testCannotCallWhenExecutorOnModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.when(1, "phpinfo") %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    public function testCannotCallEachExecutorOnModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.each("phpinfo") %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    public function testCannotGetConnectionResolverFromModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set r = model.getConnectionResolver() %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    // The DatabaseManager (ConnectionResolverInterface) forwards any method to a live Connection
    public function testCannotRunArbitrarySqlViaConnectionResolver()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set rows = resolver.select("SELECT 1") %}
        ', ['resolver' => app('db')]);
    }

    // extend() runs an arbitrary callable bound to the model — a direct RCE primitive
    public function testCannotExecuteCallableViaExtend()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.extend("phpinfo") %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    public function testCannotRunCallableViaUnguarded()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.unguarded("phpinfo") %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    // Deferred callable-injection via the extension callback registrars
    public function testCannotRegisterExtendCallbackOnModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.extendableExtendCallback("phpinfo") %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    public function testCannotRepointAModelTable()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.setTable("backend_users") %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    // RCE PoC: writing PHP into the current page/layout code section via the Halcyon Builder
    public function testCannotUpdateViaHalcyonBuilder()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = this.page.newQuery().update({ code: "<?php echo 1; ?>" }) %}
        ');
    }

    public function testCannotUpdateALayoutModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = this.layout.update({ code: "x" }) %}
        ');
    }

    public function testCannotWriteThemeConfig()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = this.theme.writeConfig({ foo: "bar" }) %}
        ');
    }

    public function testCannotRunNestedPageCycleFromController()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = this.controller.run("/") %}
        ');
    }

    public function testCannotFireSystemEventFromController()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = this.controller.fireSystemEvent("test.event", []) %}
        ');
    }

    public function testCannotUseSourceFunction()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedFunctionError::class);
        $this->renderTwigInCmsController('
            {{ source("backend::index") }}
        ');
    }

    public function testCannotUseConstantFunction()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedFunctionError::class);
        $this->renderTwigInCmsController('
            {{ constant("PHP_VERSION") }}
        ');
    }

    // Regression guard: the custom GetAttrNode must still enforce the policy (forward $sandboxed)
    public function testCustomAttributeNodeStillEnforcesPolicy()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = model.save() %}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    //
    // Halcyon models forward to the Halcyon Builder via __call, so the builder's blocklist
    // must apply to a CMS page/layout/partial receiver as well. `insert` writes a template
    // file — code section included — straight to the theme datasource, bypassing save() and
    // therefore Safe Mode.
    //

    public function testCannotInsertViaAHalcyonModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = this.page.insert({ code: "<?php echo 1; ?>" }) %}
        ');
    }

    // newInstance()/newFromBuilder() hand out a model with a template-chosen file name, so
    // the write is not limited to the file that is already being rendered. Asserted on the file
    // as well as on the exception: a write that got through would leave a template behind in the
    // active theme, which the exception assertion alone would not notice.
    public function testCannotInsertViaAMintedHalcyonModel()
    {
        $injected = Theme::getActiveTheme()->getPath() . '/pages/injected.htm';
        $error = null;

        try {
            $this->renderTwigInCmsController('
                {% set _ = this.page.newInstance({ fileName: "injected" }).insert({ markup: "x" }) %}
            ');
        } catch (\Throwable $ex) {
            $error = $ex;
        }

        $written = file_exists($injected);
        \File::delete($injected);

        $this->assertFalse($written, 'A template file was written to the theme.');
        $this->assertInstanceOf(\Twig\Sandbox\SecurityNotAllowedMethodError::class, $error);
    }

    public function testCannotTruncateViaAHalcyonModel()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = this.layout.truncate() %}
        ');
    }

    // Builder::from() re-points the datasource directory, which reads files outside the theme.
    public function testCannotRepointTheHalcyonQueryDirectory()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = this.page.from("../../..") %}
        ');
    }

    // Invalidation: the read side of the Halcyon query API must keep working from a template.
    public function testCanStillQueryHalcyonModelsReadOnly()
    {
        $result = trim($this->renderTwigInCmsController('
            {%- set query = this.page.newQuery().whereFileName("does-not-exist").limit(1) -%}
            {{- query.getModel().getObjectTypeDirName() -}}
        '));
        $this->assertEquals('pages', $result);
    }

    //
    // ComponentBase::__call falls back to the CMS controller, so the controller's blocklist
    // must apply to a component receiver as well.
    //

    public function testCannotRunANestedPageCycleThroughAComponent()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = component.run("/") %}
        ', ['component' => new SecurityPolicyTestComponent()]);
    }

    public function testCannotGetTwigLoaderThroughAComponent()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = component.getLoader() %}
        ', ['component' => new SecurityPolicyTestComponent()]);
    }

    public function testCannotRenderArbitraryPartialsThroughAComponent()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ component.renderPartial("secret") }}
        ', ['component' => new SecurityPolicyTestComponent()]);
    }

    // Invalidation: a component's own methods and properties stay callable from a template.
    public function testCanStillCallAComponentsOwnMethods()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- component.items()|join(",") -}}',
            ['component' => new SecurityPolicyTestComponent()]
        ));
        $this->assertEquals('first,second', $result);
    }

    //
    // SafeCollection — higher-order callable arguments are neutralised, reads still work
    //

    // filter("is_numeric") would keep only numeric items; stripped to filter(null) it only drops
    // falsy values, so all three truthy strings survive — proving the callable was neutralised.
    public function testSafeCollectionStripsMethodCallback()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- items.filter("is_numeric").count() -}}',
            ['items' => collect(['1', 'a', '2'])]
        ));
        $this->assertEquals('3', $result);
    }

    // The built-in attribute() function compiles to an ANY_CALL and must be cast as well.
    public function testSafeCollectionStripsViaAttributeFunction()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- attribute(items, "filter", ["is_numeric"]).count() -}}',
            ['items' => collect(['1', 'a', '2'])]
        ));
        $this->assertEquals('3', $result);
    }

    public function testSafeCollectionRemainsIterable()
    {
        $result = trim($this->renderTwigInCmsController(
            '{% for i in items.filter("is_numeric") %}{{ i }}{% endfor %}',
            ['items' => collect(['1', 'a', '2'])]
        ));
        $this->assertEquals('1a2', $result);
    }

    public function testTwigMapFilterFormStillWorks()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- items|map(v => v ~ "!")|join(",") -}}',
            ['items' => collect(['a', 'b'])]
        ));
        $this->assertEquals('a!,b!', $result);
    }

    //
    // Array and object callables are callables too
    //
    // Note: the method name deliberately is not `get`; Storm defines a global `get()`
    // helper, so `['...\File', 'get']` was already neutered by the element recursion.
    // `sharedGet` is not a global function, so it exercises the real gap.
    //

    // reduce() invokes its callback with the initial value as its first argument, so the
    // callback is handed a path the template chose.
    public function testSafeCollectionStripsArrayCallableFileRead()
    {
        $output = $this->renderExpectingStrippedCallable(
            '{{ items.reduce(["Illuminate\\\\Support\\\\Facades\\\\File", "sharedGet"], path) }}',
            [
                'items' => collect(['x']),
                'path' => base_path('modules/system/tests/fixtures/themes/test/pages/index.htm'),
            ]
        );

        $this->assertStringNotContainsString('My Webpage', $output);
    }

    // merge() puts a template-supplied value into the collection, so the callback's second
    // argument comes from the template too.
    public function testSafeCollectionStripsArrayCallableFileWrite()
    {
        $target = sys_get_temp_dir() . '/sandbox-callable-probe-' . getmypid() . '.php';
        @unlink($target);

        $this->renderExpectingStrippedCallable(
            '{{ items.merge(["<?php echo 1; ?>"]).reduce(["Illuminate\\\\Support\\\\Facades\\\\File", "put"], path) }}',
            [
                'items' => collect([]),
                'path' => $target,
            ]
        );

        $this->assertFileDoesNotExist($target);
    }

    // An [object, method] pair is a callable as well, and invoking one this way never goes
    // through checkMethodAllowed() — setAttribute() is blocked as a method call but would
    // otherwise run here.
    public function testSafeCollectionStripsObjectCallable()
    {
        $model = new \Winter\Storm\Database\Model();

        $this->renderExpectingStrippedCallable(
            '{% set _ = items.reduce([model, "setAttribute"], "pwned") %}',
            [
                'items' => collect(['value']),
                'model' => $model,
            ]
        );

        $this->assertNull($model->pwned);
    }

    public function testSafePaginatorStripsArrayCallable()
    {
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(['ab'], 1, 10, 1);

        $this->renderExpectingStrippedCallable(
            '{{ pager.through(["Winter\\\\Storm\\\\Support\\\\Str", "upper"]).items()|join(",") }}',
            ['pager' => $paginator]
        );

        $this->assertEquals(['ab'], $paginator->items());
    }

    //
    // Invalidation: two-element arrays that are not PHP callables must survive untouched
    //

    public function testSafeCollectionKeepsNonCallableArrayArguments()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- items.merge(["name", "asc"])|join(",") -}}',
            ['items' => collect(['a'])]
        ));
        $this->assertEquals('a,name,asc', $result);
    }

    /**
     * Collection::sortByMany() reads each [key, direction] pair with data_get() and never
     * invokes it, so the pair is data even though is_callable() is true for it whenever the
     * first element names an aliased facade.
     */
    public function testSafeCollectionKeepsAMultiSortSpecification()
    {
        // Guards against this passing for the wrong reason if the alias were ever dropped
        $this->assertTrue(is_callable(['Date', 'desc']), 'the probe pair is not callable');

        $result = trim($this->renderTwigInCmsController(
            '{{- items.sortBy([["Date", "desc"], ["name", "asc"]]).pluck("name")|join(",") -}}',
            ['items' => collect([
                ['Date' => 2, 'name' => 'b'],
                ['Date' => 1, 'name' => 'a'],
                ['Date' => 1, 'name' => 'c'],
            ])]
        ));

        $this->assertEquals('b,a,c', $result);
    }

    /**
     * Only the pair itself is data. sortByMany() does invoke a first element that is not a
     * string, so a callable nested inside the pair is still stripped.
     */
    public function testSafeCollectionStripsACallableNestedInAMultiSortSpecification()
    {
        SandboxCanary::reset();

        $this->renderExpectingStrippedCallable(
            '{% set _ = items.sortBy([[[canary, "invoke"], "asc"]]) %}',
            [
                'items' => collect([['name' => 'a'], ['name' => 'b']]),
                'canary' => SandboxCanary::class,
            ]
        );

        $this->assertEquals(0, SandboxCanary::$invocations, 'the nested callable was invoked');
    }

    public function testSafeCollectionKeepsArrayOfKeyNames()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- items.groupBy(["type", "status"]).keys()|join(",") -}}',
            ['items' => collect([['type' => 'a', 'status' => 'new']])]
        ));
        $this->assertEquals('a', $result);
    }

    public function testSafeCollectionKeepsHybridStringArguments()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- items.sortBy("n").pluck("n")|join(",") -}}',
            ['items' => collect([['n' => 'b'], ['n' => 'a']])]
        ));
        $this->assertEquals('a,b', $result);
    }

    //
    // CmsCompoundObject::__call dispatches its $passthru methods
    // (lists, where, sortBy, whereComponent, withComponent) straight onto the collection of
    // every object of that type, so the receiver cast has to target that collection: casting
    // the model itself leaves the call unguarded.
    //

    // sortBy() reaches Collection::sortBy(), which invokes the callback per item.
    public function testCmsCompoundObjectPassthruStripsArrayCallable()
    {
        Cache::put('sandbox-passthru-probe', 'intact', 60);

        $this->renderTwigInTestTheme(
            '{% set _ = this.page.sortBy(["Illuminate\\\\Support\\\\Facades\\\\Cache", "flush"]) %}'
        );

        $this->assertEquals('intact', Cache::get('sandbox-passthru-probe'));
    }

    // withComponent() calls call_user_func($callback, $component) with no useAsCallable()
    // guard, so a plain string callable executes there too.
    public function testCmsCompoundObjectPassthruStripsStringCallable()
    {
        Cache::put('sandbox-passthru-probe', 'intact', 60);

        $this->renderTwigInTestTheme(
            '{% set _ = this.page.withComponent("testArchive", "Illuminate\\\\Support\\\\Facades\\\\Cache::flush") %}'
        );

        $this->assertEquals('intact', Cache::get('sandbox-passthru-probe'));
    }

    // attribute() compiles to an ANY_CALL and reaches the same dispatch.
    public function testCmsCompoundObjectPassthruStripsViaAttributeFunction()
    {
        Cache::put('sandbox-passthru-probe', 'intact', 60);

        $this->renderTwigInTestTheme(
            '{% set _ = attribute(this.page, "sortBy", [["Illuminate\\\\Support\\\\Facades\\\\Cache", "flush"]]) %}'
        );

        $this->assertEquals('intact', Cache::get('sandbox-passthru-probe'));
    }

    // Invalidation: the passthru methods are public CMS template API and take property and
    // component names, not callbacks. "url" is also a global function, so it is exactly the
    // shape that callable stripping must not swallow.
    public function testCmsCompoundObjectPassthruStillWorks()
    {
        $this->assertEquals('/', trim($this->renderTwigInTestTheme(
            '{{- this.page.lists("url")|sort|first -}}'
        )));

        $this->assertEquals('1', trim($this->renderTwigInTestTheme(
            '{{- this.page.where("url", "/").count() -}}'
        )));

        $this->assertEquals('2', trim($this->renderTwigInTestTheme(
            '{{- this.page.withComponent("testArchive").count() -}}'
        )));

        $this->assertEquals('0', trim($this->renderTwigInTestTheme(
            '{{- this.page.whereComponent("testArchive", "posts", "nope").count() -}}'
        )));

        $total = trim($this->renderTwigInTestTheme('{{- this.page.lists("fileName")|length -}}'));
        $this->assertGreaterThan(0, (int) $total);
        $this->assertEquals($total, trim($this->renderTwigInTestTheme(
            '{{- this.page.sortBy("fileName").count() -}}'
        )));
    }

    /**
     * withComponent() takes a component name, or a list of them, and a component name collides
     * with a callable readily - `[session]` is the component Winter.User ships and `session()`
     * is a Laravel helper - so the name has to survive the callable stripping. Its second
     * argument is a real callback and still does not.
     */
    public function testCmsObjectCollectionKeepsAComponentNameThatLooksLikeACallable()
    {
        $this->assertTrue(is_callable('session'), 'the probe name is not callable');
        $this->assertTrue(is_callable(['session', 'testArchive']), 'the probe list is not callable');

        $objects = new \Cms\Classes\CmsObjectCollection([new SecurityPolicyComponentProbe()]);

        $this->assertEquals('1', trim($this->renderTwigInCmsController(
            '{{- objects.withComponent("session").count() -}}',
            ['objects' => $objects]
        )));

        $this->assertEquals('1', trim($this->renderTwigInCmsController(
            '{{- objects.withComponent(["session", "testArchive"]).count() -}}',
            ['objects' => $objects]
        )));

        $this->assertEquals('0', trim($this->renderTwigInCmsController(
            '{{- objects.withComponent("nothingHere").count() -}}',
            ['objects' => $objects]
        )));
    }

    public function testCmsObjectCollectionWithComponentStillStripsItsCallback()
    {
        $objects = new \Cms\Classes\CmsObjectCollection([new SecurityPolicyComponentProbe()]);

        foreach (
            [
                '["Illuminate\\\\Support\\\\Facades\\\\Cache", "flush"]',
                '"Illuminate\\\\Support\\\\Facades\\\\Cache::flush"',
            ] as $callback
        ) {
            Cache::put('sandbox-withcomponent-probe', 'intact', 60);

            $this->renderTwigInCmsController(
                '{% set _ = objects.withComponent("session", ' . $callback . ') %}',
                ['objects' => $objects]
            );

            $this->assertEquals('intact', Cache::get('sandbox-withcomponent-probe'), $callback);
        }
    }

    // The same key names reach SafeCollection directly through listPages(); they were being
    // nulled there too, because is_callable("url") is true.
    public function testSafeCollectionKeepsCmsObjectCollectionKeyNames()
    {
        $this->assertEquals('1', trim($this->renderTwigInTestTheme(
            '{{- this.theme.listPages().where("url", "/").count() -}}'
        )));
    }


    //
    // Callable arguments — a template must never be able to name a PHP callable that runs
    //

    // Str::of() hands the template a Stringable, whose pipe()/tap() call the given callable
    // with the (template-supplied) string.
    public function testCannotPipeAStringableIntoACallable()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ str_of("id").pipe("shell_exec") }}
        ');
    }

    public function testCannotTapAnObjectWithACallable()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ str_of("id").tap("shell_exec") }}
        ');
    }

    // The whole when*() family forwards its first argument to Conditionable::when()
    public function testCannotConditionallyExecuteACallableOnAStringable()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ str_of("/etc/passwd").whenNotEmpty("file_get_contents") }}
        ');
    }

    public function testCannotConditionallyExecuteACallableOnAnyObject()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ date.when(true, "shell_exec") }}
        ', ['date' => now()]);
    }

    public function testStringableMethodsStillWork()
    {
        $result = trim($this->renderTwigInCmsController('{{- str_of("hello world").limit(5).upper() -}}'));
        $this->assertEquals('HELLO...', $result);
    }

    //
    // Wildcard markup extensions — str_*, array_*, html_*, url_*, form_*
    //

    // Arr::map() executes the callable it is given for every item
    public function testCannotPassACallbackToAWildcardMarkupFunction()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ array_map(["id"], "shell_exec")|join }}
        ');
    }

    public function testCannotPassACallbackToAWildcardMarkupFunctionOfAFacade()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ url_set_session_resolver("phpinfo") }}
        ');
    }

    // A macro registered from markup is callable through the same wildcard
    public function testCannotRegisterAMacroThroughAWildcardMarkupFunction()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ str_macro("pwn", "shell_exec") }}{{ str_pwn("id") }}
        ');
    }

    // Laravel leaves its hybrid callback parameters untyped - Arr::sort(), Arr::sortDesc()
    // and Arr::keyBy() all declare `$callback` with no type - so a guard keyed on the
    // declared parameter type misses them entirely. File::put($path, 0) writes the file,
    // which is observable whether or not the surrounding render then fails.
    public function testCannotPassACallableValueToAnUntypedWildcardParameter()
    {
        foreach (['array_sort', 'array_sort_desc', 'array_key_by'] as $function) {
            $this->assertWildcardCallbackBlocked($function, $function
                . '(["%s"], ["Illuminate\\\\Support\\\\Facades\\\\File", "put"])');
        }
    }

    // The setters store the callable for a later call rather than running it, so "did the
    // callback run during this render" is not a sufficient test on its own either. The
    // payload is a real declared static method, which PHP's `callable` type accepts - a
    // facade pair passes is_callable() but is rejected by the type check, which would make
    // this look blocked for the wrong reason.
    public function testCannotStoreACallableValueThroughAWildcardSetter()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {% set _ = str_create_uuids_using(["Winter\\\\Storm\\\\Support\\\\Str", "upper"]) %}
        ');
    }

    public function testCannotPassAStringCallableToATypedWildcardParameter()
    {
        foreach (['array_map', 'array_first', 'array_last', 'array_where', 'array_build'] as $function) {
            $this->assertWildcardCallbackBlocked($function, $function . '(["%s"], "touch")');
        }
    }

    // Wildcard helpers must keep working, including with string arguments that happen to
    // name a PHP function and with the optional callback left out
    public function testWildcardMarkupFunctionsStillWork()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- str_replace("trim", "X", "a trim b") -}}|'
            . '{{- array_get({"count": 7}, "count") -}}|'
            . '{{- array_first(["a", "b"]) -}}|'
            . '{{- array_sort([{n: 2}, {n: 1}], "n")|first.n -}}|'
            . '{{- array_key_by([{id: "x"}], "id")|keys|first -}}|'
            . '{{- html_link("/a", "t") -}}'
        ));
        $this->assertEquals('a X b|7|a|1|x|<a href="' . url('/a') . '">t</a>', $result);
    }

    /**
     * The narrowing above is only for a list of two strings. Every other callable shape is
     * still refused wherever it appears, including where the callee would never invoke it.
     */
    public function testWildcardMarkupFunctionsRefuseANonStringCallableInAnyPosition()
    {
        foreach (
            [
                '{{ html_ul([component, "items"])|raw }}',
                '{{ html_attributes({class: [component, "items"]})|raw }}',
                '{{ array_only({"a": 1}, [component, "items"])|json_encode|raw }}',
            ] as $source
        ) {
            $error = null;

            try {
                $this->renderTwigInCmsController($source, ['component' => new SecurityPolicyTestComponent()]);
            } catch (\Throwable $ex) {
                $error = $ex;
            }

            $this->assertInstanceOf(\Twig\Sandbox\SecurityNotAllowedMethodError::class, $error, $source);
        }
    }

    /**
     * A two-element list of strings is how a template writes an ordinary short list, and
     * is_callable() is true for any such list whose first element names a class that can
     * dispatch the second - which every aliased facade does, through __callStatic. The list is
     * read as data outside the positions the callee could invoke, so a list whose values happen
     * to spell one of those names keeps working. It is refused in the callback positions, which
     * testCannotPassACallableValueToAnUntypedWildcardParameter covers.
     */
    public function testWildcardMarkupFunctionsAcceptATwoElementListOfNames()
    {
        // Guards against this test passing for the wrong reason if the alias were ever dropped
        $this->assertTrue(is_callable(['Log', 'Debug']), 'the probe list is not callable');

        $result = trim($this->renderTwigInCmsController(
            '{{- html_ul(["Log", "Debug"])|raw -}}|'
            . '{{- array_only({"Log": 1, "Debug": 2, "Web": 3}, ["Log", "Debug"])|json_encode|raw -}}|'
            . '{{- array_except({"Log": 1, "Debug": 2, "Web": 3}, ["Log", "Debug"])|json_encode|raw -}}|'
            . '{{- str_replace(["Log", "Debug"], "x", "Log and Debug") -}}|'
            . '{{- array_flatten([["Log", "Debug"]])|join(",") -}}|'
            . '{{- array_sort(["Log", "Debug"])|join(",") -}}'
        ));

        $this->assertEquals(
            '<ul><li>Log</li><li>Debug</li></ul>|{"Log":1,"Debug":2}|{"Web":3}|x and x|Log,Debug|Debug,Log',
            $result
        );
    }

    /**
     * A variadic signature describes every further argument with its last parameter, so those
     * positions are classified like it. Arr::crossJoin(...$arrays) is the one variadic wildcard
     * target and it invokes nothing, so an ordinary list of names is accepted in any position.
     */
    public function testWildcardMarkupFunctionsAcceptAListOfNamesInAVariadicPosition()
    {
        $this->assertTrue(is_callable(['File', 'Edit']), 'the probe list is not callable');

        $result = trim($this->renderTwigInCmsController(
            '{{- array_cross_join(["Red", "Blue"], ["File", "Edit"])|length -}}'
        ));

        $this->assertEquals('4', $result);
    }

    //
    // Raw SQL
    //

    // selectSub() passes a plain string through as raw SQL (Query\Builder::parseSub)
    public function testCannotInjectRawSqlThroughASubquery()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ model.newQuery().selectSub("select persist_code from backend_users", "x").toSql() }}
        ', ['model' => new \Winter\Storm\Database\Model()]);
    }

    //
    // Attachments
    //

    // from*() copies the named file into the publicly served uploads disk, from where
    // getContents()/getPath() would hand it back
    public function testCannotReadAServerFileThroughAnAttachment()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ file.fromFile("/etc/passwd", "leak.txt").getContents() }}
        ', ['file' => new \Winter\Storm\Database\Attach\File()]);
    }

    public function testCannotFetchARemoteUrlThroughAnAttachment()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ file.fromUrl("http://169.254.169.254/latest/meta-data/") }}
        ', ['file' => new \Winter\Storm\Database\Attach\File()]);
    }

    // getDisk() returns a live FilesystemAdapter for the uploads disk
    public function testCannotReachTheStorageDiskThroughAnAttachment()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ file.getDisk().put("shell.php", "<?php eval($_GET[0]);") }}
        ', ['file' => new \Winter\Storm\Database\Attach\File()]);
    }

    public function testCanStillReadAnAttachmentsOwnMetadata()
    {
        $file = new \Winter\Storm\Database\Attach\File();
        $file->file_name = 'photo.jpg';

        $result = trim($this->renderTwigInCmsController(
            '{{- file.getFilename() -}}|{{- file.getExtension() -}}',
            ['file' => $file]
        ));
        $this->assertEquals('photo.jpg|jpg', $result);
    }

    //
    // Reserved session keys
    //

    // admin_auth* is backend authentication state, not scratch space for a template
    public function testCannotWriteTheBackendAuthSessionKey()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ this.session.put("admin_auth_impersonator", 1) }}
        ');
    }

    // Backend\Traits\SessionMaker feeds widget.* session values to unserialize()
    public function testCannotWriteTheWidgetStateSessionKey()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ this.session.put("widget.Backend-Users-userForm", "payload") }}
        ');
    }

    /**
     * The session store resolves keys with Arr::get(), so a value written to the container a
     * reserved prefix hangs from is read back under that prefix. Backend\Traits\SessionMaker
     * reads `widget.<id>` and hands what it finds to unserialize().
     */
    public function testCannotWriteTheContainerOfAReservedSessionKey()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ this.session.put("widget", {"Backend-Users-userForm": "payload"}) }}
        ');
    }

    public function testCannotWriteTheContainerOfAReservedSessionKeyAsAnArray()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ this.session.put({"widget": {"Backend-Users-userForm": "payload"}}) }}
        ');
    }

    public function testCannotWriteAReservedSessionKeyAsAnArray()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ this.session.put({"_token": "known"}) }}
        ');
    }

    public function testCannotForgetAReservedSessionKey()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ this.session.forget("_token") }}
        ');
    }

    // forget() takes a list of keys, not a key => value map
    public function testCannotForgetAReservedSessionKeyFromAList()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ this.session.forget(["cart", "_token"]) }}
        ');
    }

    public function testCannotPullAReservedSessionKey()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {{ this.session.pull("admin_auth") }}
        ');
    }

    public function testSessionKeysOutsideTheReservedSetStillWork()
    {
        $value = trim($this->renderTwigInCmsController('
            {{ this.session.put("cart", {"id": 1}) }}
            {{ this.session.pull("cart").id }}
        '));
        $this->assertEquals('1', $value);
    }

    // The session surface is the same whichever object a template was handed: the manager the
    // CMS controller supplies, or the underlying store, which a plugin or component can put in
    // the page variables. An argument-less access is not cast to SafeSession, so the store needs
    // its own entry in the policy for the two shapes to agree.
    public function testMethodsOutsideTheSessionSurfaceAreBlockedOnAStoreToo()
    {
        foreach (['token', 'invalidate', 'regenerateToken', 'regenerate', 'migrate', 'all', 'getId', 'save'] as $method) {
            $refused = false;

            try {
                $this->renderTwigInCmsController(
                    '{{ store.' . $method . ' }}',
                    ['store' => app('session')->driver()]
                );
            } catch (\Twig\Sandbox\SecurityNotAllowedMethodError $ex) {
                $refused = true;
            }

            $this->assertTrue($refused, 'store.' . $method . ' was not refused');
        }
    }

    public function testTheSessionSurfaceStillWorksOnAStore()
    {
        $value = trim($this->renderTwigInCmsController('
            {{ store.put("cart", {"id": 2}) }}
            {% if store.has("cart") %}{{ store.pull("cart").id }}{% else %}failure{% endif %}
        ', ['store' => app('session')->driver()]));
        $this->assertEquals('2', $value);
    }

    /**
     * The key a template names rather than positions reaches the session just the same.
     *
     * @dataProvider namedSessionKeyProvider
     */
    public function testCannotWriteAReservedSessionKeyNamedAsAnArgument(string $source)
    {
        \Session::forget('admin_auth_probe');
        $error = null;

        try {
            $this->renderTwigInCmsController($source);
        } catch (\Throwable $ex) {
            $error = $ex;
        }

        $written = \Session::get('admin_auth_probe');
        \Session::forget('admin_auth_probe');

        $this->assertNull($written, 'a reserved session key was written');
        $this->assertTrue(
            $error instanceof \Twig\Sandbox\SecurityNotAllowedMethodError
                || $error instanceof \Twig\Error\SyntaxError,
            'expected the call to be refused, got: '
                . ($error ? get_class($error) . ': ' . $error->getMessage() : 'no error at all')
        );
    }

    public function namedSessionKeyProvider(): array
    {
        return [
            'put(key=)' => ['{{ this.session.put(key="admin_auth_probe", value="x") }}'],
            'put(key:)' => ['{{ this.session.put(key: "admin_auth_probe", value: "x") }}'],
            'put(spread)' => ['{{ this.session.put(...{key: "admin_auth_probe", value: "x"}) }}'],
            'attribute(put)' => ['{{ attribute(this.session, "put", {key: "admin_auth_probe", value: "x"}) }}'],
            'forget(keys=)' => ['{{ this.session.forget(keys="admin_auth_probe") }}'],
            'pull(key=)' => ['{{ this.session.pull(key="admin_auth_probe") }}'],
        ];
    }

    public function testCannotWriteAReservedSessionKeyThroughAStore()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController(
            '{{ store.put("admin_auth_impersonator", 1) }}',
            ['store' => app('session')->driver()]
        );
    }


    //
    // Builder methods that reach a table the model does not own, or that
    // turn a string argument into raw SQL. Blocked on the Query/Eloquent Builder and therefore
    // also when reached through a Model, Relation or Eloquent Builder ($blockedForwarders).
    //

    /**
     * @dataProvider builderEscapeProvider
     */
    public function testBuilderEscapesAreBlocked(string $expression)
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('{% set _ = model.' . $expression . ' %}', [
            'model' => new \Winter\Storm\Database\Model(),
        ]);
    }

    public function builderEscapeProvider(): array
    {
        return [
            // Reaching a second table
            'join' => ['join("backend_users", "backend_users.id", "=", "id")'],
            'joinWhere' => ['joinWhere("backend_users", "backend_users.id", "=", 1)'],
            'leftJoin' => ['leftJoin("backend_users", "backend_users.id", "=", "id")'],
            'leftJoinWhere' => ['leftJoinWhere("backend_users", "backend_users.id", "=", 1)'],
            'rightJoin' => ['rightJoin("backend_users", "backend_users.id", "=", "id")'],
            'rightJoinWhere' => ['rightJoinWhere("backend_users", "backend_users.id", "=", 1)'],
            'crossJoin' => ['crossJoin("backend_users")'],
            'union' => ['union("select * from backend_users")'],
            'unionAll' => ['unionAll("select * from backend_users")'],

            // A plain string argument that becomes raw SQL
            'selectSub' => ['selectSub("select password from backend_users", "leak")'],
            'selectConcat' => ['selectConcat(["(select password from backend_users)"], "leak")'],
            'aggregate' => ['aggregate("(select password from backend_users limit 1) || count")'],
            'numericAggregate' => ['numericAggregate("count")'],
            'lock' => ['lock(" union select login, password from backend_users")'],
            'inRandomOrder' => ['inRandomOrder("1) , (select 1")'],
            'useIndex' => ['useIndex("a) union select 1 -- ")'],
            'forceIndex' => ['forceIndex("a) union select 1 -- ")'],
            'ignoreIndex' => ['ignoreIndex("a) union select 1 -- ")'],
            'whereRowValues' => ['whereRowValues(["id"], "= 1 or 1=1 or ? =", [1])'],
            'orWhereRowValues' => ['orWhereRowValues(["id"], "= 1 or 1=1 or ? =", [1])'],
            'mergeWheres' => ['mergeWheres([{ type: "Raw", sql: "1=1", boolean: "and" }], [])'],
            'fromQuery' => ['fromQuery("select login, password from backend_users")'],
            'searchWhere' => ['searchWhere("x", ["(select password from backend_users)"], "exact")'],
            'orSearchWhere' => ['orSearchWhere("x", ["(select password from backend_users)"], "exact")'],
            'withAggregate' => ['withAggregate("rel", "*", "(select password from backend_users limit 1) || count")'],

            // Executing a callable, or instantiating a class, named by a string argument
            'beforeQuery' => ['beforeQuery("phpinfo")'],
            'withCasts' => ['withCasts({ name: "Cms\\Classes\\Theme:test" })'],

            // Repointing the builder at another query, the Eloquent-side setTable()
            'setQuery' => ['setQuery(null)'],
        ];
    }

    // Illuminate\Database\Query\Builder aliases Macroable::__call to a *public* macroCall(),
    // which dispatches on the macro name it is handed, so the policy only ever sees "macrocall".
    public function testCannotDispatchByNameThroughTheMacroCallAlias()
    {
        $probe = new SecurityPolicyMacroProbe();

        try {
            $this->renderTwigInCmsController('{% set _ = probe.macroCall("anything", []) %}', [
                'probe' => $probe,
            ]);
            $this->fail('macroCall() was not blocked');
        } catch (\Twig\Sandbox\SecurityNotAllowedMethodError $e) {
            $this->assertFalse($probe->called);
        }
    }

    //
    // A paginator forwards every method it does not implement to its own
    // collection (AbstractPaginator::__call), so the collection proxy's blocklist has to apply
    // on the paginator path too. The canary counts constructor calls the sandbox let through.
    //

    /**
     * @dataProvider paginatorInstantiationProvider
     */
    public function testPaginatorCannotInstantiateAnArbitraryClass(string $source, array $vars)
    {
        SandboxCanary::reset();

        $this->renderTwigInCmsController($source, $vars);

        $this->assertEquals(0, SandboxCanary::$instantiations);
    }

    public function paginatorInstantiationProvider(): array
    {
        $canary = SandboxCanary::class;
        $source = new SecurityPolicyPaginatorSource();

        return [
            // The negative control: already blocked on the collection path before this fix.
            'collection.mapInto' => [
                '{% set _ = items.mapInto(\'' . $canary . '\') %}',
                ['items' => collect([1, 2, 3])],
            ],
            // The reported gap, in both the forms the report gives.
            'paginator.mapInto' => [
                '{% set _ = source.paginate().mapInto(\'' . $canary . '\') %}',
                ['source' => $source],
            ],
            'paginator.pipeInto' => [
                '{% set _ = source.paginate().pipeInto(\'' . $canary . '\') %}',
                ['source' => $source],
            ],
            'attribute(paginator, "mapInto")' => [
                '{% set _ = attribute(source.paginate(), "mapInto", [\'' . $canary . '\']) %}',
                ['source' => $source],
            ],
            // AbstractCursorPaginator::__call forwards the same way.
            'cursorPaginator.mapInto' => [
                '{% set _ = source.cursorPaginate().mapInto(\'' . $canary . '\') %}',
                ['source' => $source],
            ],
            // A paginator handed straight to the template as a variable is cast just the same:
            // the cast is driven by the attribute access, not by where the object came from.
            'paginator-as-variable.mapInto' => [
                '{% set _ = pag.mapInto(\'' . $canary . '\') %}',
                ['pag' => new \Illuminate\Pagination\LengthAwarePaginator([1, 2, 3], 3, 10)],
            ],
        ];
    }

    // Callable stripping must still reach the methods the paginator forwards to its collection.
    // filter("is_numeric") stripped to filter(null) only drops falsy values, so all three
    // truthy strings survive.
    public function testSafePaginatorStripsCallablesOnTheForwardedCollectionPath()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- pag.filter("is_numeric").count() -}}',
            ['pag' => new \Illuminate\Pagination\LengthAwarePaginator(['1', 'a', '2'], 3, 10)]
        ));
        $this->assertEquals('3', $result);
    }

    // Invalidation: the navigation surface a real theme uses must keep working.
    public function testPaginatorNavigationMethodsStillWork()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- pag.total() -}}|{{- pag.currentPage() -}}|{{- pag.lastPage() -}}|'
            . '{%- for item in pag %}{{ item }}{% endfor -%}',
            ['pag' => new \Illuminate\Pagination\LengthAwarePaginator([1, 2], 4, 2, 1)]
        ));
        $this->assertEquals('4|1|2|12', $result);
    }

    // Invalidation: join/crossJoin/union also exist on Collection, which is not part of the
    // query builder forwarder chain, so blocking them on the builder must not touch collections.
    public function testCollectionJoinUnionAndCrossJoinStillWork()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- items.join(",") -}}|{{- items.union([3]).count() -}}|{{- items.crossJoin(["x"]).count() -}}',
            ['items' => collect([1, 2])]
        ));
        $this->assertEquals('1,2|2|2', $result);
    }

    // Invalidation: the Twig `join` filter is a filter, not a method, and is unaffected.
    public function testTwigJoinFilterStillWorks()
    {
        $result = trim($this->renderTwigInCmsController(
            '{{- items|join("-") -}}',
            ['items' => collect(['a', 'b'])]
        ));
        $this->assertEquals('a-b', $result);
    }

    // Invalidation: the hybrid carve-out (a string is an attribute name, not a callback) must
    // survive the move of the proxy lists onto SafeProxy.
    public function testCollectionHybridStringArgumentsStillWork()
    {
        $result = trim($this->renderTwigInCmsController(
            '{% if items.contains("count") %}yes{% else %}no{% endif %}',
            ['items' => collect(['count', 'other'])]
        ));
        $this->assertEquals('yes', $result);
    }

    //
    // Paginator views — render()/links() include a view, and views are plain PHP that runs
    // outside the sandbox, so a template may only name a paginator view.
    //

    public function testPaginatorRendersItsDefaultView()
    {
        $result = $this->renderTwigInCmsController(
            '{{ pag.render()|raw }}',
            ['pag' => $this->makePaginator()]
        );
        $this->assertStringContainsString('class="pagination"', $result);
    }

    // Winter points defaultSimpleView at its own system::pagination.simple-default view.
    public function testPaginatorRendersWintersSimpleDefaultView()
    {
        $result = $this->renderTwigInCmsController(
            '{{ pag.render()|raw }}',
            ['pag' => new \Illuminate\Pagination\Paginator([1, 2, 3], 2, 1)]
        );
        $this->assertStringContainsString('class="pagination"', $result);
        $this->assertStringContainsString('page=2', $result);
    }

    public function testPaginatorRendersAnExplicitlyNamedPaginationView()
    {
        foreach (['pagination::tailwind', 'pagination::bootstrap-5', 'system::pagination.simple-default'] as $view) {
            $result = $this->renderTwigInCmsController(
                '{{ pag.render("' . $view . '")|raw }}',
                ['pag' => $this->makePaginator()]
            );
            $this->assertStringContainsString('page=2', $result, $view . ' did not render');
        }
    }

    public function testPaginatorLinksRendersTheDefaultView()
    {
        $result = $this->renderTwigInCmsController(
            '{{ pag.links()|raw }}',
            ['pag' => $this->makePaginator()]
        );
        $this->assertStringContainsString('class="pagination"', $result);
    }

    public function testPaginatorAppendsAndFragmentStillWork()
    {
        $result = $this->renderTwigInCmsController(
            '{{ pag.appends({"q": "x"}).fragment("top").render()|raw }}',
            ['pag' => $this->makePaginator()]
        );
        $this->assertStringContainsString('q=x', $result);
        $this->assertStringContainsString('#top', $result);
    }

    // A view outside the pagination namespaces is refused, whichever of the two methods asks
    // for it. is_callable("system::exception") is false, so this is the new check firing and
    // not the proxy's callable stripping nulling the argument.
    public function testCannotRenderAnUnrelatedSystemViewThroughAPaginator()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController(
            '{{ pag.render("system::exception")|raw }}',
            ['pag' => $this->makePaginator()]
        );
    }

    /**
     * A plugin or theme shipping its own pagination views puts them in its `pagination`
     * directory, and naming one is allowed. The view does not exist here, so the view finder
     * is what rejects it - reaching that at all is what proves the sandbox let the name pass,
     * since a refused name throws SecurityNotAllowedMethodError before any lookup.
     */
    public function testCanRenderAPluginPaginationViewThroughAPaginator()
    {
        $this->expectException(\Twig\Error\RuntimeError::class);
        $this->expectExceptionMessageMatches('/winter\.tester/');
        $this->renderTwigInCmsController(
            '{{ pag.render("winter.tester::pagination.does-not-exist")|raw }}',
            ['pag' => $this->makePaginator()]
        );
    }

    public function testCannotRenderAPluginViewThroughAPaginator()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController(
            '{{ pag.render("winter.tester::index")|raw }}',
            ['pag' => $this->makePaginator()]
        );
    }

    public function testCannotRenderAnUnrelatedViewThroughPaginatorLinks()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController(
            '{{ pag.links("system::exception")|raw }}',
            ['pag' => $this->makePaginator()]
        );
    }

    // The view finder turns the name into a path, so an allowed prefix must not be walkable.
    public function testCannotWalkOutOfThePaginationViewNamespace()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController(
            '{{ pag.render("pagination::../../../../modules/system/ServiceProvider")|raw }}',
            ['pag' => $this->makePaginator()]
        );
    }

    /**
     * An argument a template names, rather than positions, still arrives at the method: the
     * proxies forward with `$object->$method(...$parameters)` and PHP spreads a string key as a
     * named argument. So the view name has to be read either way.
     *
     * @dataProvider namedPaginatorViewProvider
     */
    public function testCannotRenderAnUnrelatedViewNamedAsAPaginatorArgument(string $source)
    {
        $error = null;

        try {
            $this->renderTwigInCmsController($source, ['pag' => $this->makePaginator()]);
        } catch (\Throwable $ex) {
            $error = $ex;
        }

        // A Twig that cannot parse the form at all is an acceptable outcome too - what must not
        // happen is the view being included.
        $this->assertTrue(
            $error instanceof \Twig\Sandbox\SecurityNotAllowedMethodError
                || $error instanceof \Twig\Error\SyntaxError,
            'expected the call to be refused, got: '
                . ($error ? get_class($error) . ': ' . $error->getMessage() : 'no error at all')
        );
    }

    public function namedPaginatorViewProvider(): array
    {
        return [
            'render(view=)' => ['{{ pag.render(view="system::exception")|raw }}'],
            'links(view=)' => ['{{ pag.links(view="system::exception")|raw }}'],
            'render(view:)' => ['{{ pag.render(view: "system::exception")|raw }}'],
            'render(spread)' => ['{{ pag.render(...{view: "system::exception"})|raw }}'],
            'attribute(render)' => ['{{ attribute(pag, "render", {view: "system::exception"})|raw }}'],
            'attribute(links)' => ['{{ attribute(pag, "links", {view: "system::exception"})|raw }}'],
        ];
    }

    /**
     * A dot is how a view name spells a directory, and the allowed namespaces are allowed as a
     * whole, so a name nested inside one resolves inside that namespace's own directories.
     */
    public function testPaginatorRendersANestedViewInsideThePaginationNamespace()
    {
        $directory = $this->probeDirectory() . '/views';
        \File::makeDirectory($directory . '/sub', 0777, true);
        file_put_contents($directory . '/sub/nested.blade.php', 'NESTED-VIEW');
        \View::addNamespace('pagination', [$directory]);

        try {
            $result = trim($this->renderTwigInCmsController(
                '{{ pag.render("pagination::sub.nested")|raw }}',
                ['pag' => $this->makePaginator()]
            ));
        } finally {
            \File::deleteDirectory($directory);
        }

        $this->assertEquals('NESTED-VIEW', $result);
    }

    //
    // Which view a paginator renders is the application's decision, made by a service provider
    // (Paginator::defaultView()). A template may name an allowed view per call, but not repoint
    // the default — those setters are static, so their effect outlives the render — and not
    // reach the view factory, which resolves any view, or any file, by name.
    //

    public function testCannotRepointThePaginatorDefaultViewFromATemplate()
    {
        $saved = \Illuminate\Pagination\AbstractPaginator::$defaultView;
        $refused = false;

        try {
            $this->renderTwigInCmsController(
                '{% set _ = pag.defaultView("system::exception") %}{{ pag.render()|raw }}',
                ['pag' => $this->makePaginator()]
            );
        } catch (\Twig\Sandbox\SecurityNotAllowedMethodError $ex) {
            $refused = true;
        } finally {
            $observed = \Illuminate\Pagination\AbstractPaginator::$defaultView;
            \Illuminate\Pagination\AbstractPaginator::$defaultView = $saved;
        }

        $this->assertTrue($refused, 'defaultView() was not refused');
        $this->assertSame($saved, $observed, 'the default view was repointed');
    }

    public function testCannotRepointThePaginatorDefaultSimpleViewFromATemplate()
    {
        $saved = \Illuminate\Pagination\AbstractPaginator::$defaultSimpleView;
        $refused = false;

        try {
            $this->renderTwigInCmsController(
                '{% set _ = pag.defaultSimpleView("system::exception") %}{{ pag.render()|raw }}',
                ['pag' => new \Illuminate\Pagination\Paginator([1, 2, 3], 2, 1)]
            );
        } catch (\Twig\Sandbox\SecurityNotAllowedMethodError $ex) {
            $refused = true;
        } finally {
            $observed = \Illuminate\Pagination\AbstractPaginator::$defaultSimpleView;
            \Illuminate\Pagination\AbstractPaginator::$defaultSimpleView = $saved;
        }

        $this->assertTrue($refused, 'defaultSimpleView() was not refused');
        $this->assertSame($saved, $observed, 'the simple default view was repointed');
    }

    public function testCannotReachTheViewFactoryThroughAPaginator()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController(
            '{{ pag.viewFactory().make("system::exception").render()|raw }}',
            ['pag' => $this->makePaginator()]
        );
    }

    // An argument-less access is not cast to SafePaginator, so the policy carries the same names
    // for the paginator itself. Every name in SafePaginator::VIEW_CONFIG_METHODS is covered here.
    public function testTheViewConfigurationIsBlockedOnAnArgumentLessPaginatorAccess()
    {
        $savedView = \Illuminate\Pagination\AbstractPaginator::$defaultView;
        $savedSimpleView = \Illuminate\Pagination\AbstractPaginator::$defaultSimpleView;

        $names = [
            'viewFactory',
            'viewFactoryResolver',
            'defaultView',
            'defaultSimpleView',
            'useTailwind',
            'useBootstrap',
            'useBootstrapThree',
            'useBootstrapFour',
            'useBootstrapFive',
        ];

        try {
            foreach ($names as $name) {
                $refused = false;

                try {
                    $this->renderTwigInCmsController(
                        '{{ pag.' . $name . ' }}',
                        ['pag' => $this->makePaginator()]
                    );
                } catch (\Twig\Sandbox\SecurityNotAllowedMethodError $ex) {
                    $refused = true;
                }

                $this->assertTrue($refused, 'pag.' . $name . ' was not refused');
            }
        } finally {
            \Illuminate\Pagination\AbstractPaginator::$defaultView = $savedView;
            \Illuminate\Pagination\AbstractPaginator::$defaultSimpleView = $savedSimpleView;
        }
    }

    // AbstractCursorPaginator has its own viewFactory(), and a cursor paginator is not an
    // AbstractPaginator, so it needs the entry of its own that it has.
    public function testCannotReachTheViewFactoryThroughACursorPaginator()
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController(
            '{{ pag.viewFactory }}',
            ['pag' => new \Illuminate\Pagination\CursorPaginator([1, 2, 3], 2)]
        );
    }

    // The application's own choice of default view is still honoured, which is the supported way
    // for a plugin or theme to supply its own pagination markup.
    public function testAPaginatorRendersTheApplicationConfiguredDefaultView()
    {
        $saved = \Illuminate\Pagination\AbstractPaginator::$defaultView;
        \Illuminate\Pagination\AbstractPaginator::defaultView('pagination::bootstrap-5');

        try {
            $result = $this->renderTwigInCmsController(
                '{{ pag.render()|raw }}',
                ['pag' => $this->makePaginator()]
            );
        } finally {
            \Illuminate\Pagination\AbstractPaginator::$defaultView = $saved;
        }

        $this->assertStringContainsString('fw-semibold', $result);
        $this->assertStringContainsString('page=2', $result);
    }

    protected function makePaginator(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return new \Illuminate\Pagination\LengthAwarePaginator([1, 2], 4, 2, 1);
    }

    /**
     * Renders against the CMS test theme fixture, which is the only harness here with a real
     * theme datasource behind this.page / this.theme.
     */
    protected function renderTwigInTestTheme(string $source): string
    {
        Config::set('cms.activeTheme', 'test');
        Config::set('cms.themesPath', '/modules/cms/tests/fixtures/themes');
        Event::flush('cms.theme.getActiveTheme');
        Theme::resetCache();

        return $this->renderTwigInCmsController($source, [], Theme::getActiveTheme());
    }

    /**
     * Renders a template whose only callable argument has been stripped. Laravel type-hints
     * most of these parameters as `callable`, so passing null raises a TypeError that Twig
     * wraps — the escape failing loudly is the expected outcome.
     */
    protected function renderExpectingStrippedCallable(string $source, array $vars = []): string
    {
        try {
            return $this->renderTwigInCmsController($source, $vars);
        } catch (\Twig\Error\RuntimeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e->getPrevious());
            return '';
        }
    }

    /**
     * Renders a wildcard markup call whose callback, if it runs, writes $path, and asserts
     * both that nothing was written and that the sandbox refused the call. Asserting on the
     * side effect rather than on rendered output matters: printing the result of these
     * helpers raises "Array to string conversion", which would look like a block even though
     * the callback had already run.
     */
    protected function assertWildcardCallbackBlocked(string $label, string $twigCallFormat): void
    {
        // Normalised because the path is interpolated into a double-quoted Twig string, where a
        // Windows separator would be read as an escape sequence and the path would not match the
        // one checked below - leaving the assertion vacuous rather than failing.
        $path = (new Filesystem())->normalizePath($this->probeDirectory() . '/' . $label . '.txt');
        $error = null;

        try {
            $this->renderTwigInCmsController('{% set _ = ' . sprintf($twigCallFormat, $path) . ' %}');
        } catch (\Throwable $ex) {
            $error = $ex->getPrevious() ?: $ex;
        }

        $written = file_exists($path);
        @unlink($path);

        $this->assertFalse($written, sprintf('%s() executed the callback', $label));
        $this->assertInstanceOf(\Twig\Sandbox\SecurityNotAllowedMethodError::class, $error);
    }

    protected function probeDirectory(): string
    {
        $path = base_path('storage/framework/cache/securitypolicy-test');

        if (!\File::isDirectory($path)) {
            \File::makeDirectory($path, 0777, true);
        }

        return $path;
    }

    protected function tearDown(): void
    {
        \File::deleteDirectory(base_path('storage/framework/cache/securitypolicy-test'));

        parent::tearDown();
    }

    protected function renderTwigInCmsController(string $source, array $vars = [], ?Theme $theme = null)
    {
        $controller = new Controller($theme);
        $twig = $controller->getTwig();
        $template = $twig->createTemplate($source, 'test.case');

        return $twig->render($template, [
            'this' => array_merge($controller->getControllerGlobalVars(), [
                'theme' => $theme ?? new Theme(),
            ]),
        ] + $vars);
    }
}

/**
 * Hands the template a paginator from a method call, which is how a theme really gets one.
 */
class SecurityPolicyPaginatorSource
{
    public function paginate()
    {
        return new \Illuminate\Pagination\LengthAwarePaginator([1, 2, 3], 3, 10);
    }

    public function cursorPaginate()
    {
        return new \Illuminate\Pagination\CursorPaginator([1, 2, 3], 10);
    }
}

/**
 * Stands in for a CMS object inside a CmsObjectCollection. withComponent() asks each object for
 * a component by name, which is the argument that has to survive the callable stripping.
 */
class SecurityPolicyComponentProbe
{
    public function hasComponent($componentName)
    {
        return in_array($componentName, ['session', 'testArchive'], true) ? $componentName : false;
    }

    public function getComponent($componentName)
    {
        return $this->hasComponent($componentName) ? new SecurityPolicyTestComponent() : null;
    }
}

/**
 * Stands in for Illuminate\Database\Query\Builder, which exposes Macroable::__call publicly
 * under the name macroCall().
 */
class SecurityPolicyMacroProbe
{
    public $called = false;

    public function macroCall($method, $parameters)
    {
        $this->called = true;
    }
}
