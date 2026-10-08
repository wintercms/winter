<?php

namespace Backend\Tests\Behaviors;

use Backend\Classes\BackendController;
use Backend\Classes\Controller;
use Backend\Models\User as BackendUser;
use Backend\Tests\Fixtures\Models\RelationBehaviorFixture;
use Database\Tester\Models\Author;
use Database\Tester\Models\Post;
use Database\Tester\Models\Tag;
use Db;
use Symfony\Component\HttpKernel\Exception\HttpException;
use System\Models\File;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Exception\SystemException;

/**
 * A controller whose relations are declared read only, which the documentation describes as
 * disabling "the ability to add, update, delete or create relations".
 */
class ReadOnlyRelationController extends Controller
{
    public $implement = [\Backend\Behaviors\RelationController::class];

    public $requiredPermissions = [];

    public $relationConfig = [
        'posts' => [
            'label' => 'Posts',
            'readOnly' => true,
            'view' => [
                'list' => ['columns' => ['title' => ['label' => 'Title']]],
                'toolbarButtons' => 'create|delete|add|remove',
            ],
            'manage' => [
                'form' => ['fields' => ['title' => ['label' => 'Title']]],
                'list' => ['columns' => ['title' => ['label' => 'Title']]],
            ],
        ],
        'tags' => [
            'label' => 'Tags',
            'readOnly' => true,
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
            ],
            'manage' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
            ],
            'pivot' => [
                'form' => [
                    'fields' => [
                        'pivot[added_by]' => ['label' => 'Added by'],
                    ],
                ],
            ],
        ],
    ];

    /**
     * The model a real page action binds to the relation. beforeAjax() calls pageAction()
     * before anything else, so a handler that sets forceManageMode in its own body has already
     * set it by the time initRelation() caches the manage mode. Priming from the test instead
     * would reverse that order and exercise a state no request produces.
     */
    public $primeModel;

    public function pageAction()
    {
        if ($this->primeModel) {
            $this->initRelation($this->primeModel, post('_relation_field'));
        }
    }
}

/**
 * The same relations with the read only flag removed, used to prove the enforcement does not
 * break a relation that is supposed to be writable.
 */
class WritableRelationController extends ReadOnlyRelationController
{
    public function __construct()
    {
        $this->relationConfig = array_map(function ($config) {
            unset($config['readOnly']);

            return $config;
        }, $this->relationConfig);

        parent::__construct();
    }
}

/**
 * Drag-and-drop reordering of a read only relation, which writes the relation's sort order
 * through `Backend\Widgets\Lists::onReorder()` rather than through a relation handler.
 */
class ReadOnlySortableRelationController extends Controller
{
    public $implement = [\Backend\Behaviors\RelationController::class];

    public $requiredPermissions = [];

    public $relationConfig = [
        'children' => [
            'label' => 'Children',
            'readOnly' => true,
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
                'sortable' => true,
            ],
            'manage' => [
                'form' => ['fields' => ['name' => ['label' => 'Name']]],
            ],
        ],
    ];
}

class WritableSortableRelationController extends ReadOnlySortableRelationController
{
    public function __construct()
    {
        unset($this->relationConfig['children']['readOnly']);

        parent::__construct();
    }
}

/**
 * A read only relation whose manage form carries a nested form widget (`fileupload`) with
 * mutating AJAX handlers of its own, plus the same field on the single-record view form.
 */
class ReadOnlyNestedWidgetRelationController extends Controller
{
    public $implement = [\Backend\Behaviors\RelationController::class];

    public $requiredPermissions = [];

