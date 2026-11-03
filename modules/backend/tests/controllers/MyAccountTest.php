<?php

namespace Backend\Tests\Controllers;

use Backend\Controllers\MyAccount;
use Backend\Controllers\Users;
use Backend\Tests\Fixtures\Models\UserFixture;
use System\Tests\Bootstrap\PluginTestCase;

class MyAccountTest extends PluginTestCase
{
    public function testMyAccountRequiresNoSpecificPermission(): void
    {
        $user = new UserFixture;
        $this->actingAs($user);

        $controller = new MyAccount;
        $this->assertEmpty($controller->requiredPermissions);
    }

    public function testUsersControllerNoLongerNullifiesPermissionsForMyaccount(): void
    {
        $user = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($user);

        $controller = new Users;
        $this->assertEquals(['backend.manage_users'], $controller->requiredPermissions);
    }
}
