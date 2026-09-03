<?php

/*
 * Analysis bootstrap for PHPStan.
 *
 * The modules are loaded through Winter's own class loader rather than composer, so the same
 * loader is registered here, mirroring modules/system/tests/bootstrap/app.php. Winter also
 * registers global class aliases (Model, BackendAuth, and friends) while the application boots,
 * and module code references the aliases directly, so the alias map is registered as well.
 */
require __DIR__ . '/vendor/autoload.php';

$classLoader = new Winter\Storm\Support\ClassLoader(
    new Winter\Storm\Filesystem\Filesystem(),
    __DIR__,
    __DIR__ . '/storage/framework/classes.php'
);

$classLoader->register();

foreach (glob(__DIR__ . '/modules/*', GLOB_ONLYDIR) as $modulePath) {
    $classLoader->autoloadPackage(basename($modulePath), $modulePath);
}

Illuminate\Foundation\AliasLoader::getInstance(
    require __DIR__ . '/modules/system/aliases.php'
)->register();
