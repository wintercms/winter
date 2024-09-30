<?php

$root = dirname(dirname(dirname(__DIR__)));

require_once $root . '/vendor/autoload.php';

use Winter\LaravelConfigWriter\ArrayFile;
use Winter\LaravelConfigWriter\EnvFile;

$config = ArrayFile::open($root . '/config/app.php');

$config->set('trustedHosts', [
    'localhost',
    '^(.+\.)?app.github.dev',
]);
$config->set('trustedProxies', '*');

$config->write();

$env = EnvFile::open($root . '/.env');

$env->set('APP_URL', 'https://' . $_ENV['CODESPACE_NAME'] . '.app.github.dev');
$env->set('LINK_POLICY', 'force');

$env->write();
