<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Winter uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'channels' => [
                'single',
            ],
            'driver' => 'stack',
            'ignore_exceptions' => false,
        ],
        'single' => [
            'driver' => 'single',
            'level' => env('LOG_LEVEL', 'debug'),
            'path' => storage_path('logs/system.log'),
        ],
        'daily' => [
            'days' => 14,
            'driver' => 'daily',
            'level' => env('LOG_LEVEL', 'debug'),
            'path' => storage_path('logs/system.log'),
        ],
        'slack' => [
            'driver' => 'slack',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Winter Log',
        ],
        'papertrail' => [
            'driver' => 'monolog',
            'handler' => env('LOG_PAPERTRAIL_HANDLER', \Monolog\Handler\SyslogUdpHandler::class),
            'handler_with' => [
                'connectionString' => 'tls://' . env('PAPERTRAIL_URL') . ':' . env('PAPERTRAIL_PORT'),
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
            'level' => env('LOG_LEVEL', 'debug'),
        ],
        'stderr' => [
            'driver' => 'monolog',
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'handler' => \Monolog\Handler\StreamHandler::class,
            'level' => env('LOG_LEVEL', 'debug'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],
        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],
        'null' => [
            'driver' => 'monolog',
            'handler' => \Monolog\Handler\NullHandler::class,
        ],
        'emergency' => [
            'path' => storage_path('logs/system.log'),
        ],
    ],
];
