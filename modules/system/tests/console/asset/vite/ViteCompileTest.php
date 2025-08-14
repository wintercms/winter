<?php

namespace System\Tests\Console\Asset\Vite;

use System\Classes\Asset\PackageManager;
use Winter\Storm\Support\Facades\File;

class ViteCompileTest extends BaseViteTestCase
{
    protected string $viteVersion;

    protected string $themePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->themePath = base_path('modules/system/tests/fixtures/themes/assettest');

        // Add our testing theme because it won't be auto discovered
        PackageManager::instance()->registerPackage(
            'theme-assettest',
            $this->themePath . '/vite.config.mjs',
            'vite'
        );
    }

    public function testThemeCompile(): void
    {
        // Run the vite:compile command
        $this->artisan('vite:compile', [
            'theme-assettest',
            '--manifest' => 'modules/system/tests/fixtures/npm/package-vitetheme.json',
            '--silent' => true
        ])->assertExitCode(0);

        $manifestPath = $this->themePath . '/public/build/manifest.json';

        // Validate the manifest was written
        $this->assertFileExists($manifestPath);

        // Get the contents of the manifest
        $manifest = json_decode(File::get($manifestPath), JSON_OBJECT_AS_ARRAY);

        // Validate the css was compiled correctly
        $this->assertArrayHasKey('assets/css/theme.css', $manifest);
        $this->assertFileExists($this->themePath . '/public/build/' . $manifest['assets/css/theme.css']['file']);
        $this->assertEquals(
            'h1{color:red}',
            trim(File::get($this->themePath . '/public/build/' . $manifest['assets/css/theme.css']['file']))
        );

        // Validate the js was compiled correctly
        $this->assertArrayHasKey('assets/javascript/theme.js', $manifest);
        $this->assertFileExists($this->themePath . '/public/build/' . $manifest['assets/javascript/theme.js']['file']);
        $this->assertEquals(
            'window.alert("hello world");',
            trim(File::get($this->themePath . '/public/build/' . $manifest['assets/javascript/theme.js']['file']))
        );
    }

    public function testThemeCompileFailed(): void
    {
        // Rename the css file so vite cannot find it
        File::move($this->themePath . '/assets/css/theme.css', $this->themePath . '/assets/css/theme.back');

        // Run the vite:compile command
        $this->artisan('vite:compile', [
            'theme-assettest',
            '--manifest' => 'modules/system/tests/fixtures/npm/package-vitetheme.json',
            '--disable-tty' => true
        ])
            ->expectsOutputToContain('Could not resolve entry module "assets/css/theme.css".')
            ->assertExitCode(1);

        // Validate the manifest was not written
        $this->assertFileNotExists($this->themePath . '/public/build/manifest.json');

        // Put the css file back
        File::move($this->themePath . '/assets/css/theme.back', $this->themePath . '/assets/css/theme.css');
    }

    public function testThemeCompileWarning(): void
    {
        // Rename the css file so vite cannot find it
        $contents = File::get($this->themePath . '/assets/css/theme.css');

        File::put($this->themePath . '/assets/css/theme.css', 'h1 {color:');

        // It looks like around vite 6.2 (this is a guess as I could not find it in their change log) they updated
        // something which causes failed compilations to throw errors...
        // To support both the older style of warnings and the new style of errors, we can do a version check to
        // insure the installed version acts as we'd expect
        if (version_compare($this->viteVersion, '6.2.0', '>')) {
            $this->artisan('vite:compile', [
                'theme-assettest',
                '--manifest' => 'modules/system/tests/fixtures/npm/package-vitetheme.json',
                '--disable-tty' => true
            ])
                ->expectsOutputToContain('assettest/assets/css/theme.css:1:1: Unclosed block')
                ->assertExitCode(1);

            // Validate the manifest was not written
            $this->assertFileNotExists($this->themePath . '/public/build/manifest.json');
        } else {
            $this->artisan('vite:compile', [
                'theme-assettest',
                '--manifest' => 'modules/system/tests/fixtures/npm/package-vitetheme.json',
                '--disable-tty' => true
            ])
                ->expectsOutputToContain('[WARNING] Expected "}" to go with "{" [css-syntax-error]')
                ->assertExitCode(0);

            // Validate the manifest was written
            $this->assertFileExists($this->themePath . '/public/build/manifest.json');
        }

        // Put the css file back
        File::put($this->themePath . '/assets/css/theme.css', $contents);
    }

    public function tearDown(): void
    {
        File::deleteDirectory('modules/system/tests/fixtures/themes/assettest/public');
        parent::tearDown();
    }
}
