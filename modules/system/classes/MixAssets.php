<?php namespace System\Classes;

use File;
use ApplicationException;
use Cms\Classes\Theme;
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
     *
     * @var string
     */
    protected $packageJson = 'package.json';

    /**
     * A list of packages registered for mixing.
     *
     * @var array
     */
    protected $packages = [];

    /**
     * Registered callbacks.
     *
     * @var array
     */
    protected static $callbacks = [];

    /**
     * Constructor.
     *
     * @return void
     */
    public function init()
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
        *           'package-name-1' => 'winter-mix.js',
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
                    $this->registerPackage($name, PluginManager::instance()->getPluginPath($pluginCode), $package);
                }
            }
        }

        // Allow current theme to define mix assets
        $theme = Theme::getActiveTheme();

        if (!is_null($theme)) {
            $mix = $theme->getConfigValue('mix', []);

            if (count($mix)) {
                foreach ($mix as $name => $file) {
                    $path = PathResolver::resolve($theme->getPath() . '/' . $file);
                    $pinfo = pathinfo($path);

                    $this->registerPackage($name, $pinfo['dirname'], $pinfo['basename']);
                }
            }
        }
    }

    /**
     * Registers a callback for processing.
     *
     * @param callable $callback
     * @return void
     */
    public static function registerCallback(callable $callback)
    {
        static::$callbacks[] = $callback;
    }

    /**
     * Calls the deferred callbacks.
     *
     * @return void
     */
    public function fireCallbacks()
    {
        // Call callbacks
        foreach (static::$callbacks as $callback) {
            $callback($this);
        }
    }

    /**
     * Returns the count of packages registered.
     *
     * @return int
     */
    public function getPackageCount()
    {
        return count($this->packages);
    }

    /**
     * Returns all packages registered.
     *
     * @return array
     */
    public function getPackages()
    {
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
     * By default, the MixAssets class will look for a `package.json` file for Node dependencies, and a `
     *
     * @param string $name
     * @param string $path
     * @param string $packageJson
     * @param string $mixJson
     * @return void
     */
    public function registerPackage($name, $path, $mixJs = 'winter-mix.js')
    {
        // Require JS file for $mixJs
        $extension = File::extension($mixJs);
        if ($extension !== 'js') {
            throw new ApplicationException(
                sprintf('The mix configuration for package "%s" must be a JavaScript file ending with .js', $name)
            );
        }

        $path = rtrim(File::symbolizePath($path), '/\\');
        if (!File::exists($path . DIRECTORY_SEPARATOR . $this->packageJson)) {
            throw new ApplicationException(
                sprintf('Missing file "%s" in path "%s" for package "%s"', $this->packageJson, $path, $name)
            );
        }
        if (!File::exists($path . DIRECTORY_SEPARATOR . $mixJs)) {
            throw new ApplicationException(
                sprintf('Missing file "%s" in path "%s" for package "%s"', $mixJs, $path, $name)
            );
        }

        $this->packages[$name] = [
            'path' => $path,
            'package' => $path . DIRECTORY_SEPARATOR . $this->packageJson,
            'mix' => $path . DIRECTORY_SEPARATOR . $mixJs,
        ];
    }
}
