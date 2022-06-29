<?php

namespace System\Tests\Plugins\Database;

use System\Tests\Bootstrap\PluginTestCase;
use System\Models\File as FileModel;
use Database\Tester\Models\User;
use Model;

class AttachManyModelTest extends PluginTestCase
{
    public function setUp() : void
    {
        parent::setUp();

        include_once base_path() . '/modules/system/tests/fixtures/plugins/database/tester/models/User.php';

        $this->runPluginRefreshCommand('Database.Tester');
    }

    public function testDeleteFlagDestroyRelationship()
    {
        Model::unguard();
        $user = User::create(['name' => 'Stevie', 'email' => 'stevie@example.com']);
        Model::reguard();

        $this->assertEmpty($user->photos);
        $user->photos()->create(['data' => base_path() . '/modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png']);
        $user->reloadRelations();
        $this->assertNotEmpty($user->photos);

        $photo = $user->photos->first();
        $photoId = $photo->id;

        $user->photos()->remove($photo);
        $this->assertNull(FileModel::find($photoId));
    }

    public function testDeleteFlagDeleteModel()
    {
        Model::unguard();
        $user = User::create(['name' => 'Stevie', 'email' => 'stevie@example.com']);
        Model::reguard();

        $this->assertEmpty($user->photos);
        $user->photos()->create(['data' => base_path() . '/modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png']);
        $user->reloadRelations();
        $this->assertNotEmpty($user->photos);

        $photo = $user->photos->first();
        $this->assertNotNull($photo);
        $photoId = $photo->id;

        $user->delete();
        $this->assertNull(FileModel::find($photoId));
    }
}
