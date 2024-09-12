<?php

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
