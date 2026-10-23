<?php

namespace Backend\Tests\Behaviors;

use Backend\Classes\BackendController;
use Backend\Classes\Controller;
use Database\Tester\Models\Author;
use Database\Tester\Models\Country;
use Database\Tester\Models\Meta;
use Database\Tester\Models\Phone;
use Database\Tester\Models\Post;
use Database\Tester\Models\Role;
use Database\Tester\Models\Tag;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Exception\ApplicationException;

/**
 * A controller managing the relations of the parent record its page action loaded.
 */
class ScopedRelationController extends Controller
{
    public $implement = [\Backend\Behaviors\RelationController::class];

    public $requiredPermissions = [];

    public $relationConfig = [
        'posts' => [
            'label' => 'Posts',
            'view' => [
                'list' => ['columns' => ['title' => ['label' => 'Title']]],
                'toolbarButtons' => 'create|delete|add|remove',
            ],
            'manage' => [
                'list' => ['columns' => ['title' => ['label' => 'Title']]],
                'form' => ['fields' => ['title' => ['label' => 'Title']]],
            ],
        ],
        'phone' => [
            'label' => 'Phone',
            'view' => ['form' => ['fields' => ['number' => ['label' => 'Number']]]],
            'manage' => [
                'list' => ['columns' => ['number' => ['label' => 'Number']]],
                'form' => ['fields' => ['number' => ['label' => 'Number']]],
            ],
        ],
        'meta' => [
            'label' => 'Meta',
            'view' => ['form' => ['fields' => ['meta_title' => ['label' => 'Title']]]],
            'manage' => ['form' => ['fields' => ['meta_title' => ['label' => 'Title']]]],
        ],
        'roles' => [
            'label' => 'Role',
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'toolbarButtons' => 'add|remove',
            ],
            'manage' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'form' => ['fields' => ['name' => ['label' => 'Name']]],
            ],
        ],
        'tags' => [
            'label' => 'Tag',
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'toolbarButtons' => 'add|remove',
            ],
            'manage' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'form' => ['fields' => ['name' => ['label' => 'Name']]],
            ],
        ],
        'country' => [
            'label' => 'Country',
            'view' => [
                'form' => ['fields' => ['name' => ['label' => 'Name']]],
                'toolbarButtons' => 'link|unlink',
            ],
            'manage' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'form' => ['fields' => ['name' => ['label' => 'Name']]],
            ],
        ],
    ];

    /**
     * @var \Winter\Storm\Database\Model The record the page action "loaded".
     */
    protected $pageRecord;

    public function loadRecord(Model $record)
    {
        $this->pageRecord = $record;

        return $this;
    }

    /**
     * `RelationController::beforeAjax()` runs the page action and expects it to have supplied
     * the parent record, exactly as `FormController::initForm()` does on a real update page.
     * Driving the relation from here (rather than calling `initRelation()` from the test) is
     * what makes `forceManageMode` and the posted `_relation_mode` behave as they do over HTTP.
     */
    public function pageAction()
    {
        $this->initRelation(clone $this->pageRecord);
    }
}

/**
 * The same controller with the relation held in deferred mode, so records reach the manager
 * through the deferred-binding session rather than the relation's own foreign key.
 */
class DeferredScopedRelationController extends ScopedRelationController
{
    public $relationConfig = [
        'posts' => [
            'label' => 'Posts',
            'deferredBinding' => true,
            'view' => [
                'list' => ['columns' => ['title' => ['label' => 'Title']]],
                'toolbarButtons' => 'create|delete',
            ],
            'manage' => [
                'form' => ['fields' => ['title' => ['label' => 'Title']]],
            ],
        ],
    ];
}

/**
 * A tenanted controller: the records that may be added to the relation are narrowed with the
 * documented `manage.conditions` and `manage.scope` options, which is how a deployment says
 * "this parent may only be given these records".
 */
class ConstrainedRelationController extends ScopedRelationController
{
    public $relationConfig = [
        'roles' => [
            'label' => 'Role',
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'toolbarButtons' => 'add|remove',
            ],
            'manage' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'scope' => 'assignableTo',
            ],
        ],
        'tags' => [
            'label' => 'Tag',
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'toolbarButtons' => 'add|remove',
            ],
            'manage' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'conditions' => "name like 'assignable%'",
            ],
        ],
        'country' => [
            'label' => 'Country',
            'view' => [
                'form' => ['fields' => ['name' => ['label' => 'Name']]],
                'toolbarButtons' => 'link|unlink',
            ],
            'manage' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'conditions' => "name like 'assignable%'",
            ],
        ],
    ];
}

