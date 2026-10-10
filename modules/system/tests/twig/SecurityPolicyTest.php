<?php

namespace System\Tests\Twig;

use Cms\Classes\Controller;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use System\Tests\Bootstrap\TestCase;
use Twig\Environment;
use Winter\Storm\Filesystem\Filesystem;
use Winter\Storm\Halcyon\Datasource\FileDatasource;

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

    protected function renderTwigInCmsController(string $source, array $vars = [])
    {
        $controller = new Controller();
        $twig = $controller->getTwig();
        $template = $twig->createTemplate($source, 'test.case');

        return $twig->render($template, [
            'this' => array_merge($controller->getControllerGlobalVars(), [
                'theme' => new Theme(),
            ]),
        ] + $vars);
    }
}
