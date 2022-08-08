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
        putenv('NPM_PACKAGE_MANIFEST=modules/system/tests/fixtures/npm/package-ab.json');

        [$command, $output] = $this->makeCommand();

        $result = $command->run(new ArrayInput([]), $output);

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
    }

    public function testCompileMultipleWithErrors()
    {
        putenv('NPM_PACKAGE_MANIFEST=modules/system/tests/fixtures/npm/package-abc.json');

        [$command, $output] = $this->makeCommand();

        $result = $command->run(new ArrayInput([]), $output);

        $this->assertIsInt($result);
        $this->assertEquals(1, $result);
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    public function testCompileTarget()
    {
        putenv('NPM_PACKAGE_MANIFEST=modules/system/tests/fixtures/npm/package-abc.json');

        [$command, $output] = $this->makeCommand();

        $result = $command->run(new ArrayInput(['--package' => 'mix.testa']), $output);

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
    }

    public function testCompileTargetWithError()
    {
        putenv('NPM_PACKAGE_MANIFEST=modules/system/tests/fixtures/npm/package-abc.json');

        [$command, $output] = $this->makeCommand();

        $result = $command->run(new ArrayInput(['--package' => 'mix.testc']), $output);

        $this->assertIsInt($result);
        $this->assertEquals(1, $result);
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testa/assets/dist/app.js'));
        $this->assertFileNotExists(base_path('modules/system/tests/fixtures/plugins/mix/testb/assets/dist/app.js'));
        $this->assertFileExists(base_path('modules/system/tests/fixtures/plugins/mix/testc/assets/dist/app.js'));
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
