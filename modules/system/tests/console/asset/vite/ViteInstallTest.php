<?php

namespace System\Tests\Console\Asset\Vite;

use System\Classes\Asset\PackageManager;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Support\Facades\File;

class ViteInstallTest extends TestCase
{
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
        $this->fixturePath = base_path('modules/system/tests');
        $this->jsonPath = $this->fixturePath . '/package.json';
        $this->lockPath = $this->fixturePath . '/package-lock.json';
        $this->backupPath = $this->fixturePath . '/package-testing.json';

        // Add our testing theme because it won't be auto discovered
        PackageManager::instance()->registerPackage(
            'theme-assettest',
            base_path('modules/system/tests/fixtures/themes/assettest/vite.config.mjs'),
            'vite'
        );
        PackageManager::instance()->registerPackage(
            'theme-npmtest',
            base_path('modules/system/tests/fixtures/themes/npmtest/vite.config.mjs'),
            'vite'
        );
    }

    public function testViteInstallMissingPackageJson(): void
    {
        $this->artisan('vite:install', [
            'assetPackage' => ['theme-assettest'],
            '--package-json' => '/some/file',
            '--no-install' => true
        ])
            ->expectsOutputToContain('The supplied --package-json path does not exist.')
            ->assertExitCode(1);
    }

    public function testViteInstallSinglePackage(): void
    {
        $this->withPackageJsonRestore(function () {
            $this->artisan('vite:install', [
                'assetPackage' => ['theme-assettest'],
                '--package-json' => $this->jsonPath,
                '--no-install' => true
            ])
                ->expectsQuestion('vite was not found as a dependency in package.json, would you like to add it?', true)
                ->expectsQuestion('laravel-vite-plugin was not found as a dependency in package.json, would you like to add it?', true)
                ->expectsOutput('Adding theme-assettest (modules/system/tests/fixtures/themes/assettest) to the workspaces.packages property in package.json')
                ->assertExitCode(0);

            $this->assertFileExists($this->jsonPath);
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $this->assertArrayHasKey('devDependencies', $packageJson);
            $this->assertArrayHasKey('vite', $packageJson['devDependencies']);
            $this->assertArrayHasKey('laravel-vite-plugin', $packageJson['devDependencies']);

            $this->assertArrayHasKey('workspaces', $packageJson);
            $this->assertArrayHasKey('packages', $packageJson['workspaces']);
            $this->assertContains('modules/system/tests/fixtures/themes/assettest', $packageJson['workspaces']['packages']);
        });
    }

    public function testViteInstallMultiplePackages(): void
    {
        $this->withPackageJsonRestore(function () {
            $this->artisan('vite:install', [
                'assetPackage' => ['theme-assettest', 'theme-npmtest'],
                '--package-json' => $this->jsonPath,
                '--no-install' => true
            ])
                ->expectsQuestion('vite was not found as a dependency in package.json, would you like to add it?', true)
                ->expectsQuestion('laravel-vite-plugin was not found as a dependency in package.json, would you like to add it?', true)
                ->expectsOutput('Adding theme-assettest (modules/system/tests/fixtures/themes/assettest) to the workspaces.packages property in package.json')
                ->expectsOutput('Adding theme-npmtest (modules/system/tests/fixtures/themes/npmtest) to the workspaces.packages property in package.json')
                ->assertExitCode(0);

            $this->assertFileExists($this->jsonPath);
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $this->assertArrayHasKey('devDependencies', $packageJson);
            $this->assertArrayHasKey('vite', $packageJson['devDependencies']);
            $this->assertArrayHasKey('laravel-vite-plugin', $packageJson['devDependencies']);

            $this->assertArrayHasKey('workspaces', $packageJson);
            $this->assertArrayHasKey('packages', $packageJson['workspaces']);
            $this->assertContains('modules/system/tests/fixtures/themes/assettest', $packageJson['workspaces']['packages']);
            $this->assertContains('modules/system/tests/fixtures/themes/npmtest', $packageJson['workspaces']['packages']);
        });
    }

    public function testViteInstallMissingPackage(): void
    {
        // We should receive an exception for a missing package
        $this->withPackageJsonRestore(function () {
            $this->expectException(SystemException::class);
            $this->expectExceptionMessage('PackageNotFoundException: The package `theme-assettest2` does not exist.');

            $this->artisan('vite:install', [
                'assetPackage' => ['theme-assettest2'],
                '--package-json' => $this->jsonPath,
                '--no-install' => true
            ])
                ->assertExitCode(1);
        });
    }

    public function testViteInstallRelativePath(): void
    {
        $this->withPackageJsonRestore(function () {
            $this->artisan('vite:install', [
                'assetPackage' => ['theme-assettest'],
                '--package-json' => 'modules/system/tests/package.json',
                '--no-install' => true
            ])
                ->expectsQuestion('vite was not found as a dependency in package.json, would you like to add it?', true)
                ->expectsQuestion('laravel-vite-plugin was not found as a dependency in package.json, would you like to add it?', true)
                ->expectsOutput('Adding theme-assettest (modules/system/tests/fixtures/themes/assettest) to the workspaces.packages property in package.json')
                ->assertExitCode(0);
        });
    }

    public function testViteInstallIgnoredPackage(): void
    {
        $this->withPackageJsonRestore(function () {
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $packageJson['workspaces'] = [
                'ignoredPackages' => [
                    'modules/system/tests/fixtures/themes/assettest'
                ]
            ];

            File::put($this->jsonPath, json_encode($packageJson, JSON_PRETTY_PRINT));

            $this->artisan('vite:install', [
                'assetPackage' => ['theme-assettest'],
                '--package-json' => $this->jsonPath,
                '--no-install' => true
            ])
                ->expectsQuestion('vite was not found as a dependency in package.json, would you like to add it?', false)
                ->expectsQuestion('laravel-vite-plugin was not found as a dependency in package.json, would you like to add it?', false)
                ->expectsOutput('The requested package theme-assettest (modules/system/tests/fixtures/themes/assettest) is ignored, remove it from package.json to continue.')
                ->assertExitCode(0);

            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $this->assertArrayHasKey('workspaces', $packageJson);
            $this->assertArrayNotHasKey('packages', $packageJson['workspaces']);
            $this->assertArrayNotHasKey('dependencies', $packageJson);
            $this->assertArrayNotHasKey('devDependencies', $packageJson);
        });
    }

    public function testViteInstallWithNpmInstall(): void
    {
        $this->withPackageJsonRestore(function () {
            $this->assertDirectoryDoesNotExist($this->fixturePath . '/node_modules');

            $this->artisan('vite:install', [
                'assetPackage' => ['theme-assettest'],
                '--package-json' => $this->jsonPath,
                '--disable-tty' => true
            ])
                ->expectsQuestion('vite was not found as a dependency in package.json, would you like to add it?', true)
                ->expectsQuestion('laravel-vite-plugin was not found as a dependency in package.json, would you like to add it?', true)
                ->expectsOutput('Adding theme-assettest (modules/system/tests/fixtures/themes/assettest) to the workspaces.packages property in package.json')
                ->expectsOutputToContain('packages, and audited') // output from npm i
                ->assertExitCode(0);

            $this->assertFileExists($this->jsonPath);
            $packageJson = json_decode(File::get($this->jsonPath), JSON_OBJECT_AS_ARRAY);
            $this->assertArrayHasKey('devDependencies', $packageJson);
            $this->assertArrayHasKey('vite', $packageJson['devDependencies']);
            $this->assertArrayHasKey('laravel-vite-plugin', $packageJson['devDependencies']);

            $this->assertArrayHasKey('workspaces', $packageJson);
            $this->assertArrayHasKey('packages', $packageJson['workspaces']);
            $this->assertContains('modules/system/tests/fixtures/themes/assettest', $packageJson['workspaces']['packages']);

            $this->assertFileExists($this->lockPath);

            $this->assertDirectoryExists($this->fixturePath . '/node_modules');
            $this->assertDirectoryExists($this->fixturePath . '/node_modules/vite');
            $this->assertDirectoryExists($this->fixturePath . '/node_modules/laravel-vite-plugin');
        });
    }

    /**
     * Helper to run test logic and handle restoring package.json file after
     */
    protected function withPackageJsonRestore(callable $callback): void
    {
        File::copy($this->backupPath, $this->jsonPath);
        $callback();
        File::delete($this->jsonPath);
    }

    public function tearDown(): void
    {
        if (File::isDirectory($this->fixturePath . '/node_modules')) {
            File::deleteDirectory($this->fixturePath . '/node_modules');
        }

        if (File::exists($this->lockPath)) {
            File::delete($this->lockPath);
        }

        parent::tearDown();
    }
}
