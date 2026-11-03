<?php

namespace Backend\Tests\Models;

use Backend\Models\User;
use Backend\Models\UserRole;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Auth\AuthorizationException;
use Winter\Storm\Database\Model as Eloquent;

class UserTest extends PluginTestCase
{
    protected User $superuser;
    protected User $admin;
    protected User $lowPriv;

    public function setUp(): void
    {
        parent::setUp();

        Eloquent::unguarded(function () {
            $developerRole = UserRole::where('code', UserRole::CODE_DEVELOPER)->first();
            $publisherRole = UserRole::where('code', UserRole::CODE_PUBLISHER)->first();

            $this->superuser = User::create([
                'email'                 => 'superuser@test.com',
                'login'                 => 'superuser',
                'password'              => 'Testing123!',
                'password_confirmation' => 'Testing123!',
                'first_name'            => 'Super',
                'last_name'             => 'User',
                'is_superuser'          => true,
                'is_activated'          => true,
                'role_id'               => $developerRole->id,
                'permissions'           => [],
            ]);

            $this->admin = User::create([
                'email'                 => 'admin@test.com',
                'login'                 => 'admin_user',
                'password'              => 'Testing123!',
                'password_confirmation' => 'Testing123!',
                'first_name'            => 'Admin',
                'last_name'             => 'User',
                'is_superuser'          => false,
                'is_activated'          => true,
                'role_id'               => $publisherRole->id,
                'permissions'           => ['backend.manage_users' => 1],
            ]);

            $this->lowPriv = User::create([
                'email'                 => 'lowpriv@test.com',
                'login'                 => 'lowpriv',
                'password'              => 'Testing123!',
                'password_confirmation' => 'Testing123!',
                'first_name'            => 'Low',
                'last_name'             => 'Priv',
                'is_superuser'          => false,
                'is_activated'          => true,
                'role_id'               => null,
                'permissions'           => [],
            ]);
        });
    }

    // ---- Denied operations ----

    public function testSelfEditRoleThrows()
    {
        $this->actingAs($this->admin);

        $this->admin->role_id = $this->admin->role_id + 1;

        $this->expectException(AuthorizationException::class);
        $this->admin->save();
    }

    public function testSelfEditSuperuserThrows()
    {
        $this->actingAs($this->admin);

        $this->admin->is_superuser = true;

        $this->expectException(AuthorizationException::class);
        $this->admin->save();
    }

    public function testSelfEditPermissionsThrows()
    {
        $this->actingAs($this->admin);

        $this->admin->permissions = ['backend.manage_users' => 1, 'backend.access_dashboard' => 1];

        $this->expectException(AuthorizationException::class);
        $this->admin->save();
    }

    public function testNonSuperuserGrantSuperuserThrows()
    {
        $this->actingAs($this->admin);

        $this->lowPriv->is_superuser = true;

        $this->expectException(AuthorizationException::class);
        $this->lowPriv->save();
    }

    public function testNonSuperuserRevokeSuperuserThrows()
    {
        $this->actingAs($this->admin);

        $this->superuser->is_superuser = false;

        $this->expectException(AuthorizationException::class);
        $this->superuser->save();
    }

    public function testNonSuperuserEditingSuperuserThrows()
    {
        $this->actingAs($this->admin);

        $this->superuser->first_name = 'Changed';

        $this->expectException(AuthorizationException::class);
        $this->superuser->save();
    }

    public function testNoManageUsersEditingOtherThrows()
    {
        $this->actingAs($this->lowPriv);

        $this->admin->first_name = 'Changed';

        $this->expectException(AuthorizationException::class);
        $this->admin->save();
    }

    public function testNoManageUsersCreatingUserThrows()
    {
        $this->actingAs($this->lowPriv);

        $this->expectException(AuthorizationException::class);
        Eloquent::unguarded(function () {
            User::create([
                'email'                 => 'newuser@test.com',
                'login'                 => 'newuser',
                'password'              => 'Testing123!',
                'password_confirmation' => 'Testing123!',
                'first_name'            => 'New',
                'last_name'             => 'User',
                'is_superuser'          => false,
                'is_activated'          => true,
                'permissions'           => [],
            ]);
        });
    }

    // ---- Allowed operations ----

    public function testSelfEditNonProtectedFieldsAllowed()
    {
        $this->actingAs($this->admin);

        $this->admin->first_name = 'NewFirst';
        $this->admin->last_name = 'NewLast';
        $this->admin->email = 'newemail@test.com';
        $this->admin->save();

        $this->admin->refresh();
        $this->assertEquals('NewFirst', $this->admin->first_name);
        $this->assertEquals('NewLast', $this->admin->last_name);
        $this->assertEquals('newemail@test.com', $this->admin->email);
    }

    public function testAdminCanChangeOtherUserRole()
    {
        $this->actingAs($this->admin);

        $publisherRole = UserRole::where('code', UserRole::CODE_PUBLISHER)->first();
        $this->lowPriv->role_id = $publisherRole->id;
        $this->lowPriv->save();

        $this->lowPriv->refresh();
        $this->assertEquals($publisherRole->id, $this->lowPriv->role_id);
    }

    public function testSuperuserCanGrantSuperuser()
    {
        $this->actingAs($this->superuser);

        $this->lowPriv->is_superuser = true;
        $this->lowPriv->save();

        $this->lowPriv->refresh();
        $this->assertTrue((bool) $this->lowPriv->is_superuser);
    }

    public function testAdminCanChangeOtherUserPermissions()
    {
        $this->actingAs($this->admin);

        $this->lowPriv->permissions = ['backend.access_dashboard' => 1];
        $this->lowPriv->save();

        $this->lowPriv->refresh();
        $this->assertEquals(['backend.access_dashboard' => 1], $this->lowPriv->permissions);
    }

    public function testNoAuthUserCanModifyAnyField()
    {
        // No actingAs — simulates CLI/artisan/queue context
        $developerRole = UserRole::where('code', UserRole::CODE_DEVELOPER)->first();

        $this->lowPriv->role_id = $developerRole->id;
        $this->lowPriv->is_superuser = true;
        $this->lowPriv->permissions = ['backend.manage_users' => 1];
        $this->lowPriv->save();

        $this->lowPriv->refresh();
        $this->assertEquals($developerRole->id, $this->lowPriv->role_id);
        $this->assertTrue((bool) $this->lowPriv->is_superuser);
        $this->assertEquals(['backend.manage_users' => 1], $this->lowPriv->permissions);
    }
}
