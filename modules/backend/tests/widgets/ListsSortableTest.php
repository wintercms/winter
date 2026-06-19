<?php

namespace Backend\Tests\Widgets;

use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Exception\ApplicationException;
use Backend\Tests\Fixtures\Models\UserFixture;
use Backend\Tests\Fixtures\Models\SortableFixture;
use Backend\Widgets\Lists;
use Illuminate\Http\Request as HttpRequest;

class ListsSortableTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        SortableFixture::migrateUp();

        $this->actingAs((new UserFixture)->asSuperUser());
    }

    public function tearDown(): void
    {
        SortableFixture::migrateDown();

        parent::tearDown();
    }

    protected function makeList(array $overrides = []): Lists
    {
        return new Lists(null, array_merge([
            'model' => new SortableFixture,
            'alias' => 'testlist',
            'arrayName' => 'array',
            'sortable' => true,
            'columns' => [
                'name' => ['type' => 'text', 'label' => 'Name'],
                'label' => ['type' => 'text', 'label' => 'Label'],
            ],
        ], $overrides));
    }

    protected function seedRecords(): array
    {
        $records = [];
        foreach (['Alpha', 'Bravo', 'Charlie'] as $i => $name) {
            $records[] = SortableFixture::create([
                'name' => strtolower($name),
                'label' => $name,
                'sort_order' => $i + 1,
            ]);
        }
        return $records;
    }

    protected function postRequest(array $data): void
    {
        $request = HttpRequest::create('/', 'POST', $data);
        $this->app->instance('request', $request);
        \Request::swap($request);
    }

    public function testSortableDisablesPaginationAndColumnSorting()
    {
        $list = $this->makeList();
        $list->render();

        $this->assertFalse($list->showPagination);
        // With every column forced non-sortable, no sort column is resolved.
        $this->assertFalse($list->getSortColumn());

        foreach ($list->getColumns() as $column) {
            $this->assertFalse($column->sortable, "Column {$column->columnName} should not be sortable");
        }
    }

    public function testSortableAddsDragHandleToColumnTotal()
    {
        $sortable = $this->makeList();
        $plain = $this->makeList(['sortable' => false]);

        $method = new \ReflectionMethod(Lists::class, 'getTotalColumns');
        $method->setAccessible(true);

        $this->assertSame(
            $method->invoke($plain) + 1,
            $method->invoke($sortable),
            'Sortable list should reserve one extra column for the drag handle'
        );
    }

    public function testOnReorderGeneratesSequentialOrdersServerSide()
    {
        $records = $this->seedRecords();
        $ids = [$records[2]->id, $records[0]->id, $records[1]->id];

        $list = $this->makeList();

        $captured = null;
        $list->bindEvent('list.reorder', function ($eventIds, $eventOrders) use (&$captured) {
            $captured = [$eventIds, $eventOrders];
        });

        // The client sends only the record ids in their new order; the server assigns the
        // sort order values 1..N by position.
        $this->postRequest(['record_ids' => $ids]);
        $list->onReorder();

        $this->assertNotNull($captured, 'list.reorder event should have fired');
        $this->assertSame(array_map('strval', $ids), array_map('strval', $captured[0]));
        $this->assertSame([1, 2, 3], $captured[1]);
    }

    public function testOnReorderRejectsRecordsOutsideQueryScope()
    {
        $records = $this->seedRecords();

        $list = $this->makeList();

        // 99999 is not a seeded record id.
        $this->postRequest(['record_ids' => [$records[0]->id, 99999]]);

        $this->expectException(ApplicationException::class);
        $list->onReorder();
    }

    public function testOnReorderThrowsWhenNotSortable()
    {
        $list = $this->makeList(['sortable' => false]);
        $this->postRequest(['record_ids' => [1]]);

        $this->expectException(ApplicationException::class);
        $list->onReorder();
    }
}
