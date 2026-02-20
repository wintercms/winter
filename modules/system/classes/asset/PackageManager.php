<?php

namespace System\Classes\Asset;

use Cms\Classes\Theme;
use InvalidArgumentException;
use System\Classes\PluginManager;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Filesystem\PathResolver;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Str;

/**
 * Package manager.
 *
 * This class manages compilable asset "packages" registered by modules, plugins, and themes that
 * provide configurations for Node.js based compilers (e.g. mix or vite) to process.
 *
 * @package winter\wn-system-module
 * @author Jack Wilkinson <me@jackwilky.com>
 * @copyright Winter CMS Maintainers
 */
class PackageManager
{
    use \Winter\Storm\Support\Traits\Singleton;

    public const TYPE_THEME = 'theme';
    public const TYPE_MODULE = 'module';
    public const TYPE_PLUGIN = 'plugin';

    /**
     * The filename that stores the package definition.
     */
    protected PackageJson $packageJson;

    /**
     * @var array<string, array<string, string>> List of package types and registration methods
     */
    protected array $compilableConfigs = [
        'mix' => [
            'configFile' => 'winter.mix.js'
        ],
        'vite' => [
            'configFile' => 'vite.config.mjs'
        ]
    ];

    /**
     * A list of packages registered for compiling.
     */
    protected array $packages = [];

    /**
     * Registered callbacks.
     */
    protected static array $callbacks = [];

    /**
     * Constructor.
     */
    public function init(): void
    {
        $this->setPackageJsonPath(base_path('package.json'));

        $packagePaths = [];

        /*
         * Get packages registered in plugins.
         *
         * In the Plugin.php file for your plugin, you can define the `registerMixPackages` or `registerVitePackages`
         * method and return an array, with the name of the package being the key, and the build config path - relative
         * to the plugin directory - as the value.
         *
         * Example:
         *
         *   public function registerMixPackages(): array
         *   {
         *       return [
         *           'package-name-1' => 'winter.mix.js',
         *           'package-name-2' => 'assets/js/build.js',
         *       ];
         *   }
         *
         *   public function registerVitePackages(): array
         *   {
         *       return [
         *           'package-name-1' => 'vite.config.mjs',
         *           'package-name-2' => 'assets/js/build.js',
         *       ];
         *   }
         */
        foreach ($this->compilableConfigs as $type => $config) {
            $packages = PluginManager::instance()->getRegistrationMethodValues(
                $this->getRegistrationMethod($type)
            );
            if (count($packages)) {
                foreach ($packages as $pluginCode => $packageArray) {
                    if (!is_array($packageArray)) {
                        continue;
                    }

                    foreach ($packageArray as $name => $package) {
                        $this->registerPackage(
                            $name,
                            PluginManager::instance()->getPluginPath($pluginCode) . '/' . $package,
                            $type
                        );
                    }
                }
            }

            // Get the currently enabled modules
            $enabledModules = Config::get('cms.loadModules', []);

            if (in_array('Cms', $enabledModules)) {
                // Allow current theme to define mix assets
                $theme = Theme::getActiveTheme();

                if (!is_null($theme)) {
                    $mix = $theme->getConfigValue($type, []);

                    if (count($mix)) {
                        foreach ($mix as $name => $file) {
                            $this->registerPackage($name, $theme->getPath() . '/' . $file, $type);
                        }
                    }
                }
            }

            // Search modules for compilable packages to autoregister
            foreach ($enabledModules as $module) {
                $module = strtolower($module);
                $path = base_path('modules' . DIRECTORY_SEPARATOR . $module) . DIRECTORY_SEPARATOR . $config['configFile'];
                if (File::exists($path)) {
                    $packagePaths[$type]["module-$module"] = $path;
                }
            }

            // Search plugins for compilable packages to autoregister
            $plugins = PluginManager::instance()->getPlugins();
            foreach ($plugins as $plugin) {
                $path = $plugin->getPluginPath() . '/' . $config['configFile'];
                if (File::exists($path)) {
                    $packagePaths[$type][$plugin->getPluginIdentifier()] = $path;
                }
            }

            // Search themes for compilable packages to autoregister
            if (in_array('Cms', $enabledModules)) {
                $themes = Theme::all();
                foreach ($themes as $theme) {
                    $path = $theme->getPath() . '/' . $config['configFile'];
                    if (File::exists($path)) {
                        $packagePaths[$type]["theme-" . $theme->getId()] = $path;
                    }
                }
            }
        }

        // Register the autodiscovered compilable packages
        foreach ($packagePaths as $type => $packages) {
            foreach ($packages as $package => $path) {
                try {
                    $this->registerPackage($package, $path, $type);
                } catch (SystemException $e) {
                    // Either the package name or the config file path have already been registered, skip.
                    continue;
                }
            }
        }
    }

    /**
     * Register a compilable config.
     */
    public function registerCompilable(string $name, array $config): void
    {
        $this->compilableConfigs[$name] = $config;
    }

    /**
     * Registers a callback for processing.
     */
    public static function registerCallback(callable $callback): void
    {
        static::$callbacks[] = $callback;
    }

    /**
     * Calls the deferred callbacks.
     */
    public function fireCallbacks(): static
    {
        // Call callbacks
        foreach (static::$callbacks as $callback) {
            $callback($this);
        }

        return $this;
    }

    /**
     * Returns the count of packages registered.
     */
    public function getPackageCount(): int
    {
        return array_sum(array_map(fn ($packages) => count($packages), ...$this->packages));
    }

