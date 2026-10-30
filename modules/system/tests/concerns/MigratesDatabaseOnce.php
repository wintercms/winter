<?php

namespace System\Tests\Concerns;

use Artisan;

/**
 * Migrates the test database once per process instead of once per test.
 *
 * `PluginTestCase` runs `winter:up` in every `setUp()`, which rebuilds the whole schema from empty
 * because each test gets a new application and therefore a new `:memory:` database. This trait
 * migrates on the first test, keeps that connection alive for the rest of the process, and wraps
 * every test in a transaction that is rolled back afterwards — so each test still starts from the
 * freshly migrated schema with no rows of its own.
 *
 * Opt in from a test class:
 *
 * ```php
 * class MyTest extends PluginTestCase
 * {
 *     use \System\Tests\Concerns\MigratesDatabaseOnce;
 * }
 * ```
 *
 * Do not use it when a test commits its own transactions, reconnects the database, or issues DDL
 * on a driver that commits implicitly (MySQL does; SQLite does not). Those escape the wrapping
 * transaction and leak state into the tests that follow.
 *
 * @package winter/wn-system-module
 */
trait MigratesDatabaseOnce
{
    /**
     * Migrates on first use, then reuses that database under a transaction.
     */
    protected function runTestMigrations(): void
    {
        $connection = $this->app['db']->connection();

        if (is_null(TestDatabaseState::$pdo)) {
            Artisan::call('winter:up');
            TestDatabaseState::$pdo = $connection->getPdo();
        } else {
            $connection->setPdo(TestDatabaseState::$pdo);
        }

        $this->withoutConnectionEvents($connection, fn () => $connection->beginTransaction());

        $this->beforeApplicationDestroyed(function () use ($connection) {
            $this->withoutConnectionEvents($connection, fn () => $connection->rollBack());
        });
    }

    /**
     * Runs a transaction control statement without announcing it to listeners.
     *
     * Mirrors Laravel's own `DatabaseTransactions`: the wrapping transaction is test scaffolding,
     * not something the code under test should be able to observe.
     */
    protected function withoutConnectionEvents($connection, callable $callback): void
    {
        $dispatcher = $connection->getEventDispatcher();

        $connection->unsetEventDispatcher();
        $callback();
        $connection->setEventDispatcher($dispatcher);
    }
}
