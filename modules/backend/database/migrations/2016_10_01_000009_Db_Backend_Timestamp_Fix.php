<?php

use Winter\Storm\Database\Updates\Migration;

/**
 * This migration addresses a MySQL specific issue around STRICT MODE.
 * In past versions, Laravel would give timestamps a bad default value
 * of "0" considered invalid by MySQL. Strict mode is disabled and the
 * the timestamps are patched up. Credit for this work: Dave Shoreman.
 */
class DbBackendTimestampFix extends Migration
{
    protected $backendTables = [
        'backend_users',
        'backend_user_groups',
        'backend_access_log',
    ];

    public function up()
    {
        DbDongle::disableStrictMode();

        foreach ($this->backendTables as $table) {
            DbDongle::convertTimestamps($table);
        }

        // Use this opportunity to reset backend preferences for stable
        Db::table('backend_user_preferences')
            ->where('namespace', 'backend')
            ->where('group', 'backend')
            ->where('item', 'preferences')
            ->delete();
    }

    public function down()
    {
        // ...
    }
}