    /**
     * Returns all packages registered.
     */
    public function getPackages(string $type, bool $includeIgnored = false): array
    {
        $packages = $this->packages[$type] ?? [];

        foreach ($packages as $index => $package) {
            $packages[$index]['ignored'] = $this->isPackageIgnored($package['path']);
        }

        ksort($packages);

        if (!$includeIgnored) {
            return array_filter($packages, function ($package) {
                return !($package['ignored'] ?? false);
            });
        }

        return $packages;
    }

    /**
     * Returns if package(s) is registered.
     */
    public function hasPackage(string $name, bool $includeIgnored = false): bool
    {
        foreach ($this->packages ?? [] as $packages) {
            foreach ($packages as $packageName => $package) {
                if ($name === $packageName) {
                    if ((!$this->isPackageIgnored($package['path']) || $includeIgnored)) {
                        return true;
                    }

                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Returns package(s).
     */
    public function getPackage(string $name, bool $includeIgnored = false): array
    {
        $results = [];
        foreach ($this->packages ?? [] as $type => $packages) {
            foreach ($packages as $packageName => $package) {
                if (($name === $packageName)) {
                    if (!$this->isPackageIgnored($package['path']) || $includeIgnored) {
                        $results[] = $package + ['type' => $type];
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Registers an entity as a package for compilation.
     *
     * Entities can include plugins, components, themes, modules and much more.
     *
     * The name of the package is an alias that can be used to reference this package in other methods within this
     * class.
     *
     * By default, the `PackageManager` class will look for a `package.json` file for Node dependencies, and a config
     * file for the compilable configuration
     *
     * @param string $name The name of the package being registered
     * @param string $path The path to the compilable JS configuration file (it must be inside of the base_path()). If there is a related package.json file
     *                     then it is required to be present in the same directory as the config file
     * @param string $type The type of compilable
     * @throws SystemException
     */
    public function registerPackage(string $name, string $path, string $type = 'mix'): void
    {
        // Symbolize the path
        $path = File::symbolizePath($path);

        // Normalize the arguments
        $name = strtolower($name);
        $resolvedPath = PathResolver::resolve($path);
        $pinfo = pathinfo($resolvedPath);
        $relativePath = Str::after($pinfo['dirname'], base_path() . DIRECTORY_SEPARATOR);
        $configFile = $pinfo['basename'];

        // Require $configFile to be a JS file
        $extension = File::extension($configFile);
        if (!in_array($extension, ['js', 'mjs'])) {
            throw new SystemException(sprintf(
                'Compilable configuration for package "%s" must be a JavaScript file ending with .js or .mjs',
                $name
            ));
        }

        // Check that the package path exists
        if (!File::exists(base_path($relativePath))) {
            throw new InvalidArgumentException(sprintf(
                'Cannot register "%s" as a compilable package; the "%s" path does not exist.',
                $name,
                base_path($relativePath)
            ));
        }

        $package = $relativePath . '/package.json';
        $config = $relativePath . DIRECTORY_SEPARATOR . $configFile;

        if (!File::exists(base_path($config))) {
            throw new SystemException(sprintf(
                'Cannot register "%s" as a compilable package; the config file "%s" does not exist.',
                $name,
                $config
            ));
        }

        // Check for any existing packages already registered under the provided name
        if (isset($this->packages[$name])) {
            throw new SystemException(sprintf(
                'Cannot register "%s" as a compilable package; it has already been registered at %s.',
                $name,
                $this->packages[$name]['config']
            ));
        }

        // Check for any existing package that already registers the given compilable config path
        foreach ($this->packages[$type] ?? [] as $packageName => $settings) {
            if ($settings['config'] === $config) {
                // If the package name is the same, we'll just discard the repeated registration
                if ($packageName === $name) {
                    return;
                }
                throw new SystemException(sprintf(
                    'Cannot register "%s" (%s) as a compilable package; it has already been registered as %s.',
                    $name,
                    $config,
                    $packageName
                ));
            }
        }

        // Register the package
        $this->packages[$type][$name] = [
            'path' => $relativePath,
            'package' => $package,
            'config' => $config
        ];
    }

    /**
     * Returns an expected package type from its name
     */
    public function getPackageTypeFromName(string $package): ?string
    {
        // Check if package could be a module
        if (Str::startsWith($package, 'module-') && !in_array($package, ['system', 'backend', 'cms'])) {
            return static::TYPE_MODULE;
        }

        // Check if package could be a theme
        if (
            in_array('Cms', Config::get('cms.loadModules'))
            && Str::startsWith($package, 'theme-')
            && Theme::exists(Str::after($package, 'theme-'))
        ) {
            return static::TYPE_THEME;
        }

        // Check if a package could be a plugin
        if (PluginManager::instance()->exists($package)) {
            return static::TYPE_PLUGIN;
        }

        return null;
    }

    /**
     * Set the package.json file path used for checking if packages are in workspaces or ignored
     */
    public function setPackageJsonPath(string $packageJsonPath): static
    {
        $this->packageJson = new PackageJson($packageJsonPath);
        return $this;
    }

    /**
     * Returns the registration method for a compiler type
     */
    protected function getRegistrationMethod(string $type): string
    {
        return sprintf('register%sPackages', ucfirst($type));
    }

    /**
     * Check if the provided package is ignored.
     */
    protected function isPackageIgnored(string $packagePath): bool
    {
        return $this->packageJson->hasIgnoredPackage($packagePath);
    }
}