/**
 * A tenanted controller that narrows the addable records with the `relationExtendManageWidget`
 * hook instead of configuration, the other documented way of doing it.
 */
class ExtendedManageRelationController extends ScopedRelationController
{
    public function relationExtendManageWidget($widget, $field, $model)
    {
        if ($field !== 'roles') {
            return;
        }

        $widget->bindEvent('list.extendQuery', function ($query) {
            $query->where('database_tester_roles.name', 'like', 'assignable%');
        });
    }
}

/**
 * A `belongsToMany` relation carrying pivot data, so records are added through the pivot form.
 */
class PivotRelationController extends ScopedRelationController
{
    public $relationConfig = [
        'roles' => [
            'label' => 'Role',
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'toolbarButtons' => 'add|remove',
            ],
            'manage' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'conditions' => "name like 'assignable%'",
            ],
            'pivot' => [
                'form' => ['fields' => ['pivot[clearance_level]' => ['label' => 'Clearance']]],
            ],
        ],
    ];
}

/**
 * `RelationController` addresses related records by a key taken straight from the request
 * (`manage_id`, `record_id`, `foreign_id`, `checked[]`). Two invariants govern those keys:
 *
 * 1. A handler that operates on an *existing* related record (manage form, update, delete,
 *    remove) may only reach records the relation manager is currently showing -- the relation's
 *    own records, plus records bound through the active deferred-binding session.
 *
 * 2. A handler whose purpose is to attach a record that is *not* related yet (add, link, pivot
 *    create) must by definition reach outside the relation, so the relation cannot be its
 *    scope. Its scope is the manage list -- the selection list the documentation says the Add
 *    and Link buttons display -- which a deployment narrows with `manage.conditions`,
 *    `manage.scope` (which receives the parent record) or the `relationExtendManageWidget`
 *    hook.
 *
 * `ListController::index_onDelete()` and `Backend\Widgets\Lists::onReorder()` already resolve
 * their `checked[]` ids against the list's own query in the same way.
 */
class RelationControllerScopingTest extends PluginTestCase
{
    protected Author $owner;
    protected Author $operator;

    public function setUp(): void
    {
        parent::setUp();

        // Controllers read their action from this static, which another test's routed
        // request will have left set; these are driven directly, with no page action.
        BackendController::$action = null;
        BackendController::$params = [];

        Model::unguard();

        $this->operator = Author::create(['name' => 'Operator', 'email' => 'operator@test.com']);
        $this->owner = Author::create(['name' => 'Owner', 'email' => 'owner@test.com']);
    }

    public function tearDown(): void
    {
        Model::reguard();

        parent::tearDown();
    }

    protected function postData(array $data): void
    {
        request()->setMethod('POST');
        request()->request->replace($data);
    }

    protected function controller(Author $parent, string $class = ScopedRelationController::class): ScopedRelationController
    {
        return (new $class)->loadRecord($parent);
    }

    /**
     * Initialises the relation the way the page action does, for assertions about the widgets
     * themselves rather than about a handler.
     */
    protected function initialisedController(
        Author $parent,
        string $field,
        string $class = ScopedRelationController::class
    ): ScopedRelationController {
        $controller = $this->controller($parent, $class);
        $controller->initRelation(clone $parent, $field);

        return $controller;
    }

    protected function pivotWidgetOf(Controller $controller)
    {
        $behavior = $controller->getClassExtension(\Backend\Behaviors\RelationController::class);
        $property = new \ReflectionProperty($behavior, 'pivotWidget');
        $property->setAccessible(true);

        return $property->getValue($behavior);
    }

    //
    // Existing records: another parent's related rows must not be addressable
    //

    public function testManageFormCannotLoadAnUnrelatedRecord(): void
    {
        $victimPost = $this->owner->posts()->create(['title' => 'owner secret', 'slug' => 'owner-secret']);

        $this->postData(['_relation_field' => 'posts', 'manage_id' => $victimPost->id]);

        $this->expectException(ApplicationException::class);
        $this->initialisedController($this->operator, 'posts');
    }

