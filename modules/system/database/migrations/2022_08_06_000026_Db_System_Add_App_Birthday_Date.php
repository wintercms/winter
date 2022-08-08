<?php

use Winter\Storm\Database\Updates\Migration;
use Carbon\Carbon;
use System\Models\Parameter;
use System\Models\PluginVersion;

class DbSystemAddAppBirthdayDate extends Migration
{
    public function up()
    {
        $appBirthday = Parameter::get('system::app.birthday');

        if ($appBirthday) {
            return $appBirthday;
        }

        $appBirthdayFromPlugins = PluginVersion::orderBy('created_at')->first()?->created_at;
        $appBirthday = $appBirthdayFromPlugins ? Carbon::parse($appBirthdayFromPlugins)->timestamp : Carbon::now()->timestamp;

        Parameter::set('system::app.birthday', $appBirthday);

        return $appBirthday;
    }

    public function down()
    {
    }
}
