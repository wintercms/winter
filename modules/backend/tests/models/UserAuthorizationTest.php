<?php

namespace Backend\Tests\Models;

use Backend\Facades\BackendAuth;
use Backend\Models\User;
use Backend\Models\UserGroup;
use Backend\Tests\Fixtures\Models\UserFixture;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Auth\AuthorizationException;
use Winter\Storm\Database\Model;

class UserAuthorizationTest extends PluginTestCase
{
    protected User $targetUser;

    public function setUp(): void
    {
        parent::setUp();

        // Create a real target user in the database for operations that need it
        Model::unguard();
        $this->targetUser = User::create([
            'first_name' => 'Target',
            'last_name' => 'User',
            'login' => 'targetuser',
            'email' => 'target@test.com',
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'is_superuser' => false,
        ]);
        Model::reguard();
    }

    //
    // canBeManagedByUser()
    //

    public function testCanBeManagedByUserReturnsTrueForCli(): void
    {
        // No authenticated user (CLI context)
        BackendAuth::logout();
        $this->assertTrue($this->targetUser->canBeManagedByUser());
    }

    public function testCanBeManagedByUserReturnsFalseWithoutPermission(): void
    {
        $actor = (new UserFixture)->withPermission('backend.manage_users', false);
        $this->assertFalse($this->targetUser->canBeManagedByUser($actor));
    }

    public function testCanBeManagedByUserReturnsTrueWithPermission(): void
    {
        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->assertTrue($this->targetUser->canBeManagedByUser($actor));
    }

    public function testCanBeManagedByUserReturnsFalseForNonSuperuserTargetingSuperuser(): void
    {
        $this->targetUser->is_superuser = true;
        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->assertFalse($this->targetUser->canBeManagedByUser($actor));
    }

    public function testCanBeManagedByUserReturnsTrueForSuperuserTargetingSuperuser(): void
    {
        $this->targetUser->is_superuser = true;
        $actor = (new UserFixture)->asSuperUser()->withPermission('backend.manage_users', true);
        $this->assertTrue($this->targetUser->canBeManagedByUser($actor));
    }

    public function testCanBeManagedByUserChecksPreviousSuperuserStatus(): void
    {
        // Simulate a record that WAS a superuser (getOriginal returns true)
        $this->targetUser->syncOriginal();
        $this->targetUser->is_superuser = true;
        $this->targetUser->syncOriginal();
        $this->targetUser->is_superuser = false;

        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        // getOriginal('is_superuser') is true, so non-superuser can't manage
        $this->assertFalse($this->targetUser->canBeManagedByUser($actor));
    }

    public function testCanBeManagedByUserFallsBackToAuthenticatedUser(): void
    {
        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        // No explicit user passed — falls back to authenticated user
        $this->assertTrue($this->targetUser->canBeManagedByUser());
    }

    //
    // beforeCreate() / beforeUpdate()
    //

    public function testUpdateAllowsCliContext(): void
    {
        BackendAuth::logout();
        $this->targetUser->first_name = 'Updated';
        $this->targetUser->save();
        $this->assertEquals('Updated', $this->targetUser->first_name);
    }

    public function testUpdateAllowsSelfNonEscalatingChanges(): void
    {
        $this->actingAs($this->targetUser);
        $this->targetUser->first_name = 'NewName';
        $this->targetUser->save();
        $this->assertEquals('NewName', $this->targetUser->first_name);
    }

    public function testUpdateBlocksSelfEscalation(): void
    {
        $this->actingAs($this->targetUser);
        $this->targetUser->is_superuser = true;

        $this->expectException(AuthorizationException::class);
        $this->targetUser->save();
    }

    public function testUpdateBlocksUnauthorizedUserModifyingOthers(): void
    {
        $actor = new UserFixture;
        $this->actingAs($actor);

        $this->targetUser->first_name = 'Hacked';
        $this->expectException(AuthorizationException::class);
        $this->targetUser->save();
    }

    public function testUpdateAllowsManageUsersActorModifyingOthers(): void
    {
        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        $this->targetUser->first_name = 'AdminUpdated';
        $this->targetUser->save();
        $this->assertEquals('AdminUpdated', $this->targetUser->first_name);
    }

