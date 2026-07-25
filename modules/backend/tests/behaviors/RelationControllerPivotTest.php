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
 * contains nothing but pivot data used to save the related model too. For a belongsToMany
 * relation to `Backend\Models\User` that no-op save tripped `User::beforeSave()`, locking
 * operators without `backend.manage_users` out of editing pivot data entirely.
 */
class RelationControllerPivotTest extends PluginTestCase
{
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

        $this->behavior = $this->makeBehavior();
    }

    public function tearDown(): void
    {
        PivotRelationFixture::migrateDown();

        parent::tearDown();
    }

    /**
     * Builds the behavior without its controller, then supplies the only collaborator
     * `relationSavePivotModels()` uses: the pivot widget's session key.
     */
    protected function makeBehavior(): RelationController
    {
        $behavior = (new \ReflectionClass(RelationController::class))->newInstanceWithoutConstructor();

        static::setProtectedProperty($behavior, 'pivotWidget', new class {
            public function getSessionKey(): string
            {
                return 'pivottestsessionkey';
            }
        });

        return $behavior;
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
     * Runs the production sequence used by both pivot handlers.
     */
    protected function savePivotForm(User $hydratedModel, array $saveData): void
    {
        $modelsToSave = static::callProtectedMethod(
            $this->behavior,
            'prepareModelsToSave',
            [$hydratedModel, $saveData]
        );

        static::callProtectedMethod($this->behavior, 'relationSavePivotModels', [$modelsToSave]);
    }

    protected function getPivotValue()
    {
        return Db::table('backend_test_pivot_relation_users')
            ->where('user_id', $this->targetUser->id)
            ->value('is_default');
    }

    /**
     * The related model is queued for saving even when the form only submitted pivot data.
     * This is the underlying cause and is expected — the fix is in what gets saved, not
     * in what gets queued.
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
     * written — the fix only skips models that nothing changed.
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
     * The fix must not become an authorization bypass: editing the related model's own
     * fields without the permission is still refused.
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
     * Skipping the save must not drop pending relation work. A deferred binding leaves the
     * owning model clean, so it would be lost if the model were simply passed over.
     */
    public function testDeferredBindingsAreCommittedForOtherwiseCleanModels(): void
    {
        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', true));

        $hydrated = $this->getHydratedRelatedUser();

        // Bind a group to the untouched user under the pivot widget's session key
        $group = UserGroup::create([
            'name' => 'Test Group',
            'code' => 'test-group',
        ]);
        $hydrated->groups()->add($group, 'pivottestsessionkey');

        $this->assertFalse($hydrated->isDirty(), 'The deferred binding leaves the user clean');
        $this->assertEquals(0, $hydrated->groups()->count(), 'Nothing committed yet');

        $this->savePivotForm($hydrated, ['pivot' => ['is_default' => true]]);

        $this->assertEquals(1, $hydrated->groups()->count(), 'The deferred binding was committed');
        $this->assertEquals(1, $this->getPivotValue());
    }

    /**
     * Skipping the save must not become an authorization bypass. A deferred binding leaves the
     * owning model clean, so a naive skip would commit relation changes - such as adding a user
     * to a group, which carries permissions - without ever consulting `User::beforeSave()`.
     */
    public function testDeferredBindingsAreStillAuthorizedForOtherwiseCleanModels(): void
    {
        $this->actingAs((new UserFixture)->withPermission('backend.manage_users', false));

        $hydrated = $this->getHydratedRelatedUser();

        $group = UserGroup::create([
            'name' => 'Test Group',
            'code' => 'test-group',
        ]);
        $hydrated->groups()->add($group, 'pivottestsessionkey');

        $this->assertFalse($hydrated->isDirty(), 'The deferred binding leaves the user clean');

        $this->expectException(AuthorizationException::class);

        $this->savePivotForm($hydrated, ['pivot' => ['is_default' => true]]);
    }
}
