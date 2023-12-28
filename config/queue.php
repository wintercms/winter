<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Winter's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Winter. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        'database' => [
            'after_commit' => false,
            'driver' => 'database',
            'queue' => 'default',
            'retry_after' => 90,
            'table' => 'jobs',
        ],
        'beanstalkd' => [
            'after_commit' => false,
            'block_for' => 0,
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
        ],
        'sqs' => [
            'after_commit' => false,
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'suffix' => env('SQS_SUFFIX'),
        ],
        'redis' => [
            'after_commit' => false,
            'block_for' => null,
            'connection' => 'default',
            'driver' => 'redis',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Batching
    |--------------------------------------------------------------------------
    |
    | The following options configure the database and table that store job
    | batching information. These options can be updated to any database
    | connection and table which has been defined by your application.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'table' => 'failed_jobs',
    ],
];
