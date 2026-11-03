<?php

namespace System\Tests\Models;

use System\Models\LogSetting;
use System\Models\RequestLog;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Support\Facades\DB;

class RequestLogTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        LogSetting::set('log_requests', true);
    }

    /**
     * Returns the names of the indexes defined on the provided table.
     */
    protected function getIndexNames(string $table): array
    {
        $connection = DB::connection();

        switch ($connection->getDriverName()) {
            case 'sqlite':
                $indexes = $connection->select('PRAGMA index_list(' . $connection->getTablePrefix() . $table . ')');
                return array_column(array_map(fn ($row) => (array) $row, $indexes), 'name');

            case 'mysql':
                $indexes = $connection->select('SHOW INDEX FROM ' . $connection->getTablePrefix() . $table);
                return array_column(array_map(fn ($row) => (array) $row, $indexes), 'Key_name');

            case 'pgsql':
                $indexes = $connection->select(
                    'SELECT indexname AS name FROM pg_indexes WHERE tablename = ?',
                    [$connection->getTablePrefix() . $table]
                );
                return array_column(array_map(fn ($row) => (array) $row, $indexes), 'name');
        }

        $this->markTestSkipped('Unsupported database driver: ' . $connection->getDriverName());
    }

    public function testUrlAndStatusCodeAreIndexed()
    {
        // Without this index every logged request performs a full table scan of a table
        // that only ever grows
        $this->assertContains(
            'system_request_logs_url_status_code_index',
            $this->getIndexNames('system_request_logs')
        );
    }

    public function testAddCreatesRecord()
    {
        $record = RequestLog::add(404);

        $this->assertNotNull($record);
        $this->assertEquals(1, $record->count);
        $this->assertEquals(404, $record->status_code);
        $this->assertEquals(1, RequestLog::count());
    }

    public function testAddIncrementsExistingRecord()
    {
        RequestLog::add(404);
        RequestLog::add(404);
        RequestLog::add(404);

        $this->assertEquals(1, RequestLog::count());
        $this->assertEquals(3, RequestLog::first()->count);
    }

    public function testAddSeparatesStatusCodes()
    {
        RequestLog::add(404);
        RequestLog::add(500);

        $this->assertEquals(2, RequestLog::count());
    }

    public function testAddRespectsLogRequestsSetting()
    {
        LogSetting::set('log_requests', false);

        $this->assertNull(RequestLog::add(404));
        $this->assertEquals(0, RequestLog::count());
    }
}