    public function testManageFormCannotLoadAnotherParentsMorphOneRecord(): void
    {
        $victimMeta = $this->owner->meta()->create(['meta_title' => 'owner meta']);

        $this->postData(['_relation_field' => 'meta', 'manage_id' => $victimMeta->id]);

        $this->expectException(ApplicationException::class);
        $this->initialisedController($this->operator, 'meta');
    }

    public function testManageFormCannotLoadAnUnattachedBelongsToManyRecord(): void
    {
        $victimRole = Role::create(['name' => 'owner role']);
        $this->owner->roles()->add($victimRole);

        // The manage mode is normally the selection list for this relation type; the request
        // decides which manage mode is built, so the form is one post parameter away.
        $this->postData([
            '_relation_field' => 'roles',
            '_relation_mode' => 'form',
            'manage_id' => $victimRole->id,
        ]);

        $this->expectException(ApplicationException::class);
        $this->initialisedController($this->operator, 'roles');
    }

    public function testManageUpdateCannotModifyAnUnrelatedRecord(): void
    {
        $victimPost = $this->owner->posts()->create(['title' => 'original', 'slug' => 'orig']);

        $this->postData([
            '_relation_field' => 'posts',
            'manage_id' => $victimPost->id,
            'Post' => ['title' => 'overwritten'],
        ]);

        try {
            $this->controller($this->operator)->onRelationManageUpdate();
            $this->fail('An unrelated manage_id must not resolve');
        } catch (ApplicationException $ex) {
            // Expected: the record is not in this relation.
        }

        $this->assertEquals('original', Post::find($victimPost->id)->title);
    }

    public function testManageUpdateCannotModifyAnUnattachedBelongsToManyRecord(): void
    {
        $victimRole = Role::create(['name' => 'original']);
        $this->owner->roles()->add($victimRole);

        $this->postData([
            '_relation_field' => 'roles',
            '_relation_mode' => 'form',
            'manage_id' => $victimRole->id,
            'Role' => ['name' => 'overwritten'],
        ]);

        try {
            $this->controller($this->operator)->onRelationManageUpdate();
            $this->fail('An unattached manage_id must not resolve');
        } catch (ApplicationException $ex) {
            // Expected: the record is not in this relation.
        }

        $this->assertEquals('original', Role::find($victimRole->id)->name);
    }

    public function testManageUpdateCannotModifyAnUnlinkedBelongsToRecord(): void
    {
        $victimCountry = Country::create(['name' => 'original']);
        $this->owner->country = $victimCountry;
        $this->owner->save();

        $this->postData([
            '_relation_field' => 'country',
            '_relation_mode' => 'form',
            'manage_id' => $victimCountry->id,
            'Country' => ['name' => 'overwritten'],
        ]);

        try {
            $this->controller($this->operator)->onRelationManageUpdate();
            $this->fail('An unlinked manage_id must not resolve');
        } catch (ApplicationException $ex) {
            // Expected: the record is not in this relation.
        }

        $this->assertEquals('original', Country::find($victimCountry->id)->name);
    }

    public function testManageDeleteCannotDeleteAnUnrelatedRecord(): void
    {
        $victimPost = $this->owner->posts()->create(['title' => 'owner post', 'slug' => 'owner-post']);

        $this->postData(['_relation_field' => 'posts', 'checked' => [$victimPost->id]]);
        $this->controller($this->operator)->onRelationManageDelete();

        $this->assertNotNull(Post::find($victimPost->id), 'Another record\'s related row must survive');
    }

    public function testManageDeleteCannotDeleteAnUnattachedMorphToManyRecord(): void
    {
        $victimTag = Tag::create(['name' => 'owner tag']);
        $this->owner->tags()->add($victimTag);

        $this->postData(['_relation_field' => 'tags', 'checked' => [$victimTag->id]]);
        $this->controller($this->operator)->onRelationManageDelete();

        $this->assertNotNull(Tag::find($victimTag->id), 'Another parent\'s tag row must survive');
    }

    public function testManageRemoveCannotDetachAnUnrelatedHasManyRecord(): void
    {
        $victimPost = $this->owner->posts()->create(['title' => 'owner post', 'slug' => 'owner-post-2']);

        $this->postData(['_relation_field' => 'posts', 'checked' => [$victimPost->id]]);
        $this->controller($this->operator)->onRelationManageRemove();

        $this->assertEquals(
            $this->owner->id,
            Post::find($victimPost->id)->author_id,
            'Another record\'s related row must stay attached to its own parent'
        );
    }

