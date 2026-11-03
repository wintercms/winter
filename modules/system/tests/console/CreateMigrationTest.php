<?php

namespace System\Tests\Console;

use File;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use System\Tests\Bootstrap\PluginTestCase;
use Yaml;

class CreateMigrationTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->setPluginsPath(base_path() . '/modules/system/tests/fixtures/plugins/');

        $this->table = 'winter_tester_test_model';
        $this->versionFile = plugins_path('winter/tester/updates/version.yaml');
        $this->versionFolder = plugins_path('winter/tester/updates/v0.0.1');
        $this->prefixedVersionFolder = plugins_path('winter/tester/updates/v1.2.10');
        $this->newVersionFolder = plugins_path('winter/tester/updates/v2.0.0');

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
            'cb'            => ['type'=>'boolean'],
            'switch'        => ['type'=>'boolean'],
            'int'           => ['type'=>'integer'],
            'uint'          => ['type'=>'integer', 'required'=>true],
            'double'        => ['type'=>'float'],
            'range'         => ['type'=>'integer', 'required'=>true],
            'datetime'      => ['type'=>'datetime'],
            'date'          => ['type'=>'date', 'required'=>true],
            'time'          => ['type'=>'time'],
            'md'            => ['type'=>'text'],
            'textarea'      => ['type'=>'text'],
            'text'          => ['type'=>'string', 'required'=>true],
            'phone_id'      => ['type'=>'integer', 'index'=>true, 'required'=>true],
            'user_id'       => ['type'=>'integer', 'index'=>true, 'required'=>true],
            'data'          => ['type'=>'text'],
            'sort_order'    => ['type'=>'integer', 'index'=>true],
            'taggable_id'   => ['type'=>'integer', 'index'=>'morphable_index'],
            'taggable_type' => ['type'=>'string', 'index'=>'morphable_index'],
            'created_at'    => ['type'=>'datetime'],
            'updated_at'    => ['type'=>'datetime'],
        ];

        $table = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails($this->table);

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
            $this->assertEquals(array_get($definition, 'required', false), $table->getColumn($name)->getNotnull());
        }

        $migration->down();
        $this->assertFalse(Schema::hasTable($this->table));
    }

    public function testCreateMigrationReusesExistingPrefixedVersionKey()
    {
        $this->artisan('create:migration Winter.Tester --force --for-version v1.2.10 --name create_table');

        $versions = Yaml::parseFile($this->versionFile);

        // The fixture mixes prefixed and unprefixed keys, and 1.5.1 is the highest, so the most
        // recent key is unprefixed. The migration must still be merged into the existing prefixed
        // key: adding a second key for the same version would collide in
        // VersionManager::getFileVersions(), which normalizes keys.
        $this->assertArrayHasKey('v1.2.10', $versions);
        $this->assertArrayNotHasKey('1.2.10', $versions);
        $this->assertContains('v1.2.10/create_table.php', $versions['v1.2.10']);
    }

    public function testCreateMigrationDeducesConventionFromMostRecentKey()
    {
        // No key matches 2.0.0, so the version is new: the convention is deduced from
        // the most recent key, which is unprefixed (1.5.1) in this fixture. The new key
        // is therefore unprefixed, and only the migration directory keeps the 'v'.
        $this->artisan('create:migration Winter.Tester --force --for-version v2.0.0 --name create_table');

        $versions = Yaml::parseFile($this->versionFile);

        $this->assertArrayHasKey('2.0.0', $versions);
        $this->assertArrayNotHasKey('v2.0.0', $versions);
        $this->assertContains('v2.0.0/create_table.php', $versions['2.0.0']);
    }

    public function testCreateMigrationWithEmptyVersionFile()
    {
        // Winter.NoUpdates ships an empty version file, so there is no convention to
        // deduce: the documented one applies — an unprefixed key, with the 'v'
        // retained only on the migration directory name.
        $noUpdatesFile = plugins_path('winter/noupdates/updates/version.yaml');
        $noUpdatesFolder = plugins_path('winter/noupdates/updates/v1.0.0');
        File::copy($noUpdatesFile, $noUpdatesFile . '.bak');

        try {
            $this->artisan('create:migration Winter.NoUpdates --force --for-version v1.0.0 --name create_table');

            $versions = Yaml::parseFile($noUpdatesFile);

            $this->assertArrayHasKey('1.0.0', $versions);
            $this->assertArrayNotHasKey('v1.0.0', $versions);
            $this->assertContains('v1.0.0/create_table.php', $versions['1.0.0']);
        } finally {
            File::move($noUpdatesFile . '.bak', $noUpdatesFile);
            File::deleteDirectory($noUpdatesFolder);
        }
    }

    public function tearDown() : void
    {
        File::move($this->versionFile . '.bak', $this->versionFile);
        File::deleteDirectory($this->versionFolder);
        File::deleteDirectory($this->prefixedVersionFolder);
        File::deleteDirectory($this->newVersionFolder);

        parent::tearDown();
    }
}
