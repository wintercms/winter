<?php

namespace System\Tests\Console;

use System\Tests\Bootstrap\TestCase;

class CreateMigrationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

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
