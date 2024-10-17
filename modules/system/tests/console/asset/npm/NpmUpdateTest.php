<?php

namespace System\tests\console\asset\npm;

use System\Classes\Asset\PackageManager;
use System\Tests\Bootstrap\TestCase;
use System\Tests\Console\Asset\NpmTestTrait;
use Winter\Storm\Support\Facades\File;

class NpmUpdateTest extends TestCase
{
    use NpmTestTrait;

    protected string $themePath;
    protected string $jsonPath;
    protected string $lockPath;
    protected string $backupPath;

    public function setUp(): void
    {
        parent::setUp();

        if (!File::exists(base_path('node_modules'))) {
            $this->markTestSkipped('This test requires node_modules to be installed');
        }

        // Define some helpful paths
        $this->themePath = base_path('modules/system/tests/fixtures/themes/npmtest');
        $this->jsonPath = $this->themePath . '/package.json';
        $this->lockPath = $this->themePath . '/package-lock.json';
        $this->backupPath = $this->themePath . '/package.backup.json';

        // Add our testing theme because it won't be auto discovered
        PackageManager::instance()->registerPackage(
            'theme-npmtest',
            $this->themePath . '/vite.config.mjs',
            'vite'
        );
    }

    /**
     * Test the ability to install a single npm package via artisan
     */
    public function testNpmUpdate(): void
    {
        // Validate the package Json does not have dependencies
        $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
        $this->assertArrayNotHasKey('dependencies', $packageJson);

        // Validate node_modules not found
        $this->assertDirectoryDoesNotExist($this->themePath . '/node_modules');
        $this->assertFileNotExists($this->lockPath);

        $this->withPackageJsonRestore(function () {
            // Update the contents of package.json to include a package at an old patch
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $packageJson['dependencies'] = [
                'is-odd' => '^3.0.0'
            ];
            File::put($this->jsonPath, json_encode($packageJson, JSON_PRETTY_PRINT));

            // Run npm update
            $this->artisan('npm:update', [
                'package' => 'theme-npmtest',
                '--save' => true,
                '--disable-tty' => true
            ])
                ->assertExitCode(0);

            // Validate lock file was generated successfully
            $this->assertFileExists($this->lockPath);

            // Get the new contents of package.json
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);

            // Validate that the package.json contents does not match the old patch value we added above
            $this->assertArrayHasKey('dependencies', $packageJson);
            $this->assertArrayHasKey('is-odd', $packageJson['dependencies']);
            $this->assertNotEquals('^3.0.0', $packageJson['dependencies']['is-odd']);

            // Validate that node_modules paths exist
            $this->assertDirectoryExists($this->themePath . '/node_modules');
            $this->assertDirectoryExists($this->themePath . '/node_modules/is-odd');
        });
    }

    /**
     * Cleanup the test theme
     */
    public function tearDown(): void
    {
        if (File::isDirectory($this->themePath . '/node_modules')) {
            File::deleteDirectory($this->themePath . '/node_modules');
        }

        foreach ([$this->backupPath, $this->lockPath] as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        parent::tearDown();
    }
}