    public function testSaveWithNoChangesRequiresNoPermission(): void
    {
        $actor = new UserFixture;
        $this->actingAs($actor);

        // The guard fires on create/update, which only happen when there is
        // something to write — a no-op save is allowed for anyone (#1464)
        try {
            $this->targetUser->save();
        } catch (AuthorizationException $e) {
            $this->fail('Should not throw AuthorizationException for a save with no changes');
        }
        $this->assertTrue(true);
    }

    public function testCreateBlocksUnauthorizedUser(): void
    {
        $actor = new UserFixture;
        $this->actingAs($actor);

        $user = new User;
        $user->first_name = 'New';
        $user->last_name = 'User';
        $user->login = 'newuser';
        $user->email = 'new@test.com';
        $user->password = 'TestPassword1';
        $user->password_confirmation = 'TestPassword1';

        $this->expectException(AuthorizationException::class);
        $user->save();
    }

    public function testUpdateBlocksNonSuperuserModifyingSuperuser(): void
    {
        // Make the target a superuser via direct DB update to bypass model events
        $this->targetUser->newQuery()->where('id', $this->targetUser->id)->update(['is_superuser' => true]);

        // Re-fetch the model fresh to avoid stale password hash triggering validation
        $superTarget = User::find($this->targetUser->id);

        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        $superTarget->first_name = 'Hacked';
        $this->expectException(AuthorizationException::class);
        $superTarget->save();
    }

    public function testUpdateSuperuserCanModifyAnyone(): void
    {
        $actor = (new UserFixture)->asSuperUser();
        $this->actingAs($actor);

        $this->targetUser->first_name = 'SuperUpdated';
        $this->targetUser->save();
        $this->assertEquals('SuperUpdated', $this->targetUser->first_name);
    }

    //
    // Group membership (model.relation.* guard)
    //

    protected function makeGroup(): UserGroup
    {
        return UserGroup::create([
            'name' => 'Test Group',
            'code' => 'test-group',
        ]);
    }

    public function testGroupAttachAllowsManageUsersActor(): void
    {
        $group = $this->makeGroup();

        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        $this->targetUser->groups()->add($group);
        $this->assertEquals(1, $this->targetUser->groups()->count());
    }

    public function testGroupAttachBlocksUnauthorizedUser(): void
    {
        $group = $this->makeGroup();

        $actor = new UserFixture;
        $this->actingAs($actor);

        $this->expectException(AuthorizationException::class);
        $this->targetUser->groups()->add($group);
    }

    public function testGroupDetachBlocksUnauthorizedUser(): void
    {
        $group = $this->makeGroup();
        $this->targetUser->groups()->add($group);

        $actor = new UserFixture;
        $this->actingAs($actor);

        $this->expectException(AuthorizationException::class);
        $this->targetUser->groups()->remove($group);
    }

    public function testGroupSyncAssignmentBlocksUnauthorizedUser(): void
    {
        $group = $this->makeGroup();

        $actor = new UserFixture;
        $this->actingAs($actor);

        // Assigning a relation value queues a sync that runs during save without
        // dirtying any attributes — the relation guard must still catch it
        $this->targetUser->groups = [$group->id];

        $this->expectException(AuthorizationException::class);
        $this->targetUser->save();
    }

    public function testSelfGroupChangeAllowed(): void
    {
        $group = $this->makeGroup();

        $this->actingAs($this->targetUser);

        // Groups carry no permissions out of the box, so changing your own
        // membership is not an escalation vector and needs no permission
        $this->targetUser->groups()->add($group);
        $this->assertEquals(1, $this->targetUser->groups()->count());
    }

    //
    // beforeDelete()
    //

    public function testBeforeDeleteAllowsCliContext(): void
    {
        BackendAuth::logout();
        $this->targetUser->delete();
        $this->assertTrue($this->targetUser->trashed());
    }

    public function testBeforeDeleteBlocksUnauthorizedUser(): void
    {
        $actor = new UserFixture;
        $this->actingAs($actor);

        $this->expectException(AuthorizationException::class);
        $this->targetUser->delete();
    }

    public function testBeforeDeleteAllowsManageUsersActor(): void
    {
        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        $this->targetUser->delete();
        $this->assertTrue($this->targetUser->trashed());
    }