    public $relationConfig = [
        'children' => [
            'label' => 'Children',
            'readOnly' => true,
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
            ],
            'manage' => [
                'form' => [
                    'tabs' => [
                        'fields' => [
                            'name' => ['label' => 'Name', 'tab' => 'Details'],
                            'thumb' => [
                                'label' => 'Thumb',
                                'tab' => 'Details',
                                'type' => \Backend\FormWidgets\FileUpload::class,
                            ],
                            'notes' => [
                                'label' => 'Notes',
                                'tab' => 'Details',
                                'type' => \Backend\FormWidgets\MarkdownEditor::class,
                            ],
                            'points' => [
                                'label' => 'Points',
                                'tab' => 'Details',
                                'type' => \Backend\FormWidgets\Repeater::class,
                                'form' => ['fields' => ['point' => ['label' => 'Point']]],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];
}

class WritableNestedWidgetRelationController extends ReadOnlyNestedWidgetRelationController
{
    public function __construct()
    {
        unset($this->relationConfig['children']['readOnly']);

        parent::__construct();
    }
}

/**
 * A read only single-record relation, whose *view* form is a preview form that also binds its
 * nested widgets to the controller.
 */
class ReadOnlySingleRelationController extends Controller
{
    public $implement = [\Backend\Behaviors\RelationController::class];

    public $requiredPermissions = [];

    public $relationConfig = [
        'primaryChild' => [
            'label' => 'Primary child',
            'readOnly' => true,
            'view' => [
                'form' => [
                    'fields' => [
                        'name' => ['label' => 'Name'],
                        'thumb' => ['label' => 'Thumb', 'type' => \Backend\FormWidgets\FileUpload::class],
                    ],
                ],
            ],
            'manage' => [
                'form' => [
                    'fields' => [
                        'name' => ['label' => 'Name'],
                        'thumb' => ['label' => 'Thumb', 'type' => \Backend\FormWidgets\FileUpload::class],
                    ],
                ],
            ],
        ],
    ];
}

class WritableSingleRelationController extends ReadOnlySingleRelationController
{
    public function __construct()
    {
        unset($this->relationConfig['primaryChild']['readOnly']);

        parent::__construct();
    }
}

/**
 * The supported way to make a relation read only for a role: the behavior calls
 * relationExtendConfig() on every request, AJAX included, so the flag is recomputed per user.
 */
class ExtendedConfigRelationController extends WritableRelationController
{
    public bool $denyWrites = true;

    public function relationExtendConfig($config, $field, $model)
    {
        $config->readOnly = $this->denyWrites;
    }
}

/**
 * `RelationController` documents `readOnly` as disabling the ability to add, update, delete or
 * create relations, and the framework enforces the identically named control server side in
 * `Backend\Widgets\MediaManager::abortIfReadOnly()`. In the relation behavior the flag was only
 * ever consulted while rendering (toolbar buttons, list checkboxes, the manage popup title and
 * preview mode), so every relation write handler reached its mutating sink regardless.
 *
 * On top of that, the flag round trips through the browser in `_relation_extra_config`, an
 * unsigned base64 JSON blob that `applyExtraConfig()` merged straight back into the relation
 * configuration, so the value the client returns had the last word. Enforcing the flag at the
 * handlers alone would therefore not have been enough: the merge has to be monotonic too.
 *
 * The invariant: when the relation configuration says the relation is read only, no relation
 * management handler may write, and nothing the client sends may relax that.
 */
class RelationControllerReadOnlyTest extends PluginTestCase
{
    protected Author $author;

    public function setUp(): void
    {
        parent::setUp();

        // Controllers read their action from this static, which another test's routed
        // request will have left set; these are driven directly, with no page action.
        BackendController::$action = null;
        BackendController::$params = [];

        Model::unguard();

        // MarkdownEditor (and other widgets) read the authenticated backend user while rendering.
        $this->actingAs(BackendUser::create([
            'first_name' => 'Read',
            'last_name' => 'Only',
            'login' => 'readonlytester',
            'email' => 'readonly@test.com',
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'is_superuser' => true,
        ]), 'backend');

        $this->author = Author::create(['name' => 'Author', 'email' => 'author@test.com']);
    }

    public function tearDown(): void
    {
        RelationBehaviorFixture::migrateDown();
        Model::reguard();

        parent::tearDown();
    }

    protected function postData(array $data): void
    {
        request()->setMethod('POST');
        request()->request->replace($data);
    }

    /*
     * Most tests here read relation state off the controller, so the relation is primed by
     * default. Pass false for a handler that sets forceManageMode in its own body: that runs
     * before beforeAjax() reaches initRelation(), so priming first would fix the manage mode
     * ahead of the handler and test a state no real request produces.
     */
    protected function controller(string $field = 'posts'): ReadOnlyRelationController
    {
        $controller = new ReadOnlyRelationController;
        $controller->primeModel = $this->author;
        $controller->initRelation($this->author, $field);

        return $controller;
    }

    protected function writableController(string $field = 'posts'): WritableRelationController
    {
        $controller = new WritableRelationController;
        $controller->primeModel = $this->author;
        $controller->initRelation($this->author, $field);

        return $controller;
    }

    /**
     * Runs the handler and asserts that it was refused with a 403, the same response
     * `MediaManager::abortIfReadOnly()` produces.
     */
    protected function assertForbidden(Controller $controller, string $handler): void
    {
        try {
            $controller->$handler();
        } catch (HttpException $ex) {
            $this->assertEquals(403, $ex->getStatusCode(), $handler . ' must be refused with a 403');

            return;
        }

        $this->fail($handler . ' must not run against a read only relation');
    }

    protected function pivotValue(Tag $tag): ?string
    {
        return Db::table('database_tester_taggables')
            ->where('tag_id', $tag->id)
            ->where('taggable_id', $this->author->id)
            ->where('taggable_type', Author::class)
            ->value('added_by');
    }

    //
    // Blocked: a read only relation must reject every write
    //

    public function testManageCreateIsRefused(): void
    {
        $this->postData([
            '_relation_field' => 'posts',
            'Post' => ['title' => 'created', 'slug' => 'created'],
        ]);

        $this->assertForbidden($this->controller(), 'onRelationManageCreate');
        $this->assertEquals(0, Post::count(), 'No related record may be created');
    }

    public function testManageUpdateIsRefused(): void
    {
        $post = $this->author->posts()->create(['title' => 'original', 'slug' => 'original']);

        $this->postData([
            '_relation_field' => 'posts',
            'manage_id' => $post->id,
            'Post' => ['title' => 'overwritten'],
        ]);

        $this->assertForbidden($this->controller(), 'onRelationManageUpdate');
        $this->assertEquals('original', Post::find($post->id)->title);
    }

    public function testManageDeleteIsRefused(): void
    {
        $post = $this->author->posts()->create(['title' => 'keep me', 'slug' => 'keep-me']);

        $this->postData(['_relation_field' => 'posts', 'checked' => [$post->id]]);

        $this->assertForbidden($this->controller(), 'onRelationManageDelete');
        $this->assertNotNull(Post::find($post->id), 'The related record must survive');
    }

    public function testManageAddIsRefused(): void
    {
        $post = Post::create(['title' => 'unrelated', 'slug' => 'unrelated']);

        $this->postData(['_relation_field' => 'posts', 'record_id' => $post->id]);

        $this->assertForbidden($this->controller(), 'onRelationManageAdd');
        $this->assertNull(Post::find($post->id)->author_id, 'The record must not be attached');
    }

    public function testManageRemoveIsRefused(): void
    {
        $post = $this->author->posts()->create(['title' => 'attached', 'slug' => 'attached']);

        $this->postData(['_relation_field' => 'posts', 'checked' => [$post->id]]);

        $this->assertForbidden($this->controller(), 'onRelationManageRemove');
        $this->assertEquals($this->author->id, Post::find($post->id)->author_id);
    }

    public function testManagePivotCreateIsRefused(): void
    {
        $tag = Tag::create(['name' => 'tag']);

        $this->postData([
            '_relation_field' => 'tags',
            '_relation_mode' => 'pivot',
            'foreign_id' => $tag->id,
            'Tag' => ['pivot' => ['added_by' => 'somebody else']],
        ]);

        $this->assertForbidden($this->controller('tags'), 'onRelationManagePivotCreate');
        $this->assertNull($this->pivotValue($tag), 'The pivot row must not be created');
    }

    public function testManagePivotUpdateIsRefused(): void
    {
        $tag = Tag::create(['name' => 'tag']);
        $this->author->tags()->add($tag, null, ['added_by' => 'owner']);

        $this->postData([
            '_relation_field' => 'tags',
            '_relation_mode' => 'pivot',
            'manage_id' => $tag->id,
            'Tag' => ['pivot' => ['added_by' => 'somebody else']],
        ]);

        $this->assertForbidden($this->controller('tags'), 'onRelationManagePivotUpdate');
        $this->assertEquals('owner', $this->pivotValue($tag));
    }

    /**
     * The button and list handlers are thin aliases over the management handlers, so the
     * enforcement has to hold when a write is reached through them too.
     */
    public function testAliasHandlersAreRefused(): void
    {
        $post = $this->author->posts()->create(['title' => 'keep me', 'slug' => 'alias']);

        foreach (['onRelationButtonDelete', 'onRelationButtonRemove', 'onRelationButtonUnlink'] as $handler) {
            $this->postData(['_relation_field' => 'posts', 'checked' => [$post->id]]);
            $this->assertForbidden($this->controller(), $handler);
        }

        $unrelated = Post::create(['title' => 'unrelated', 'slug' => 'alias-unrelated']);
        $this->postData(['_relation_field' => 'posts', 'record_id' => $unrelated->id]);
        $this->assertForbidden($this->controller(), 'onRelationClickManageList');

        $this->assertEquals($this->author->id, Post::find($post->id)->author_id);
        $this->assertNull(Post::find($unrelated->id)->author_id);
    }

    /**
     * `_relation_extra_config` is rendered into the page by `relationRender()` and returned by
     * the browser with every relation AJAX request. It is unsigned, so a client can rewrite it:
     * enforcing `readOnly` at the handlers is worthless unless the client is also unable to
     * switch it off from the request.
     */
    public function testExtraConfigCannotDisableReadOnly(): void
    {
        $post = $this->author->posts()->create(['title' => 'keep me', 'slug' => 'extra-config']);

        $this->postData([
            '_relation_field' => 'posts',
            '_relation_extra_config' => base64_encode(json_encode(['readOnly' => false])),
            'checked' => [$post->id],
        ]);

        $controller = $this->controller();

        $this->assertTrue(
            $controller->asExtension(\Backend\Behaviors\RelationController::class)->readOnly,
            'A client must not be able to switch readOnly off'
        );

        $this->assertForbidden($controller, 'onRelationManageDelete');
        $this->assertNotNull(Post::find($post->id));
    }

    /**
     * Every shape a client might use to spell "readOnly is off". The rule is deliberately
     * value-shaped rather than type-shaped -- anything falsy is dropped, anything truthy is a
     * restriction and is kept -- so none of these can relax the relation.
     *
     * @dataProvider extraConfigDowngradeProvider
     */
    public function testExtraConfigCannotDisableReadOnlyInAnyShape($extraConfig): void
    {
        $post = $this->author->posts()->create(['title' => 'keep me', 'slug' => 'shapes']);

        $this->postData([
            '_relation_field' => 'posts',
            '_relation_extra_config' => $extraConfig,
            'checked' => [$post->id],
        ]);

        $controller = $this->controller();

        $this->assertTrue(
            (bool) $controller->asExtension(\Backend\Behaviors\RelationController::class)->readOnly,
            'A client must not be able to switch readOnly off'
        );

        $this->assertForbidden($controller, 'onRelationManageDelete');
        $this->assertNotNull(Post::find($post->id));
    }

    public function extraConfigDowngradeProvider(): array
    {
        return [
            'encoded false'          => [base64_encode(json_encode(['readOnly' => false]))],
            'encoded zero'           => [base64_encode(json_encode(['readOnly' => 0]))],
            'encoded float zero'     => [base64_encode('{"readOnly":0.0}')],
            'encoded null'           => [base64_encode(json_encode(['readOnly' => null]))],
            'encoded empty string'   => [base64_encode(json_encode(['readOnly' => '']))],
            'encoded empty array'    => [base64_encode(json_encode(['readOnly' => []]))],
            'duplicate json keys'    => [base64_encode('{"readOnly":true,"readOnly":false}')],
            'case variant key'       => [base64_encode('{"ReadOnly":false,"READONLY":false}')],
            'json list wrapper'      => [base64_encode('[{"readOnly":false}]')],
            'request array zero'     => [['readOnly' => '0']],
            'request array empty'    => [['readOnly' => '']],
            'request nested array'   => [['readOnly' => ['nested' => '0']]],
            'request array false'    => [['readOnly' => false]],
        ];
    }

    /**
     * post() returns whatever shape the request used, so the same attempt has to be refused
     * when the extra config arrives as a request array rather than an encoded string.
     */
    public function testExtraConfigArrayCannotDisableReadOnly(): void
    {
        $post = $this->author->posts()->create(['title' => 'keep me', 'slug' => 'extra-config-array']);

        $this->postData([
            '_relation_field' => 'posts',
            '_relation_extra_config' => ['readOnly' => '0'],
            'checked' => [$post->id],
        ]);

        $controller = $this->controller();

        $this->assertTrue(
            $controller->asExtension(\Backend\Behaviors\RelationController::class)->readOnly,
            'A client must not be able to switch readOnly off'
        );

        $this->assertForbidden($controller, 'onRelationManageDelete');
        $this->assertNotNull(Post::find($post->id));
    }

    //
    // Invalidation: a writable relation must keep working, and a read only one must still render
    //

    public function testWritableRelationStillCreates(): void
    {
        $this->postData([
            '_relation_field' => 'posts',
            'Post' => ['title' => 'created', 'slug' => 'created'],
        ]);

        $this->writableController()->onRelationManageCreate();

        $this->assertEquals('created', $this->author->posts()->first()->title);
    }

    public function testWritableRelationStillUpdates(): void
    {
        $post = $this->author->posts()->create(['title' => 'original', 'slug' => 'original']);

        $this->postData([
            '_relation_field' => 'posts',
            'manage_id' => $post->id,
            'Post' => ['title' => 'renamed'],
        ]);

        $this->writableController()->onRelationManageUpdate();

        $this->assertEquals('renamed', Post::find($post->id)->title);
    }

    public function testWritableRelationStillDeletes(): void
    {
        $post = $this->author->posts()->create(['title' => 'delete me', 'slug' => 'delete-me']);

        $this->postData(['_relation_field' => 'posts', 'checked' => [$post->id]]);

        $this->writableController()->onRelationManageDelete();

        $this->assertNull(Post::find($post->id));
    }

    public function testWritableRelationStillAddsAndRemoves(): void
    {
        $post = Post::create(['title' => 'unrelated', 'slug' => 'add-me']);

        $this->postData(['_relation_field' => 'posts', 'record_id' => $post->id]);
        $this->writableController()->onRelationManageAdd();
        $this->assertEquals($this->author->id, Post::find($post->id)->author_id);

        $this->postData(['_relation_field' => 'posts', 'checked' => [$post->id]]);
        $this->writableController()->onRelationManageRemove();
        $this->assertNull(Post::find($post->id)->author_id);
    }

    public function testWritableRelationStillWritesPivotData(): void
    {
        $tag = Tag::create(['name' => 'tag']);

        $this->postData([
            '_relation_field' => 'tags',
            '_relation_mode' => 'pivot',
            'foreign_id' => $tag->id,
            'Tag' => ['pivot' => ['added_by' => 'owner']],
        ]);
        $this->writableController('tags')->onRelationManagePivotCreate();
        $this->assertEquals('owner', $this->pivotValue($tag));

        $this->postData([
            '_relation_field' => 'tags',
            '_relation_mode' => 'pivot',
            'manage_id' => $tag->id,
            'Tag' => ['pivot' => ['added_by' => 'editor']],
        ]);
        $this->writableController('tags')->onRelationManagePivotUpdate();
        $this->assertEquals('editor', $this->pivotValue($tag));
    }

    /**
     * Read only disables writing, not viewing: the relation must still render and refresh.
     */
    public function testReadOnlyRelationStillRenders(): void
    {
        $this->author->posts()->create(['title' => 'visible', 'slug' => 'visible']);

        $this->postData(['_relation_field' => 'posts']);
        $controller = $this->controller();

        $this->assertIsArray($controller->onRelationButtonRefresh());
        $this->assertIsString($controller->onRelationManageForm());
    }

    //
    // Reordering, which writes the relation's sort order through the view list widget
    //

    /**
     * Returns a parent holding two children in a known order, and posts the reversed order.
     */
    protected function makeSortableParent(): RelationBehaviorFixture
    {
        RelationBehaviorFixture::migrateUp();

        $parent = RelationBehaviorFixture::create(['name' => 'Parent']);
        $first = $parent->children()->create(['name' => 'first', 'sort_order' => 1]);
        $second = $parent->children()->create(['name' => 'second', 'sort_order' => 2]);

        $this->postData([
            '_relation_field' => 'children',
            'record_ids' => [$second->id, $first->id],
        ]);

        return $parent;
    }

    public function testReadOnlyRelationCannotBeReordered(): void
    {
        $parent = $this->makeSortableParent();

        $controller = new ReadOnlySortableRelationController;
        $controller->initRelation($parent, 'children');

        try {
            $controller->relationGetViewWidget()->onReorder();
            $this->fail('A read only relation must not be reorderable');
        } catch (HttpException $ex) {
            $this->assertEquals(403, $ex->getStatusCode(), 'Reordering must be refused with a 403');
        }

        $this->assertEquals(
            ['first', 'second'],
            $parent->children()->orderBy('sort_order')->pluck('name')->all()
        );
    }

    /**
     * The refusal lives on the write, not on the list's `sortable` flag: that flag is also what
     * makes the list present the relation in its stored order instead of applying a sort order of
     * its own, so switching it off for a read only relation would silently reorder the records
     * the operator is reading.
     */
    public function testReadOnlyRelationIsStillPresentedInItsStoredOrder(): void
    {
        $parent = $this->makeSortableParent();

        $controller = new ReadOnlySortableRelationController;
        $controller->initRelation($parent, 'children');

        $viewWidget = $controller->relationGetViewWidget();

        $this->assertTrue($viewWidget->sortable, 'The list must still present the stored order');
        $this->assertFalse(
            $viewWidget->getSortColumn(),
            'The list must not apply a sort column of its own over the relation order'
        );
        $this->assertEquals(
            ['first', 'second'],
            $viewWidget->prepareQuery()->get()->pluck('name')->all()
        );
    }

    /**
     * The same question asked of the markup, since the view list also runs `list.extendRecords`.
     */
    public function testReadOnlyRelationRendersInItsStoredOrder(): void
    {
        $parent = $this->makeSortableParent();

        $controller = new ReadOnlySortableRelationController;
        $controller->initRelation($parent, 'children');

        $html = $controller->relationRender('children');

        preg_match_all('/>\s*(first|second)\s*</', $html, $matches);

        $this->assertEquals(['first', 'second'], array_values(array_unique($matches[1])));
    }

    public function testWritableRelationCanStillBeReordered(): void
    {
        $parent = $this->makeSortableParent();

        $controller = new WritableSortableRelationController;
        $controller->initRelation($parent, 'children');

        $viewWidget = $controller->relationGetViewWidget();
        $this->assertTrue($viewWidget->sortable);

        $viewWidget->onReorder();

        $this->assertEquals(
            ['second', 'first'],
            $parent->children()->orderBy('sort_order')->pluck('name')->all()
        );
    }

    /**
     * relationExtendConfig() runs inside initRelation() on every request, so a controller can
     * make a relation read only per user. It is also the escape hatch that keeps the extra
     * config's restrict-only rule from trapping anyone: the hook runs last and may still switch
     * readOnly back off.
     */
    public function testRelationExtendConfigDecidesReadOnly(): void
    {
        $post = $this->author->posts()->create(['title' => 'keep me', 'slug' => 'extend-config']);

        $this->postData(['_relation_field' => 'posts', 'checked' => [$post->id]]);

        $denied = new ExtendedConfigRelationController;
        $denied->initRelation($this->author, 'posts');
        $this->assertForbidden($denied, 'onRelationManageDelete');
        $this->assertNotNull(Post::find($post->id));

        $this->postData([
            '_relation_field' => 'posts',
            '_relation_extra_config' => base64_encode(json_encode(['readOnly' => true])),
            'checked' => [$post->id],
        ]);

        $allowed = new ExtendedConfigRelationController;
        $allowed->denyWrites = false;
        $allowed->initRelation($this->author, 'posts');
        $allowed->onRelationManageDelete();

        $this->assertNull(Post::find($post->id), 'relationExtendConfig() must still be able to allow writes');
    }

    //
    // The relation's own widget surface: the forms the behavior builds bind every nested form
    // widget to the controller, so those widgets' handlers are dispatched by alias without ever
    // passing through a relation handler.
    //

    /**
     * Returns a parent with one child that already owns an attachment, and posts the request the
     * nested widget will read. The post has to happen before the controller is built: FileUpload
     * resolves its config form (and with it post('file_id')) in init().
     */
    protected function makeChildWithAttachment(array $extraPostData = []): array
    {
        RelationBehaviorFixture::migrateUp();

        $parent = RelationBehaviorFixture::create(['name' => 'Parent']);
        $child = $parent->children()->create(['name' => 'Child']);

        $file = new File;
        $file->data = base_path('modules/backend/tests/fixtures/reference/file1.txt');
        $file->title = 'original title';
        $file->save();
        $child->thumb()->add($file);

        $this->postData([
            '_relation_field' => 'children',
            'manage_id' => $child->id,
            'file_id' => $file->id,
        ] + $extraPostData);

        return [$parent, $child, $file];
    }

    /**
     * The same, for the single-record (hasOne) relation whose view form is a preview form.
     */
    protected function makeSingleChildWithAttachment(array $extraPostData = []): array
    {
        RelationBehaviorFixture::migrateUp();

        $parent = RelationBehaviorFixture::create(['name' => 'Parent']);
        $child = $parent->primaryChild()->create(['name' => 'Child']);

        $file = new File;
        $file->data = base_path('modules/backend/tests/fixtures/reference/file1.txt');
        $file->title = 'original title';
        $file->save();
        $child->thumb()->add($file);

        $this->postData([
            '_relation_field' => 'primaryChild',
            'file_id' => $file->id,
        ] + $extraPostData);

        return [$parent, $child, $file];
    }

    /**
     * Dispatches a widget handler the way the framework does, by alias through
     * Controller::runAjaxHandler(). Returns false when no widget answered to that alias.
     */
    protected function runWidgetHandler(Controller $controller, string $handler)
    {
        try {
            return static::callProtectedMethod($controller, 'runAjaxHandler', [$handler]);
        } catch (SystemException $ex) {
            return false;
        }
    }

    /**
     * Dispatches a widget handler and returns the HTTP status it was refused with, or 200.
     */
    protected function widgetHandlerStatus(Controller $controller, string $handler): int
    {
        try {
            static::callProtectedMethod($controller, 'runAjaxHandler', [$handler]);
        } catch (HttpException $ex) {
            return $ex->getStatusCode();
        }

        return 200;
    }

    /**
     * Returns the aliases of every widget currently bound to the controller for dispatch.
     */
    protected function boundWidgetAliases(Controller $controller): array
    {
        return array_keys((array) $controller->widget);
    }

    /**
     * The structural invariant: a read only relation's forms and their field widgets are all
     * bound, so every read still works; what stops a write is the widget's own previewMode
     * guard, which the relation propagates down from the form.
     */
    public function testReadOnlyRelationPutsItsFieldWidgetsInPreviewMode(): void
    {
        [$parent] = $this->makeChildWithAttachment();

        $controller = new ReadOnlyNestedWidgetRelationController;
        $controller->initRelation($parent, 'children');
        $aliases = $this->boundWidgetAliases($controller);

        $this->assertContains('relationChildrenManageForm', $aliases);
        $this->assertContains(
            'relationChildrenManageFormThumb',
            $aliases,
            'Field widgets stay bound so their read handlers keep working'
        );

        $manageWidget = $controller->relationGetManageWidget();
        $this->assertTrue($manageWidget->previewMode);

        foreach ($manageWidget->getFormWidgets() as $name => $widget) {
            $this->assertTrue($widget->previewMode, "Field widget {$name} must be in preview mode");
        }
    }

    /**
     * The regression the previous approach sacrificed and this one restores: the read handlers
     * on the field widgets keep working, as does the form's own read side.
     */
    public function testReadOnlyRelationStillDispatchesReadHandlers(): void
    {
        [$parent] = $this->makeChildWithAttachment([
            'target' => '#detailsTab',
            'name' => 'Details',
            'section' => 'primary',
            '_repeater_index' => 0,
            'RelationBehaviorFixture' => ['notes' => '**live preview**'],
        ]);

        $controller = new ReadOnlyNestedWidgetRelationController;
        $controller->initRelation($parent, 'children');

        $lazyTab = $this->runWidgetHandler($controller, 'relationChildrenManageForm::onLazyLoadTab');
        $this->assertIsArray($lazyTab, 'A read only relation must still load its lazy tabs');
        $this->assertArrayHasKey('#detailsTab', $lazyTab);

        $this->assertIsArray(
            $this->runWidgetHandler($controller, 'relationChildrenManageForm::onRefresh'),
            'A read only relation must still refresh its form'
        );
        $this->assertIsArray(
            $this->runWidgetHandler($controller, 'relationChildrenManageFormNotes::onRefresh'),
            'Markdown live preview must still work on a read only relation'
        );
        $this->assertIsArray(
            $this->runWidgetHandler($controller, 'relationChildrenManageFormPoints::onRefresh'),
            'Repeater refresh must still work on a read only relation'
        );
        $this->assertIsString(
            $this->runWidgetHandler($controller, 'relationChildrenManageFormThumb::onLoadAttachmentConfig'),
            'Loading the attachment config form is a read and must still work'
        );
    }

    /**
     * The repeater's mutating handlers are refused, while its refresh above is not.
     */
    public function testReadOnlyRelationRefusesRepeaterMutations(): void
    {
        [$parent] = $this->makeChildWithAttachment();

        $controller = new ReadOnlyNestedWidgetRelationController;
        $controller->initRelation($parent, 'children');

        foreach (['onAddItem', 'onRemoveItem'] as $handler) {
            $this->assertEquals(
                403,
                $this->widgetHandlerStatus($controller, 'relationChildrenManageFormPoints::' . $handler),
                $handler . ' must be refused on a read only relation'
            );
        }
    }

    public function testReadOnlyRelationRefusesAttachmentRemoval(): void
    {
        [$parent, $child, $file] = $this->makeChildWithAttachment();

        $controller = new ReadOnlyNestedWidgetRelationController;
        $controller->initRelation($parent, 'children');

        $this->assertEquals(
            403,
            $this->widgetHandlerStatus($controller, 'relationChildrenManageFormThumb::onRemoveAttachment')
        );

        $this->assertNotNull(File::find($file->id), 'The attachment must survive');
        $this->assertEquals($file->id, RelationBehaviorFixture::find($child->id)->thumb->id);
    }

    public function testNestedManageFormWidgetCannotWrite(): void
    {
        [$parent, $child, $file] = $this->makeChildWithAttachment([
            'RelationBehaviorFixture' => ['thumb' => ['title' => 'renamed title']],
        ]);

        $controller = new ReadOnlyNestedWidgetRelationController;
        $controller->initRelation($parent, 'children');

        $this->assertEquals(
            403,
            $this->widgetHandlerStatus($controller, 'relationChildrenManageFormThumb::onSaveAttachmentConfig'),
            'A read only relation must refuse its manage form widgets\' write handlers'
        );

        // The handler's response is misleading -- it reports a display name either way.
        // The database is the evidence.
        $this->assertEquals('original title', File::find($file->id)->title);
    }

    /**
     * The single-record view form is a preview form, and its nested widgets inherit that.
     */
    public function testReadOnlySingleRelationPutsItsFieldWidgetsInPreviewMode(): void
    {
        [$parent] = $this->makeSingleChildWithAttachment();

        $controller = new ReadOnlySingleRelationController;
        $controller->initRelation($parent, 'primaryChild');
        $aliases = $this->boundWidgetAliases($controller);

        $this->assertContains('relationPrimaryChildViewFormThumb', $aliases);
        $this->assertContains('relationPrimaryChildManageFormThumb', $aliases);

        $this->assertTrue($controller->relationGetViewWidget()->previewMode);
        $this->assertTrue($controller->relationGetManageWidget()->previewMode);
    }

    public function testNestedViewFormWidgetCannotWrite(): void
    {
        [$parent, $child, $file] = $this->makeSingleChildWithAttachment([
            'RelationBehaviorFixture' => ['thumb' => ['title' => 'renamed title']],
        ]);

        $controller = new ReadOnlySingleRelationController;
        $controller->initRelation($parent, 'primaryChild');

        $this->assertEquals(
            403,
            $this->widgetHandlerStatus($controller, 'relationPrimaryChildViewFormThumb::onSaveAttachmentConfig'),
            'A read only relation must refuse its view form widgets\' write handlers'
        );
        $this->assertEquals(
            403,
            $this->widgetHandlerStatus($controller, 'relationPrimaryChildManageFormThumb::onSaveAttachmentConfig')
        );

        $this->assertEquals('original title', File::find($file->id)->title);
    }

    /**
     * Invalidation: a writable relation still dispatches its nested widget handlers.
     */
    public function testWritableRelationStillDispatchesNestedWidgetHandlers(): void
    {
        [$parent, $child, $file] = $this->makeChildWithAttachment([
            'RelationBehaviorFixture' => ['thumb' => ['title' => 'renamed title']],
        ]);

        $controller = new WritableNestedWidgetRelationController;
        $controller->initRelation($parent, 'children');

        $result = $this->runWidgetHandler($controller, 'relationChildrenManageFormThumb::onSaveAttachmentConfig');

        $this->assertNotFalse($result, 'A writable relation must keep its nested widget handlers');
        $this->assertEquals('renamed title', File::find($file->id)->title);
    }

    /**
     * Invalidation: a read only relation must still page and refresh its list, so the view
     * widget itself stays bound even though the relation is read only.
     */
    public function testReadOnlyRelationStillDispatchesViewListHandlers(): void
    {
        $this->author->posts()->create(['title' => 'visible', 'slug' => 'visible-list']);

        $this->postData(['_relation_field' => 'posts']);
        $controller = $this->controller();

        $this->assertNotFalse(
            $this->runWidgetHandler($controller, 'relationPostsViewList::onRefresh'),
            'A read only relation must still refresh its list'
        );
    }

    /**
     * The round trip still has to work in the restricting direction: a relation rendered read
     * only by `relationRender()` reports that back through `_relation_extra_config`, and the
     * handlers must honour it.
     */
    /**
     * The extra configuration blob is returned by the browser with every relation request, so it
     * decides nothing about whether the relation is writable - in either direction. Whether it can
     * switch readOnly off is the half that matters, and testExtraConfigCannotDisableReadOnly
     * covers it; this is the other half, kept so the asymmetry is not read as an oversight.
     */
    public function testExtraConfigCannotEnableReadOnly(): void
    {
        $post = $this->author->posts()->create(['title' => 'keep me', 'slug' => 'tighten']);

        $this->postData([
            '_relation_field' => 'posts',
            '_relation_extra_config' => base64_encode(json_encode(['readOnly' => true])),
            'checked' => [$post->id],
        ]);

        $this->writableController()->onRelationManageDelete();

        $this->assertNull(Post::find($post->id), 'the relation configuration decides, not the request');
    }

    /**
     * The other side of that round trip, and the reason the render-time option is a presentation
     * option rather than a control: it reaches the server only while the browser keeps returning
     * it. The manage popup posts the relation field, the mode and the record without the extra
     * configuration -- see `relationcontroller/partials/_manage_form.php` -- so the relation's own
     * configuration is all that handler ever sees.
     */
    /**
     * A readOnly passed to relationRender() styles that one render. It cannot be enforced - the
     * client decides whether it comes back - so it is not remembered and not treated as a control.
     * Only the relation's own configuration is.
     */
    public function testRenderTimeReadOnlyIsNotAServerSideControl(): void
    {
        $post = $this->author->posts()->create(['title' => 'original', 'slug' => 'round-trip']);

        $markup = $this->writableController()->relationRender('posts', ['readOnly' => true]);

        preg_match("/_relation_extra_config: '([^']*)'/", $markup, $matches);
        $blob = html_entity_decode($matches[1] ?? '');

        $this->assertEquals(
            ['readOnly' => true],
            json_decode(base64_decode($blob), true),
            'the container asks the browser to return the rendered setting'
        );

        $this->postData([
            '_relation_field' => 'posts',
            '_relation_mode' => 'form',
            'manage_id' => $post->id,
            'Post' => ['title' => 'renamed'],
        ]);

        $this->writableController()->onRelationManageUpdate();

        $this->assertEquals('renamed', Post::find($post->id)->title, 'the manage form carries no extra configuration');

        $this->postData([
            '_relation_field' => 'posts',
            '_relation_mode' => 'form',
            '_relation_extra_config' => $blob,
            'manage_id' => $post->id,
            'Post' => ['title' => 'renamed again'],
        ]);

        $this->writableController()->onRelationManageUpdate();

        $this->assertEquals(
            'renamed again',
            Post::find($post->id)->title,
            'the returned blob does not make a writable relation read only'
        );
    }

    /**
     * `readOnly` is a public property of the behavior, documented as a bool, and read from a
     * configuration where an absent key reads as null. It is cast where it is assigned, so a
     * relation that does not mention `readOnly` reports false rather than null.
     */
    public function testReadOnlyIsAlwaysABooleanProperty(): void
    {
        $this->postData(['_relation_field' => 'posts']);

        $writable = $this->writableController()->asExtension(\Backend\Behaviors\RelationController::class);

        $this->assertIsBool($writable->readOnly);
        $this->assertFalse($writable->readOnly);

        $readOnly = $this->controller()->asExtension(\Backend\Behaviors\RelationController::class);

        $this->assertIsBool($readOnly->readOnly);
        $this->assertTrue($readOnly->readOnly);
    }
}
