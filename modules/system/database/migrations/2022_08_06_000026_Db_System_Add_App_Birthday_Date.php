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

        if (is_null($appBirthday)) {
            $appBirthdayFromPlugins = PluginVersion::orderBy('created_at')->first()?->created_at;

            if (is_null($appBirthdayFromPlugins)) {
                $appBirthday = Carbon::now()->timestamp;
            } else {
                $appBirthday = Carbon::parse($appBirthdayFromPlugins)->timestamp;
            }

            Parameter::set('system::app.birthday', $appBirthday);
        }

        return $appBirthday;
    }

    public function down()
    {
    }
}
