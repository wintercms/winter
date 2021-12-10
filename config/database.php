<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_CLASS,

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

    'default' => 'mysql',

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
            'driver'                  => 'sqlite',
            // 'url'                  => env('DATABASE_URL'),
            'database'                => base_path('storage/database.sqlite'),
            'prefix'                  => '',
            'foreign_key_constraints' => true,
        ],

        'mysql' => [
            'driver'         => 'mysql',
            // 'url'         => env('DATABASE_URL'),
            'engine'         => 'InnoDB',
            'host'           => '127.0.0.1',
            'port'           => 3306,
            'database'       => 'database',
            'username'       => 'root',
            'password'       => '',
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_unicode_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => true,
            'varcharmax'     => 191,
        ],

        'pgsql' => [
            'driver'         => 'pgsql',
            // 'url'         => env('DATABASE_URL'),
            'host'           => '127.0.0.1',
            'port'           => 5432,
            'database'       => 'database',
            'username'       => 'root',
            'password'       => '',
            'charset'        => 'utf8',
            'prefix'         => '',
            'prefix_indexes' => true,
            'schema'         => 'public',
            'sslmode'        => 'prefer',
        ],

        'sqlsrv' => [
            'driver'         => 'sqlsrv',
            // 'url'         => env('DATABASE_URL'),
            'host'           => '127.0.0.1',
            'port'           => 1433,
            'database'       => 'database',
            'username'       => 'root',
            'password'       => '',
            'charset'        => 'utf8',
            'prefix'         => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk have not actually be run in the databases.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Winter CMS makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'options' => [
            'cluster' => 'redis',
            'prefix'  => '',
        ],

        'default' => [
            // 'url'   => env('REDIS_URL'),
            'host'     => '127.0.0.1',
            'password' => null,
            'port'     => 6379,
            'database' => 0,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Use DB configuration for testing
    |--------------------------------------------------------------------------
    |
    | When running plugin tests Winter CMS by default uses SQLite in memory.
    | You can override this behavior by setting `useConfigForTesting` to true.
    |
    | After that Winter CMS will take DB parameters from the config.
    | If file `/config/testing/database.php` exists, config will be read from it,
    | but remember that when not specified it will use parameters specified in
    | `/config/database.php`.
    |
    */

    'useConfigForTesting' => false,

];
