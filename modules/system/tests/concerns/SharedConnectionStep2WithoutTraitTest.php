<?php

namespace System\Tests\Concerns;

use Db;
use Schema;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * Step 2 of 3: a test that does not use the trait is unaffected by the ones that do.
 *
 * It keeps the plain `PluginTestCase` behaviour, so `winter:up` gives it a `:memory:` database of
 * its own that no other test can reach. See `SharedConnectionStep1TraitTest` for the whole sequence.
 *
 * @package winter/wn-system-module
 */
class SharedConnectionStep2WithoutTraitTest extends PluginTestCase
{
    /**
     * @depends System\Tests\Concerns\SharedConnectionStep1TraitTest::testATestUsingTheTraitWritesOnTheSharedConnection
     */
    public function testATestWithoutTheTraitGetsAMigratedDatabaseOfItsOwn(): void
    {
        $this->assertNotNull(TestDatabaseState::$pdo, 'the trait never ran, so this proves nothing');
        $this->assertNotSame(TestDatabaseState::$pdo, Db::connection()->getPdo());

        $this->assertTrue(Schema::hasTable('system_request_logs'));
        $this->assertSame(0, Db::table('system_request_logs')->count());

        // Written outside any wrapping transaction, so step 3 can show it stayed in here.
        Db::table('system_request_logs')->insert(['url' => '/step-2', 'status_code' => 404]);

        $this->assertSame(1, Db::table('system_request_logs')->count());
    }
}
