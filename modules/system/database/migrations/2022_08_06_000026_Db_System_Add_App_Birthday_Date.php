<?php

use Winter\Storm\Database\Updates\Migration;
use Carbon\Carbon;
use System\Models\Parameter;
use System\Models\PluginVersion;

return new class extends Migration
{
    public function up()
    {
        $appBirthday = Parameter::get('system::app.birthday');
        if ($appBirthday) {
            // Return early if the birthday has already been set
            return;
        }

        $firstPluginCreated = PluginVersion::orderBy('created_at')
            ->first()
            ?->created_at;

        $appBirthday = $firstPluginCreated ?: Carbon::now();

        Parameter::set('system::app.birthday', $appBirthday);
    }

    public function down()
    {
    }
};
