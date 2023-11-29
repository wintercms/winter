<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbBackendAddUserMetadata extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('backend_users', 'metadata')) {
            Schema::table('backend_users', function (Blueprint $table) {
                $table->mediumText('metadata')->nullable()->after('permissions');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('backend_users', 'metadata')) {
            Schema::table('backend_users', function (Blueprint $table) {
                $table->dropColumn('metadata');
            });
        }
    }
}
