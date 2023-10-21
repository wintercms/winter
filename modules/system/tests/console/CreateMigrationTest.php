<?php

namespace System\Tests\Console;

use System\Tests\Bootstrap\PluginTestCase;

class CreateMigrationTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->setPluginsPath(base_path() . '/modules/system/tests/fixtures/plugins');

        $this->artisan('create:migration Winter.Tester -c --model TestModel');
    }

    public function testCreateMigration()
    {
    }

    public function testUpdateMigration()
    {
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
