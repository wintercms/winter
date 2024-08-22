<?php

namespace System\Tests\Classes\Asset;

use Cms\Classes\Theme;
use System\Classes\Asset\Mix;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Event;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Facades\Url;

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

    public function testThrowsExceptionWhenMixManifestIsMissing(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The Mix manifest does not exist');

        Mix::mix('assets/dist/foo.css');
    }

    public function testGeneratesAssetUrls(): void
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

            $this->assertStringStartsWith(
                Url::asset(Config::get('cms.themesPath', '/themes') . '/' . $theme->getDirName()),
                $mixAssetUrl
            );
        }
    }

    public function testThemeCanOverrideMixManifestPath(): void
    {
        $theme = Theme::getActiveTheme();

        Event::listen('cms.theme.extendConfig', function ($dirName, &$config) {
            $config['mix_manifest_path'] = 'assets/dist';
        });

        rename(
            $theme->getPath($theme->getDirName() . '/winter.mix.js'),
            $theme->getPath($theme->getDirName() . '/winter.mix.js.bak')
        );

        copy(
            $theme->getPath($theme->getDirName() . '/winter.mix-manifest-override.js'),
            $theme->getPath($theme->getDirName() . '/winter.mix.js')
        );

        try {
            $this->artisan('mix:compile', [
                'theme-mixtest',
                '--manifest' => 'modules/system/tests/fixtures/npm/package-mixtest.json',
                '--disable-tty' => true,
            ])->assertExitCode(0);

            $this->assertFileExists($theme->getPath($theme->getDirName() . '/assets/dist/mix-manifest.json'));

            $manifest = json_decode(file_get_contents($theme->getPath($theme->getDirName() . '/assets/dist/mix-manifest.json')), true);

            foreach ($manifest as $key => $value) {
                $this->assertStringContainsString($key, (string) Mix::mix($key));
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
            rename(
                $theme->getPath($theme->getDirName() . '/winter.mix.js.bak'),
                $theme->getPath($theme->getDirName() . '/winter.mix.js')
            );
        }
    }

    public function testThrowsAnExceptionForInvalidMixFileWhenDebugIsEnabled()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to locate Mix file: /assets/dist/foo.css');

        $this->artisan('mix:compile', [
            'theme-mixtest',
            '--manifest' => 'modules/system/tests/fixtures/npm/package-mixtest.json',
            '--disable-tty' => true,
        ])->assertExitCode(0);

        Mix::mix('assets/dist/foo.css');
    }

    public function testDoesNotThrowAnExceptionForInvalidMixFileWhenDebugIsDisabled(): void
    {
        Config::set('app.debug', false);

        $this->artisan('mix:compile', [
            'theme-mixtest',
            '--manifest' => 'modules/system/tests/fixtures/npm/package-mixtest.json',
            '--disable-tty' => true,
        ])->assertExitCode(0);

        $this->assertEquals('/assets/dist/foo.css', Mix::mix('assets/dist/foo.css'));
    }
}
