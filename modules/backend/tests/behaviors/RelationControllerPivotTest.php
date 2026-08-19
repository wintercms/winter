<?php

namespace Backend\Tests\Behaviors;

use Backend\Behaviors\RelationController;
use Backend\Models\User;
use Backend\Models\UserGroup;
use Backend\Tests\Fixtures\Models\PivotRelationFixture;
use Backend\Tests\Fixtures\Models\UserFixture;
use Db;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Auth\AuthorizationException;
use Winter\Storm\Database\Model;

/**
 * Regression coverage for wintercms/winter#1464.
 *
 * `prepareModelsToSave()` always queues the related model, so a pivot form submission that
 * contains nothing but pivot data saves the related model too. For a belongsToMany relation
 * to `Backend\Models\User` that no-op save used to trip the authorization guard in
 * `User::beforeSave()`, locking operators without `backend.manage_users` out of editing
 * pivot data entirely.
 *
 * The fix moves the guard onto the events that correspond to actual writes — create/update
 * for attributes, the `model.relation.*` events for relation changes — so the pivot handlers
 * can save every prepared model unconditionally: a save with nothing to write is authorized
 * for anyone, while any real change is still guarded at the point it happens.
 */
class RelationControllerPivotTest extends PluginTestCase
{
    protected const SESSION_KEY = 'pivottestsessionkey';

    protected User $targetUser;
    protected PivotRelationFixture $fixture;
    protected RelationController $behavior;

    public function setUp(): void
    {
        parent::setUp();

        PivotRelationFixture::migrateUp();

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

        $this->fixture = PivotRelationFixture::create(['name' => 'Test Fixture']);
        $this->fixture->users()->add($this->targetUser, null, ['is_default' => false]);

        $this->behavior = (new \ReflectionClass(RelationController::class))->newInstanceWithoutConstructor();
    }

    public function tearDown(): void
    {
        PivotRelationFixture::migrateDown();

        parent::tearDown();
    }

    /**
     * Loads the related user *through the relation*, exactly as
     * `onRelationManagePivotUpdate()` does via `$this->pivotWidget->model`.
     */
    protected function getHydratedRelatedUser(): User
    {
        return $this->fixture->users()
            ->where('backend_users.id', $this->targetUser->id)
            ->firstOrFail();
    }

    /**
     * Runs the production sequence used by both pivot handlers: prepare the models
     * from the submitted data, then save each one under the pivot session key.
     */
    protected function savePivotForm(User $hydratedModel, array $saveData): void
    {
        $modelsToSave = static::callProtectedMethod(
            $this->behavior,
            'prepareModelsToSave',
            [$hydratedModel, $saveData]
        );

        foreach ($modelsToSave as $modelToSave) {
            $modelToSave->save(null, static::SESSION_KEY);
        }
    }

    protected function getPivotValue()
    {
        return Db::table('backend_test_pivot_relation_users')
            ->where('user_id', $this->targetUser->id)
            ->value('is_default');
    }

    /**
     * The related model is queued for saving even when the form only submitted pivot data.
     * This is expected — a save with nothing to write is harmless now that authorization
     * happens on the actual write events.
     */
    public function testPrepareModelsToSaveQueuesTheRelatedModelForPivotOnlyData(): void
    {
        $modelsToSave = static::callProtectedMethod(
            $this->behavior,
            'prepareModelsToSave',
            [$this->getHydratedRelatedUser(), ['pivot' => ['is_default' => true]]]
        );

        $this->assertContains(User::class, array_map('get_class', $modelsToSave));
    }

    /**
     * A pivot-only submission leaves the related model completely unchanged.
     */
    public function testPivotOnlySaveDataLeavesTheRelatedModelClean(): void
    {
        $hydrated = $this->getHydratedRelatedUser();

        static::callProtectedMethod(
            $this->behavior,
            'prepareModelsToSave',
            [$hydrated, ['pivot' => ['is_default' => true]]]
        );

        $this->assertFalse($hydrated->isDirty(), 'The related user has no changed attributes');
        $this->assertTrue($hydrated->pivot->isDirty(), 'Only the pivot changed');
    }

    /**
     * #1464: an operator without `backend.manage_users` can edit pivot data.
     */
    public function testPivotOnlyUpdateSucceedsWithoutManageUsersPermission(): void
    {
        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', false));

        $this->savePivotForm($this->getHydratedRelatedUser(), ['pivot' => ['is_default' => true]]);

        $this->assertEquals(1, $this->getPivotValue());
    }