    public function testManageRemoveCannotDetachAnUnrelatedHasOneRecord(): void
    {
        $victimPhone = $this->owner->phone()->create(['number' => '555-0100']);

        $this->postData(['_relation_field' => 'phone', 'record_id' => $victimPhone->id]);
        $this->controller($this->operator)->onRelationManageRemove();

        $this->assertEquals(
            $this->owner->id,
            Phone::find($victimPhone->id)->author_id,
            'Another record\'s hasOne row must stay attached to its own parent'
        );
    }

    public function testManageRemoveCannotDetachAnUnrelatedMorphOneRecord(): void
    {
        $victimMeta = $this->owner->meta()->create(['meta_title' => 'owner meta']);

        $this->postData(['_relation_field' => 'meta', 'record_id' => $victimMeta->id]);
        $this->controller($this->operator)->onRelationManageRemove();

        $this->assertEquals(
            $this->owner->id,
            Meta::find($victimMeta->id)->taggable_id,
            'Another record\'s morphOne row must stay attached to its own parent'
        );
    }

    //
    // Existing records, invalidation: the relation manager must still manage its own records
    //

    public function testManageFormLoadsItsOwnRelatedRecord(): void
    {
        $ownPost = $this->operator->posts()->create(['title' => 'my post', 'slug' => 'my-post']);

        $this->postData(['_relation_field' => 'posts', 'manage_id' => $ownPost->id]);
        $controller = $this->initialisedController($this->operator, 'posts');

        $this->assertEquals($ownPost->id, $controller->relationGetManageWidget()->model->id);
    }

    public function testManageFormLoadsItsOwnBelongsToManyRecord(): void
    {
        $ownRole = Role::create(['name' => 'my role']);
        $this->operator->roles()->add($ownRole);

        $this->postData([
            '_relation_field' => 'roles',
            '_relation_mode' => 'form',
            'manage_id' => $ownRole->id,
        ]);
        $controller = $this->initialisedController($this->operator, 'roles');

        $this->assertEquals($ownRole->id, $controller->relationGetManageWidget()->model->id);
    }

    public function testManageFormLoadsItsOwnMorphOneRecord(): void
    {
        $ownMeta = $this->operator->meta()->create(['meta_title' => 'my meta']);

        $this->postData(['_relation_field' => 'meta', 'manage_id' => $ownMeta->id]);
        $controller = $this->initialisedController($this->operator, 'meta');

        $this->assertEquals($ownMeta->id, $controller->relationGetManageWidget()->model->id);
    }

    public function testManageUpdateModifiesItsOwnRelatedRecord(): void
    {
        $ownPost = $this->operator->posts()->create(['title' => 'original', 'slug' => 'my-orig']);

        $this->postData([
            '_relation_field' => 'posts',
            'manage_id' => $ownPost->id,
            'Post' => ['title' => 'renamed'],
        ]);
        $this->controller($this->operator)->onRelationManageUpdate();

        $this->assertEquals('renamed', Post::find($ownPost->id)->title);
    }

    public function testManageUpdateModifiesItsOwnLinkedBelongsToRecord(): void
    {
        $ownCountry = Country::create(['name' => 'original']);
        $this->operator->country = $ownCountry;
        $this->operator->save();

        $this->postData([
            '_relation_field' => 'country',
            '_relation_mode' => 'form',
            'manage_id' => $ownCountry->id,
            'Country' => ['name' => 'renamed'],
        ]);
        $this->controller($this->operator)->onRelationManageUpdate();

        $this->assertEquals('renamed', Country::find($ownCountry->id)->name);
    }

    public function testManageDeleteDeletesItsOwnRelatedRecords(): void
    {
        $first = $this->operator->posts()->create(['title' => 'a', 'slug' => 'a']);
        $second = $this->operator->posts()->create(['title' => 'b', 'slug' => 'b']);

        $this->postData(['_relation_field' => 'posts', 'checked' => [$first->id, $second->id]]);
        $this->controller($this->operator)->onRelationManageDelete();

        $this->assertNull(Post::find($first->id));
        $this->assertNull(Post::find($second->id));
    }

