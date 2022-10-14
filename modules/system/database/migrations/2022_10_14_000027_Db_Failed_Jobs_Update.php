<?php

use Winter\Storm\Database\Schema\Blueprint;

return new class extends \Winter\Storm\Database\Updates\Migration
{
    public function up()
    {
        Schema::table($this->getTableName(), function (Blueprint $table) {
            $table->string('uuid')->unique()->after('id');
            $table->longText('payload')->change();
            $table->longText('exception')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table($this->getTableName(), function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }

    protected function getTableName()
    {
        return Config::get('queue.failed.table', 'failed_jobs');
    }
};
