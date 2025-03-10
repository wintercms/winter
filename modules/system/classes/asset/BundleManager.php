<?php

namespace System\Classes\Asset;

use Closure;
use Winter\Storm\Support\Traits\Singleton;

/**
 * Asset Bundle manager.
 *
 * This class manages "asset bundles" registered by the core and plugins that are used by the
 * [mix|vite]:create commands to generate & populate the required files for a given bundle.
 * Bundles include information on the specific packages & versions required for the bundle
 * to function in the context of the Winter package (plugin or theme) it is being used in,
 * as well as dependencies specific to the desired compiler (e.g. mix or vite).
 *
 * @package winter\wn-system-module
 * @author Jack Wilkinson <me@jackwilky.com>
 * @copyright Winter CMS Maintainers
 */
class BundleManager
{
    use Singleton;

    protected const HANDLER_SETUP = '_setup';
    protected const HANDLER_SCAFFOLD = '_scaffold';

    /**
     * List of packages available to install. Allows for `$compilerName` => [`CompilerSpecificPackage`]
     */
    protected array $defaultPackages = [
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
        'react' => [
            'react' => '^19.0.0',
            'react-dom' => '^19.0.0',
            'vite' => [
                '@vitejs/plugin-react' => '^4.3.4',
            ],
        ],
    ];

    /**
     * List of registered asset bundles in the system
     */
    protected array $registeredBundles = [];

    /**
     * Initialize the singleton
     */
    public function init(): void
    {
        // Register the default bundles
        $this->registerCallback(function (self $manager) {
            $manager->registerBundles($this->defaultPackages);

            $manager->registerSetupHandler('tailwind', function (string $packagePath, string $packageType) {
                $this->writeFile(
                    $packagePath . '/tailwind.config.js',
                    $this->getFixture('tailwind/tailwind.' . $packageType . '.config.js.fixture')
                );

                $this->writeFile(
                    $packagePath . '/postcss.config.mjs',
                    $this->getFixture('tailwind/postcss.config.js.fixture')
                );
            });

            $manager->registerSetupHandler('react', function (string $packagePath, string $packageType) use ($manager) {
                if ($this->option('no-stubs')) {
                    return;
                }

                $this->writeFile(
                    $packagePath . '/assets/src/js/components/App.jsx',
                    $this->getFixture('react/App.jsx.fixture')
                );

                $this->writeFile(
                    $packagePath . '/assets/src/js/' . strtolower($this->argument('packageName')) . '.jsx',
                    $this->getFixture('react/package.jsx.fixture')
                );
            });

            $manager->registerScaffoldHandler('tailwind', function (string $contents, string $contentType) {
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

            $manager->registerScaffoldHandler('vue', function (string $contents, string $contentType) {
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

            $manager->registerScaffoldHandler('react', function (string $contents, string $contentType) {
                return match ($contentType) {
                    'vite' => str_replace(
                        '}),',
                        <<<JAVASCRIPT
                        }),
                                react(),
                        JAVASCRIPT,
                        str_replace(
                            'import laravel from \'laravel-vite-plugin\';',
                            'import laravel from \'laravel-vite-plugin\';' . PHP_EOL . 'import react from \'@vitejs/plugin-react\';',
                            $contents
                        )
                    ),
                    'mix' => str_replace(
                        'mix.js(\'assets/src/js/{{packageName}}.js\', \'assets/dist/js/{{packageName}}.js\');',
                        'mix.js(\'assets/src/js/{{packageName}}.js\', \'assets/dist/js/{{packageName}}.js\').react();',
                        $contents
                    ),
                    'js' => str_replace(
                        '{{packageName}}',
                        strtolower($this->argument('packageName')),
                        $this->getFixture('react/package.js.fixture')
                    ),
                    default => $contents
                };
            });
        });
    }

    /**
     * Returns a list of the registered asset bundles.
     */
    public function listRegisteredBundles(): array
    {
        return $this->registeredBundles;
    }

    /**
     * Get all bundles configured
     */
    public function getBundles(): array
    {
        return array_keys($this->listRegisteredBundles());
    }

    /**
     * Get the packages for a bundle, with compiler specific packages
     */
    public function getBundlePackages(string $name, string $assetType): array
    {
        $config = $this->listRegisteredBundles()[$name] ?? [];

        $packages = [];
        foreach ($config as $key => $value) {
            // Skip handlers
            if (in_array($key, [static::HANDLER_SETUP, static::HANDLER_SCAFFOLD])) {
                continue;
            }

            // Merge in any compiler specific packages for the current compiler
            if (is_array($value)) {
                if ($key === $assetType) {
                    $packages = array_merge($packages, $value);
                }
                continue;
            }

            $packages[$key] = $value;
        }

        return $packages;
    }

    /**
     * Registers a callback function that defines asset bundles. The callback function
     * should register bundles by calling the manager's registerBundles() function.
     * This instance is passed to the callback function as an argument. Usage:
     *
     *     BundleManager::registerCallback(function ($manager) {
     *         $manager->registerAssetBundles([...]);
     *     });
     *
     */
    public function registerCallback(callable $callback): static
    {
        $callback($this);

        return $this;
    }

    /**
     * Registers asset bundles.
     */
    public function registerBundles(array $definitions): static
    {
        foreach ($definitions as $name => $definition) {
            $this->registerBundle($name, $definition);
        }

        return $this;
    }

    /**
     * Registers a single asset bundle.
     */
    public function registerBundle(string $name, array $definition): static
    {
        $this->registeredBundles[$name] = $definition;

        return $this;
    }

    /**
     * Registers a single bundle setup handler.
     */
    public function registerSetupHandler(string $name, Closure $closure): static
    {
        $this->registeredBundles[$name][static::HANDLER_SETUP] = $closure;

        return $this;
    }

    /**
     * Registers a single bundle scaffold handler.
     */
    public function registerScaffoldHandler(string $name, Closure $closure): static
    {
        $this->registeredBundles[$name][static::HANDLER_SCAFFOLD] = $closure;

        return $this;
    }

    /**
     * Gets the setup handler for a bundle.
     */
    public function getSetupHandler(string $name): ?Closure
    {
        return $this->listRegisteredBundles()[$name][static::HANDLER_SETUP] ?? null;
    }

    /**
     * Gets the scaffold handler for a bundle.
     */
    public function getScaffoldHandler(string $name): ?Closure
    {
        return $this->listRegisteredBundles()[$name][static::HANDLER_SCAFFOLD] ?? null;
    }
}
