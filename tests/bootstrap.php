<?php

/*
 * Winter autoloader
 */
require __DIR__ . '/../bootstrap/autoload.php';

/*
 * Fallback autoloader
 */
$loader = new Winter\Storm\Support\ClassLoader(
    new Winter\Storm\Filesystem\Filesystem,
    __DIR__ . '/../',
    __DIR__ . '/../storage/framework/classes.php'
);

$loader->register();
$loader->addDirectories([
    'modules',
    'plugins'
]);
