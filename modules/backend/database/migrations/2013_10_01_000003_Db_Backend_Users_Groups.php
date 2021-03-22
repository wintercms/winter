<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbBackendUsersGroups extends Migration
{
    public function up()
    {
        Schema::create('backend_users_groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned();
            $table->integer('user_group_id')->unsigned();
            $table->primary(['user_id', 'user_group_id'], 'user_group');
        });
    }

    public function down()
    {
        Schema::dropIfExists('backend_users_groups');
    }
}
