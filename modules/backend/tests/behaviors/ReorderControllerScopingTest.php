<?php

namespace Backend\Tests\Behaviors;

use Backend\Classes\Controller;
use Backend\Tests\BackendTestCase;
use Backend\Tests\Fixtures\Models\RelationBehaviorFixture;

/**
 * `reorderExtendQuery()` is the documented way for a controller to narrow the records a user may
 * see and reorder. It was applied to the listing only, so `onReorder()` resolved the ids posted
 * back against the bare model and could move a record the page never offered.
 */
class ScopedReorderController extends Controller
{
    public $implement = [\Backend\Behaviors\ReorderController::class];

    public $requiredPermissions = [];

    public $reorderConfig = [
        'title' => 'Reorder',
        'modelClass' => RelationBehaviorFixture::class,
        'nameFrom' => 'name',
        'sortMode' => 'simple',
    ];

    /**
     * Only the records of the parent this page is for.
     */
    public function reorderExtendQuery($query)
    {
        $query->where('parent_id', 1);
    }
}

/**
 * A scope that joins a table carrying its own `id` column.
 */
class JoinedScopeReorderController extends ScopedReorderController
{
    public function reorderExtendQuery($query)
    {
        $query
            ->join('backend_users', 'backend_users.id', '=', 'backend_test_relation_behavior_fixtures.parent_id')
            ->where('backend_test_relation_behavior_fixtures.parent_id', 1);
    }
}

class ReorderControllerScopingTest extends BackendTestCase
{
    protected RelationBehaviorFixture $offered;
    protected RelationBehaviorFixture $hidden;

    public function setUp(): void
    {
        parent::setUp();

        RelationBehaviorFixture::migrateUp();

        $this->offered = RelationBehaviorFixture::create(['name' => 'offered', 'parent_id' => 1, 'sort_order' => 1]);
        $this->hidden = RelationBehaviorFixture::create(['name' => 'hidden', 'parent_id' => 2, 'sort_order' => 1]);
    }

    public function tearDown(): void
    {
        RelationBehaviorFixture::migrateDown();

        parent::tearDown();
    }

    public function testAnOfferedRecordIsStillReordered(): void
    {
        $this->postData(['record_ids' => [$this->offered->id], 'sort_orders' => [9]]);

        (new ScopedReorderController)->onReorder();

        $this->assertEquals(9, $this->offered->fresh()->sort_order);
    }

    public function testARecordTheQueryDoesNotOfferIsNotReordered(): void
    {
        $this->postData(['record_ids' => [$this->hidden->id], 'sort_orders' => [9]]);

        (new ScopedReorderController)->onReorder();

        $this->assertEquals(1, $this->hidden->fresh()->sort_order, 'a record outside the scope must not move');
    }

    /**
     * A scope may join another table that also has an `id`, so the key the offered set is read
     * back with has to be qualified or the query is ambiguous.
     */
    public function testAScopeThatJoinsAnotherTableStillReorders(): void
    {
        $this->postData(['record_ids' => [$this->offered->id], 'sort_orders' => [4]]);

        (new JoinedScopeReorderController)->onReorder();

        $this->assertEquals(4, $this->offered->fresh()->sort_order);
    }

    /**
     * The scoped query selects only the reordered model's columns, so a joined table's own `id`
     * cannot be hydrated onto the record and send a move to the wrong row.
     */
    public function testAJoinedTableDoesNotOverwriteTheRecordKey(): void
    {
        $query = self::callProtectedMethod(
            (new JoinedScopeReorderController)->asExtension(\Backend\Behaviors\ReorderController::class),
            'reorderScopedQuery'
        );

        $this->assertSame(
            [$this->offered->getTable() . '.*'],
            $query->getQuery()->columns,
            'the scoped query must select only the reordered model'
        );
        $this->assertEquals($this->offered->id, $query->find($this->offered->id)->id);
    }

    public function testAnOfferedRecordStillMovesWhenSentBesideOneThatIsNot(): void
    {
        $this->postData([
            'record_ids' => [$this->offered->id, $this->hidden->id],
            'sort_orders' => [7, 9],
        ]);

        (new ScopedReorderController)->onReorder();

        $this->assertEquals(7, $this->offered->fresh()->sort_order);
        $this->assertEquals(1, $this->hidden->fresh()->sort_order);
    }
}
