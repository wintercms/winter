<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'url' => '/storage/app',
            'visibility' => 'public',
        ],
        's3' => [
            'bucket' => env('AWS_BUCKET'),
            'driver' => 's3',
            'endpoint' => env('AWS_ENDPOINT'),
            'key' => env('AWS_ACCESS_KEY_ID'),
            'region' => env('AWS_DEFAULT_REGION'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'url' => env('AWS_URL'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],
    ],
];
