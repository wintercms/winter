<?php

/*
 * Analysis bootstrap for PHPStan.
 *
 * Winter registers global class aliases (Model, Db, and friends) while the application boots, and
 * module classes extend those aliases directly. PHPStan reflects the module classes without an
 * application, so the same aliases are registered here from the module's own alias map.
 */
require __DIR__ . '/vendor/autoload.php';

Illuminate\Foundation\AliasLoader::getInstance(
    require __DIR__ . '/modules/system/aliases.php'
)->register();
