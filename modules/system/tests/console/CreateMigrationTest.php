<?php

namespace System\Tests\Console;

use File;
use Schema;
use System\Tests\Bootstrap\PluginTestCase;

class CreateMigrationTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->setPluginsPath(base_path() . '/modules/system/tests/fixtures/plugins/');
        $this->versionFile = plugins_path('winter/tester/updates/version.yaml');
        $this->versionFolder = plugins_path('winter/tester/updates/v0.0.1');

        File::copy($this->versionFile, $this->versionFile . '.bak');
    }

    public function testCreateMigration()
    {
        $this->artisan('create:migration Winter.Tester -c --force --for-version v0.0.1 --model TestModel --name create_table');
        $this->assertFileExists($this->versionFolder . '/create_table.php');

        $migration = require_once $this->versionFolder . '/create_table.php';
        $migration->up();

        $table = 'winter_tester_test_model';
        $this->assertTrue(Schema::hasTable($table));

        $columns = [
            'id' => 'integer',
            'cb' => 'boolean',
            'switch' => 'boolean',
            'int' => 'integer',
            'uint' => 'integer', # 'unsignedInteger'
            'double' => 'float', # 'double'
            'range' => 'integer', # 'unsignedInteger'
            'datetime' => 'datetime',
            'date' => 'date',
            'time' => 'time',
            'md' => 'text', # 'mediumText'
            'textarea' => 'text',
            'text' => 'string',
            'phone_id' => 'integer', # 'unsignedInteger'
            'user_id' => 'integer', # 'unsignedInteger'
            'data' => 'text', # mediumText
            'taggable_type' => 'string',
            'taggable_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];

        foreach ($columns as $name => $type) {
            $this->assertEquals($type, Schema::getColumnType($table, $name));
        }

        $migration->down();
        $this->assertFalse(Schema::hasTable($table));
    }

    public function tearDown(): void
    {
        File::move($this->versionFile . '.bak', $this->versionFile);
        File::deleteDirectory($this->versionFolder);

        parent::tearDown();
    }
}
