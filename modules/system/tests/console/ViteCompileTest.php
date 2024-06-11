<?php

namespace System\Tests\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use System\Classes\CompilableAssets;
use System\Console\Asset\Vite\ViteCompile;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\File;

class ViteCompileTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!File::exists(base_path('node_modules'))) {
            $this->markTestSkipped('This test requires node_modules to be installed');
        }
    }

    public function testThemeCompile(): void
    {
        $themePath = base_path('modules/system/tests/fixtures/themes/vitetest');

        // Add our testing theme because it won't be auto discovered
        CompilableAssets::instance()->registerPackage(
            'theme-vitetest',
            $themePath . '/vite.config.mjs',
            'vite'
        );

        // Create command and output objects
        [$command, $output] = $this->makeCommand();
        // Run the vite:compile command
        $result = $command->run(new ArrayInput([
            'theme-vitetest',
            '--manifest' => 'modules/system/tests/fixtures/npm/package-vitetheme.json',
            '--silent' => true
        ]), $output);

        // Confirm command ran correctly
        $this->assertIsInt($result);
        $this->assertEquals(0, $result, 'Vite command execution failed');

        $manifestPath = $themePath . '/public/build/manifest.json';

        // Validate the manifest was written
        $this->assertFileExists($manifestPath);

        // Get the contents of the manifest
        $manifest = json_decode(File::get($manifestPath), JSON_OBJECT_AS_ARRAY);

        // Validate the css was compiled correctly
        $this->assertArrayHasKey('assets/css/theme.css', $manifest);
        $this->assertFileExists($themePath . '/public/build/' . $manifest['assets/css/theme.css']['file']);
        $this->assertEquals(
            'h1{color:red}',
            trim(File::get($themePath . '/public/build/' . $manifest['assets/css/theme.css']['file']))
        );

        // Validate the js was compiled correctly
        $this->assertArrayHasKey('assets/javascript/theme.js', $manifest);
        $this->assertFileExists($themePath . '/public/build/' . $manifest['assets/javascript/theme.js']['file']);
        $this->assertEquals(
            'window.alert("hello world");',
            trim(File::get($themePath . '/public/build/' . $manifest['assets/javascript/theme.js']['file']))
        );
    }

    protected function makeCommand(): array
    {
        $output = new BufferedOutput();
        $command = new ViteCompile();
        $command->setLaravel($this->app);
        return [$command, $output];
    }

    public function tearDown(): void
    {
        File::deleteDirectory('modules/system/tests/fixtures/themes/vitetest/public');
        parent::tearDown();
    }
}
