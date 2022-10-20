<?php

namespace System\Tests\Console;

use File;
use System\Classes\MixAssets;
use System\Console\MixCompile;
use System\Tests\Bootstrap\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class MixCompileTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!File::exists(base_path('node_modules'))) {
            $this->markTestSkipped('This test requires node_modules to be installed');
        }
    }

    public function testCompileMultiple()
    {
        [$command, $output] = $this->makeCommand();

        $result = $command->run(new ArrayInput([
            '--manifest' => 'modules/system/tests/fixtures/npm/package-ac.json',
            '--silent' => true
        ]), $output);

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    public function testCompileMultipleWithErrors()
    {
        [$command, $output] = $this->makeCommand();

        $result = $command->run(new ArrayInput([
            '--manifest' => 'modules/system/tests/fixtures/npm/package-abc.json',
            '--silent' => true
        ]), $output);

        $this->assertIsInt($result);
        $this->assertEquals(1, $result);
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    public function testCompileTarget()
    {
        [$command, $output] = $this->makeCommand();

        $result = $command->run(new ArrayInput([
            '--manifest' => 'modules/system/tests/fixtures/npm/package-abc.json',
            '--package' => 'mix.testa',
            '--silent' => true
        ]), $output);

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    public function testCompileTargetWithError()
    {
        [$command, $output] = $this->makeCommand();

        $result = $command->run(new ArrayInput([
            '--manifest' => 'modules/system/tests/fixtures/npm/package-abc.json',
            '--package' => 'mix.testb',
            '--silent' => true
        ]), $output);

        $this->assertIsInt($result);
        $this->assertEquals(1, $result);
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
    }

    public function testCompileTargetStopOnError()
    {
        [$command, $output] = $this->makeCommand();

        $result = $command->run(new ArrayInput([
            '--manifest' => 'modules/system/tests/fixtures/npm/package-abc.json',
            '--stop-on-error' => true,
            '--silent' => true
        ]), $output);

        $this->assertIsInt($result);
        $this->assertEquals(1, $result);
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    protected function makeCommand(): array
    {
        $output = new BufferedOutput();
        $command = new MixCompile();
        $command->setLaravel($this->app);
        return [$command, $output];
    }

    public function tearDown(): void
    {
        File::deleteDirectory('modules/system/tests/fixtures/plugins/mix/testa/assets/dist');
        File::deleteDirectory('modules/system/tests/fixtures/plugins/mix/testb/assets/dist');
        File::deleteDirectory('modules/system/tests/fixtures/plugins/mix/testc/assets/dist');
        parent::tearDown();
    }
}
