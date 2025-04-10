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
    | There are 5 reserved disk names in Winter CMS, if not configured they will
    | be dynamically injected by the System module:
    |
    | "system", "modules", "themes", & "plugins": All automatically injected by the
    | System ServiceProvider for internal usage.
    |
    | - "uploads-public" & "uploads-protected": Used by the \System\Models\File model
    | for storing file uploads. Paths are automatically prefixed with "public" on
    | the uploads-public disk and "protected" on the uploads-protected disk
    |
    | - "media": The default disk used by the \System\Classes\MediaLibrary
    |
    | - "resized": The disk used by the \System\Classes\ImageResizer
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
            'stream_uploads' => env('AWS_S3_STREAM_UPLOADS', false),
            'url' => env('AWS_URL'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],
        'uploads-public' => [
            'driver' => 'scoped',
            'disk' => 'local',
            'visibility' => 'public',
            'prefix' => 'uploads',
        ],
        'uploads-protected' => [
            'driver' => 'scoped',
            'disk' => 'local',
            'visibility' => 'private',
            'temporaryUrlTTL' => 3600,
            'prefix' => 'uploads',
        ],
        'media' => [
            'driver' => 'scoped',
            'disk' => 'local',
            'visibility' => 'public',
            'prefix' => 'media',

        ],
        'resized' => [
            'driver' => 'scoped',
            'disk' => 'local',
            'visibility' => 'public',
            'prefix' => 'uploads',

        ],
    ],
];
