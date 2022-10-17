<?php

use Winter\Storm\Database\Schema\Blueprint;

return new class extends \Winter\Storm\Database\Updates\Migration
{
    public function up()
    {
        Schema::table($this->getTableName(), function (Blueprint $table) {
            $table->longText('payload')->change();
        });
    }

    public function down()
    {
    }

    protected function getTableName()
    {
        return Config::get('queue.connections.database.table', 'jobs');
    }
};
