<?php

/*
 * Analysis bootstrap for PHPStan.
 *
 * Two things need registering that a plain composer autoload does not provide:
 *
 * - The modules follow Winter's class loader convention (StudlyCase namespaces over lowercase
 *   directories) rather than composer PSR-4, so a matching autoloader is registered here. PHPStan
 *   only needs it to load classes referenced indirectly, such as alias targets; the module code
 *   itself is discovered through the scanDirectories setting.
 * - Winter registers global class aliases (Model, BackendAuth, and friends) while the application
 *   boots, and module code references the aliases directly. The same alias map is registered here.
 */
require __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function (string $class): void {
    foreach (['System' => 'system', 'Backend' => 'backend', 'Cms' => 'cms'] as $prefix => $directory) {
        if (str_starts_with($class, $prefix . '\\')) {
            $parts = explode('\\', substr($class, strlen($prefix) + 1));
            $file = array_pop($parts) . '.php';
            $path = __DIR__ . '/modules/' . $directory
                . '/' . strtolower(implode('/', $parts))
                . ($parts === [] ? '' : '/') . $file;

            if (is_file($path)) {
                require_once $path;
            }

            return;
        }
    }
});

Illuminate\Foundation\AliasLoader::getInstance(
    require __DIR__ . '/modules/system/aliases.php'
)->register();
