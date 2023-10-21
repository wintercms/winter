<?php

namespace System\Tests\Console;

use File;
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
        $this->artisan('create:migration Winter.Tester -c --force --for-version v0.0.1 --model TestModel --name create_model');
        $this->assertFileExists($this->versionFolder . '/create_model.php');
    }

    public function testUpdateMigration()
    {
        $this->artisan('create:migration Winter.Tester -u --force --for-version v0.0.1 --model TestModel --name update_model');
        $this->assertFileExists($this->versionFolder . '/update_model.php');
    }

    public function tearDown(): void
    {
        File::move($this->versionFile . '.bak', $this->versionFile);
        File::deleteDirectory($this->versionFolder);

        parent::tearDown();
    }
}
