<?php

namespace System\Tests\Classes\Asset;

use Cms\Classes\Theme;
use System\Classes\Asset\Mix;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Event;
use Winter\Storm\Support\Facades\File;

class MixTest extends TestCase
{
    protected string $themePath;

    protected string $originalThemesPath = '';

    protected string $originalThemesPathLocal = '';

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
        Config::set('cms.themesPath', '/modules/system/tests/fixtures/themes');

        $this->originalThemesPathLocal = Config::get('cms.themesPathLocal');
        Config::set('cms.themesPathLocal', base_path('modules/system/tests/fixtures/themes'));
        $this->app->setThemesPath(Config::get('cms.themesPathLocal'));

        $this->themePath = base_path('modules/system/tests/fixtures/themes/mixtest');

        Config::set('cms.activeTheme', 'mixtest');

        Event::flush('cms.theme.getActiveTheme');
        Theme::resetCache();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory('modules/system/tests/fixtures/themes/mixtest/assets/dist');
        File::delete('modules/system/tests/fixtures/themes/mixtest/mix-manifest.json');

        Config::set('cms.themesPath', $this->originalThemesPath);

        Config::set('cms.themesPathLocal', $this->originalThemesPathLocal);
        $this->app->setThemesPath($this->originalThemesPathLocal);

        parent::tearDown();
    }

    public function testGeneratesVersionedUrl(): void
    {
        $this->artisan('mix:compile', [
            'theme-mixtest',
            '--manifest' => 'modules/system/tests/fixtures/npm/package-mixtest.json',
            '--disable-tty' => true,
        ])->assertExitCode(0);

        $theme = Theme::getActiveTheme();

        $this->assertFileExists($theme->getPath($theme->getDirName() . '/mix-manifest.json'));

        $manifest = json_decode(file_get_contents($theme->getPath($theme->getDirName() . '/mix-manifest.json')), true);

        foreach ($manifest as $key => $value) {
            $mixAssetUrl = Mix::mix($key);

            $mixQueryParams = parse_url($mixAssetUrl, PHP_URL_QUERY);
            parse_str($mixQueryParams, $mixResult);

            $manifestQueryParams = parse_url($value, PHP_URL_QUERY);
            parse_str($manifestQueryParams, $manifestResult);

            $this->assertArrayHasKey('id', $mixResult);
            $this->assertEquals($manifestResult['id'], $mixResult['id']);

            $this->assertNotFalse(filter_var($mixAssetUrl, FILTER_VALIDATE_URL), 'Mix asset URL did not return a valid URL.');
        }
    }

    public function testThemeCanOverrideMixManifestPath(): void
    {

    }
}
