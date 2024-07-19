<?php

namespace System\Classes\Asset;

use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Traits\Singleton;

/**
 * Asset Bundle manager.
 *
 * This class manages "asset bundles" registered by the core and plugins that are used by the
 * [mix|vite]:create commands to generate & populate the required files for a given bundle.
 * Bundles include information on the specific packages & versions required for the bundle
 * to function the context of the Winter package (plugin or theme) it is being used in;
 * as well as dependencies specific to the desired compiler (e.g. mix or vite).
 *
 * @package winter\wn-system-module
 * @author Jack Wilkinson <me@jackwilky.com>
 * @copyright Winter CMS Maintainers
 */
class BundleManager
{
    use Singleton;

    /**
     * Packages required for compilers
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
     * List of packages available to install. Allows for `$compilerName` => [`CompilerSpecificPackage`]
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
            ],
        ],
    ];

    /**
     * Handlers to allow for config generation
     */
    protected array $setupHandlers = [];

    /**
     * Handlers to allow for config generation
     */
    protected array $scaffoldHandlers = [];

    /**
     * Initialize the singleton, pulls any handlers from config and binds them to the instance
     */
    public function init(): void
    {
        foreach (Config::get('node.setupHandlers', []) as $name => $handlers) {
            foreach ($handlers as $handler) {
                $this->addSetupHandler($name, $handler);
            }
        }

        foreach (Config::get('node.scaffoldHandlers', []) as $name => $handlers) {
            foreach ($handlers as $handler) {
                $this->addScaffoldHandler($name, $handler);
            }
        }

        $this->addSetupHandler('tailwind', function (string $packagePath, string $packageType) {
            $this->writeFile(
                $packagePath . '/tailwind.config.js',
                $this->getFixture('tailwind/tailwind.' . $packageType . '.config.js.fixture')
            );

            $this->writeFile(
                $packagePath . '/postcss.config.mjs',
                $this->getFixture('tailwind/postcss.config.js.fixture')
            );
        });

        $this->addScaffoldHandler('tailwind', function (string $contents, string $contentType) {
            return match ($contentType) {
                'mix' => $contents . PHP_EOL . <<<JAVASCRIPT
                mix.postCss('assets/src/css/{{packageName}}.css', 'assets/dist/css/{{packageName}}.css', [
                    require('postcss-import'),
                    require('tailwindcss'),
                    require('autoprefixer'),
                ]);
                JAVASCRIPT,
                'css' => $this->getFixture('css/tailwind.css.fixture'),
                default => $contents
            };
        });

        $this->addScaffoldHandler('vue', function (string $contents, string $contentType) {
            return match ($contentType) {
                'vite' => str_replace(
                    '}),',
                    <<<JAVASCRIPT
                    }),
                            vue({
                                template: {
                                    transformAssetUrls: {
                                        // The Vue plugin will re-write asset URLs, when referenced
                                        // in Single File Components, to point to the Laravel web
                                        // server. Setting this to `null` allows the Laravel plugin
                                        // to instead re-write asset URLs to point to the Vite
                                        // server instead.
                                        base: null,

                                        // The Vue plugin will parse absolute URLs and treat them
                                        // as absolute paths to files on disk. Setting this to
                                        // `false` will leave absolute URLs un-touched so they can
                                        // reference assets in the public directory as expected.
                                        includeAbsolute: false,
                                    },
                                },
                            }),
                    JAVASCRIPT,
                    str_replace(
                        'import laravel from \'laravel-vite-plugin\';',
                        'import laravel from \'laravel-vite-plugin\';' . PHP_EOL . 'import vue from \'@vitejs/plugin-vue\';',
                        $contents
                    )
                ),
                'mix' => str_replace(
                    'mix.js(\'assets/src/js/{{packageName}}.js\', \'assets/dist/js/{{packageName}}.js\');',
                    'mix.js(\'assets/src/js/{{packageName}}.js\', \'assets/dist/js/{{packageName}}.js\').vue({ version: 3 });',
                    $contents
                ),
                'js' => $this->getFixture('js/vue.js.fixture'),
                default => $contents
            };
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
     * Add a setup handler for a bundle
     */
    public function addSetupHandler(string $name, callable $callable): void
    {
        $this->setupHandlers[$name][] = $callable;
    }

    /**
     * Get all setup handlers for a bundle
     */
    public function getSetupHandlers(string $name): array
    {
        return $this->setupHandlers[$name] ?? [];
    }

    /**
     * Add a scaffold handler for a bundle
     */
    public function addScaffoldHandler(string $name, callable $callable): void
    {
        $this->scaffoldHandlers[$name][] = $callable;
    }

    /**
     * Get all scaffold handlers for a bundle
     */
    public function getScaffoldHandlers(string $name): array
    {
        return $this->scaffoldHandlers[$name] ?? [];
    }
}
