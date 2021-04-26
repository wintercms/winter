<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbAddPivotDataToDeferredBindings extends Migration
{
    public function up()
    {
        Schema::table('deferred_bindings', function (Blueprint $table) {
            $table->mediumText('pivot_data')->nullable()->after('slave_id');
        });
    }

    public function down()
    {
        Schema::table('deferred_bindings', function (Blueprint $table) {
            $table->dropColumn('pivot_data');
        });
    }
}