    public function testManageDeleteDeletesItsOwnMorphToManyRecord(): void
    {
        $ownTag = Tag::create(['name' => 'my tag']);
        $this->operator->tags()->add($ownTag);

        $this->postData(['_relation_field' => 'tags', 'checked' => [$ownTag->id]]);
        $this->controller($this->operator)->onRelationManageDelete();

        $this->assertNull(Tag::find($ownTag->id));
    }

    public function testManageRemoveDetachesItsOwnRelatedRecord(): void
    {
        $ownPost = $this->operator->posts()->create(['title' => 'a', 'slug' => 'detach-me']);

        $this->postData(['_relation_field' => 'posts', 'checked' => [$ownPost->id]]);
        $this->controller($this->operator)->onRelationManageRemove();

        $this->assertNull(Post::find($ownPost->id)->author_id);
    }

    public function testManageRemoveDetachesItsOwnBelongsToManyRecord(): void
    {
        $ownRole = Role::create(['name' => 'my role']);
        $this->operator->roles()->add($ownRole);

        $this->postData(['_relation_field' => 'roles', 'checked' => [$ownRole->id]]);
        $this->controller($this->operator)->onRelationManageRemove();

        $this->assertCount(0, $this->operator->roles()->get());
        $this->assertNotNull(Role::find($ownRole->id), 'Remove detaches, it does not destroy');
    }

    public function testManageRemoveDetachesItsOwnMorphToManyRecord(): void
    {
        $ownTag = Tag::create(['name' => 'my tag']);
        $this->operator->tags()->add($ownTag);

        $this->postData(['_relation_field' => 'tags', 'checked' => [$ownTag->id]]);
        $this->controller($this->operator)->onRelationManageRemove();

        $this->assertCount(0, $this->operator->tags()->get());
    }

    public function testManageRemoveDetachesItsOwnHasOneRecord(): void
    {
        $ownPhone = $this->operator->phone()->create(['number' => '555-0199']);

        $this->postData(['_relation_field' => 'phone', 'record_id' => $ownPhone->id]);
        $this->controller($this->operator)->onRelationManageRemove();

        $this->assertNull(Phone::find($ownPhone->id)->author_id);
    }

    /**
     * The case a relation-only scope would break: while deferred, a record reaches the manager
     * through `deferred_bindings` and not through the relation's foreign key at all.
     */
    public function testDeferredBoundRecordsAreStillAddressable(): void
    {
        $sessionKey = 'relationscopingdeferred';

        $post = Post::create(['title' => 'deferred', 'slug' => 'deferred']);
        $this->operator->posts()->add($post, $sessionKey);

        $this->postData([
            '_relation_field' => 'posts',
            '_relation_session_key' => $sessionKey,
            'manage_id' => $post->id,
            'Post' => ['title' => 'deferred renamed'],
        ]);

        $controller = $this->initialisedController(
            $this->operator,
            'posts',
            DeferredScopedRelationController::class
        );

        $this->assertEquals($post->id, $controller->relationGetManageWidget()->model->id);

        $controller->onRelationManageUpdate();
        $this->assertEquals('deferred renamed', Post::find($post->id)->title);
    }

    /**
     * And a record bound to somebody else's deferred session is still out of reach.
     */
    public function testAnotherSessionsDeferredRecordIsNotAddressable(): void
    {
        $post = Post::create(['title' => 'theirs', 'slug' => 'theirs-deferred']);
        $this->owner->posts()->add($post, 'someoneelsessessionkey');

        $this->postData([
            '_relation_field' => 'posts',
            '_relation_session_key' => 'relationscopingdeferred',
            'manage_id' => $post->id,
        ]);

        $this->expectException(ApplicationException::class);
        $this->initialisedController($this->operator, 'posts', DeferredScopedRelationController::class);
    }

    //
    // The pivot path, which already resolved `manage_id` through the relation
    //

    public function testPivotFormCannotLoadAnUnattachedRecord(): void
    {
        $victimRole = Role::create(['name' => 'assignable owner role']);
        $this->owner->roles()->add($victimRole);

        $this->postData(['_relation_field' => 'roles', 'manage_id' => $victimRole->id]);

        $this->expectException(ApplicationException::class);
        $this->initialisedController($this->operator, 'roles', PivotRelationController::class);
    }