    public function testBeforeDeleteBlocksNonSuperuserDeletingSuperuser(): void
    {
        $this->targetUser->newQuery()->where('id', $this->targetUser->id)->update(['is_superuser' => true]);
        $this->targetUser->refresh();

        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        $this->expectException(AuthorizationException::class);
        $this->targetUser->delete();
    }

    public function testBeforeDeleteSuperuserCanDeleteAnyone(): void
    {
        $actor = (new UserFixture)->asSuperUser();
        $this->actingAs($actor);

        $this->targetUser->delete();
        $this->assertTrue($this->targetUser->trashed());
    }

    //
    // beforeRestore()
    //

    public function testBeforeRestoreAllowsCliContext(): void
    {
        BackendAuth::logout();
        $this->targetUser->delete();
        $this->targetUser->restore();
        $this->assertFalse($this->targetUser->trashed());
    }

    public function testBeforeRestoreBlocksUnauthorizedUser(): void
    {
        BackendAuth::logout();
        $this->targetUser->delete();

        $actor = new UserFixture;
        $this->actingAs($actor);

        $this->expectException(AuthorizationException::class);
        $this->targetUser->restore();
    }

    public function testBeforeRestoreAllowsManageUsersActor(): void
    {
        BackendAuth::logout();
        $this->targetUser->delete();

        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        $this->targetUser->restore();
        $this->assertFalse($this->targetUser->trashed());
    }

    public function testBeforeRestoreBlocksNonSuperuserRestoringSuperuser(): void
    {
        $this->targetUser->newQuery()->where('id', $this->targetUser->id)->update(['is_superuser' => true]);
        $this->targetUser->refresh();
        BackendAuth::logout();
        $this->targetUser->delete();

        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        $this->expectException(AuthorizationException::class);
        $this->targetUser->restore();
    }

    public function testBeforeRestoreSuperuserCanRestoreAnyone(): void
    {
        BackendAuth::logout();
        $this->targetUser->delete();

        $actor = (new UserFixture)->asSuperUser();
        $this->actingAs($actor);

        $this->targetUser->restore();
        $this->assertFalse($this->targetUser->trashed());
    }

    //
    // unsuspend()
    //

    public function testUnsuspendBlocksUnauthorizedUser(): void
    {
        $actor = new UserFixture;
        $this->actingAs($actor);

        $this->expectException(AuthorizationException::class);
        $this->targetUser->unsuspend();
    }

    public function testUnsuspendAllowsManageUsersActor(): void
    {
        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        // Should not throw — the throttle record may not exist but that's fine
        // for testing the authorization gate
        try {
            $this->targetUser->unsuspend();
        } catch (AuthorizationException $e) {
            $this->fail('Should not throw AuthorizationException for manage_users actor');
        }
        $this->assertTrue(true);
    }

    public function testUnsuspendAllowsCliContext(): void
    {
        BackendAuth::logout();

        try {
            $this->targetUser->unsuspend();
        } catch (AuthorizationException $e) {
            $this->fail('Should not throw AuthorizationException in CLI context');
        }
        $this->assertTrue(true);
    }

    //
    // getResetPasswordCode()
    //

    public function testGetResetPasswordCodeAllowsSelfService(): void
    {
        $this->actingAs($this->targetUser);
        $code = $this->targetUser->getResetPasswordCode();
        $this->assertNotEmpty($code);
    }

    public function testGetResetPasswordCodeBlocksUnauthorizedUserForOthers(): void
    {
        $actor = new UserFixture;
        $this->actingAs($actor);

        $this->expectException(AuthorizationException::class);
        $this->targetUser->getResetPasswordCode();
    }

    public function testGetResetPasswordCodeAllowsManageUsersActorForOthers(): void
    {
        $actor = (new UserFixture)->withPermission('backend.manage_users', true);
        $this->actingAs($actor);

        $code = $this->targetUser->getResetPasswordCode();
        $this->assertNotEmpty($code);
    }

    public function testGetResetPasswordCodeAllowsCliContext(): void
    {
        BackendAuth::logout();
        $code = $this->targetUser->getResetPasswordCode();
        $this->assertNotEmpty($code);
    }
}
