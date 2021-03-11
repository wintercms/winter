<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbSystemAddFrozenFlag extends Migration
{
    public function up()
    {
        Schema::table('system_plugin_versions', function (Blueprint $table) {
            $table->boolean('is_frozen')->default(0);
        });
    }

    public function down()
    {
        Schema::table('system_plugin_versions', function (Blueprint $table) {
            $table->dropColumn('is_frozen');
        });
    }
}
