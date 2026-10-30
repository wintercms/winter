<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbBackendAddResetPasswordCreatedAt extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('backend_users', 'reset_password_created_at')) {
            Schema::table('backend_users', function (Blueprint $table) {
                $table->timestamp('reset_password_created_at')->nullable()->after('reset_password_code');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('backend_users', 'reset_password_created_at')) {
            Schema::table('backend_users', function (Blueprint $table) {
                $table->dropColumn('reset_password_created_at');
            });
        }
    }
}
