<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Winter CMS is shown below to make development simple.
    |
    | All database work in Winter CMS is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'sqlite' => [
            'database' => env('DB_DATABASE', storage_path('database.sqlite')),
            'driver' => 'sqlite',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'prefix' => '',
            'url' => env('DATABASE_URL'),
        ],
        'mysql' => [
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'database' => env('DB_DATABASE', 'winter'),
            'driver' => 'mysql',
            'engine' => 'InnoDB',
            'host' => env('DB_HOST', '127.0.0.1'),
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
            'password' => env('DB_PASSWORD', ''),
            'port' => env('DB_PORT', '3306'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'unix_socket' => env('DB_SOCKET', ''),
            'url' => env('DATABASE_URL'),
            'username' => env('DB_USERNAME', 'winter'),
        ],
        'pgsql' => [
            'charset' => 'utf8',
            'database' => env('DB_DATABASE', 'winter'),
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'password' => env('DB_PASSWORD', ''),
            'port' => env('DB_PORT', '5432'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
            'url' => env('DATABASE_URL'),
            'username' => env('DB_USERNAME', 'winter'),
        ],
        'sqlsrv' => [
            'charset' => 'utf8',
            'database' => env('DB_DATABASE', 'winter'),
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', '127.0.0.1'),
            'password' => env('DB_PASSWORD', ''),
            'port' => env('DB_PORT', '1433'),
            'prefix' => '',
            'prefix_indexes' => true,
            'url' => env('DATABASE_URL'),
            'username' => env('DB_USERNAME', 'winter'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Winter makes it easy to dig right in.
    |
    */

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', str_slug(env('APP_NAME', 'winter'), '_') . '_database_'),
        ],
        'default' => [
            'database' => env('REDIS_DB', '0'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'url' => env('REDIS_URL'),
        ],
        'cache' => [
            'database' => env('REDIS_CACHE_DB', '1'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'url' => env('REDIS_URL'),
        ],
    ],
];
