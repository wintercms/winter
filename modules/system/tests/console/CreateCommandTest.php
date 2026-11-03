<?php

namespace System\Tests\Console;

use File;
use InvalidArgumentException;
use System\Tests\Bootstrap\TestCase;

class CreateCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->setPluginsPath(base_path() . '/modules/system/tests/fixtures/plugins/');
    }

    public function testCreatingCommand()
    {
        $this->artisan('create:command Winter.Tester TestCommand')
            ->assertExitCode(0);
        $this->artisan('create:command Winter.Tester Test1Command')
            ->assertExitCode(0);

        $this->artisan('create:command Winter.Tester4You TestCommand')
            ->assertExitCode(0);
        $this->artisan('create:command Winter.Tester4You Test1Command')
            ->assertExitCode(0);
    }

    public function testNotCreatingCommandBeginingWithNumber()
    {
        $this->artisan('create:command Winter.Tester 1Command')->assertFailed();
        $this->artisan('create:command Winter.Tester4You 1Command')->assertFailed();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(plugins_path('winter/tester/console'));
        File::deleteDirectory(plugins_path('winter/tester4you'));

        parent::tearDown();
    }
}
