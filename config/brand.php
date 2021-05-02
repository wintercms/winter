<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Specify the default backend brand settings
    |--------------------------------------------------------------------------
    |
    | These values are overridden by the Brand settings set in the database
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Logo Path
    |--------------------------------------------------------------------------
    |
    | The path to the logo to be used for BrandSetting::getDefaultLogo().
    | Can use ~ to refer to the application directory
    | Can use $ to refer to the plugins directory
    |
    */

    // 'logoPath' => '$/myauthor/myplugin/assets/images/logo.png',

    /*
    |--------------------------------------------------------------------------
    | Favicon Path
    |--------------------------------------------------------------------------
    |
    | The path to the favicon to be used for BrandSetting::getDefaultFavicon().
    | Can use ~ to refer to the application directory
    | Can use $ to refer to the plugins directory
    |
    */

    // 'faviconPath' => '$/myauthor/myplugin/assets/images/favicon-32x32.png',

    /*
    |--------------------------------------------------------------------------
    | Backend Application Name
    |--------------------------------------------------------------------------
    |
    | BrandSetting::get('app_name')
    |
    */

    'appName' => config('app.name', Lang::get('system::lang.app.name')),

    /*
    |--------------------------------------------------------------------------
    | Backend Application Tagline
    |--------------------------------------------------------------------------
    |
    | BrandSetting::get('app_tagline')
    |
    */

    'tagline' => Lang::get('system::lang.app.tagline'),

];
