<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbMediaItems extends Migration
{
    public function up()
    {
        Schema::create('media_items', function (Blueprint $table) {
            // Schema
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('location', 192)->nullable();
            $table->string('type', 20)->nullable();
            $table->string('file_type', 20)->nullable();
            $table->string('name');
            $table->string('extension', 20)->nullable();
            $table->string('path')->nullable();
            $table->bigInteger('size')->unsigned();
            $table->text('metadata')->nullable();
            $table->dateTime('modified_at')->nullable();

            // Indexes
            $table->index(['parent_id'], 'media_idx_parent_id');
            $table->index(['type'], 'media_idx_type');
            $table->index(['file_type'], 'media_idx_file_type');
        });
    }

    public function down()
    {
        Schema::drop('media_items');
    }
}
