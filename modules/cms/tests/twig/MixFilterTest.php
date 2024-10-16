<?php

namespace Cms\Tests\Twig;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Cms\Twig\Extension;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Event;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Facades\Twig;

class MixFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!is_dir(base_path('node_modules'))) {
            $this->markTestSkipped('This test requires node_modules to be installed');
        }

        if (!is_file(base_path('node_modules/.bin/mix'))) {
            $this->markTestSkipped('This test requires the mix package to be installed');
        }

        $this->originalThemesPath = Config::get('cms.themesPath');
        Config::set('cms.themesPath', '/modules/cms/tests/fixtures/themes');

        $this->themePath = base_path('modules/cms/tests/fixtures/themes/mixtest');

        Config::set('cms.activeTheme', 'mixtest');

        Event::flush('cms.theme.getActiveTheme');
        Theme::resetCache();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory('modules/cms/tests/fixtures/themes/mixtest/assets/dist');
        File::delete('modules/cms/tests/fixtures/themes/mixtest/mix-manifest.json');

        Config::set('cms.themesPath', $this->originalThemesPath);

        parent::tearDown();
    }

    public function testGeneratesAssetUrl(): void
    {
        $theme = Theme::getActiveTheme();

        $this->artisan('mix:compile', [
            'theme-mixtest',
            '--manifest' => 'modules/cms/tests/fixtures/npm/package-mixtheme.json',
            '--disable-tty' => true,
        ])->assertExitCode(0);

        $this->assertFileExists($theme->getPath($theme->getDirName() . '/mix-manifest.json'));

        $controller = Controller::getController() ?: new Controller();

        $extension = new Extension();
        $extension->setController($controller);

        $this->app->make('twig.environment')
            ->addExtension($extension);

        $contents = Twig::parse("{{ 'assets/dist/css/theme.css' | mix }}");

        $this->assertStringContainsString('/assets/dist/css/theme.css?id=', $contents);
    }
}
