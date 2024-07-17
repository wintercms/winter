<?php

namespace System\Classes;

use Winter\Storm\Support\Facades\Config;

/**
 * Node package version manager.
 *
 * This is a centralised system for managing node package versions and allowing for user customisation
 *
 * @package winter\wn-system-module
 * @author Jack Wilkinson <me@jackwilky.com>
 * @copyright Winter CMS Maintainers
 */
class NodePackageVersions
{
    /**
     * @var array|string[] List of default package versions
     */
    protected static array $defaultVersions = [
        // Mix compiler
        'laravel-mix' => '^6.0.41',

        // Vite compiler
        'vite' => '^5.2.11',
        'laravel-vite-plugin' => '^1.0.4',

        // Plugins
        'tailwindcss' => '^3.4.0',
        '@tailwindcss/forms' => '^0.5.3',
        '@tailwindcss/typography' => '^0.5.2',
        'vue' => '^3.4.0',
        '@vitejs/plugin-vue' => '^5.0.5',

        // Test package - this allows us to statically test with a version that will not change
        'winter-test-js-package' => '^1.0.3',
    ];

    /**
     * Returns the version of a package, over-writable via config
     */
    public static function get(string $package): ?string
    {
        return Config::get('node.packages.' . $package, static::$defaultVersions[$package] ?? null);
    }
}
