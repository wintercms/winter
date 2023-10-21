<?php

namespace System\Tests\Console;

use System\Tests\Bootstrap\PluginTestCase;

class CreateMigrationTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->setPluginsPath(base_path() . '/modules/system/tests/fixtures/plugins/');
    }

    public function testCreateMigration()
    {
        $this->artisan('create:migration Winter.Tester -c --force --model TestModel');
    }

    public function testUpdateMigration()
    {
        $this->artisan('create:migration Winter.Tester -u --force --model TestModel');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
