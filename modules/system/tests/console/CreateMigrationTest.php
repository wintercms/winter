<?php

namespace System\Tests\Console;

use File;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use System\Tests\Bootstrap\PluginTestCase;

class CreateMigrationTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->setPluginsPath(base_path() . '/modules/system/tests/fixtures/plugins/');

        $this->table = 'winter_tester_test_model';
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

        $this->assertTrue(Schema::hasTable($this->table));

        $columns = [
            'id'            => ['type'=>'integer', 'index'=>'primary'],
            'cb'            => ['type'=>'boolean'],
            'switch'        => ['type'=>'boolean'],
            'int'           => ['type'=>'integer'],
            'uint'          => ['type'=>'integer'],
            'double'        => ['type'=>'float'],
            'range'         => ['type'=>'integer'],
            'datetime'      => ['type'=>'datetime'],
            'date'          => ['type'=>'date'],
            'time'          => ['type'=>'time'],
            'md'            => ['type'=>'text'],
            'textarea'      => ['type'=>'text'],
            'text'          => ['type'=>'string'],
            'phone_id'      => ['type'=>'integer', 'index'=>true],
            'user_id'       => ['type'=>'integer', 'index'=>true],
            'data'          => ['type'=>'text'],
            'sort_order'    => ['type'=>'integer', 'index'=>true],
            'taggable_id'   => ['type'=>'integer', 'index'=>'morphable_index'],
            'taggable_type' => ['type'=>'string', 'index'=>'morphable_index'],
            'created_at'    => ['type'=>'datetime'],
            'updated_at'    => ['type'=>'datetime'],
        ];

        $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();
        $table = $schemaManager->listTableDetails($this->table);

        foreach ($columns as $name => $definition) {
            $this->assertEquals(array_get($definition, 'type'), Schema::getColumnType($this->table, $name));

            // assert an index has been created for the primary, morph and foreign keys
            if ($indexName = array_get($definition, 'index')) {
                if ($indexName === true) {
                    $indexName = sprintf("%s_%s_index", $this->table, $name);
                }
                $this->assertTrue($table->hasIndex($indexName));

                if ($indexName === 'morphable_index') {
                    $index = $table->getIndex($indexName);
                    $this->assertTrue(in_array($name, $index->getColumns()));
                }
            }
        }

        $migration->down();
        $this->assertFalse(Schema::hasTable($this->table));
    }

    public function tearDown(): void
    {
        File::move($this->versionFile . '.bak', $this->versionFile);
        File::deleteDirectory($this->versionFolder);

        parent::tearDown();
    }
}
