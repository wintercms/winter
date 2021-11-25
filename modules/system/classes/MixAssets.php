<?php namespace System\Classes;

use ApplicationException;
use File;

/**
 * Mix assets using Laravel Mix for Node.js compilation and processing.
 *
 * This works similar to the `System\Classes\CombineAssets` class in that it allows modules, plugins and themes to
 * register configurations that will be passed on to Laravel Mix and Node.js for compilation and processing.
 *
 * This class requires the Laravel Mix package to be included by Composer - by default, this is a "dev" dependency as
 * some hosts will not allow Node.js to run. It is intended for this to only run within your development environment.
 *
 * Package registration involves registering the package as a "workspace" in Node.js, thereby combining all requirements
 * into one main dependency map.
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
        // Call plugins
        $packages = PluginManager::instance()->getRegistrationMethodValues('registerMixPackages');
        if (count($packages)) {

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
    public function registerPackage($name, $path, $mixJs = 'mix.js')
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
