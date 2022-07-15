<?php

$baseDir = realpath(__DIR__ . '/../../../..');

/*
 * Winter autoloader
 */
require $baseDir . '/bootstrap/autoload.php';

/*
 * Fallback autoloader
 */
$loader = new Winter\Storm\Support\ClassLoader(
    new Winter\Storm\Filesystem\Filesystem,
    $baseDir,
    $baseDir . '/storage/framework/classes.php'
);

$loader->register();
$loader->addDirectories([
    'modules',
    'plugins'
]);
