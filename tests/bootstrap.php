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

/*
 * Monkey patch PHPUnit\Framework\MockObject\Generator to avoid
 * "Function ReflectionType::__toString() is deprecated" warnings
 */
$generatorPatchPath = __DIR__ . '/resources/patches/php-generator-7.php';
$generatorSourcePath = __DIR__ . '/../vendor/phpunit/phpunit-mock-objects/src/Generator.php';

if (file_exists($generatorSourcePath)) {
    file_put_contents($generatorSourcePath, file_get_contents($generatorPatchPath));
}