    public function testPivotFormLoadsItsOwnAttachedRecord(): void
    {
        $ownRole = Role::create(['name' => 'assignable my role']);
        $this->operator->roles()->add($ownRole);

        $this->postData(['_relation_field' => 'roles', 'manage_id' => $ownRole->id]);
        $controller = $this->initialisedController($this->operator, 'roles', PivotRelationController::class);

        $this->assertEquals($ownRole->id, $this->pivotWidgetOf($controller)->model->id);
    }

    //
    // Adding an existing record: the manage list is the addable set
    //

    public function testManageAddAttachesAnExistingBelongsToManyRecord(): void
    {
        $role = Role::create(['name' => 'free agent']);

        $this->postData(['_relation_field' => 'roles', '_relation_mode' => 'list', 'checked' => [$role->id]]);
        $this->controller($this->operator)->onRelationManageAdd();

        $this->assertEquals([$role->id], $this->operator->roles()->pluck('database_tester_roles.id')->all());
    }

    public function testManageAddAttachesAnExistingMorphToManyRecord(): void
    {
        $tag = Tag::create(['name' => 'free agent']);

        $this->postData(['_relation_field' => 'tags', '_relation_mode' => 'list', 'checked' => [$tag->id]]);
        $this->controller($this->operator)->onRelationManageAdd();

        $this->assertEquals([$tag->id], $this->operator->tags()->pluck('database_tester_tags.id')->all());
    }

    /**
     * Adding an existing `hasMany` record moves it from whichever parent held it: that is what
     * the Add button on a `hasMany` relation is documented to do, so it must keep working.
     */
    public function testManageAddReassignsAnExistingHasManyRecord(): void
    {
        $post = $this->owner->posts()->create(['title' => 'transferable', 'slug' => 'transferable']);

        $this->postData(['_relation_field' => 'posts', '_relation_mode' => 'list', 'checked' => [$post->id]]);
        $this->controller($this->operator)->onRelationManageAdd();

        $this->assertEquals($this->operator->id, Post::find($post->id)->author_id);
    }

    public function testManageAddLinksAnExistingBelongsToRecord(): void
    {
        $country = Country::create(['name' => 'free agent']);

        $this->postData([
            '_relation_field' => 'country',
            '_relation_mode' => 'list',
            'record_id' => $country->id,
        ]);
        $this->controller($this->operator)->onRelationManageAdd();

        $this->assertEquals($country->id, Author::find($this->operator->id)->country_id);
    }

    public function testManageAddLinksAnExistingHasOneRecord(): void
    {
        $phone = Phone::create(['number' => '555-0123']);

        $this->postData([
            '_relation_field' => 'phone',
            '_relation_mode' => 'list',
            'record_id' => $phone->id,
        ]);
        $this->controller($this->operator)->onRelationManageAdd();

        $this->assertEquals($this->operator->id, Phone::find($phone->id)->author_id);
    }

    public function testManageAddRefusesARecordOutsideTheManageListConditions(): void
    {
        $addable = Tag::create(['name' => 'assignable tag']);
        $offLimits = Tag::create(['name' => 'privileged tag']);

        $this->postData([
            '_relation_field' => 'tags',
            '_relation_mode' => 'list',
            'checked' => [$addable->id, $offLimits->id],
        ]);
        $this->controller($this->operator, ConstrainedRelationController::class)->onRelationManageAdd();

        $this->assertEquals(
            [$addable->id],
            $this->operator->tags()->pluck('database_tester_tags.id')->all(),
            'Only the records the manage list offers may be added'
        );
    }

    public function testManageAddRefusesARecordOutsideTheManageListScope(): void
    {
        $addable = Role::create(['name' => 'ours', 'description' => 'tenant-' . $this->operator->id]);
        $offLimits = Role::create(['name' => 'theirs', 'description' => 'tenant-' . $this->owner->id]);

        $this->postData([
            '_relation_field' => 'roles',
            '_relation_mode' => 'list',
            'checked' => [$addable->id, $offLimits->id],
        ]);
        $this->controller($this->operator, ConstrainedRelationController::class)->onRelationManageAdd();

        $this->assertEquals(
            [$addable->id],
            $this->operator->roles()->pluck('database_tester_roles.id')->all(),
            'A manage.scope receives the parent record and must bound what that parent may add'
        );
    }

