<?php

namespace Backend\Tests\Behaviors;

use Backend\Classes\Controller;
use Backend\Tests\Fixtures\Models\ListSelectionFixture;
use Backend\Tests\Fixtures\Models\UserFixture;
use Illuminate\Http\Request as HttpRequest;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\Flash;

/**
 * Coverage for bulk deletion resolving through the list's selection.
 *
 * `index_onDelete()` used to rebuild its own query from the model and delete whatever ids the
 * client posted. It now asks the list widget for the selection, which means the active search
 * and filters apply, and a "select all matching" selection can delete records that were never
 * on screen. Two things therefore have to hold no matter what the client sends: the set stays
 * inside the list's own query, and every matching record is actually deleted - the chunked walk
 * is the part most likely to quietly drop records.
 *
 * @see modules/backend/behaviors/ListController.php
 */
class SelectionController extends Controller
{
    public $implement = [
        \Backend\Behaviors\ListController::class,
    ];

    public $listConfig = [
        'list' => [
            'modelClass' => ListSelectionFixture::class,
            'recordsPerPage' => 10,
            'showCheckboxes' => true,
            'selectAllMatching' => true,
            // Sorted by something other than the key: chunkById() pages by key, and leaving
            // this ORDER BY in place would make it skip records.
            'defaultSort' => ['column' => 'name', 'direction' => 'desc'],
            'list' => [
                'columns' => [
                    'name' => ['type' => 'text', 'label' => 'Name', 'searchable' => true],
                    'category' => ['type' => 'text', 'label' => 'Category'],
                ],
            ],
        ],
        'archive' => [
            'modelClass' => ListSelectionFixture::class,
            'recordsPerPage' => 10,
            'showCheckboxes' => true,
            'list' => [
                'columns' => [
                    'name' => ['type' => 'text', 'label' => 'Name'],
                ],
            ],
        ],
    ];

    /**
     * The primary list is a subset: a permission-scoped list is the shape that must never be
     * escaped, whatever the client asks for.
     */
    public function listExtendQuery($query, $definition = null)
    {
        if ($definition === 'list') {
            $query->where('category', 'alpha');
        }
    }
}

/**
 * Leaves `selectAllMatching` at its default, to prove the default is off.
 */
class SelectionDefaultController extends SelectionController
{
    public $listConfig = [
        'list' => [
            'modelClass' => ListSelectionFixture::class,
            'recordsPerPage' => 10,
            'showCheckboxes' => true,
            'list' => [
                'columns' => [
                    'name' => ['type' => 'text', 'label' => 'Name'],
                ],
            ],
        ],
    ];

    public function listExtendQuery($query, $definition = null)
    {
    }
}

class ListControllerSelectionTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        ListSelectionFixture::migrateUp();
        ListSelectionFixture::seed();

        $this->actingAs((new UserFixture)->asSuperUser());
    }

    public function tearDown(): void
    {
        // Static listeners bound by a test would otherwise leak into the next one.
        ListSelectionFixture::flushEventListeners();
        ListSelectionFixture::migrateDown();

        parent::tearDown();
    }

    protected function postRequest(array $data): void
    {
        $request = HttpRequest::create('/', 'POST', $data);
        $this->app->instance('request', $request);
        \Request::swap($request);
    }

    /**
     * The fingerprint as a separate request would compute it, which is the point: it has to
     * match across requests, not just within one controller instance.
     */
    protected function fingerprint(?string $definition = null): string
    {
        $controller = new SelectionController;
        $controller->makeLists();

        return $controller->listGetWidget($definition)->getSelectionFingerprint();
    }

    public function testQueryModeDeletesEveryMatchingRecord(): void
    {
        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $this->fingerprint(),
        ]);

        (new SelectionController)->index_onDelete();

        // Every alpha record is gone, including the 5 that were never on the first page ...
        $this->assertSame(0, ListSelectionFixture::where('category', 'alpha')->count());
        // ... and the records the list's own scope excludes are untouched.
        $this->assertSame(15, ListSelectionFixture::where('category', 'beta')->count());
    }

    public function testQueryModeDeletesAcrossChunksWhenSortedByANonKeyColumn(): void
    {
        // More records than one chunk, ordered by name rather than by key. chunkById() only
        // drops existing orders for the key column, so without reorder() the keyset walk
        // leaves records behind - silently, with a smaller count in the flash.
        ListSelectionFixture::truncate();
        ListSelectionFixture::seed(600, ['alpha']);

        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $this->fingerprint(),
        ]);

        (new SelectionController)->index_onDelete();

        $this->assertSame(0, ListSelectionFixture::count());
    }

    public function testStaleFingerprintDeletesNothing(): void
    {
        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => 'a fingerprint from a query that no longer applies',
        ]);

        try {
            (new SelectionController)->index_onDelete();
            $this->fail('a stale selection should not be acted on');
        } catch (ApplicationException $ex) {
            // expected
        }

        $this->assertSame(30, ListSelectionFixture::count());
    }

    public function testWholeQuerySelectionIsRefusedWhenTheListDoesNotOfferIt(): void
    {
        $controller = new SelectionDefaultController;
        $controller->makeLists();

        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $controller->listGetWidget()->getSelectionFingerprint(),
        ]);

        try {
            (new SelectionDefaultController)->index_onDelete();
            $this->fail('a list that does not offer whole-query selection should refuse it');
        } catch (ApplicationException $ex) {
            // expected
        }

        $this->assertSame(30, ListSelectionFixture::count());
    }

    public function testPageModeStillDeletesCheckedIds(): void
    {
        $checked = ListSelectionFixture::where('category', 'alpha')->limit(2)->pluck('id')->all();

        $this->postRequest(['checked' => $checked]);

        (new SelectionController)->index_onDelete();

        $this->assertSame(13, ListSelectionFixture::where('category', 'alpha')->count());
        $this->assertSame(15, ListSelectionFixture::where('category', 'beta')->count());
    }

    public function testPageModeDropsIdsOutsideTheListScope(): void
    {
        $beta = ListSelectionFixture::where('category', 'beta')->limit(1)->pluck('id')->all();

        $this->postRequest(['checked' => $beta]);

        (new SelectionController)->index_onDelete();

        // The primary list only shows alpha records, so a checked beta id is not deletable.
        $this->assertSame(30, ListSelectionFixture::count());
    }

    public function testASecondaryDefinitionResolvesItsOwnSelection(): void
    {
        $this->postRequest([
            'definition' => 'archive',
            'checked' => ListSelectionFixture::where('category', 'beta')->limit(1)->pluck('id')->all(),
        ]);

        (new SelectionController)->index_onDelete();

        // The archive list is not scoped to alpha, so the beta record is in range there.
        $this->assertSame(14, ListSelectionFixture::where('category', 'beta')->count());
    }

    public function testUnknownDefinitionThrows(): void
    {
        $this->postRequest(['definition' => 'nope', 'checked' => [1]]);

        // Caught by the handler's own definition check, before a widget is ever resolved.
        $this->expectException(ApplicationException::class);
        (new SelectionController)->index_onDelete();
    }

    public function testTheAccessorsRejectAnUnknownDefinition(): void
    {
        // The accessors are public API, so a plugin can reach them with any definition name
        // without going through the handler that validates it first.
        $this->postRequest(['checked' => [1]]);

        foreach (['listGetSelectionQuery', 'listGetSelectedIds'] as $method) {
            try {
                (new SelectionController)->$method('nope');
                $this->fail($method . '() should reject an unknown definition');
            } catch (ApplicationException $ex) {
                $this->assertStringContainsString('nope', $ex->getMessage());
            }
        }
    }

    public function testDeletingFiresModelEventsPerRecord(): void
    {
        $deleting = [];
        ListSelectionFixture::deleting(function ($record) use (&$deleting) {
            $deleting[] = $record->getKey();
        });

        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $this->fingerprint(),
        ]);

        (new SelectionController)->index_onDelete();

        // Deleted one record at a time, so model events and cascades still run.
        $this->assertCount(15, $deleting);
    }

    public function testAVetoedDeleteIsNotCounted(): void
    {
        ListSelectionFixture::deleting(function ($record) {
            if ($record->name === 'alpha record 001') {
                return false;
            }
        });

        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $this->fingerprint(),
        ]);

        (new SelectionController)->index_onDelete();

        $this->assertSame(1, ListSelectionFixture::where('category', 'alpha')->count());
        $this->assertStringContainsString('14', Flash::get('success')[0] ?? '');
    }

    public function testTheConfigKeyReachesTheWidget(): void
    {
        // The behavior copies a fixed whitelist of keys into the widget config, so a list
        // option that is not in it silently does nothing.
        $enabled = new SelectionController;
        $enabled->makeLists();
        $widget = $enabled->listGetWidget();
        $widget->render();
        $this->assertTrue($widget->vars['showSelectAll']);

        $default = new SelectionDefaultController;
        $default->makeLists();
        $widget = $default->listGetWidget();
        $widget->render();
        $this->assertFalse($widget->vars['showSelectAll'], 'the feature is opt-in');
    }

    public function testCoreLogControllersNoLongerDuplicateTheHandler(): void
    {
        foreach ([
            \System\Controllers\EventLogs::class,
            \System\Controllers\RequestLogs::class,
            \Cms\Controllers\ThemeLogs::class,
        ] as $class) {
            $this->assertFalse(
                (new \ReflectionClass($class))->hasMethod('index_onDelete'),
                $class . ' should inherit the behavior handler, not re-implement it'
            );
        }
    }
}
