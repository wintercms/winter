<?php

return new class extends \Winter\Storm\Database\Updates\Migration
{
    public function up()
    {
        Schema::table('system_request_logs', function ($table) {
            $table->index(['url', 'status_code']);
        });
    }

    public function down()
    {
        Schema::table('system_request_logs', function ($table) {
            $table->dropIndex(['url', 'status_code']);
        });
    }
};
