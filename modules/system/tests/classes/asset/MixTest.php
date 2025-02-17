<?php

namespace System\Tests\Classes\Asset;

use Cms\Classes\Theme;
use Exception;
use System\Classes\Asset\Mix;
use System\Classes\Asset\PackageManager;
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
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The Mix manifest does not exist');

        app(Mix::class)(['assets/dist/foo.css'], 'theme-mixtest');
    }

    public function testMixWithJsOnly(): void
    {
        $this->artisan('mix:compile', [
            'theme-mixtest',
            '--manifest' => 'modules/system/tests/fixtures/npm/package-mixtest.json',
            '--disable-tty' => true,
        ])->assertExitCode(0);

        $package = PackageManager::instance()->getPackage('theme-mixtest')[0];

        $manifest = json_decode(file_get_contents($package['path'].'/mix-manifest.json'), true);

        $mixFileUrl = collect($manifest)->firstWhere(fn ($value, $key) => $key === '/assets/dist/js/theme.js');
        $mixFileUrl = Url::asset($package['path'] . $mixFileUrl);

        $result = app(Mix::class)(['assets/dist/js/theme.js'], 'theme-mixtest');

        $this->assertStringEndsWith('<script src="'.$mixFileUrl.'"></script>', $result->toHtml());
    }

    public function testMixWithCssAndJs(): void
    {
        $this->artisan('mix:compile', [
            'theme-mixtest',
            '--manifest' => 'modules/system/tests/fixtures/npm/package-mixtest.json',
            '--disable-tty' => true,
        ])->assertExitCode(0);

        $package = PackageManager::instance()->getPackage('theme-mixtest')[0];

        $manifest = collect(json_decode(file_get_contents($package['path'].'/mix-manifest.json'), true))
            ->map(fn ($value, $key) => Url::asset($package['path'].$value));

        $result = app(Mix::class)(['assets/dist/css/theme.css', 'assets/dist/js/theme.js'], 'theme-mixtest');

        $this->assertStringEndsWith(
            '<link rel="stylesheet" href="'.$manifest['/assets/dist/css/theme.css'].'" />'
            .'<script src="'.$manifest['/assets/dist/js/theme.js'].'"></script>',
            $result->toHtml()
        );
    }

    public function testThemeCanOverrideMixManifestPath(): void
    {
        Event::listen('cms.theme.extendConfig', function ($dirName, &$config) {
            $config['mix_manifest_path'] = 'assets/dist';
        });

        $package = PackageManager::instance()->getPackage('theme-mixtest')[0];

        rename(
            $package['path'] . '/winter.mix.js',
            $package['path'] . '/winter.mix.js.bak'
        );

        copy(
            $package['path'] . '/winter.mix-manifest-override.js',
            $package['path'] . '/winter.mix.js'
        );

        try {
            $this->artisan('mix:compile', [
                'theme-mixtest',
                '--manifest' => 'modules/system/tests/fixtures/npm/package-mixtest.json',
                '--disable-tty' => true,
            ])->assertExitCode(0);

            $this->assertFileExists($package['path'] . '/assets/dist/mix-manifest.json');

            $manifest = json_decode(file_get_contents($package['path'] . '/assets/dist/mix-manifest.json'), true);

            foreach ($manifest as $key => $value) {
                $this->assertStringContainsString($key, (string) app(Mix::class)($key, 'theme-mixtest', 'assets/dist/mix-manifest.json'));
            }
        } catch (Exception $e) {
            throw $e;
        } finally {
            rename(
                $package['path'] . '/winter.mix.js.bak',
                $package['path'] . '/winter.mix.js'
            );
        }
    }

    public function testThrowsAnExceptionForInvalidMixFile()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to locate file in Mix manifest: /assets/dist/foo.css');

        $this->artisan('mix:compile', [
            'theme-mixtest',
            '--manifest' => 'modules/system/tests/fixtures/npm/package-mixtest.json',
            '--disable-tty' => true,
        ])->assertExitCode(0);

        app(Mix::class)('assets/dist/foo.css', 'theme-mixtest');
    }
}