    /**
     * The same operation keeps working for an operator who does have the permission.
     */
    public function testPivotOnlyUpdateSucceedsWithManageUsersPermission(): void
    {
        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', true));

        $this->savePivotForm($this->getHydratedRelatedUser(), ['pivot' => ['is_default' => true]]);

        $this->assertEquals(1, $this->getPivotValue());
    }

    /**
     * A pivot form may also edit fields on the related model. Those changes must still be
     * written.
     */
    public function testRelatedModelIsStillSavedWhenItsOwnFieldsAreEdited(): void
    {
        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', true));

        $this->savePivotForm($this->getHydratedRelatedUser(), [
            'first_name' => 'Renamed',
            'pivot' => ['is_default' => true],
        ]);

        $this->assertEquals('Renamed', User::find($this->targetUser->id)->first_name);
        $this->assertEquals(1, $this->getPivotValue());
    }

    /**
     * Editing the related model's own fields without the permission is still refused.
     */
    public function testRelatedModelSaveIsStillAuthorizedWhenItsOwnFieldsAreEdited(): void
    {
        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', false));

        $this->expectException(AuthorizationException::class);

        $this->savePivotForm($this->getHydratedRelatedUser(), [
            'first_name' => 'Renamed',
            'pivot' => ['is_default' => true],
        ]);
    }

    /**
     * A relation field on the related model (saved via `setSimpleValue()`, e.g. a
     * checkboxlist bound to `groups`) leaves the model's attributes clean and is applied
     * as a queued sync during the save. The unconditional save must carry it through.
     */
    public function testRelatedModelRelationFieldIsStillApplied(): void
    {
        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', true));

        $group = UserGroup::create([
            'name' => 'Test Group',
            'code' => 'test-group',
        ]);

        $this->savePivotForm($this->getHydratedRelatedUser(), [
            'groups' => [$group->id],
            'pivot' => ['is_default' => true],
        ]);

        $this->assertEquals(1, $this->targetUser->groups()->count(), 'The queued relation sync was applied');
        $this->assertEquals(1, $this->getPivotValue());
    }

    /**
     * The same queued relation sync is refused without the permission: the guard fires on
     * the relation write itself, even though the model's attributes are clean.
     */
    public function testRelatedModelRelationFieldIsStillAuthorized(): void
    {
        $group = UserGroup::create([
            'name' => 'Test Group',
            'code' => 'test-group',
        ]);

        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', false));

        $this->expectException(AuthorizationException::class);

        $this->savePivotForm($this->getHydratedRelatedUser(), [
            'groups' => [$group->id],
            'pivot' => ['is_default' => true],
        ]);
    }

    /**
     * A deferred binding leaves the owning model clean; committing it during the save must
     * still work when the operator is authorized.
     */
    public function testDeferredBindingsAreCommittedForOtherwiseCleanModels(): void
    {
        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', true));

        $hydrated = $this->getHydratedRelatedUser();

        // Bind a group to the untouched user under the pivot session key
        $group = UserGroup::create([
            'name' => 'Test Group',
            'code' => 'test-group',
        ]);
        $hydrated->groups()->add($group, static::SESSION_KEY);

        $this->assertFalse($hydrated->isDirty(), 'The deferred binding leaves the user clean');
        $this->assertEquals(0, $hydrated->groups()->count(), 'Nothing committed yet');

        $this->savePivotForm($hydrated, ['pivot' => ['is_default' => true]]);

        $this->assertEquals(1, $hydrated->groups()->count(), 'The deferred binding was committed');
        $this->assertEquals(1, $this->getPivotValue());
    }

    /**
     * Committing a deferred binding is a relation change to another user's record and must
     * still be refused without the permission, even though the owning model's attributes
     * are clean.
     */
    public function testDeferredBindingsAreStillAuthorizedForOtherwiseCleanModels(): void
    {
        $hydrated = $this->getHydratedRelatedUser();

        $group = UserGroup::create([
            'name' => 'Test Group',
            'code' => 'test-group',
        ]);
        $hydrated->groups()->add($group, static::SESSION_KEY);

        $this->assertFalse($hydrated->isDirty(), 'The deferred binding leaves the user clean');

        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', false));

        $this->expectException(AuthorizationException::class);

        $this->savePivotForm($hydrated, ['pivot' => ['is_default' => true]]);
    }
}
