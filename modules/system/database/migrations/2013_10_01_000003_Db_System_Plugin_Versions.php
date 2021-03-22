<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbSystemPluginVersions extends Migration
{
    public function up()
    {
        Schema::create('system_plugin_versions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code')->index();
            $table->string('version', 50);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_plugin_versions');
    }
}
