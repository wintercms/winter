<?php

namespace System\Tests\Console\Asset\Npm;

use System\Classes\Asset\PackageManager;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\File;

class NpmRunTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!File::exists(base_path('node_modules'))) {
            $this->markTestSkipped('This test requires node_modules to be installed');
        }

        // Add our testing theme because it won't be auto discovered
        PackageManager::instance()->registerPackage(
            'theme-npmtest',
            base_path('modules/system/tests/fixtures/themes/npmtest/vite.config.mjs'),
            'vite'
        );
    }

    /**
     * Test the ability to run a npm script via artisan
     */
    public function testNpmRunScript(): void
    {
        $this->artisan('npm:run', [
            'package' => 'theme-npmtest',
            'script' => 'testScript',
            '--disable-tty' => true
        ])
            ->expectsOutputToContain('> echo "Winter says $((1+2))"')
            ->expectsOutputToContain('Winter says 3')
            ->assertExitCode(0);
    }

    /**
     * Test the error handling of a missing script
     */
    public function testNpmRunScriptFailed(): void
    {
        $this->artisan('npm:run', [
            'package' => 'theme-npmtest',
            'script' => 'testMissingScript',
            '--disable-tty' => true
        ])
            ->expectsOutputToContain('Script "testMissingScript" is not defined in package "theme-npmtest"')
            ->assertExitCode(1);
    }
}
