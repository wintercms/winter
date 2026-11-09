<?php

use Winter\Storm\Database\Schema\Blueprint;

return new class extends \Winter\Storm\Database\Updates\Migration
{
    public function up()
    {
        $schema = Schema::connection($this->getLockConnection());

        if ($schema->hasTable($this->getTableName())) {
            return;
        }

        $schema->create($this->getTableName(), function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration')->index();
        });
    }

    public function down()
    {
        Schema::connection($this->getLockConnection())->dropIfExists($this->getTableName());
    }

    protected function getLockConnection()
    {
        return Config::get('cache.stores.database.lock_connection')
            ?? Config::get('cache.stores.database.connection');
    }

    protected function getTableName()
    {
        return Config::get('cache.stores.database.lock_table', 'cache_locks');
    }
};
