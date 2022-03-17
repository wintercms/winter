<?php namespace System\Classes;

use Db;
use App;
use Str;
use Log;
use File;
use Lang;
use View;
use Cache;
use Config;
use Schema;
use SystemException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Winter\Storm\Support\ClassLoader;
use Backend\Classes\NavigationManager;

/**
 * Plugin manager
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginManager
{
    use \Winter\Storm\Support\Traits\Singleton;

    public const DISABLED_MISSING = 'disabled-missing';
    public const DISABLED_REQUEST = 'disabled-request';
    public const DISABLED_BY_USER = 'disabled-user';
    public const DISABLED_REPLACED = 'disabled-replaced';
    public const DISABLED_REPLACEMENT_FAILED = 'disabled-replacement-failed';
    public const DISABLED_BY_CONFIG = 'disabled-config';
    public const DISABLED_MISSING_DEPENDENCIES = 'disabled-dependencies';

    /**
     * The application instance, since Plugins are an extension of a Service Provider
     */
    protected $app;

    /**
     * @var PluginBase[] Container array used for storing plugin information objects.
     */
    protected $plugins = [];

    /**
     * @var array A map of plugins and their directory paths.
     */
    protected $pathMap = [];

    /**
     * @var array A map of normalized plugin identifiers [lowercase.identifier => Normalized.Identifier]
     */
    protected $normalizedMap = [];

    /**
     * @var array A map of plugin identifiers with their replacements [Original.Plugin => Replacement.Plugin]
     */
    protected $replacementMap = [];

    /**
     * @var array A map of plugins that are currently replaced [Original.Plugin => Replacement.Plugin]
     */
    protected $activeReplacementMap = [];

    /**
     * @var bool Flag to indicate that all plugins have had the register() method called by registerAll() being called on this class.
     */
    protected $registered = false;

    /**
     * @var bool Flag to indicate that all plugins have had the boot() method called by bootAll() being called on this class.
     */
    protected $booted = false;

    /**
     * @var string Cache key for the disabled plugin data
     */
    protected $disabledCacheKey = 'system-plugins-disabled';

    /**
     * @var array Array of disabled plugins
     */
    protected $disabledPlugins = [];

    /**
     * @var array Cache of registration method results.
     */
    protected $registrationMethodCache = [];

    /**
     * @var bool Prevent all plugins from registering or booting
     */
    public static $noInit = false;

    /**
     * Initializes the plugin manager
     */
    protected function init()
    {
        $this->app = App::make('app');
        $this->loadDisabled();
        $this->loadPlugins();
        $this->loadDependencies();

        $this->registerReplacedPlugins();
    }

    /**
     * Finds all available plugins and loads them in to the $this->plugins array.
     *
     * @return array
     */
    public function loadPlugins()
    {
        $this->plugins = [];

        /**
         * Locate all plugins and binds them to the container
         */
        foreach ($this->getPluginNamespaces() as $namespace => $path) {
            $this->loadPlugin($namespace, $path);
        }

        $this->sortDependencies();

        return $this->plugins;
    }

    /**
     * Loads a single plugin into the manager.
     *
     * @param string $namespace Eg: Acme\Blog
     * @param string $path Eg: plugins_path().'/acme/blog';
     * @return void
     */
    public function loadPlugin($namespace, $path)
    {
        $className = $namespace . '\Plugin';
        $classPath = $path . '/Plugin.php';

        try {
            // Autoloader failed?
            if (!class_exists($className)) {
                include_once $classPath;
            }

            // Not a valid plugin!
            if (!class_exists($className)) {
                return;
            }

            $pluginObj = new $className($this->app);
        } catch (\Throwable $e) {
            Log::error('Plugin ' . $className . ' could not be instantiated.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return;
        }

        $classId = $this->getIdentifier($pluginObj);

        $this->plugins[$classId] = $pluginObj;
        $this->pathMap[$classId] = $path;
        $this->normalizedMap[strtolower($classId)] = $classId;

        $replaces = $pluginObj->getReplaces();
        if ($replaces) {
            foreach ($replaces as $replace) {
                $this->replacementMap[$replace] = $classId;
            }
        }

        return $pluginObj;
    }

    /**
     * Runs the register() method on all plugins. Can only be called once.
     *
     * @param bool $force Defaults to false, if true will force the re-registration of all plugins. Use unregisterAll() instead.
     * @return void
     */
    public function registerAll($force = false)
    {
        if ($this->registered && !$force) {
            return;
        }

        foreach ($this->plugins as $pluginId => $plugin) {
            $this->registerPlugin($plugin, $pluginId);
        }

        // Ensure that route attributes are properly loaded
        // @see Illuminate\Foundation\Support\Providers\RouteServiceProvider->register()
        // @fixes wintercms/winter#106
        $this->app->booting(function () {
            $this->app['router']->getRoutes()->refreshNameLookups();
            $this->app['router']->getRoutes()->refreshActionLookups();
        });

        $this->registered = true;
    }

    /**
     * Unregisters all plugins: the inverse of registerAll().
     *
     * @return void
     */
    public function unregisterAll()
    {
        $this->registered = false;
        $this->plugins = [];
        $this->replacementMap = [];
    }

    /**
     * Registers a single plugin object.
     */
    public function registerPlugin(PluginBase $plugin, ?string $pluginId = null): void
    {
        if (!$pluginId) {
            $pluginId = $this->getIdentifier($plugin);
        }

        $pluginPath = $this->getPluginPath($plugin);
        $pluginNamespace = strtolower($pluginId);

        /*
         * Register language namespaces
         */
        $langPath = $pluginPath . '/lang';
        if (File::isDirectory($langPath)) {
            Lang::addNamespace($pluginNamespace, $langPath);
        }

        /**
         * Prevent autoloaders from loading if plugin is disabled
         */
        if ($this->isDisabled($pluginId)) {
            return;
        }

        /*
         * Register plugin class autoloaders
         */
        $autoloadPath = $pluginPath . '/vendor/autoload.php';
        if (File::isFile($autoloadPath)) {
            ComposerManager::instance()->autoload($pluginPath . '/vendor');
        }

        /*
         * Register configuration path
         */
        $configPath = $pluginPath . '/config';
        if (File::isDirectory($configPath)) {
            Config::package($pluginNamespace, $configPath, $pluginNamespace);
        }

        /*
         * Register views path
         */
        $viewsPath = $pluginPath . '/views';
        if (File::isDirectory($viewsPath)) {
            View::addNamespace($pluginNamespace, $viewsPath);
        }

        /*
         * Register namespace aliases for any replaced plugins
         */
        if ($replaces = $plugin->getReplaces()) {
            foreach ($replaces as $replace) {
                $replaceNamespace = $this->getNamespace($replace);

                $this->app->make(ClassLoader::class)->addNamespaceAliases([
                    // class_alias() expects order to be $real, $alias
                    $this->getNamespace($pluginId) => $replaceNamespace,
                ]);
            }
        }

        /**
         * Disable plugin registration for restricted pages, unless elevated
         */
        if (self::$noInit && !$plugin->elevated) {
            return;
        }

        /**
         * Run the plugin's register() method
         */
        $plugin->register();

        /*
         * Add init, if available
         */
        $initFile = $pluginPath . '/init.php';
        if (File::exists($initFile)) {
            require $initFile;
        }

        /*
         * Add routes, if available
         */
        $routesFile = $pluginPath . '/routes.php';
        if (File::exists($routesFile) && !$this->app->routesAreCached()) {
            require $routesFile;
        }
    }

    /**
     * Runs the boot() method on all plugins. Can only be called once.
     *
     * @param bool $force Defaults to false, if true will force the re-booting of all plugins
     * @return void
     */
    public function bootAll($force = false)
    {
        if ($this->booted && !$force) {
            return;
        }

        foreach ($this->plugins as $plugin) {
            $this->bootPlugin($plugin);
        }

        $this->booted = true;
    }

    /**
     * Boots the provided plugin object.
     */
    public function bootPlugin(PluginBase $plugin): void
    {
        if (
            !$plugin
            || (self::$noInit && !$plugin->elevated)
            || $this->isDisabled($plugin)
        ) {
            return;
        }

        $plugin->boot();
    }

    /**
     * Returns the directory path to a plugin
     *
     * @param PluginBase|string $id The plugin to get the path for
     * @return string|null
     */
    public function getPluginPath($id)
    {
        $classId = $this->getIdentifier($id);
        $classId = $this->normalizeIdentifier($classId);
        if (!isset($this->pathMap[$classId])) {
            return null;
        }

        return File::normalizePath($this->pathMap[$classId]);
    }

    /**
     * Check if a plugin exists and is enabled.
     *
     * @param string $id Plugin identifier, eg: Namespace.PluginName
     * @return bool
     */
    public function exists($id)
    {
        return $this->findByIdentifier($id) && !$this->isDisabled($id);
    }

    /**
     * Returns an array with all enabled plugins
     *
     * @return array [$code => $pluginObj]
     */
    public function getPlugins()
    {
        return array_diff_key($this->plugins, $this->disabledPlugins);
    }

    /**
     * Returns an array will all plugins detected on the filesystem
     *
     * @return array [$code => $pluginObj]
     */
    public function getAllPlugins()
    {
        return $this->plugins;
    }

    /**
     * Returns a plugin registration class based on its namespace (Author\Plugin).
     *
     * @param string $namespace
     * @return PluginBase|null
     */
    public function findByNamespace($namespace)
    {
        $identifier = $this->getIdentifier($namespace);

        return $this->plugins[$identifier] ?? null;
    }

    /**
     * Returns a plugin registration class based on its identifier (Author.Plugin).
     *
     * @param string|PluginBase $identifier
     * @param bool $ignoreReplacements
     * @return PluginBase|null
     */
    public function findByIdentifier($identifier, bool $ignoreReplacements = false)
    {
        if (!$ignoreReplacements && is_string($identifier) && isset($this->replacementMap[$identifier])) {
            $identifier = $this->replacementMap[$identifier];
        }

        if (!isset($this->plugins[$identifier])) {
            $code = $this->getIdentifier($identifier);
            $identifier = $this->normalizeIdentifier($code);
        }

        return $this->plugins[$identifier] ?? null;
    }

    /**
     * Checks to see if a plugin has been registered.
     *
     * @param string|PluginBase
     * @return bool
     */
    public function hasPlugin($namespace)
    {
        $classId = $this->getIdentifier($namespace);
        $normalized = $this->normalizeIdentifier($classId);

        return isset($this->plugins[$normalized]) || isset($this->replacementMap[$normalized]);
    }

    /**
     * Returns a flat array of vendor plugin namespaces and their paths
     *
     * @return array ['Author\Plugin' => 'plugins/author/plugin']
     */
    public function getPluginNamespaces()
    {
        $classNames = [];

        foreach ($this->getVendorAndPluginNames() as $vendorName => $vendorList) {
            foreach ($vendorList as $pluginName => $pluginPath) {
                $namespace = '\\'.$vendorName.'\\'.$pluginName;
                $namespace = Str::normalizeClassName($namespace);
                $classNames[$namespace] = $pluginPath;
            }
        }

        return $classNames;
    }

    /**
     * Returns a 2 dimensional array of vendors and their plugins.
     *
     * @return array ['vendor' => ['author' => 'plugins/author/plugin']]
     */
    public function getVendorAndPluginNames()
    {
        $plugins = [];

        $dirPath = plugins_path();
        if (!File::isDirectory($dirPath)) {
            return $plugins;
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::FOLLOW_SYMLINKS)
        );
        $it->setMaxDepth(2);
        $it->rewind();

        while ($it->valid()) {
            if (($it->getDepth() > 1) && $it->isFile() && (strtolower($it->getFilename()) == "plugin.php")) {
                $filePath = dirname($it->getPathname());
                $pluginName = basename($filePath);
                $vendorName = basename(dirname($filePath));
                $plugins[$vendorName][$pluginName] = $filePath;
            }

            $it->next();
        }

        return $plugins;
    }

    /**
     * Resolves a plugin identifier (Author.Plugin) from a plugin class name or object.
     *
     * @param mixed Plugin class name or object
     * @return string Identifier in format of Author.Plugin
     */
    public function getIdentifier($namespace)
    {
        $namespace = Str::normalizeClassName($namespace);
        if (strpos($namespace, '\\') === null) {
            return $namespace;
        }

        $parts = explode('\\', $namespace);
        $slice = array_slice($parts, 1, 2);
        $namespace = implode('.', $slice);

        return $namespace;
    }

    /**
     * Resolves a plugin namespace (Author\Plugin) from a plugin class name, identifier or object.
     *
     * @param mixed Plugin class name, identifier or object
     * @return string Namespace in format of Author\Plugin
     */
    public function getNamespace($identifier)
    {
        if (
            is_object($identifier)
            || (is_string($identifier) && strpos($identifier, '.') === null)
        ) {
            return Str::normalizeClassName($identifier);
        }

        $parts = explode('.', $identifier);
        $slice = array_slice($parts, 0, 2);
        $namespace = implode('\\', $slice);

        return Str::normalizeClassName($namespace);
    }

    /**
     * Takes a human plugin code (acme.blog) and makes it authentic (Acme.Blog)
     * Returns the provided identifier if a match isn't found
     *
     * @param  string $identifier
     * @return string
     */
    public function normalizeIdentifier($identifier)
    {
        $id = strtolower($identifier);
        if (isset($this->normalizedMap[$id])) {
            return $this->normalizedMap[$id];
        }

        return $identifier;
    }

    /**
     * Spins over every plugin object and collects the results of a method call. Results are cached in memory.
     *
     * @param  string $methodName
     * @return array
     */
    public function getRegistrationMethodValues($methodName)
    {
        if (isset($this->registrationMethodCache[$methodName])) {
            return $this->registrationMethodCache[$methodName];
        }

        $results = [];
        $plugins = $this->getPlugins();

        foreach ($plugins as $id => $plugin) {
            if (!is_callable([$plugin, $methodName])) {
                continue;
            }

            $results[$id] = $plugin->{$methodName}();
        }

        return $this->registrationMethodCache[$methodName] = $results;
    }

    //
    // Disability
    //

    /**
     * Clears the disabled plugins cache file
     *
     * @return void
     */
    public function clearDisabledCache()
    {
        // Storage::delete($this->metaFile);
        Cache::forget($this->disabledCacheKey);
        $this->disabledPlugins = [];
    }

    /**
     * Loads all disabled plugins from the cached JSON file.
     *
     * @return void
     */
    protected function loadDisabled()
    {
        // Check the config files for disabled plugins
        if (($configDisabled = Config::get('cms.disablePlugins')) && is_array($configDisabled)) {
            foreach ($configDisabled as $disabled) {
                $this->flagPlugin($disabled, static::DISABLED_BY_CONFIG);
            }
        }

        // Check the database for disabled plugins
        $userDisabled = Cache::rememberForever($this->disabledCacheKey, function () {
            return $this->getDisabledPluginsFromDb();
        });
        foreach ($userDisabled as $code) {
            $this->flagPlugin($code, static::DISABLED_BY_USER);
        }
    }

    /**
     * Determines if a plugin is disabled by looking at the meta information
     * or the application configuration.
     *
     * @param string|PluginBase $id
     * @return bool
     */
    public function isDisabled($id)
    {
        $code = $this->getIdentifier($id);
        $normalized = $this->normalizeIdentifier($code);

        return isset($this->disabledPlugins[$normalized]);
    }

    /**
     * Write the disabled plugins to a meta file.
     *
     * @return void
     */
    protected function writeDisabled()
    {
        Cache::forever($this->disabledCacheKey, $this->disabledPlugins);
    }

    /**
     * Returns plugin codes that have been flagged disabled in the database
     */
    protected function getDisabledPluginsFromDb(): array
    {
        $disabled = [];

        if (
            $this->app->hasDatabase()
            && Schema::hasTable('system_plugin_versions')
        ) {
            $disabled = Db::table('system_plugin_versions')->where('is_disabled', 1)->lists('code') ?? [];
        }

        return $disabled;
    }

    /**
     * Returns the plugin replacements defined in $this->replacementMap
     *
     * @return array
     */
    public function getReplacementMap()
    {
        return $this->replacementMap;
    }

    /**
     * Returns the actively replaced plugins defined in $this->activeReplacementMap
     * @param string $pluginIdentifier Plugin code/namespace
     * @return array|null
     */
    public function getActiveReplacementMap(string $pluginIdentifier = null)
    {
        if (!$pluginIdentifier) {
            return $this->activeReplacementMap;
        }
        return $this->activeReplacementMap[$pluginIdentifier] ?? null;
    }

    /**
     * Evaluates and initializes the plugin replacements defined in $this->replacementMap
     *
     * @return void
     */
    public function registerReplacedPlugins()
    {
        if (empty($this->replacementMap)) {
            return;
        }

        foreach ($this->replacementMap as $target => $replacement) {
            // Alias the replaced plugin to the replacing plugin if the replaced plugin isn't present
            if (!isset($this->plugins[$target])) {
                $this->aliasPluginAs($replacement, $target);
                continue;
            }

            // Only allow one of the replaced plugin or the replacing plugin to exist
            // at once depending on whether the version constraints are met or not
            if ($this->plugins[$replacement]->canReplacePlugin($target, $this->plugins[$target]->getPluginVersion())) {
                // Alias the replaced plugin to the replacing plugin
                $this->aliasPluginAs($replacement, $target);

                // Set the plugin flags
                $this->flagPlugin($target, static::DISABLED_REPLACED);
                $this->unflagPlugin($replacement, static::DISABLED_REPLACEMENT_FAILED);

                // Register this plugin as actively replaced
                $this->activeReplacementMap[$target] = $replacement;
            } else {
                // Set the plugin flags
                $this->flagPlugin($replacement, static::DISABLED_REPLACEMENT_FAILED);
                $this->unflagPlugin($target, static::DISABLED_REPLACED);

                // Remove the replacement alias to prevent redirection to a disabled plugin
                unset($this->replacementMap[$target]);
            }
        }
    }

    /**
     * Registers namespace aliasing for multiple subsystems
     *
     * @param string $namespace Plugin code
     * @param string $alias     Plugin alias code
     * @return void
     */
    protected function aliasPluginAs(string $namespace, string $alias)
    {
        Lang::registerNamespaceAlias($namespace, $alias);
        Config::registerNamespaceAlias($namespace, $alias);
        Config::registerPackageFallback($namespace, $alias);
        SettingsManager::lazyRegisterOwnerAlias($namespace, $alias);
        NavigationManager::lazyRegisterOwnerAlias($namespace, $alias);
    }

    /**
     * Sets the provided flag on the provided plugin
     */
    protected function flagPlugin(string $plugin, string $flag): void
    {
        $plugin = $this->normalizeIdentifier($plugin);
        $this->pluginFlags[$plugin][$flag] = true;
    }

    /**
     * Removes the provided flag from the provided plugin
     */
    protected function unflagPlugin(string $plugin, string $flag): void
    {
        $plugin = $this->normalizeIdentifier($plugin);
        unset($this->pluginFlags[$plugin][$flag]);
    }

    /**
     * Get the PluginVersion record for the provided plugin
     *
     * @throws InvalidArgumentException if unable to find the requested plugin record in the database
     */
    protected function getPluginRecord(string $plugin): PluginVersion
    {
        $plugin = $this->normalizeIdentifier($plugin);
        if (isset($this->pluginRecords[$plugin])) {
            return $this->pluginRecords[$plugin];
        }

        $record = PluginVersion::where('code', $plugin)->first();
        if (!$record) {
            throw new InvalidArgumentException("$plugin was not found in the database.");
        }

        return $this->pluginRecords[$plugin] = $record;
    }

    /**
     * Flags the provided plugin as "frozen" (updates cannot be downloaded / installed)
     */
    public function freezePlugin(string $plugin): void
    {
        $record = $this->getPluginRecord($plugin);
        $record->is_frozen = true;
        $record->save();
    }

    /**
     * "Unfreezes" the provided plugin, allowing for updates to be performed
     */
    public function unfreezePlugin(string $plugin): void
    {
        $record = $this->getPluginRecord($plugin);
        $record->is_frozen = false;
        $record->save();
    }

    /**
     * Disables the provided plugin using the provided flag (defaults to static::DISABLED_BY_USER)
     */
    public function disablePlugin(string $plugin, $flag = static::DISABLED_BY_USER): bool
    {
        if ($flag === true) {
            $flag = static::DISABLED_BY_USER;
        }

        $code = $this->getIdentifier($id);
        $code = $this->normalizeIdentifier($code);
        if (isset($this->disabledPlugins[$code])) {
            return false;
        }

        $this->disabledPlugins[$code] = $isUser;
        $this->writeDisabled();

        // Updates the database record for the plugin if required
        if ($flag === static::DISABLED_BY_USER) {
            $record = $this->getPluginRecord($plugin);
            $record->is_disabled = true;
            $record->save();
        }

        // @TODO: Update the cahce of disabled plugins

        return true;
    }

    /**
     * Enables the provided plugin using the provided flag (defaults to static::DISABLED_BY_USER)
     */
    public function enablePlugin(string $plugin, $flag = static::DISABLED_BY_USER): bool
    {
        if ($flag === true) {
            $flag = static::DISABLED_BY_USER;
        }

        $code = $this->getIdentifier($id);
        $code = $this->normalizeIdentifier($code);
        if (!isset($this->disabledPlugins[$code])) {
            return false;
        }

        // Prevent system from enabling plugins disabled by the user
        if (!$isUser && $this->disabledPlugins[$code] === true) {
            return false;
        }

        unset($this->disabledPlugins[$code]);
        $this->writeDisabled();

        return true;
    }

    //
    // Dependencies
    //

    /**
     * Scans the system plugins to locate any dependencies that are not currently
     * installed. Returns an array of missing plugin codes keyed by the plugin that requires them.
     *
     *     ['Author.Plugin' => ['Required.Plugin1', 'Required.Plugin2']
     *
     *     PluginManager::instance()->findMissingDependencies();
     *
     * @return array
     */
    public function findMissingDependencies()
    {
        $missing = [];

        foreach ($this->plugins as $id => $plugin) {
            if (!$required = $this->getDependencies($plugin)) {
                continue;
            }

            foreach ($required as $require) {
                if ($this->hasPlugin($require)) {
                    continue;
                }

                if (!in_array($require, $missing)) {
                    $missing[$this->getIdentifier($plugin)][] = $require;
                }
            }
        }

        return $missing;
    }

    /**
     * Checks plugin dependencies and flags plugins with missing dependencies as disabled
     */
    protected function loadDependencies(): void
    {
        foreach ($this->plugins as $id => $plugin) {
            if (!$plugin->checkDependencies()) {
                $this->flagPlugin($id, static::DISABLED_MISSING_DEPENDENCIES);
            } else {
                $this->unflagPlugin($id, static::DISABLED_MISSING_DEPENDENCIES);
            }
        }
    }

    /**
     * Sorts a collection of plugins, in the order that they should be actioned,
     * according to their given dependencies. Least dependent come first.
     *
     * @return array Array of sorted plugin identifiers and instantiated classes ['Author.Plugin' => PluginBase]
     * @throws SystemException If a possible circular dependency is detected
     */
    protected function sortDependencies()
    {
        ksort($this->plugins);

        /*
         * Canvas the dependency tree
         */
        $checklist = $this->plugins;
        $result = [];

        $loopCount = 0;
        while (count($checklist)) {
            if (++$loopCount > 2048) {
                throw new SystemException('Too much recursion! Check for circular dependencies in your plugins.');
            }

            foreach ($checklist as $code => $plugin) {
                /*
                 * Get dependencies and remove any aliens, replacing any dependencies which have been superceded
                 * by another plugin.
                 */
                $depends = $this->getDependencies($plugin);

                $depends = array_map(function ($depend) {
                    if (isset($this->replacementMap[$depend])) {
                        return $this->replacementMap[$depend];
                    }

                    return $depend;
                }, $depends);

                $depends = array_filter($depends, function ($pluginCode) {
                    return isset($this->plugins[$pluginCode]);
                });

                /*
                 * No dependencies
                 */
                if (!$depends) {
                    array_push($result, $code);
                    unset($checklist[$code]);
                    continue;
                }

                /*
                 * Find dependencies that have not been checked
                 */
                $depends = array_diff($depends, $result);
                if (count($depends) > 0) {
                    continue;
                }

                /*
                 * All dependencies are checked
                 */
                array_push($result, $code);
                unset($checklist[$code]);
            }
        }

        /*
         * Reassemble plugin map
         */
        $sortedPlugins = [];

        foreach ($result as $code) {
            $sortedPlugins[$code] = $this->plugins[$code];
        }

        return $this->plugins = $sortedPlugins;
    }

    /**
     * Returns the plugin identifiers that are required by the supplied plugin.
     */
    public function getDependencies(string|PluginBase $plugin): array
    {
        if (is_string($plugin) && (!$plugin = $this->findByIdentifier($plugin))) {
            return [];
        }

        if (!isset($plugin->require) || !$plugin->require) {
            return [];
        }

        return array_map(function ($require) {
            return $this->replacementMap[$require] ?? $require;
        }, is_array($plugin->require) ? $plugin->require : [$plugin->require]);
    }

    /**
     * @deprecated Plugins are now sorted by default. See getPlugins()
     * Remove if year >= 2022
     */
    public function sortByDependencies($plugins = null)
    {
        traceLog('PluginManager::sortByDependencies is deprecated. Plugins are now sorted by default. Use PluginManager::getPlugins()');

        return array_keys($plugins ?: $this->getPlugins());
    }

    //
    // Management
    //

    /**
     * Completely roll back and delete a plugin from the system.
     *
     * @param string $id Plugin code/namespace
     * @return void
     */
    public function deletePlugin($id)
    {
        /*
         * Rollback plugin
         */
        UpdateManager::instance()->rollbackPlugin($id);

        /*
         * Delete from file system
         */
        if ($pluginPath = self::instance()->getPluginPath($id)) {
            File::deleteDirectory($pluginPath);
        }
    }

    /**
     * Tears down a plugin's database tables and rebuilds them.
     *
     * @param string $id Plugin code/namespace
     * @return void
     */
    public function refreshPlugin($id)
    {
        $manager = UpdateManager::instance();
        $manager->rollbackPlugin($id);
        $manager->updatePlugin($id);
    }
}
