<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbBackendAddUsersGroupsDeleteAt extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('backend_users_groups', 'deleted_at')) {
            Schema::table('backend_users_groups', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable()->after('user_group_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('backend_users_groups', 'deleted_at')) {
            Schema::table('backend_users_groups', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
}
