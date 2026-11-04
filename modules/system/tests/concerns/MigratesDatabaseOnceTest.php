<?php

namespace System\Tests\Concerns;

use Db;
use Illuminate\Database\Schema\Blueprint;
use PDO;
use RuntimeException;
use Schema;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * The trait's contract is that a test sees the migrated schema and nothing another test left behind.
 *
 * Reusing a connection and rolling a test back are both claims about two consecutive tests, so the
 * tests that make them declare `@depends` on the test that sets them up. PHPUnit then enforces the
 * order and skips rather than failing when only one of a pair is selected.
 *
 * @package winter/wn-system-module
 */
class MigratesDatabaseOnceTest extends PluginTestCase
{
    use MigratesDatabaseOnce;

    public function testTheTestRunsOnTheProcessWideConnectionInsideATransaction(): PDO
    {
        $connection = $this->app['db']->connection();

        $this->assertNotNull(TestDatabaseState::$pdo);
        $this->assertSame(TestDatabaseState::$pdo, $connection->getPdo());
        $this->assertSame(1, $connection->transactionLevel());

        return $connection->getPdo();
    }

    /**
     * @depends testTheTestRunsOnTheProcessWideConnectionInsideATransaction
     */
    public function testTheTestAfterThatReusesTheSameConnection(PDO $connectionTheTestBeforeRanOn): void
    {
        $this->assertSame(
            $connectionTheTestBeforeRanOn,
            $this->app['db']->connection()->getPdo(),
            'the database was migrated again instead of the previous test\'s connection being reused'
        );
    }

    public function testLosingTheWrappingTransactionIsReportedLoudly(): void
    {
        // A second connection, so the one this test will be rolled back on stays untouched.
        config(['database.connections.migrates_once_probe' => ['driver' => 'sqlite', 'database' => ':memory:']]);
        $untransacted = $this->app['db']->connection('migrates_once_probe');

        $shared = TestDatabaseState::$pdo;
        $reported = null;

        try {
            $this->assertWrappingTransactionSurvived($untransacted);
        } catch (RuntimeException $exception) {
            $reported = $exception->getMessage();
        } finally {
            TestDatabaseState::$pdo = $shared;
        }

        $this->assertNotNull($reported, 'a connection with no wrapping transaction was accepted');
        $this->assertStringContainsString('left the database outside the transaction', $reported);
        $this->assertStringContainsString(static::class, $reported);
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

    /**
     * @depends testRowsAddedByATestDoNotOutliveIt
     */
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

    /**
     * @depends testTablesCreatedByATestDoNotOutliveIt
     */
    public function testTheTableTheTestBeforeThisOneCreatedIsGone(): void
    {
        $this->assertFalse(Schema::hasTable('migrates_once_scratch'));
    }
}
