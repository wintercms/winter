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
    | Supported Drivers: "local", "ftp", "sftp", "s3", "scoped"
    |
    | NOTE: s3's stream_uploads option requires the Winter.DriverAWS plugin
    | to be installed and enabled.
    |
    */

    'disks' => [

        // The "uploads" disk is used by the System module to store uploaded files (System\Models\File).
        'uploads' => [
            'driver' => 'scoped',
            'disk' => 'local',
            'prefix' => 'uploads',
            'visibility' => 'public',
            'temporaryUrlTTL' => 3600,
        ],

        // The "media" disk is used by the System module's MediaLibrary to store media files.
        'media' => [
            'driver' => 'scoped',
            'disk' => 'local',
            'prefix' => 'media',
            'visibility' => 'public',
        ],

        // The "resized" disk is used by the System module's ImageResizer to store resized images.
        'resized' => [
            'driver' => 'scoped',
            'disk' => 'local',
            'prefix' => 'uploads',
            'visibility' => 'public',
        ],

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
            'stream_uploads' => env('AWS_S3_STREAM_UPLOADS', false),
            'url' => env('AWS_URL'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],
    ],
];
