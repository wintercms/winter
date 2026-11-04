<?php

namespace System\Tests\Concerns;

use Db;
use Schema;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * Step 3 of 3: the shared connection comes back intact after a test that did not use the trait.
 *
 * The row count covers both halves of that at once, because steps 1 and 2 wrote to the same table:
 * step 1's row was rolled back, and step 2's never reached this connection. See
 * `SharedConnectionStep1TraitTest` for the whole sequence.
 *
 * @package winter/wn-system-module
 */
class SharedConnectionStep3TraitAgainTest extends PluginTestCase
{
    use MigratesDatabaseOnce;

    /**
     * @depends System\Tests\Concerns\SharedConnectionStep2WithoutTraitTest::testATestWithoutTheTraitGetsAMigratedDatabaseOfItsOwn
     */
    public function testTheSharedConnectionSurvivesATestThatDidNotUseTheTrait(): void
    {
        $this->assertSame(TestDatabaseState::$pdo, Db::connection()->getPdo());
        $this->assertSame(1, Db::connection()->transactionLevel());

        $this->assertTrue(Schema::hasTable('backend_users'));
        $this->assertSame(
            0,
            Db::table('system_request_logs')->count(),
            'a row written by one of the two tests before this one is still visible'
        );
    }
}
