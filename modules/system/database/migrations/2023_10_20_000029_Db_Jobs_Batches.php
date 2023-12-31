<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbJobsBatches extends Migration
{
    public function up()
    {
        Schema::create($this->getTableName(), function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->getTableName());
    }

    protected function getTableName()
    {
        return Config::get('queue.batching.table', 'job_batches');
    }
}
