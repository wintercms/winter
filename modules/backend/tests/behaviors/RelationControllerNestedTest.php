<?php

namespace Backend\Tests\Behaviors;

use Backend\Behaviors\RelationController;
use Backend\Classes\Controller as BackendController;
use Backend\Tests\Fixtures\Models\UserFixture;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Schema;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;

/**
 * Self-contained fixture models for nested relation testing.
 *
 * Order -(items, hasMany)-> Item -(taxes, belongsToMany)-> Tax
 *   |                        |
 *   |                        `-(items, hasMany, self-referencing)-> Item
 *   `-(taxes, hasMany)-> Tax
 *
 * The self-referencing relation on Item is what exercises recursion:
 * the same field name ("items") appears at two different depths.
 *
 * Order's OWN "taxes" is deliberately a completely different relation
 * (hasMany via order_id, not belongsToMany via the pivot) that just
 * happens to share the same bare name as Item's nested "taxes" - this
 * is what exercises aliasNestedConfig()'s config-leak fix: resolving
 * the nested field must never corrupt the root-level field's config,
 * even when both share a name.
 */
class RelationTestOrder extends Model
{
    public $table = 'backend_test_relation_orders';
    protected $guarded = [];
    public $timestamps = false;

    public $hasMany = [
        'items' => [RelationTestItem::class, 'key' => 'order_id'],
        'taxes' => [RelationTestTax::class, 'key' => 'order_id'],
    ];

    public static function migrateUp(): void
    {
        if (Schema::hasTable('backend_test_relation_orders')) {
            return;
        }

        Schema::create('backend_test_relation_orders', function ($table) {
            $table->increments('id');
            $table->string('name')->nullable();
        });
    }

    public static function migrateDown(): void
    {
        Schema::dropIfExists('backend_test_relation_orders');
    }
}

class RelationTestItem extends Model
{
    public $table = 'backend_test_relation_items';
    protected $guarded = [];
    public $timestamps = false;

    public $belongsTo = [
        'order' => [RelationTestOrder::class, 'key' => 'order_id'],
        'parentItem' => [RelationTestItem::class, 'key' => 'parent_item_id'],
    ];

    public $hasMany = [
        // Self-referencing: an Item's own sub-items. Deliberately the
        // SAME field name ("items") as Order's own relation, so tests
        // can exercise a recursive relation - the same name reused at
        // two different depths - not just a same-named coincidence.
        'items' => [RelationTestItem::class, 'key' => 'parent_item_id'],
    ];

    public $belongsToMany = [
        'taxes' => [
            RelationTestTax::class,
            'table' => 'backend_test_relation_item_tax',
            'key' => 'item_id',
            'otherKey' => 'tax_id',
        ],
    ];

    public static function migrateUp(): void
    {
        if (Schema::hasTable('backend_test_relation_items')) {
            return;
        }

        Schema::create('backend_test_relation_items', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('parent_item_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('quantity')->nullable();
        });

        Schema::create('backend_test_relation_item_tax', function ($table) {
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('tax_id');
        });
    }

    public static function migrateDown(): void
    {
        Schema::dropIfExists('backend_test_relation_item_tax');
        Schema::dropIfExists('backend_test_relation_items');
    }
}

class RelationTestTax extends Model
{
    public $table = 'backend_test_relation_taxes';
    protected $guarded = [];
    public $timestamps = false;

    public $belongsTo = [
        'order' => [RelationTestOrder::class, 'key' => 'order_id'],
    ];

    public $belongsToMany = [
        'items' => [
            RelationTestItem::class,
            'table' => 'backend_test_relation_item_tax',
            'key' => 'tax_id',
            'otherKey' => 'item_id',
        ],
    ];

    public static function migrateUp(): void
    {
        if (Schema::hasTable('backend_test_relation_taxes')) {
            return;
        }

        Schema::create('backend_test_relation_taxes', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->nullable();
            $table->string('label')->nullable();
            $table->integer('rate')->nullable();
        });
    }

    public static function migrateDown(): void
    {
        Schema::dropIfExists('backend_test_relation_taxes');
    }
}

/**
 * Minimal backend controller implementing RelationController directly
 * (not a subclass, since nested support is now native). No FormController
 * implementation is needed - resolveFallbackRootModel() checks for a
 * formGetModel() method directly, which this stubs.
 *
 * config_relation.yaml keys use the id-stripped bracket path
 * ("items[taxes]", "items[items]"), matching real usage - see
 * RelationController::getNestedConfigKey().
 */
