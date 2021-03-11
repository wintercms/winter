<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbSystemSettings extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('item')->nullable()->index();
            $table->mediumtext('value')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
}
