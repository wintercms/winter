<?php

namespace System\Tests\Concerns;

use Db;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * The trait's contract is that a test sees the migrated schema and nothing another test left behind.
 *
 * The pairs below each assert their table or rows are absent before adding them, so whichever of
 * the pair runs second is the one that catches a failed rollback.
 *
 * @package winter/wn-system-module
 */
class MigratesDatabaseOnceTest extends PluginTestCase
{
    use MigratesDatabaseOnce;

    /**
     * @var \PDO|null The connection the first test in this class ran on.
     */
    protected static $firstSeenPdo = null;

    public function testTheTestRunsOnTheProcessWideConnectionInsideATransaction(): void
    {
        $connection = $this->app['db']->connection();

        $this->assertNotNull(TestDatabaseState::$pdo);
        $this->assertSame(TestDatabaseState::$pdo, $connection->getPdo());
        $this->assertSame(1, $connection->transactionLevel());

        static::$firstSeenPdo = $connection->getPdo();
    }

    public function testTheTestAfterThatReusesTheSameConnection(): void
    {
        $this->assertSame(
            static::$firstSeenPdo,
            $this->app['db']->connection()->getPdo(),
            'the database was migrated again instead of the first test\'s connection being reused'
        );
    }

    public function testTheMigratedSchemaIsPresent(): void
    {
        $this->assertTrue(Schema::hasTable('system_settings'));
        $this->assertTrue(Schema::hasTable('system_request_logs'));
        $this->assertTrue(Schema::hasTable('backend_users'));
    }

    public function testRowsAddedByATestDoNotOutliveIt(): void
    {
        $this->assertSame(0, Db::table('system_request_logs')->count());

        Db::table('system_request_logs')->insert(['url' => '/first', 'status_code' => 404]);

        $this->assertSame(1, Db::table('system_request_logs')->count());
    }

    public function testRowsAddedByTheTestBeforeThisOneAreGone(): void
    {
        $this->assertSame(0, Db::table('system_request_logs')->count());

        Db::table('system_request_logs')->insert(['url' => '/second', 'status_code' => 404]);

        $this->assertSame(1, Db::table('system_request_logs')->count());
    }

    public function testTablesCreatedByATestDoNotOutliveIt(): void
    {
        $this->assertFalse(Schema::hasTable('migrates_once_scratch'));

        Schema::create('migrates_once_scratch', function (Blueprint $table) {
            $table->increments('id');
        });
        Db::table('migrates_once_scratch')->insert(['id' => 1]);

        $this->assertTrue(Schema::hasTable('migrates_once_scratch'));
    }

    public function testTheTableTheTestBeforeThisOneCreatedIsGone(): void
    {
        $this->assertFalse(Schema::hasTable('migrates_once_scratch'));
    }
}
