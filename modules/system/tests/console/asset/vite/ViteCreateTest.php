<?php

namespace System\Tests\Console\Asset\Vite;

use System\Classes\PluginManager;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\File;

class ViteCreateTest extends TestCase
{
    protected string $testPlugin = 'Winter.Sample';

    public function testConfigWritten(): void
    {
        $path = PluginManager::instance()->findByIdentifier($this->testPlugin)->getPluginPath();
        $configPath = $path . '/vite.config.mjs';

        // Check file does not exist
        $this->assertFileNotExists($configPath);

        // Run the config command to generate the vite config
        $this->artisan('vite:create', [
            'packageName' => $this->testPlugin,
            '--no-stubs' => true,
        ])
            ->doesntExpectOutput('vite.config.mjs already exists, overwrite?')
            ->assertExitCode(0);

        // Validate the manifest was written
        $this->assertFileExists($configPath);

        // Get the predicted contents
        $fixture = str_replace(
            '{{packageName}}',
            'winter-sample',
            File::get(base_path('modules/system/console/asset/fixtures/config/vite/vite.config.mjs.fixture')),
        );

        // Check the file written is what was expected
        $this->assertEquals($fixture, File::get($configPath));

        // Overwrite the file content
        File::put($configPath, 'testing');

        // Check that refusing to overwrite does not replace file contents
        $this->artisan('vite:create', [
            'packageName' => $this->testPlugin,
            '--no-stubs' => true,
        ])
            ->expectsQuestion('vite.config.mjs already exists, overwrite?', false)
            ->assertExitCode(0);

        // Check file contents was not overwritten
        $this->assertNotEquals($fixture, File::get($configPath));

        // Run command confirming to overwrite file contents works
        $this->artisan('vite:create', [
            'packageName' => $this->testPlugin,
            '--no-stubs' => true,
        ])
            ->expectsQuestion('vite.config.mjs already exists, overwrite?', true)
            ->assertExitCode(0);

        // Check file contents was overwritten
        $this->assertEquals($fixture, File::get($configPath));
    }

    public function testConfigTailwind(): void
    {
        $path = PluginManager::instance()->findByIdentifier($this->testPlugin)->getPluginPath();
        $configPath = $path . '/vite.config.mjs';
        $packageJson = $path . '/package.json';

        // Check file does not exist
        $this->assertFileNotExists($configPath);

        // Run the config command to generate the vite config
        $this->artisan('vite:create', [
            'packageName' => $this->testPlugin,
            '--tailwind' => true,
            '--no-stubs' => true,
        ])
            ->assertExitCode(0);

        // Check files are created correctly
        $this->assertFileExists($configPath);
        $this->assertFileExists($path . '/tailwind.config.js');
        $this->assertFileExists($path . '/postcss.config.mjs');

        // Get the contents of the package.json
        $json = json_decode(File::get($packageJson));

        // Check tailwindcss is required
        $this->assertTrue(isset($json->devDependencies->tailwindcss));
    }

    public function testConfigReact(): void
    {
        $path = PluginManager::instance()->findByIdentifier($this->testPlugin)->getPluginPath();
        $configPath = $path . '/vite.config.mjs';
        $packageJson = $path . '/package.json';

        // Check file does not exist
        $this->assertFileNotExists($configPath);

        // Run the config command to generate the vite config with react
        $this->artisan('vite:create', [
            'packageName' => $this->testPlugin,
            '--react' => true,
            '--no-stubs' => true,
        ])
            ->assertExitCode(0);

        // Check files are created correctly
        $this->assertFileExists($configPath);
        $this->assertFileExists($packageJson);

        // Get the contents of the package.json
        $json = json_decode(File::get($packageJson));

        // Check react is required
        $this->assertTrue(isset($json->devDependencies->react));
    }

    public function testConfigTailwindReact(): void
    {
        $path = PluginManager::instance()->findByIdentifier($this->testPlugin)->getPluginPath();
        $configPath = $path . '/vite.config.mjs';
        $packageJson = $path . '/package.json';

        // Check file does not exist
        $this->assertFileNotExists($configPath);

        // Run the config command to generate the vite config with react
        $this->artisan('vite:create', [
            'packageName' => $this->testPlugin,
            '--tailwind' => true,
            '--react' => true,
            '--no-stubs' => true,
        ])
            ->assertExitCode(0);

        // Check files are created correctly
        $this->assertFileExists($configPath);
        $this->assertFileExists($packageJson);
        $this->assertFileExists($path . '/tailwind.config.js');
        $this->assertFileExists($path . '/postcss.config.mjs');

        // Get the contents of the package.json
        $json = json_decode(File::get($packageJson));

        // Check tailwind & react are required
        $this->assertTrue(isset($json->devDependencies->tailwindcss));
        $this->assertTrue(isset($json->devDependencies->react));
    }

    public function testConfigVue(): void
    {
        $path = PluginManager::instance()->findByIdentifier($this->testPlugin)->getPluginPath();
        $configPath = $path . '/vite.config.mjs';
        $packageJson = $path . '/package.json';

        // Check file does not exist
        $this->assertFileNotExists($configPath);

        // Run the config command to generate the vite config with vue
        $this->artisan('vite:create', [
            'packageName' => $this->testPlugin,
            '--vue' => true,
            '--no-stubs' => true,
        ])
            ->assertExitCode(0);

        // Check files are created correctly
        $this->assertFileExists($configPath);
        $this->assertFileExists($packageJson);

        // Get the contents of the package.json
        $json = json_decode(File::get($packageJson));

        // Check vue is required
        $this->assertTrue(isset($json->devDependencies->vue));
    }

    public function testConfigTailwindVue(): void
    {
        $path = PluginManager::instance()->findByIdentifier($this->testPlugin)->getPluginPath();
        $configPath = $path . '/vite.config.mjs';
        $packageJson = $path . '/package.json';

        // Check file does not exist
        $this->assertFileNotExists($configPath);

        // Run the config command to generate the vite config with vue
        $this->artisan('vite:create', [
            'packageName' => $this->testPlugin,
            '--tailwind' => true,
            '--vue' => true,
            '--no-stubs' => true,
        ])
            ->assertExitCode(0);

        // Check files are created correctly
        $this->assertFileExists($configPath);
        $this->assertFileExists($packageJson);
        $this->assertFileExists($path . '/tailwind.config.js');
        $this->assertFileExists($path . '/postcss.config.mjs');

        // Get the contents of the package.json
        $json = json_decode(File::get($packageJson));

        // Check tailwind & vue are required
        $this->assertTrue(isset($json->devDependencies->tailwindcss));
        $this->assertTrue(isset($json->devDependencies->vue));
    }

    public function tearDown(): void
    {
        $path = PluginManager::instance()->findByIdentifier($this->testPlugin)->getPluginPath();

        $files = [
            $path . '/vite.config.mjs',
            $path . '/package.json',
            $path . '/tailwind.config.js',
            $path . '/postcss.config.mjs',
        ];

        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }
}
