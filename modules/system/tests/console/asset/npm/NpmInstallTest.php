<?php

namespace System\Tests\Console\Asset\Npm;

use System\Classes\Asset\PackageManager;
use System\Tests\Bootstrap\TestCase;
use System\Tests\Console\Asset\NpmTestTrait;
use Winter\Storm\Support\Facades\File;

class NpmInstallTest extends TestCase
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
    public function testNpmInstallSingle(): void
    {
        // Validate the package Json does not have dependencies
        $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
        $this->assertArrayNotHasKey('dependencies', $packageJson);

        // Validate node_modules not found
        $this->assertDirectoryDoesNotExist($this->themePath . '/node_modules');
        $this->assertFileNotExists($this->lockPath);

        $this->withPackageJsonRestore(function () {
            // Run npm install for a single non-dev package
            $this->artisan('npm:install', [
                'package' => 'theme-npmtest',
                'npmArgs' => ['is-odd'],
                '--disable-tty' => true
            ])
                ->assertExitCode(0);

            // Validate lock file was generated successfully
            $this->assertFileExists($this->lockPath);

            // Validate the contents of package.json
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $this->assertArrayHasKey('dependencies', $packageJson);
            $this->assertArrayHasKey('is-odd', $packageJson['dependencies']);

            // Validate that node_modules paths exist
            $this->assertDirectoryExists($this->themePath . '/node_modules');
            $this->assertDirectoryExists($this->themePath . '/node_modules/is-odd');
        });
    }

    /**
     * Test the ability to install multiple npm packages via artisan
     */
    public function testNpmInstallMultiple(): void
    {
        // Validate the package Json does not have dependencies
        $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
        $this->assertArrayNotHasKey('dependencies', $packageJson);

        // Validate node_modules not found
        $this->assertDirectoryDoesNotExist($this->themePath . '/node_modules');
        $this->assertFileNotExists($this->lockPath);

        $this->withPackageJsonRestore(function () {
            // Run npm install for multiple non-dev packages
            $this->artisan('npm:install', [
                'package' => 'theme-npmtest',
                'npmArgs' => ['is-odd', 'is-even'],
                '--disable-tty' => true
            ])
                ->assertExitCode(0);

            // Validate lock file was generated successfully
            $this->assertFileExists($this->lockPath);

            // Validate the contents of package.json
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $this->assertArrayHasKey('dependencies', $packageJson);
            $this->assertArrayHasKey('is-odd', $packageJson['dependencies']);
            $this->assertArrayHasKey('is-even', $packageJson['dependencies']);

            // Validate that node_modules paths exist
            $this->assertDirectoryExists($this->themePath . '/node_modules');
            $this->assertDirectoryExists($this->themePath . '/node_modules/is-odd');
            $this->assertDirectoryExists($this->themePath . '/node_modules/is-even');
        });
    }


    /**
     * Test the ability to install a single dev npm package via artisan
     */
    public function testNpmInstallSingleDev(): void
    {
        // Validate the package Json does not have dependencies
        $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
        $this->assertArrayNotHasKey('devDependencies', $packageJson);

        // Validate node_modules not found
        $this->assertDirectoryDoesNotExist($this->themePath . '/node_modules');
        $this->assertFileNotExists($this->lockPath);

        $this->withPackageJsonRestore(function () {
            // Run npm install for a single dev package
            $this->artisan('npm:install', [
                'package' => 'theme-npmtest',
                'npmArgs' => ['is-odd'],
                '--dev' => true,
                '--disable-tty' => true
            ])
                ->assertExitCode(0);

            // Validate lock file was generated successfully
            $this->assertFileExists($this->lockPath);

            // Validate the contents of package.json
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $this->assertArrayHasKey('devDependencies', $packageJson);
            $this->assertArrayHasKey('is-odd', $packageJson['devDependencies']);

            // Validate that node_modules paths exist
            $this->assertDirectoryExists($this->themePath . '/node_modules');
            $this->assertDirectoryExists($this->themePath . '/node_modules/is-odd');
        });
    }

    /**
     * Test the ability to install multiple dev npm packages via artisan
     */
    public function testNpmInstallMultipleDev(): void
    {
        // Validate the package Json does not have dependencies
        $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
        $this->assertArrayNotHasKey('devDependencies', $packageJson);

        // Validate node_modules not found
        $this->assertDirectoryDoesNotExist($this->themePath . '/node_modules');
        $this->assertFileNotExists($this->lockPath);

        $this->withPackageJsonRestore(function () {
            // Run npm install for multiple dev packages
            $this->artisan('npm:install', [
                'package' => 'theme-npmtest',
                'npmArgs' => ['is-odd', 'is-even'],
                '--dev' => true,
                '--disable-tty' => true
            ])
                ->assertExitCode(0);

            // Validate lock file was generated successfully
            $this->assertFileExists($this->lockPath);

            // Validate the contents of package.json
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $this->assertArrayHasKey('devDependencies', $packageJson);
            $this->assertArrayHasKey('is-odd', $packageJson['devDependencies']);
            $this->assertArrayHasKey('is-even', $packageJson['devDependencies']);

            // Validate that node_modules paths exist
            $this->assertDirectoryExists($this->themePath . '/node_modules');
            $this->assertDirectoryExists($this->themePath . '/node_modules/is-odd');
            $this->assertDirectoryExists($this->themePath . '/node_modules/is-even');
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
