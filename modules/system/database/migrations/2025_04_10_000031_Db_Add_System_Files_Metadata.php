<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('system_files', 'metadata')) {
            Schema::table('system_files', function (Blueprint $table) {
                $table->mediumText('metadata')->nullable()->after('sort_order');
            });
        }
    }

    public function down()
    {
        Schema::table('system_files', function (Blueprint $table) {
            $table->dropColumnIfExists('metadata');
        });
    }
};
