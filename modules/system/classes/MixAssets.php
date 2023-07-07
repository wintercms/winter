<?php namespace System\Classes;

use File;
use Config;
use SystemException;
use Cms\Classes\Theme;
use Winter\Storm\Support\Str;
use System\Classes\PluginManager;
use Winter\Storm\Filesystem\PathResolver;

/**
 * Mix assets using Laravel Mix for Node.js compilation and processing.
 *
 * This works similar to the `System\Classes\CombineAssets` class in that it allows modules, plugins and themes to
 * register configurations that will be passed on to Laravel Mix and Node.js for compilation and processing.
 *
 * @package winter\wn-system-module
 * @author Ben Thomson <git@alfreido.com>, Jack Wilkinson <jax@jaxwilko.com>
 * @author Winter CMS
 */
class MixAssets
{
    use \Winter\Storm\Support\Traits\Singleton;

    /**
     * The filename that stores the package definition.
     */
    protected string $packageJson = 'package.json';

    /**
     * The filename that stores the Laravel Mix configuration
     */
    protected string $mixJs = 'winter.mix.js';

    /**
     * A list of packages registered for mixing.
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
        /*
        * Get packages registered in plugins.
        *
        * In the Plugin.php file for your plugin, you can define the "registerMixPackages" method and return an array,
        * with the name of the package being the key, and the build config path - relative to the plugin directory - as
        * the value.
        *
        * Example:
        *
        *   public function registerMixPackages()
        *   {
        *       return [
        *           'package-name-1' => 'winter.mix.js',
        *           'package-name-2' => 'assets/js/build.js',
        *       ];
        *   }
        */
        $packages = PluginManager::instance()->getRegistrationMethodValues('registerMixPackages');
        if (count($packages)) {
            foreach ($packages as $pluginCode => $packageArray) {
                if (!is_array($packageArray)) {
                    continue;
                }

                foreach ($packageArray as $name => $package) {
                    $this->registerPackage($name, PluginManager::instance()->getPluginPath($pluginCode) . '/' . $package);
                }
            }
        }

        // Get the currently enabled modules
        $enabledModules = Config::get('cms.loadModules', []);

        if (in_array('Cms', $enabledModules)) {
            // Allow current theme to define mix assets
            $theme = Theme::getActiveTheme();

            if (!is_null($theme)) {
                $mix = $theme->getConfigValue('mix', []);

                if (count($mix)) {
                    foreach ($mix as $name => $file) {
                        $this->registerPackage($name, $theme->getPath() . '/' . $file);
                    }
                }
            }
        }

        $packagePaths = [];

        // Search modules for Mix packages to autoregister
        foreach ($enabledModules as $module) {
            $module = strtolower($module);
            $path = base_path('modules' . DIRECTORY_SEPARATOR . $module) . DIRECTORY_SEPARATOR . $this->mixJs;
            if (File::exists($path)) {
                $packagePaths["module-$module"] = $path;
            }
        }

        // Search plugins for Mix packages to autoregister
        $plugins = PluginManager::instance()->getPlugins();
        foreach ($plugins as $plugin) {
            $path = $plugin->getPluginPath() . '/' . $this->mixJs;
            if (File::exists($path)) {
                $packagePaths[$plugin->getPluginIdentifier()] = $path;
            }
        }

        // Search themes for Mix packages to autoregister
        if (in_array('Cms', $enabledModules)) {
            $themes = Theme::all();
            foreach ($themes as $theme) {
                $path = $theme->getPath() . '/' . $this->mixJs;
                if (File::exists($path)) {
                    $packagePaths["theme-" . $theme->getId()] = $path;
                }
            }
        }

        // Register the autodiscovered Mix packages
        foreach ($packagePaths as $package => $path) {
            try {
                $this->registerPackage($package, $path);
            } catch (SystemException $e) {
                // Either the package name or the mixJs path have already been registered, skip.
                continue;
            }
        }
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
    public function fireCallbacks(): void
    {
        // Call callbacks
        foreach (static::$callbacks as $callback) {
            $callback($this);
        }
    }

    /**
     * Returns the count of packages registered.
     */
    public function getPackageCount(): int
    {
        return count($this->packages);
    }

    /**
     * Returns all packages registered.
     */
    public function getPackages(bool $includeIgnored = false): array
    {
        ksort($this->packages);

        if (!$includeIgnored) {
            return array_filter($this->packages, function ($package) {
                return !($package['ignored'] ?? false);
            });
        }

        return $this->packages;
    }

    /**
     * Registers an entity as a package for mixing.
     *
     * Entities can include plugins, components, themes, modules and much more.
     *
     * The name of the package is an alias that can be used to reference this package in other methods within this
     * class.
     *
     * By default, the MixAssets class will look for a `package.json` file for Node dependencies, and a `winter.mix.js`
     * file for the Laravel Mix configuration
     *
     * @param string $name The name of the package being registered
     * @param string $path The path to the Mix JS configuration file. If there is a related package.json file then it is
     *                      required to be present in the same directory as the winter.mix.js file
     */
    public function registerPackage(string $name, string $path): void
    {
        // Symbolize the path
        $path = File::symbolizePath($path);

        // Normalize the arguments
        $name = strtolower($name);
        $resolvedPath = PathResolver::resolve($path);
        $pinfo = pathinfo($resolvedPath);
        $path = Str::after($pinfo['dirname'], base_path() . DIRECTORY_SEPARATOR);
        $mixJs = $pinfo['basename'];

        // Require $mixJs to be a JS file
        $extension = File::extension($mixJs);
        if ($extension !== 'js') {
            throw new SystemException(
                sprintf('The mix configuration for package "%s" must be a JavaScript file ending with .js', $name)
            );
        }

        // Check that the package path exists
        if (!File::exists($path)) {
            throw new SystemException(
                sprintf('Cannot register "%s" as a Mix package; the "%s" path does not exist.', $name, $path)
            );
        }

        // Check for any existing packages already registered under the provided name
        if (isset($this->packages[$name])) {
            throw new SystemException(
                sprintf('Cannot register "%s" as a Mix package; it has already been registered at %s.', $name, $this->packages[$name]['mix'])
            );
        }

        $package = "$path/{$this->packageJson}";
        $mix = $path . DIRECTORY_SEPARATOR . $mixJs;

        // Check for any existing package that already registers the given Mix path
        foreach ($this->packages as $packageName => $config) {
            if ($config['mix'] === $mix) {
                throw new SystemException(
                    sprintf('Cannot register "%s" (%s) as a Mix package; it has already been registered as %s.', $name, $mix, $packageName)
                );
            }
        }

        // Register the package
        $this->packages[$name] = [
            'path' => $path,
            'package' => $package,
            'mix' => $mix,
            'ignored' => $this->isPackageIgnored($path),
        ];
    }

    /**
     * Check if the provided package is ignored.
     */
    protected function isPackageIgnored(string $packagePath): bool
    {
        // Load the main package.json for the project
        $packageJsonPath = base_path($this->packageJson);
        $packageJson = [];
        if (File::exists($packageJsonPath)) {
            $packageJson = json_decode(File::get($packageJsonPath), true);
        }
        $included = $packageJson['workspaces']['packages'] ?? [];
        $ignored = $packageJson['workspaces']['ignoredPackages'] ?? [];
        return in_array($packagePath, $ignored);
    }
}
