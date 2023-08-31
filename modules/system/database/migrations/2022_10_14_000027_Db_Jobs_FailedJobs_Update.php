<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Support\Str;

return new class extends \Winter\Storm\Database\Updates\Migration
{
    public function up()
    {
        Schema::table($this->getTableName(), function (Blueprint $table) {
            $table->longText('payload')->change();
        });

        Schema::table($this->getFailedTableName(), function (Blueprint $table) {
            $table->string('uuid')->nullable()->unique()->after('id');
            $table->longText('payload')->change();
            $table->longText('exception')->change();
        });

        // Generate UUIDs for existing failed jobs
        DB::table($this->getFailedTableName())->whereNull('uuid')->cursor()->each(function ($job) {
            DB::table($this->getFailedTableName())
                ->where('id', $job->id)
                ->update(['uuid' => (string) Str::uuid()]);
        });
    }

    public function down()
    {
        Schema::table($this->getFailedTableName(), function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }

    protected function getTableName()
    {
        return Config::get('queue.connections.database.table', 'jobs');
    }

    protected function getFailedTableName()
    {
        return Config::get('queue.failed.table', 'failed_jobs');
    }
};
