<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Support\Str;

return new class extends \Winter\Storm\Database\Updates\Migration
{
    public function up()
    {
        Schema::table('sessions', function ($table) {
            $table->index(['last_activity']);
            $table->index(['user_id']);
        });
    }

    public function down()
    {
        Schema::table('sessions', function ($table) {
            $table->dropIndex(['last_activity']);
            $table->dropIndex(['user_id']);
        });
    }
};
