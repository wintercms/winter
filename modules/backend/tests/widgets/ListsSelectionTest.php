<?php

namespace Backend\Tests\Widgets;

use Backend\Tests\Fixtures\Models\ListSelectionFixture;
use Backend\Tests\Fixtures\Models\SortableFixture;
use Backend\Tests\Fixtures\Models\UserFixture;
use Backend\Widgets\Lists;
use Illuminate\Http\Request as HttpRequest;
use Winter\Storm\Support\Facades\DB;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Exception\ApplicationException;

/**
 * Coverage for "select all records matching the current query".
 *
 * A whole-query selection never travels over the wire as ids: the client posts a flag and a
 * fingerprint, and the widget resolves the records from the search and filter state it already
 * holds in the session. That makes two properties worth pinning down. The set must always be
 * bounded by the list's own query - an explicitly checked id outside it is dropped, and "all
 * matching" is the prepared query itself - and the fingerprint must change exactly when the
 * matched set changes, so a filter altered in another browser tab is caught while a re-sort or
 * a column being hidden is not.
 *
 * @see modules/backend/widgets/Lists.php
 */
class ListsSelectionTest extends PluginTestCase
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
        ListSelectionFixture::migrateDown();

        parent::tearDown();
    }

    protected function makeList(array $overrides = []): Lists
    {
        return new Lists(null, array_merge([
            'model' => new ListSelectionFixture,
            'alias' => 'selectionlist',
            'arrayName' => 'array',
            'recordsPerPage' => 10,
            'showCheckboxes' => true,
            'selectAllMatching' => true,
            'columns' => [
                'name' => ['type' => 'text', 'label' => 'Name', 'searchable' => true],
                'category' => ['type' => 'text', 'label' => 'Category'],
            ],
        ], $overrides));
    }

    protected function postRequest(array $data): void
    {
        $request = HttpRequest::create('/', 'POST', $data);
        $this->app->instance('request', $request);
        \Request::swap($request);
    }

    protected function keysFor(string $category, int $limit = 0): array
    {
        $query = ListSelectionFixture::where('category', $category)->orderBy('id');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->pluck('id')->all();
    }

    //
    // Page selection
    //

    public function testPageModeReturnsOnlyCheckedIds(): void
    {
        $checked = $this->keysFor('alpha', 3);

        $list = $this->makeList();
        $this->postRequest(['checked' => $checked]);

        $this->assertEqualsCanonicalizing($checked, $list->getSelectedKeys());
    }

    public function testPageModeDropsIdsOutsideTheActiveQuery(): void
    {
        $alpha = $this->keysFor('alpha', 1)[0];
        $beta = $this->keysFor('beta', 1)[0];

        $list = $this->makeList();
        $list->addFilter(function ($query) {
            $query->where('category', 'alpha');
        });

        $this->postRequest(['checked' => [$alpha, $beta]]);

        // The beta record is checked but not in the filtered list, so it is not selected.
        $this->assertSame([$alpha], $list->getSelectedKeys());
    }

    public function testEmptyPageSelectionMatchesNothing(): void
    {
        $list = $this->makeList();
        $this->postRequest([]);

        $this->assertSame([], $list->getSelectedKeys());
        $this->assertSame(0, $list->getSelectionQuery()->count());
    }

    //
    // Whole-query selection
    //

    public function testQueryModeReturnsEveryMatchingRecordAcrossPages(): void
    {
        $list = $this->makeList();

        $this->postRequest([
            'checked' => $this->keysFor('alpha', 10),
            'checked_all' => 1,
            'checked_fingerprint' => $list->getSelectionFingerprint(),
        ]);

        // 30 records, 10 to a page: the selection is not what the page could show.
        $this->assertCount(30, $list->getSelectedKeys());
    }

    public function testQueryModeHonoursFilterAndSearch(): void
    {
        $list = $this->makeList();
        $list->addFilter(function ($query) {
            $query->where('category', 'alpha');
        });
        $list->setSearchTerm('record 001');

        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $list->getSelectionFingerprint(),
        ]);

        $keys = $list->getSelectedKeys();

        $this->assertCount(1, $keys);
        $this->assertSame('alpha record 001', ListSelectionFixture::find($keys[0])->name);
    }

    public function testQueryModeRejectsStaleFingerprint(): void
    {
        $list = $this->makeList();

        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => 'a fingerprint from a query that no longer applies',
        ]);

        $this->expectException(ApplicationException::class);
        $list->getSelectedKeys();
    }

    public function testQueryModeRejectedWhenTheListDoesNotOfferIt(): void
    {
        $list = $this->makeList(['selectAllMatching' => false]);

        // A valid fingerprint, because it is computed from the same query and is not a secret.
        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $list->getSelectionFingerprint(),
        ]);

        $this->expectException(ApplicationException::class);
        $list->getSelectedKeys();
    }

    public function testQueryModeRejectedWithoutCheckboxes(): void
    {
        $list = $this->makeList(['showCheckboxes' => false]);

        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $list->getSelectionFingerprint(),
        ]);

        $this->expectException(ApplicationException::class);
        $list->getSelectedKeys();
    }

    public function testSelectedKeysAreReadWithoutLoadingWholeRows(): void
    {
        $list = $this->makeList();

        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $list->getSelectionFingerprint(),
        ]);

        DB::enableQueryLog();
        $keys = $list->getSelectedKeys();
        $log = DB::getQueryLog();
        DB::disableQueryLog();

        $sql = end($log)['query'];

        $this->assertCount(30, $keys);
        $this->assertStringContainsString('id', $sql);
        $this->assertStringNotContainsString(
            '*',
            $sql,
            'the keys should be selected on their own, not read off whole rows'
        );
    }

    public function testSelectionQueryIsUnordered(): void
    {
        // chunkById() pages with `key > last` but keeps any other ORDER BY in place, and that
        // combination silently skips records, so the selection query must carry no ordering.
        $list = $this->makeList(['defaultSort' => ['column' => 'name', 'direction' => 'desc']]);
        $this->postRequest(['checked' => $this->keysFor('alpha')]);

        $this->assertNotEmpty($list->prepareQuery()->toBase()->orders, 'the list itself is sorted');
        $this->assertEmpty($list->getSelectionQuery()->toBase()->orders);
    }

    public function testSelectionResolvesThroughAJoinedQuery(): void
    {
        // A filter scope may join another table, which makes an unqualified key ambiguous.
        $checked = $this->keysFor('alpha', 2);

        $list = $this->makeList();
        $list->addFilter(function ($query) {
            $query->leftJoin(
                'backend_test_selection_fixtures as joined',
                'joined.id',
                '=',
                'backend_test_selection_fixtures.id'
            );
        });

        $this->postRequest(['checked' => $checked]);

        $this->assertEqualsCanonicalizing($checked, $list->getSelectedKeys());
    }

    public function testSelectedKeysAreUniqueThroughAOneToManyJoin(): void
    {
        // A filter scope may join one-to-many, which returns each record once per joined row.
        $list = $this->makeList();
        $list->addFilter(function ($query) {
            $query
                ->where('backend_test_selection_fixtures.category', 'alpha')
                ->leftJoin(
                    'backend_test_selection_fixtures as duplicates',
                    'duplicates.category',
                    '=',
                    'backend_test_selection_fixtures.category'
                );
        });

        $this->postRequest([
            'checked_all' => 1,
            'checked_fingerprint' => $list->getSelectionFingerprint(),
        ]);

        $keys = $list->getSelectedKeys();

        // 15 records joined to 15 rows each: 225 rows, 15 records.
        $this->assertSame(225, $list->getSelectionQuery()->count());
        $this->assertCount(15, $keys);
        $this->assertSame($keys, array_unique($keys));
    }

    public function testTheKeyComesFromTheQueryThatReplacedThePreparedOne(): void
    {
        /*
         * The backend.list.extendQuery event may return a replacement query, and its docblock
         * shows one built from a different model. Qualifying the key with the widget's own
         * model would then filter on a table the query does not select from.
         */
        SortableFixture::migrateUp();

        try {
            $list = $this->makeList();
            $list->bindEvent('list.extendQuery', function () {
                return SortableFixture::query();
            });

            $this->postRequest(['checked' => [1]]);

            $this->assertStringContainsString(
                'backend_test_sortable_fixtures"."id" in',
                $list->getSelectionQuery()->toSql()
            );
        } finally {
            SortableFixture::migrateDown();
        }
    }

    //
    // Fingerprint
    //

    public function testFingerprintIgnoresSortDirection(): void
    {
        $ascending = $this->makeList(['defaultSort' => ['column' => 'name', 'direction' => 'asc']]);
        $descending = $this->makeList(['defaultSort' => ['column' => 'name', 'direction' => 'desc']]);

        // Re-sorting does not change which records match, so it must not drop the selection.
        $this->assertSame(
            $ascending->getSelectionFingerprint(),
            $descending->getSelectionFingerprint()
        );
    }

    public function testFingerprintIgnoresVisibleColumns(): void
    {
        /*
         * Only a column with its own `select` changes the query's select list - a plain text
         * column adds nothing to it - so two plain lists would compare identical SQL and prove
         * nothing. The select list's bindings are stripped with it, which needs a polymorphic
         * relation column to exercise and is left to the browser.
         */
        $computed = $this->makeList(['columns' => [
            'name' => ['type' => 'text', 'label' => 'Name', 'select' => 'upper(name)'],
        ]]);
        $plain = $this->makeList(['columns' => [
            'name' => ['type' => 'text', 'label' => 'Name'],
        ]]);

        $this->assertStringContainsString(
            'upper(name)',
            $computed->prepareQuery()->toSql(),
            'the two lists must really select different things'
        );

        // Hiding a column through the list setup popup changes the select list, not the set.
        $this->assertSame($computed->getSelectionFingerprint(), $plain->getSelectionFingerprint());
    }

    public function testFingerprintKeepsTheSelectListWhenAHavingDependsOnIt(): void
    {
        /*
         * A WHERE cannot reference a select alias, so the select list is normally presentation
         * and is left out of the hash. A HAVING added by a query extension can reference one,
         * and then two lists that select different expressions under the same alias match
         * different records - which the fingerprint has to notice.
         */
        $fingerprintFor = function (string $select) {
            $list = $this->makeList(['columns' => [
                'name' => ['type' => 'text', 'label' => 'Name', 'select' => $select],
            ]]);

            $list->addFilter(function ($query) {
                $query->groupBy('backend_test_selection_fixtures.id')->havingRaw('name is not null');
            });

            return $list->getSelectionFingerprint();
        };

        $this->assertNotSame($fingerprintFor('length(name)'), $fingerprintFor('length(category)'));
    }

    public function testFingerprintChangesWithSearchTerm(): void
    {
        $unfiltered = $this->makeList()->getSelectionFingerprint();

        $searched = $this->makeList();
        $searched->setSearchTerm('alpha');

        $this->assertNotSame($unfiltered, $searched->getSelectionFingerprint());
    }

    public function testFingerprintChangesWithFilterScope(): void
    {
        $unfiltered = $this->makeList()->getSelectionFingerprint();

        $filtered = $this->makeList();
        $filtered->addFilter(function ($query) {
            $query->where('category', 'alpha');
        });

        $this->assertNotSame($unfiltered, $filtered->getSelectionFingerprint());
    }

    //
    // The banner is only offered where it makes sense
    //

    public function testBannerIsOfferedWhenRecordsExistBeyondThePage(): void
    {
        $list = $this->makeList();
        $list->render();

        $this->assertTrue($list->vars['showSelectAll']);
        $this->assertSame(30, $list->vars['selectionTotal']);
        $this->assertNotEmpty($list->vars['selectionFingerprint']);
    }

    public function testBannerIsNotOfferedForASinglePage(): void
    {
        $list = $this->makeList(['recordsPerPage' => 50]);
        $list->render();

        $this->assertFalse($list->vars['showSelectAll']);
    }

    public function testBannerIsNotOfferedWhenDisabledOrWithoutCheckboxes(): void
    {
        $disabled = $this->makeList(['selectAllMatching' => false]);
        $disabled->render();
        $this->assertFalse($disabled->vars['showSelectAll']);

        $unchecked = $this->makeList(['showCheckboxes' => false]);
        $unchecked->render();
        $this->assertFalse($unchecked->vars['showSelectAll']);
    }

    public function testBannerIsOfferedOnTheLastPageOfASimplePaginatedList(): void
    {
        // simplePaginate() has no total, so the offer comes from the page position instead -
        // and the last page, where hasMorePages() is false, still has records behind it.
        $this->postRequest(['page' => 3]);

        $list = $this->makeList(['showPageNumbers' => false]);
        $list->onPaginate();

        $this->assertTrue($list->vars['showSelectAll']);
        $this->assertNull($list->vars['selectionTotal']);
    }
}