class RelationTestController extends BackendController
{
    public $implement = [
        RelationController::class,
    ];

    public $relationConfig = [
        'items' => [
            'label' => 'Items',
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
            ],
            'manage' => [
                'form' => ['fields' => [
                    'name' => ['label' => 'Name', 'type' => 'text'],
                    'quantity' => ['label' => 'Quantity', 'type' => 'number'],
                    // Rendered AMBIENTLY (same-request) whenever an
                    // Item's own manage form renders - this is what
                    // exercises resolveNestedContext()'s case (a) and
                    // relationMakePartial()'s stack push/pop, as
                    // opposed to every other test in this file, which
                    // posts an already-qualified bracket path and only
                    // exercises case (b) reconstruction.
                    'taxes' => ['label' => 'Taxes', 'type' => 'relationmanager'],
                ]],
            ],
        ],
        // Root-level "taxes" - a completely different relation
        // (Order's own hasMany, not Item's belongsToMany) that just
        // happens to share the same bare field name as items[taxes]
        // below. Label is deliberately distinct, so a test can detect
        // whether resolving the NESTED "taxes" ever corrupts THIS
        // entry's config (see aliasNestedConfig()'s config-leak fix).
        'taxes' => [
            'label' => 'Order-level Taxes',
            'view' => [
                'list' => ['columns' => ['label' => ['label' => 'Label']]],
            ],
            'manage' => [
                'form' => ['fields' => [
                    'label' => ['label' => 'Label', 'type' => 'text'],
                    'rate' => ['label' => 'Rate', 'type' => 'number'],
                ]],
            ],
        ],
        'items[taxes]' => [
            'label' => 'Taxes',
            'view' => [
                'list' => ['columns' => ['label' => ['label' => 'Label']]],
            ],
            'manage' => [
                'form' => ['fields' => [
                    'label' => ['label' => 'Label', 'type' => 'text'],
                    'rate' => ['label' => 'Rate', 'type' => 'number'],
                ]],
            ],
        ],
        'items[items]' => [
            'label' => 'Sub-items',
            'view' => [
                'list' => ['columns' => ['name' => ['label' => 'Name']]],
            ],
            'manage' => [
                'form' => ['fields' => [
                    'name' => ['label' => 'Name', 'type' => 'text'],
                    'quantity' => ['label' => 'Quantity', 'type' => 'number'],
                ]],
            ],
        ],
    ];

    protected $testRootModel;

    public function setTestRootModel($model)
    {
        $this->testRootModel = $model;
    }

    /**
     * Stubs the conventional FormController accessor that
     * resolveFallbackRootModel() looks for - stands in for a full
     * FormController implementation, which isn't needed just to
     * exercise RelationController itself.
     */
    public function formGetModel()
    {
        return $this->testRootModel;
    }
}

class RelationControllerNestedTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        RelationTestOrder::migrateUp();
        RelationTestItem::migrateUp();
        RelationTestTax::migrateUp();

        $this->actingAs((new UserFixture)->asSuperUser());
    }

    public function tearDown(): void
    {
        RelationTestTax::migrateDown();
        RelationTestItem::migrateDown();
        RelationTestOrder::migrateDown();

        parent::tearDown();
    }

    /**
     * Simulates a POST request the way ListsSortableTest does - direct
     * handler invocation, not a full $controller->run() dispatch.
     * pageAction() (called by beforeAjax()) safely no-ops when
     * $this->action is unset, which it always is here - matching the
     * real-world case a nested field hits on every fresh AJAX request
     * (see resolveFallbackRootModel()'s own docblock).
     */
    protected function postRequest(array $data): void
    {
        $request = HttpRequest::create('/', 'POST', $data);
        $this->app->instance('request', $request);
        \Request::swap($request);
    }

    protected function makeController(Model $rootModel): RelationTestController
    {
        $controller = new RelationTestController;
        $controller->setTestRootModel($rootModel);

        return $controller;
    }

    // -----------------------------------------------------------------
    // Regression: root-level (non-nested) behaviour must be unaffected.
    // This is the highest-stakes check for a change to a shared core
    // class - the overwhelming majority of real installs only ever
    // exercise this path.
    // -----------------------------------------------------------------

    public function testRootLevelCreateAttachesToRootModel()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $controller = $this->makeController($order);

        $this->postRequest([
            '_relation_field' => 'items',
            'RelationTestItem' => ['name' => 'Widget', 'quantity' => 5],
        ]);

        $controller->onRelationManageCreate();

        $order = RelationTestOrder::find($order->id);
        $this->assertCount(1, $order->items);
        $this->assertEquals('Widget', $order->items->first()->name);
        $this->assertEquals(5, $order->items->first()->quantity);
    }

    public function testRootLevelUpdateModifiesCorrectRecord()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Original', 'quantity' => 1]);

        $controller = $this->makeController($order);

        $this->postRequest([
            '_relation_field' => 'items',
            'manage_id' => $item->id,
            'RelationTestItem' => ['name' => 'Updated', 'quantity' => 9],
        ]);

        $controller->onRelationManageUpdate();

        $item = RelationTestItem::find($item->id);
        $this->assertEquals('Updated', $item->name);
        $this->assertEquals(9, $item->quantity);
    }

    // -----------------------------------------------------------------
    // Nested: a relation manager rendered inside another's manage form.
    // -----------------------------------------------------------------

    public function testNestedCreateAttachesToCorrectItemNotOrder()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $itemA = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);
        $itemB = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item B']);

        $controller = $this->makeController($order);

        $this->postRequest([
            '_relation_field' => "items[{$itemA->id}][taxes]",
            'RelationTestTax' => ['label' => 'GST', 'rate' => 5],
        ]);

        $controller->onRelationManageCreate();

        $itemA = RelationTestItem::find($itemA->id);
        $itemB = RelationTestItem::find($itemB->id);

        $this->assertCount(1, $itemA->taxes, 'Tax should attach to the item named in the qualified field path');
        $this->assertEquals('GST', $itemA->taxes->first()->label);
        $this->assertCount(0, $itemB->taxes, 'A sibling item must not receive the tax meant for a different item');
    }

    public function testNestedUpdateModifiesCorrectTaxRecord()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);
        $tax = RelationTestTax::create(['label' => 'Original', 'rate' => 1]);
        $item->taxes()->add($tax);

        $controller = $this->makeController($order);

        $this->postRequest([
            '_relation_field' => "items[{$item->id}][taxes]",
            'manage_id' => $tax->id,
            'RelationTestTax' => ['label' => 'Updated', 'rate' => 15],
        ]);

        $controller->onRelationManageUpdate();

        $tax = RelationTestTax::find($tax->id);
        $this->assertEquals('Updated', $tax->label);
        $this->assertEquals(15, $tax->rate);
    }

    /**
     * Regression for a real, once-broken bug: manage_id is a single
     * global POST value with no per-field scoping. Without
     * shouldSuppressManageId()'s create/add-action suppression, a
     * stale manage_id left over from editing a DIFFERENT record
     * elsewhere on the page could make a genuine "Create" action
     * silently behave like an edit of that stale id instead.
     *
     * Must go through onRelationButtonCreate() specifically, not
     * onRelationManageCreate() (the actual save handler) - the save
     * logic creates the new record via $this->relationModel directly
     * and never consults $this->manageId at all, so a test calling the
     * save handler directly would pass even without the suppression
     * fix in place. The real risk is earlier: makeManageWidget(),
     * called unconditionally by initRelation() on EVERY handler call
     * including this one, uses manage_id to decide whether to build a
     * blank form or find() and pre-populate an existing record - a
     * stale id there means the "Create" popup opens pre-filled with a
     * record that has nothing to do with the field being created.
     */
    public function testNestedCreatePopupDoesNotPrePopulateFromStaleManageId()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);
        $unrelatedTax = RelationTestTax::create(['label' => 'Should not be pre-populated', 'rate' => 99]);

        $controller = $this->makeController($order);

        $this->postRequest([
            '_relation_field' => "items[{$item->id}][taxes]",
            // Stale manage_id, as if left over from editing a
            // different record elsewhere on the page - a genuine
            // "Create" click should never honour this.
            'manage_id' => $unrelatedTax->id,
        ]);

        $html = $controller->onRelationButtonCreate();

        $this->assertIsString($html);
        $this->assertStringNotContainsString(
            'Should not be pre-populated',
            $html,
            'A stale manage_id must not pre-populate a "Create" popup with an unrelated record\'s data'
        );
    }

    // -----------------------------------------------------------------
    // Recursive nesting: the same field name ("items") reused at two
    // depths. This is what exercises getNestedConfigKey()'s collapse
    // logic - without it, resolving config for a sub-item's own
    // sub-item would look for a config key that can never exist.
    // -----------------------------------------------------------------

    public function testRecursiveNestingAttachesSubItemToCorrectParentItem()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $parentItem = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Parent']);
        $siblingItem = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Sibling']);

        $controller = $this->makeController($order);

        $this->postRequest([
            '_relation_field' => "items[{$parentItem->id}][items]",
            'RelationTestItem' => ['name' => 'Sub-item', 'quantity' => 3],
        ]);

        $controller->onRelationManageCreate();

        $parentItem = RelationTestItem::find($parentItem->id);
        $siblingItem = RelationTestItem::find($siblingItem->id);

        $this->assertCount(1, $parentItem->items, 'Sub-item should attach to its intended parent item');
        $this->assertEquals('Sub-item', $parentItem->items->first()->name);
        $this->assertCount(0, $siblingItem->items, 'A sibling item must not receive a sub-item meant for another item');
    }

    public function testThreeLevelNestingResolvesCorrectAncestor()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item']);
        $subItem = RelationTestItem::create(['parent_item_id' => $item->id, 'name' => 'Sub-item']);
        $unrelatedSubItem = RelationTestItem::create(['parent_item_id' => $item->id, 'name' => 'Other sub-item']);

        $controller = $this->makeController($order);

        // Three hops deep: order -> item -> sub-item -> tax.
        $this->postRequest([
            '_relation_field' => "items[{$item->id}][items][{$subItem->id}][taxes]",
            'RelationTestTax' => ['label' => 'Deep Tax', 'rate' => 12],
        ]);

        $controller->onRelationManageCreate();

        $subItem = RelationTestItem::find($subItem->id);
        $unrelatedSubItem = RelationTestItem::find($unrelatedSubItem->id);

        $this->assertCount(1, $subItem->taxes, 'Tax should attach three hops down to the correct sub-item');
        $this->assertEquals('Deep Tax', $subItem->taxes->first()->label);
        $this->assertCount(0, $unrelatedSubItem->taxes, 'A sibling sub-item at the same depth must not receive the tax');
    }

    // -----------------------------------------------------------------
    // Unit-level tests for the pure parsing/resolution algorithms,
    // via reflection since they're protected methods with no side
    // effects worth the overhead of a full request simulation.
    // -----------------------------------------------------------------

    protected function invokeProtectedMethod($object, string $method, array $args = [])
    {
        $reflection = new \ReflectionMethod(get_class($object), $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $args);
    }

    protected function readProtectedProperty($object, string $property)
    {
        $reflection = new \ReflectionProperty(get_class($object), $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function testParseBracketFieldSplitsHopsAndLeaf()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $controller = $this->makeController($order);

        [$hops, $leaf] = $this->invokeProtectedMethod($controller->asExtension(RelationController::class), 'parseBracketField', [
            'items[5][taxes][2][adjustments]',
        ]);

        $this->assertEquals([['items', '5'], ['taxes', '2']], $hops);
        $this->assertEquals('adjustments', $leaf);
    }

    public function testParseBracketFieldRejectsMalformedInput()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $controller = $this->makeController($order);
        $behavior = $controller->asExtension(RelationController::class);

        $this->expectException(\Winter\Storm\Exception\ApplicationException::class);

        $this->invokeProtectedMethod($behavior, 'parseBracketField', ['not a valid bracket field']);
    }

    /**
     * The core fix for recursive relations: naively concatenating every
     * hop produces a config key that grows without bound as recursion
     * goes deeper ("items[items][items]", "items[items][items][items]",
     * ...), which could never match a finite config file. A trailing
     * repeated segment must collapse to one occurrence regardless of
     * how many times it repeats.
     */
    public function testGetNestedConfigKeyCollapsesRecursiveRepeatsAtAnyDepth()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $controller = $this->makeController($order);
        $behavior = $controller->asExtension(RelationController::class);

        // Force originalConfig into a known state matching our test
        // controller's own $relationConfig, since getNestedConfigKey()
        // checks against it directly.
        $reflection = new \ReflectionProperty($behavior, 'originalConfig');
        $reflection->setAccessible(true);
        $originalConfig = $reflection->getValue($behavior);

        $this->assertTrue(
            isset($originalConfig->{'items[items]'}),
            'Test controller config must define items[items] for this test to be meaningful'
        );

        $twoLevels = $this->invokeProtectedMethod($behavior, 'getNestedConfigKey', ['items[1][items][2][items]']);
        $threeLevels = $this->invokeProtectedMethod($behavior, 'getNestedConfigKey', ['items[1][items][2][items][3][items]']);
        $fourLevels = $this->invokeProtectedMethod($behavior, 'getNestedConfigKey', ['items[1][items][2][items][3][items][4][items]']);

        $this->assertEquals('items[items]', $twoLevels);
        $this->assertEquals('items[items]', $threeLevels, 'Collapse must apply regardless of how deep the recursion goes');
        $this->assertEquals('items[items]', $fourLevels, 'Collapse must apply regardless of how deep the recursion goes');
    }

    public function testGetNestedConfigKeyLeavesNonRecursivePathsUnaffected()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $controller = $this->makeController($order);
        $behavior = $controller->asExtension(RelationController::class);

        $key = $this->invokeProtectedMethod($behavior, 'getNestedConfigKey', ['items[1][taxes]']);

        $this->assertEquals('items[taxes]', $key);
    }

    /**
     * Regression for a real, flagged security/correctness issue:
     * resolveModelForHops() previously resolved a hop id via a bare,
     * unscoped find() against the related model's WHOLE TABLE, rather
     * than through the relation itself. That meant a crafted nested
     * field referencing an id belonging to a completely different
     * parent's record - e.g. another Order's Item - would still
     * resolve successfully, silently bypassing the relation's own
     * constraint entirely (the parent foreign key for hasMany, the
     * pivot join for belongsToMany). Scoping the lookup through
     * $model->{$relationName}()->find($id) instead means a hop can
     * only ever resolve to a record that's genuinely reachable via
     * that specific relation from that specific parent.
     */
    public function testResolveModelForHopsCannotCrossIntoADifferentParentsRecord()
    {
        $orderA = RelationTestOrder::create(['name' => 'Order A']);
        RelationTestItem::create(['order_id' => $orderA->id, 'name' => 'Item in Order A']);

        $orderB = RelationTestOrder::create(['name' => 'Order B']);
        $itemInOrderB = RelationTestItem::create(['order_id' => $orderB->id, 'name' => 'Item in Order B']);

        $controller = $this->makeController($orderA);
        $behavior = $controller->asExtension(RelationController::class);

        // A hop crafted to reference Order B's item, while resolving
        // against Order A as the root - a perfectly valid id within
        // the items table as a whole, but not a child of Order A.
        $result = $this->invokeProtectedMethod($behavior, 'resolveModelForHops', [
            $orderA,
            [['items', (string) $itemInOrderB->id]],
        ]);

        $this->assertInstanceOf(RelationTestItem::class, $result);
        $this->assertFalse(
            $result->exists,
            'A hop id belonging to a DIFFERENT parent must not resolve to that record - it should fall back to a blank instance instead'
        );
        $this->assertNotEquals(
            $itemInOrderB->id,
            $result->id,
            'The resolved model must never carry the cross-parent record\'s identity'
        );
    }

    /**
     * Companion positive-path check for the fix above: a hop id that
     * DOES legitimately belong to the current parent must still
     * resolve correctly - this is about correctly SCOPING the lookup,
     * not breaking it for the ordinary case.
     */
    public function testResolveModelForHopsStillResolvesLegitimateChild()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);

        $controller = $this->makeController($order);
        $behavior = $controller->asExtension(RelationController::class);

        $result = $this->invokeProtectedMethod($behavior, 'resolveModelForHops', [
            $order,
            [['items', (string) $item->id]],
        ]);

        $this->assertInstanceOf(RelationTestItem::class, $result);
        $this->assertTrue($result->exists);
        $this->assertEquals($item->id, $result->id);
    }

    /**
     * Regression for a config-mutation bug flagged during code review:
     * aliasNestedConfig() previously mutated $this->originalConfig
     * directly to alias a nested field's config onto its bare leaf
     * name. Since originalConfig is the SAME object $this->config is
     * reset FROM at the top of every initRelation() call - and is
     * itself never reset again after construction - that alias
     * persisted for the rest of the request. A LATER initRelation()
     * call for a genuinely root-level field sharing the same bare name
     * ("taxes" here: Order's own hasMany, completely separate from
     * Item's nested belongsToMany) would then incorrectly inherit the
     * nested field's config instead of its own.
     */
    public function testResolvingNestedFieldDoesNotCorruptRootLevelFieldOfSameName()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);

        $controller = $this->makeController($order);
        $behavior = $controller->asExtension(RelationController::class);

        // Resolve the NESTED "taxes" field first - this is what
        // previously mutated originalConfig, since aliasNestedConfig()
        // needs to alias "items[taxes]" onto the bare "taxes" key for
        // the nested resolution to find its own config at all.
        $controller->initRelation($order, "items[{$item->id}][taxes]");

        // THEN resolve the genuinely root-level "taxes" field, within
        // the SAME behavior instance - simulating both being active
        // within one request, the same way "items" and "taxes" already
        // are elsewhere in this file.
        $controller->initRelation($order, 'taxes');

        $config = $this->readProtectedProperty($behavior, 'config');

        $this->assertEquals(
            'Order-level Taxes',
            $config->label,
            'Resolving the nested "taxes" field must not corrupt the root-level "taxes" field\'s own config'
        );
    }

    /**
     * Companion check for the opposite order - resolving the root-level
     * field first was never actually broken (aliasNestedConfig() only
     * ever runs for a nested field in the first place), but confirms
     * the fix doesn't accidentally introduce a problem in that
     * direction either.
     */
    public function testResolvingRootLevelFieldFirstDoesNotAffectLaterNestedResolution()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);

        $controller = $this->makeController($order);
        $behavior = $controller->asExtension(RelationController::class);

        $controller->initRelation($order, 'taxes');
        $controller->initRelation($order, "items[{$item->id}][taxes]");

        $config = $this->readProtectedProperty($behavior, 'config');

        $this->assertEquals('Taxes', $config->label);
    }

    // -----------------------------------------------------------------
    // Regression: validateField() must prefer an already-active field
    // over stale POST data, since relationRenderToolbar()/
    // relationRenderView()/relationRefresh() all call it with no field
    // argument at all, historically assuming the POST value
    // unambiguously identifies "the" active field - false the moment a
    // nested field is active alongside its ancestor within one request.
    // -----------------------------------------------------------------

    public function testRelationRefreshDoesNotLoseNestedFieldContext()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);

        $controller = $this->makeController($order);

        // _relation_field carries the full qualified path, as a real
        // nested request would - relationRefresh()'s own internal
        // validateField(null) call must not regress $this->field back
        // to the raw bracket path once it's already been narrowed to
        // the bare leaf by the create itself.
        $this->postRequest([
            '_relation_field' => "items[{$item->id}][taxes]",
            'RelationTestTax' => ['label' => 'GST', 'rate' => 5],
        ]);

        // onRelationManageCreate() ends with $this->relationRefresh() -
        // if this throws or resolves the wrong field, that's the bug
        // this test guards against.
        $result = $controller->onRelationManageCreate();

        $this->assertIsArray($result);

        $item = RelationTestItem::find($item->id);
        $this->assertCount(1, $item->taxes);
    }

    // -----------------------------------------------------------------
    // Popup-opening handlers: onRelationButtonCreate()/
    // onRelationButtonUpdate(), which set $this->eventTarget and
    // delegate to onRelationManageForm() -> makeManageWidget(). Every
    // other test in this file exercises SAVE handlers
    // (onRelationManageCreate()/onRelationManageUpdate()), which never
    // consult manage_id or eventTarget the same way - these are the
    // actual site of the manage_id/eventTarget-driven bugs, and were
    // entirely uncovered before this addition.
    // -----------------------------------------------------------------

    public function testNestedButtonCreateBuildsBlankFormForCorrectRelation()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);

        $controller = $this->makeController($order);

        $this->postRequest([
            '_relation_field' => "items[{$item->id}][taxes]",
        ]);

        $html = $controller->onRelationButtonCreate();
        $behavior = $controller->asExtension(RelationController::class);
        $manageMode = $this->readProtectedProperty($behavior, 'manageMode');
        $manageWidget = $this->readProtectedProperty($behavior, 'manageWidget');

        $this->assertIsString($html);
        $this->assertEquals('form', $manageMode);
        $this->assertInstanceOf(RelationTestTax::class, $manageWidget->model);
        $this->assertFalse($manageWidget->model->exists, 'Create should always bind a blank, unsaved model');
    }

    public function testNestedButtonUpdateBindsCorrectExistingRecord()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);
        $otherItem = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item B']);
        $tax = RelationTestTax::create(['label' => 'GST', 'rate' => 5]);
        $decoyTax = RelationTestTax::create(['label' => 'Should not be bound', 'rate' => 1]);
        $item->taxes()->add($tax);
        $otherItem->taxes()->add($decoyTax);

        $controller = $this->makeController($order);

        $this->postRequest([
            '_relation_field' => "items[{$item->id}][taxes]",
            'manage_id' => $tax->id,
        ]);

        $html = $controller->onRelationButtonUpdate();
        $behavior = $controller->asExtension(RelationController::class);
        $manageWidget = $this->readProtectedProperty($behavior, 'manageWidget');

        $this->assertIsString($html);
        $this->assertTrue($manageWidget->model->exists);
        $this->assertEquals($tax->id, $manageWidget->model->id);
        $this->assertEquals('GST', $manageWidget->model->label);
        $this->assertStringNotContainsString('Should not be bound', $html);
    }

    // -----------------------------------------------------------------
    // Ambient (same-request) nesting: a relation field rendered INLINE
    // within another relation's own manage form, as opposed to every
    // other test in this file, which posts an already-qualified
    // bracket path and only ever exercises reconstruction from a fresh
    // request. This is the other half of resolveNestedContext() (case
    // "a") - entirely uncovered before this addition.
    //
    // Deliberately bypasses the full Backend\Widgets\Form field-type
    // rendering pipeline (i.e. never actually renders a
    // type: relationmanager field): that pipeline depends on widget
    // registration having correctly booted, which is unrelated to
    // anything RelationController itself is responsible for and
    // doesn't reliably happen in a minimal PHPUnit bootstrap (confirmed
    // - it fails with a widget-registration-shaped error even though
    // this exact field type has been manually tested working
    // correctly in a real browser many times over). Instead, this
    // simulates exactly what a widget's render() call does - push a
    // hop onto the stack the same way relationMakePartial() does, then
    // call initRelation() with a bare field name while it's non-empty
    // - which is the actual mechanism under test, independent of
    // whether any particular field widget can render in this
    // environment.
    // -----------------------------------------------------------------

    protected function pushNestingStack($behavior, string $hop): void
    {
        $reflection = new \ReflectionProperty(get_class($behavior), 'nestingStack');
        $reflection->setAccessible(true);
        $stack = $reflection->getValue($behavior);
        $stack[] = $hop;
        $reflection->setValue($behavior, $stack);
    }

    protected function popNestingStack($behavior): void
    {
        $reflection = new \ReflectionProperty(get_class($behavior), 'nestingStack');
        $reflection->setAccessible(true);
        $stack = $reflection->getValue($behavior);
        array_pop($stack);
        $reflection->setValue($behavior, $stack);
    }

    public function testAmbientFieldQualifiesUsingStackTopAsParentContext()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);

        $controller = $this->makeController($order);
        $behavior = $controller->asExtension(RelationController::class);

        // Establishes "items" as the currently-active field, the same
        // way validateField()/initRelation() would from a real request
        // - needed so aliasNestedConfig() and friends have a coherent
        // $this->field to work from during the nested call below.
        $controller->initRelation($order, 'items');

        // Simulates relationMakePartial() actively rendering Item's
        // own manage form - the exact condition resolveNestedContext()
        // checks for via end($this->nestingStack).
        $this->pushNestingStack($behavior, "items[{$item->id}]");

        // What RelationManager::render() does: initRelation() called
        // with the widget's own bound model and a BARE field name, no
        // bracket path involved at all.
        $controller->initRelation($item, 'taxes');

        $this->popNestingStack($behavior);

        $nestedField = $this->readProtectedProperty($behavior, 'nestedField');

        $this->assertEquals(
            "items[{$item->id}][taxes]",
            $nestedField,
            'A field resolved while the stack is non-empty should be qualified using the stack\'s top entry as its parent context'
        );
    }

    public function testAmbientFieldDistinguishesBetweenSiblingItems()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $itemA = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);
        $itemB = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item B']);

        $controller = $this->makeController($order);
        $behavior = $controller->asExtension(RelationController::class);
        $controller->initRelation($order, 'items');

        $this->pushNestingStack($behavior, "items[{$itemA->id}]");
        $controller->initRelation($itemA, 'taxes');
        $nestedFieldForA = $this->readProtectedProperty($behavior, 'nestedField');
        $this->popNestingStack($behavior);

        $this->pushNestingStack($behavior, "items[{$itemB->id}]");
        $controller->initRelation($itemB, 'taxes');
        $nestedFieldForB = $this->readProtectedProperty($behavior, 'nestedField');
        $this->popNestingStack($behavior);

        $this->assertEquals("items[{$itemA->id}][taxes]", $nestedFieldForA);
        $this->assertEquals("items[{$itemB->id}][taxes]", $nestedFieldForB);
        $this->assertNotEquals($nestedFieldForA, $nestedFieldForB, 'Two sibling items\' ambient contexts must resolve to distinct qualified fields');
    }

    /**
     * Narrower test for relationMakePartial() specifically - confirms
     * it actually pushes before rendering and pops after, regardless
     * of whether the render call inside it succeeds (it has its own
     * try/finally around the pop). The render itself may well fail in
     * this environment (same widget-registration issue as the tests
     * above), which is fine - this test only cares about stack state
     * after the call returns, not whether rendering succeeded.
     */
    public function testRelationMakePartialPushesAndPopsStackAroundManageFormRender()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);

        $controller = $this->makeController($order);
        $behavior = $controller->asExtension(RelationController::class);
        $controller->initRelation($order, 'items');

        // A partial name relationMakePartial() doesn't special-case
        // (only 'manage_form'/'manage_pivot' trigger push/pop) - used
        // here purely as a vehicle to inspect stack state mid-call via
        // a bound closure, without needing the real partial to exist.
        $reflection = new \ReflectionProperty(get_class($behavior), 'nestingStack');
        $reflection->setAccessible(true);

        try {
            $controller->relationMakePartial('manage_form');
        } catch (\Throwable $e) {
            // Rendering itself may fail in this environment (same
            // widget-registration issue as the tests above) - that's
            // not what this test is checking.
        }

        $stackAfterRender = $reflection->getValue($behavior);

        $this->assertEmpty($stackAfterRender, 'Stack must be empty again after relationMakePartial() returns, even if the render itself failed or threw');
    }

    // -----------------------------------------------------------------
    // Regression: _relation_mode is a single global POST value with no
    // per-field scoping. evalManageMode()'s first check trusts it
    // unconditionally, so a stale mode from an OUTER field (correctly
    // "form", since editing an Item IS a form) can force itself onto a
    // nested field's own, unrelated action - the real bug manifested as
    // makeManageWidget() attempting to find() a Tax using the outer
    // Item's own id, thrown from deep inside beforeAjax() before the
    // handler's own logic ever ran.
    // -----------------------------------------------------------------

    public function testStaleRelationModeDoesNotBreakNestedDelete()
    {
        $order = RelationTestOrder::create(['name' => 'Order 1']);
        // Decoy records first, so the item and tax ids are guaranteed
        // to diverge - otherwise both tables' autoincrement could
        // coincidentally start at the same value and mask the bug.
        RelationTestItem::create(['order_id' => $order->id, 'name' => 'Decoy 1']);
        RelationTestItem::create(['order_id' => $order->id, 'name' => 'Decoy 2']);
        $item = RelationTestItem::create(['order_id' => $order->id, 'name' => 'Item A']);
        $tax = RelationTestTax::create(['label' => 'GST', 'rate' => 5]);
        $item->taxes()->add($tax);

        $controller = $this->makeController($order);

        $this->postRequest([
            '_relation_field' => "items[{$item->id}][taxes]",
            // Both stale, as if leaked from the OUTER 'items' field's
            // own context: that field IS legitimately in 'form' mode
            // (being edited), with manage_id correctly identifying the
            // item itself - neither has any legitimate meaning for
            // this nested delete action.
            '_relation_mode' => 'form',
            'manage_id' => $item->id,
            'checked' => [$tax->id],
        ]);

        // Without suppression, this throws from inside beforeAjax()
        // itself (evalManageMode() incorrectly returns 'form' from the
        // stale POST value, which makes makeManageWidget() attempt
        // find($item->id) WITHIN THE TAXES TABLE) - before
        // onRelationManageDelete()'s own delete logic ever runs.
        $controller->onRelationButtonDelete();

        $this->assertNull(RelationTestTax::find($tax->id), 'Tax should have been deleted despite the stale _relation_mode/manage_id');

        $item = RelationTestItem::find($item->id);
        $this->assertCount(0, $item->taxes);
    }
}
