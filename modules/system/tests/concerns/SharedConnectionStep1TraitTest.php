<?php

namespace System\Tests\Concerns;

use Db;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * Step 1 of 3: a test using the trait writes to the shared connection.
 *
 * The three `SharedConnectionStep*` classes run in sequence to pin the one invariant none of them
 * can see on its own — that a class which does not use the trait sits in the middle of a suite of
 * classes that do, without being polluted by them or polluting the connection they share. Steps 2
 * and 3 declare `@depends` on the step before, so PHPUnit enforces the order instead of it resting
 * on the order files happen to be discovered in.
 *
 * @package winter/wn-system-module
 */
class SharedConnectionStep1TraitTest extends PluginTestCase
{
    use MigratesDatabaseOnce;

    public function testATestUsingTheTraitWritesOnTheSharedConnection(): void
    {
        $this->assertSame(TestDatabaseState::$pdo, Db::connection()->getPdo());
        $this->assertSame(0, Db::table('system_request_logs')->count());

        Db::table('system_request_logs')->insert(['url' => '/step-1', 'status_code' => 404]);

        $this->assertSame(1, Db::table('system_request_logs')->count());
    }
}
