<?php

namespace System\Tests\Concerns;

use PDO;

/**
 * Process-wide state for `MigratesDatabaseOnce`.
 *
 * Each test builds a fresh application, and with it a fresh database connection. Holding the PDO
 * here keeps the `:memory:` database — which only lives as long as its connection — alive across
 * those rebuilds, so the schema is migrated once rather than once per test.
 *
 * @package winter/wn-system-module
 */
class TestDatabaseState
{
    /**
     * @var PDO|null The connection the test database was migrated on.
     */
    public static $pdo = null;
}
