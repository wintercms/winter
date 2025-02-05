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
