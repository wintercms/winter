<?php

namespace System\Tests\Console\Asset\Mix;

use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\File;

class MixCompileTest extends TestCase
{
    protected string $command = 'mix:compile';

    public function setUp(): void
    {
        parent::setUp();

        if (!File::exists(base_path('node_modules'))) {
            $this->markTestSkipped('This test requires node_modules to be installed');
        }

        if (!File::exists(base_path('node_modules/.bin/mix'))) {
            $this->markTestSkipped('This test requires the mix package to be installed');
        }
    }

    public function testCompileMultiple()
    {
        $this->artisan($this->command, [
            '--manifest' => 'modules/system/tests/fixtures/npm/package-ac.json',
            '--silent' => true
        ])->assertExitCode(0);

        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    public function testCompileMultipleWithErrors()
    {
        $this->artisan($this->command, [
            '--manifest' => 'modules/system/tests/fixtures/npm/package-abc.json',
            '--disable-tty' => true
        ])
            ->expectsOutputToContain('Error: Can\'t resolve \'some-missing-package\'')
            ->assertExitCode(1);

        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    public function testCompileTarget()
    {
        $this->artisan($this->command, [
            '--manifest' => 'modules/system/tests/fixtures/npm/package-abc.json',
            '--package' => 'mix.testa',
            '--silent' => true
        ])->assertExitCode(0);

        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    public function testCompileTargetWithError()
    {
        $this->artisan($this->command, [
            '--manifest' => 'modules/system/tests/fixtures/npm/package-abc.json',
            '--package' => 'mix.testb',
            '--disable-tty' => true
        ])
            ->expectsOutputToContain('Error: Can\'t resolve \'some-missing-package\'')
            ->assertExitCode(1);

        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
    }

    public function testCompileTargetStopOnError()
    {
        $this->artisan($this->command, [
            '--manifest' => 'modules/system/tests/fixtures/npm/package-abc.json',
            '--stop-on-error' => true,
            '--disable-tty' => true
        ])
            ->expectsOutputToContain('Error: Can\'t resolve \'some-missing-package\'')
            ->assertExitCode(1);

        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    public function tearDown(): void
    {
        File::deleteDirectory('modules/system/tests/fixtures/plugins/mix/testa/assets/dist');
        File::deleteDirectory('modules/system/tests/fixtures/plugins/mix/testb/assets/dist');
        File::deleteDirectory('modules/system/tests/fixtures/plugins/mix/testc/assets/dist');
        parent::tearDown();
    }
}
