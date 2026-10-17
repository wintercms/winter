<?php

namespace Cms\Tests\Classes;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use System\Tests\Bootstrap\TestCase;
use Twig\Sandbox\SecurityNotAllowedMethodError;
use Winter\Storm\Halcyon\Model as HalcyonModel;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File;

/**
 * Renders CMS templates through the real page cycle to prove that the Twig sandbox stops a
 * template from writing to, or reading outside of, its own theme.
 *
 * A scratch theme is built under the temporary path so that a successful escape leaves an
 * artefact on disk that the assertions can catch.
 */
class ControllerSandboxTest extends TestCase
{
    /**
     * @var string Original themes path, restored on teardown.
     */
    protected string $origThemesPath;

    /**
     * @var string Absolute path of the scratch themes directory.
     */
    protected string $themesPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->origThemesPath = Config::get('cms.themesPath');
        $this->themesPath = temp_path('sandbox-themes');

        File::deleteDirectory($this->themesPath);
        foreach (['pages', 'layouts', 'partials'] as $dir) {
            File::makeDirectory($this->themesPath . '/scratch/' . $dir, 0777, true, true);
        }
        File::put($this->themesPath . '/scratch/theme.yaml', 'name: Scratch' . PHP_EOL);
        File::put($this->themesPath . '/scratch/partials/greeting.htm', 'hello from a partial');

        Config::set('cms.themesPath', '/storage/temp/sandbox-themes');
        Config::set('cms.activeTheme', 'scratch');
        Config::set('cms.enableSafeMode', true);
        Config::set('cms.databaseTemplates', false);
        app()->setThemesPath($this->themesPath);

        HalcyonModel::clearBootedModels();
        HalcyonModel::flushEventListeners();
    }

    public function tearDown(): void
    {
        File::deleteDirectory($this->themesPath);
        Config::set('cms.themesPath', $this->origThemesPath);
        app()->setThemesPath(base_path($this->origThemesPath));

        parent::tearDown();
    }

    /**
     * A CMS page reaches the Halcyon Builder through Halcyon\Model::__call, and
     * Builder::insert() writes a template file straight to the theme datasource, without going
     * through Model::save() and the checks that run there.
     */
    public function testCannotWriteATemplateFileFromATemplate()
    {
        $this->makePage('entry', '/entry', <<<'TWIG'
            {% set _ = this.page.newInstance({ fileName: 'injected' }).insert({
                settings: { url: '/injected' },
                markup: 'injected markup',
                code: "public function onStart() { echo 'escaped'; }"
            }) %}
            TWIG);

        try {
            $this->renderUrl('/entry');
            $this->fail('The sandbox allowed a template to write to the theme datasource.');
        } catch (SecurityNotAllowedMethodError $ex) {
            $this->assertEquals('insert', $ex->getMethodName());
        }

        $this->assertFileDoesNotExist($this->themesPath . '/scratch/pages/injected.htm');
    }

    /**
     * Builder::from() re-points the directory the datasource reads from, which escapes the
     * theme entirely.
     */
    public function testCannotReadOutsideTheThemeFromATemplate()
    {
        $this->makePage('leak', '/leak', "{{ this.page.from('../../../modules').get()|length }}");

        $this->expectException(SecurityNotAllowedMethodError::class);
        $this->renderUrl('/leak');
    }

    /**
     * Invalidation: read-only template usage of the CMS objects must keep working.
     */
    public function testLegitimateTemplateUsageStillRenders()
    {
        $this->makePage('read', '/read', implode(PHP_EOL, [
            '{{ this.page.fileName }}',
            '{{ this.page.id }}',
            "{% partial 'greeting' %}",
            "{{ this.page.find('read').fileName }}",
            "{{ this.page.newQuery().lists('fileName')|join(',') }}",
        ]));

        $output = $this->renderUrl('/read');

        $this->assertStringContainsString('read.htm', $output);
        $this->assertStringContainsString('hello from a partial', $output);
    }

    /**
     * Writes a CMS page into the scratch theme.
     */
    protected function makePage(string $fileName, string $url, string $markup): void
    {
        File::put(
            $this->themesPath . '/scratch/pages/' . $fileName . '.htm',
            'url = "' . $url . '"' . PHP_EOL . '==' . PHP_EOL . $markup . PHP_EOL
        );
    }

    /**
     * Runs the CMS page cycle for the given URL and returns the rendered output.
     */
    protected function renderUrl(string $url): string
    {
        $result = (new Controller(Theme::load('scratch')))->run($url);

        return is_object($result) ? $result->getContent() : (string) $result;
    }
}