    public function testManageAddRefusesARecordTheManageWidgetExtensionExcluded(): void
    {
        $addable = Role::create(['name' => 'assignable role']);
        $offLimits = Role::create(['name' => 'privileged role']);

        $this->postData([
            '_relation_field' => 'roles',
            '_relation_mode' => 'list',
            'checked' => [$addable->id, $offLimits->id],
        ]);
        $this->controller($this->operator, ExtendedManageRelationController::class)->onRelationManageAdd();

        $this->assertEquals(
            [$addable->id],
            $this->operator->roles()->pluck('database_tester_roles.id')->all(),
            'relationExtendManageWidget() must bound what may be added, not just what is shown'
        );
    }

    public function testManageAddLinkRefusesARecordOutsideTheManageList(): void
    {
        $offLimits = Country::create(['name' => 'privileged country']);

        $this->postData([
            '_relation_field' => 'country',
            '_relation_mode' => 'list',
            'record_id' => $offLimits->id,
        ]);
        $this->controller($this->operator, ConstrainedRelationController::class)->onRelationManageAdd();

        $this->assertNull(Author::find($this->operator->id)->country_id);
    }

    public function testManageAddLinkAcceptsARecordInsideTheManageList(): void
    {
        $addable = Country::create(['name' => 'assignable country']);

        $this->postData([
            '_relation_field' => 'country',
            '_relation_mode' => 'list',
            'record_id' => $addable->id,
        ]);
        $this->controller($this->operator, ConstrainedRelationController::class)->onRelationManageAdd();

        $this->assertEquals($addable->id, Author::find($this->operator->id)->country_id);
    }

    /**
     * The manage mode is posted by the browser, so it cannot decide whether the addable set is
     * applied: the handler must pick the selection list itself.
     */
    public function testManageAddIgnoresARequestSuppliedManageMode(): void
    {
        $offLimits = Role::create(['name' => 'privileged role']);

        $this->postData([
            '_relation_field' => 'roles',
            '_relation_mode' => 'form',
            'checked' => [$offLimits->id],
        ]);
        $this->controller($this->operator, ConstrainedRelationController::class)->onRelationManageAdd();

        $this->assertCount(0, $this->operator->roles()->get());
    }

    public function testPivotCreateAttachesARecordInsideTheManageList(): void
    {
        $addable = Role::create(['name' => 'assignable role']);

        $this->postData([
            '_relation_field' => 'roles',
            'foreign_id' => [$addable->id],
            'Role' => ['pivot' => ['clearance_level' => 'high']],
        ]);
        $this->controller($this->operator, PivotRelationController::class)->onRelationManagePivotCreate();

        $this->assertEquals([$addable->id], $this->operator->roles()->pluck('database_tester_roles.id')->all());
    }

    public function testPivotCreateRefusesARecordOutsideTheManageList(): void
    {
        $offLimits = Role::create(['name' => 'privileged role']);

        $this->postData([
            '_relation_field' => 'roles',
            'foreign_id' => [$offLimits->id],
            'Role' => ['pivot' => ['clearance_level' => 'high']],
        ]);

        $controller = $this->controller($this->operator, PivotRelationController::class);

        try {
            $controller->onRelationManagePivotCreate();
            $this->fail('The handler accepted a record outside the selection list');
        } catch (ApplicationException $ex) {
            $this->assertStringContainsString((string) $offLimits->id, $ex->getMessage());
        }

        $this->assertCount(0, $this->operator->roles()->get());
    }

    public function testPivotFormCannotPreloadARecordOutsideTheManageList(): void
    {
        $offLimits = Role::create(['name' => 'privileged role']);

        $this->postData(['_relation_field' => 'roles', 'foreign_id' => $offLimits->id]);

        // Reported the way the manage branch reports an id it cannot resolve, rather than
        // quietly handing the form the bare relation model. The widget is built while the
        // relation initialises, so the expectation is registered before that.
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage((string) $offLimits->id);

        $this->pivotWidgetOf(
            $this->initialisedController($this->operator, 'roles', PivotRelationController::class)
        );
    }

    public function testPivotFormPreloadsARecordInsideTheManageList(): void
    {
        $addable = Role::create(['name' => 'assignable role']);

        $this->postData(['_relation_field' => 'roles', 'foreign_id' => $addable->id]);
        $controller = $this->initialisedController($this->operator, 'roles', PivotRelationController::class);

        $this->assertEquals('assignable role', $this->pivotWidgetOf($controller)->model->name);
    }
}
