<?php
// Fixture used for `winter:env` unit tests in `tests/unit/system/console/WinterEnvTest.php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'host'       => 'localhost',
            'port'       => 3306,
            'database'   => 'data#base',
            'username'   => 'teal\'c',
            'password'   => 'test"quotes\'test',
        ],
    ],
    'useConfigForTesting' => false,
];
