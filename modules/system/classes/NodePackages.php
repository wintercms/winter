<?php

namespace System\Classes;

use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Traits\Singleton;

/**
 * Node packages manager.
 *
 * This is a centralised system for managing node package versions and allowing for user customisation
 *
 * @package winter\wn-system-module
 * @author Jack Wilkinson <me@jackwilky.com>
 * @copyright Winter CMS Maintainers
 */
class NodePackages
{
    use Singleton;

    /**
     * @var array|array[] Packages required for compilers
     */
    protected array $compilerPackages = [
        'mix' => [
            'laravel-mix' => '^6.0.41',
        ],
        'vite' => [
            'vite' => '^5.2.11',
            'laravel-vite-plugin' => '^1.0.4',
        ],
    ];

    /**
     * @var array List of packages available to install. Allows for `$compilerName` => [`CompilerSpecificPackage`]
     */
    protected array $bundlePackages = [
        'tailwind' => [
            'tailwindcss' => '^3.4.0',
            '@tailwindcss/forms' => '^0.5.3',
            '@tailwindcss/typography' => '^0.5.2',
        ],
        'vue' => [
            'vue' => '^3.4.0',
            'vite' => [
                '@vitejs/plugin-vue' => '^5.0.5'
            ]
        ]
    ];

    /**
     * @var array Handlers to allow for config generation
     */
    protected array $handlers = [];

    /**
     * Initialize the singleton, pulls any handlers from config and binds them to the instance
     */
    public function init(): void
    {
        foreach (Config::get('node.handlers', []) as $name => $handlers) {
            foreach ($handlers as $handler) {
                $this->addHandler($name, $handler);
            }
        }

        $this->addHandler('tailwind', function (string $packagePath, string $packageType) {
            $this->writeFile(
                $packagePath . '/tailwind.config.js',
                $this->getFixture('tailwind/tailwind.' . $packageType . '.config.js.fixture')
            );

            $this->writeFile(
                $packagePath . '/postcss.config.mjs',
                $this->getFixture('tailwind/postcss.config.js.fixture')
            );
        });
    }

    /**
     * Get packages required for a specific compiler
     */
    public function getCompilerPackages(string $name): array
    {
        return array_replace_recursive(
            $this->compilerPackages[$name] ?? [],
            Config::get('node.compilerPackages.' . $name, [])
        );
    }

    /**
     * Get all bundles configured
     */
    public function getBundles(): array
    {
        return array_keys(array_replace_recursive($this->bundlePackages, Config::get('node.bundlePackages', [])));
    }

    /**
     * Get the packages for a bundle, with compiler specific packages
     */
    public function getBundlePackages(string $name, string $assetType): array
    {
        $packages = array_replace_recursive(
            $this->bundlePackages[$name] ?? [],
            Config::get('node.bundlePackages.' . $name, [])
        );

        $bundle = [];
        foreach ($packages as $key => $value) {
            if (is_array($value)) {
                if ($key === $assetType) {
                    $bundle = array_merge($bundle, $value);
                }
                continue;
            }

            $bundle[$key] = $value;
        }

        return $bundle;
    }

    /**
     * Add a handler for a bundle
     */
    public function addHandler(string $name, callable $callable): void
    {
        $this->handlers[$name][] = $callable;
    }

    /**
     * Get all handlers for a bundle
     */
    public function getHandlers(string $name): array
    {
        return $this->handlers[$name] ?? [];
    }
}
