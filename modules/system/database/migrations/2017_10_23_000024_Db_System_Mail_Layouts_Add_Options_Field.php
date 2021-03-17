<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class DbSystemMailLayoutsAddOptionsField extends Migration
{
    public function up()
    {
        Schema::table('system_mail_layouts', function (Blueprint $table) {
            $table->text('options')->nullable()->after('is_locked');
        });
    }

    public function down()
    {
        Schema::table('system_mail_layouts', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
}
