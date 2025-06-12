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
            'id'            => ['type'=>'integer', 'index'=>'primary', 'required'=>true],
            'cb'            => ['type'=>'tinyint'],
            'switch'        => ['type'=>'tinyint'],
            'int'           => ['type'=>'integer'],
            'uint'          => ['type'=>'integer', 'required'=>true],
            'double'        => ['type'=>'double'],
            'range'         => ['type'=>'integer', 'required'=>true],
            'datetime'      => ['type'=>'datetime'],
            'date'          => ['type'=>'date', 'required'=>true],
            'time'          => ['type'=>'time'],
            'md'            => ['type'=>'text'],
            'textarea'      => ['type'=>'text'],
            'text'          => ['type'=>'varchar', 'required'=>true],
            'phone_id'      => ['type'=>'integer', 'index'=>true, 'required'=>true],
            'user_id'       => ['type'=>'integer', 'index'=>true, 'required'=>true],
            'data'          => ['type'=>'text'],
            'sort_order'    => ['type'=>'integer', 'index'=>true],
            'taggable_id'   => ['type'=>'integer', 'index'=>'morphable_index'],
            'taggable_type' => ['type'=>'varchar', 'index'=>'morphable_index'],
            'created_at'    => ['type'=>'datetime'],
            'updated_at'    => ['type'=>'datetime'],
        ];

        $schemaBuilder = Schema::getConnection()->getSchemaBuilder();

        $tableColumns = collect($schemaBuilder->getColumns($this->table));
        $tableIndexes = collect($schemaBuilder->getIndexes($this->table))->pluck('columns', 'name');

        foreach ($columns as $name => $definition) {
            $column = $tableColumns->where('name', $name)->first();

            $this->assertEquals(array_get($definition, 'type'), $column['type_name']);
            $this->assertEquals(array_get($definition, 'required', false), !$column['nullable']);

            // assert an index has been created for the primary, morph and foreign keys
            if ($indexName = array_get($definition, 'index')) {
                if ($indexName === true) {
                    $indexName = sprintf("%s_%s_index", $this->table, $name);
                }
                $this->assertTrue($schemaBuilder->hasIndex($this->table, $indexName));

                if ($indexColumns = array_get($indexName, $tableIndexes)) {
                    $this->assertTrue(in_array($name, $indexColumns));
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
