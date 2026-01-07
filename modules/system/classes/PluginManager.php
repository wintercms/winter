<?php

namespace System\Classes;

use Backend\Classes\NavigationManager;
use FilesystemIterator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use System\Models\PluginVersion;
use SystemException;
use Winter\Storm\Foundation\Application;
use Winter\Storm\Support\ClassLoader;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Str;

/**
 * Plugin manager
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginManager
{
    use \Winter\Storm\Support\Traits\Singleton;

    //
    // Disabled by system
    //

    public const DISABLED_MISSING = 'disabled-missing';
    public const DISABLED_REPLACED = 'disabled-replaced';
    public const DISABLED_REPLACEMENT_FAILED = 'disabled-replacement-failed';
    public const DISABLED_MISSING_DEPENDENCIES = 'disabled-dependencies';

    //
    // Explicitly disabled for a reason
    //

    public const DISABLED_REQUEST = 'disabled-request';
    public const DISABLED_BY_USER = 'disabled-user';
    public const DISABLED_BY_CONFIG = 'disabled-config';

    /**
     * The application instance, since Plugins are an extension of a Service Provider
     */
    protected Application $app;

    /**
     * @var PluginBase[] Container array used for storing plugin information objects.
     */
    protected $plugins = [];

    /**
     * @var array Array of plugin codes that contain any flags currently associated with the plugin
     */
    protected $pluginFlags = [];

    /**
     * @var PluginVersion[] Local cache of loaded PluginVersion records keyed by plugin code
     */
    protected $pluginRecords = [];

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
    protected function init(): void
    {
        $this->app = App::make('app');

        // Load the plugins from the filesystem and sort them by dependencies
        $this->loadPlugins();

        // Loads the plugin flags (disabled & replacement states) from the cache
        // regenerating them if required.
        $this->loadPluginFlags();

        // Register plugin replacements
        $this->registerPluginReplacements();
    }

    /**
     * Finds all available plugins and loads them in to the $this->plugins array.
     */
    public function loadPlugins(): array
    {
        $this->plugins = [];

        /**
         * Locate all plugins and binds them to the container
         */
        foreach ($this->getPluginNamespaces() as $namespace => $path) {
            $this->loadPlugin($namespace, $path);
        }

        // Sort all the plugins by number of dependencies
        $this->sortByDependencies();

        return $this->getAllPlugins();
    }

    /**
     * Loads a single plugin into the manager.
     *
     * @param string $namespace Eg: Acme\Blog
     * @param string $path Eg: plugins_path().'/acme/blog';
     */
    public function loadPlugin(string $namespace, string $path): ?PluginBase
    {
        $className = $namespace . '\Plugin';
        $classPath = $path . '/Plugin.php';

        $this->app->make(ClassLoader::class)->autoloadPackage($namespace, $path);

        try {
            // Autoloader failed?
            if (!class_exists($className)) {
                include_once $classPath;
            }

            // Not a valid plugin!
            if (!class_exists($className)) {
                return null;
            }

            $pluginObj = new $className($this->app);
        } catch (\Throwable $e) {
            Log::error('Plugin ' . $className . ' could not be instantiated.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }

        $classId = $this->getIdentifier($pluginObj);
        $lowerClassId = strtolower($classId);

        $this->plugins[$lowerClassId] = $pluginObj;
        $this->normalizedMap[$lowerClassId] = $classId;

        $replaces = $pluginObj->getReplaces();
        if ($replaces) {
            foreach ($replaces as $replace) {
                $lowerReplace = strtolower($replace);
                $this->replacementMap[$lowerReplace] = $lowerClassId;

                if (!isset($this->normalizedMap[$lowerReplace])) {
                    $this->normalizedMap[$lowerReplace] = $replace;
                }
            }
        }

        return $pluginObj;
    }

    /**
     * Get the cache key for the current plugin manager state
     */
    protected function getFlagCacheKey(): string
    {
        $loadedPlugins = array_keys($this->plugins);
        $configDisabledPlugins = Config::get('cms.disablePlugins', []);
        if (!is_array($configDisabledPlugins)) {
            $configDisabledPlugins = [];
        }
        $plugins = $loadedPlugins + $configDisabledPlugins;

        return 'system.pluginmanager.state.' . md5(implode('.', $plugins));
    }

    /**
     * Loads the plugin flags (disabled & replacement states) from the cache
     * regenerating them if required.
     */
    public function loadPluginFlags(): void
    {
        // Cache the data for a month so that stale keys can be autocleaned if necessary
        $data = Cache::remember($this->getFlagCacheKey(), now()->addMonths(1), function () {
            // Check the config files & database for plugins to disable
            $this->loadDisabled();

            // Check plugin dependencies for plugins to disable
            $this->loadDependencies();

            // Check plugin replacments for plugins to disable
            $this->detectPluginReplacements();

            return [
                $this->pluginFlags,
                $this->replacementMap,
                $this->activeReplacementMap,
            ];
        });

        list($this->pluginFlags, $this->replacementMap, $this->activeReplacementMap) = $data;
    }

    /**
     * Reset the plugin flag cache
     */
    public function clearFlagCache(): void
    {
        Cache::forget($this->getFlagCacheKey());
    }

    /**
     * Runs the register() method on all plugins. Can only be called once.
     *
     * @param bool $force Defaults to false, if true will force the re-registration of all plugins. Use unregisterAll() instead.
     */
    public function registerAll(bool $force = false): void
    {
        if ($this->registered && !$force) {
            return;
        }

        try {
            foreach ($this->plugins as $pluginId => $plugin) {
                $this->registerPlugin($plugin, $pluginId);
            }
        } catch (QueryException $ex) {
            // SQLSTATE[42S02]: Base table or view not found - migrations haven't run yet
            if ($ex->getCode() === '42S02') {
                Log::error("$pluginId cannot be registered, missing database content. Try running migrations. Error: " . $ex->getMessage());
            }
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
     */
    public function unregisterAll(): void
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
     */
    public function bootAll(bool $force = false): void
    {
        if ($this->booted && !$force) {
            return;
        }

        try {
            foreach ($this->plugins as $pluginId => $plugin) {
                $this->bootPlugin($plugin);
            }
        } catch (QueryException $ex) {
            // SQLSTATE[42S02]: Base table or view not found - migrations haven't run yet
            if ($ex->getCode() === '42S02') {
                Log::error("$pluginId cannot be booted, missing database content. Try running migrations. Error: " . $ex->getMessage());
            }
        }

        $this->booted = true;
    }

    /**
     * Boots the provided plugin object.
     */
    public function bootPlugin(PluginBase $plugin): void
    {
        if ((self::$noInit && !$plugin->elevated) || $this->isDisabled($plugin)) {
            return;
        }

        $plugin->boot();
    }

    /**
     * Returns the directory path to a plugin
     */
    public function getPluginPath(PluginBase|string $plugin): ?string
    {
        return $this->findByIdentifier($plugin, true)?->getPluginPath();
    }

    /**
     * Check if a plugin exists and is enabled.
     *
     * @param string $id Plugin identifier, eg: Namespace.PluginName
     * @return bool
     */
    public function exists(PluginBase|string $plugin): bool
    {
        return $this->findByIdentifier($plugin) && !$this->isDisabled($plugin);
    }

    /**
     * Returns an array with all enabled plugins
     *
     * @return array [$code => $pluginObj]
     */
    public function getPlugins(): array
    {
        $activePlugins = array_diff_key($this->plugins, $this->pluginFlags);
        return array_combine(
            array_map(
                fn($code) => $this->normalizedMap[$code],
                array_keys($activePlugins)
            ),
            $activePlugins
        );
    }

    /**
     * Returns an array will all plugins detected on the filesystem
     *
     * @return array [$code => $pluginObj]
     */
    public function getAllPlugins(): array
    {
        $plugins = [];

        foreach ($this->plugins as $code => $plugin) {
            $plugins[$this->normalizedMap[$code]] = $plugin;
        }

        return $plugins;
    }

    /**
     * Returns a plugin registration class based on its namespace (Author\Plugin).
     */
    public function findByNamespace(string $namespace): ?PluginBase
    {
        $identifier = $this->getIdentifier($namespace, true);

        return $this->plugins[$identifier] ?? null;
    }

    /**
     * Returns a plugin registration class based on its identifier (Author.Plugin).
     */
    public function findByIdentifier(PluginBase|string $identifier, bool $ignoreReplacements = false): ?PluginBase
    {
        if ($identifier instanceof PluginBase) {
            return $identifier;
        }

        $identifier = $this->getNormalizedIdentifier($identifier, true);

        if (!$ignoreReplacements && isset($this->replacementMap[$identifier])) {
            $identifier = $this->replacementMap[$identifier];
        }

        return $this->plugins[$identifier] ?? null;
    }

    /**
     * Checks to see if a plugin has been registered.
     */
    public function hasPlugin(PluginBase|string $plugin): bool
    {
        $normalized = $this->getNormalizedIdentifier($plugin, true);

        return isset($this->plugins[$normalized]) || isset($this->replacementMap[$normalized]);
    }

    /**
     * Returns a flat array of vendor plugin namespaces and their paths
     * ['Author\Plugin' => 'plugins/author/plugin']
     */
    public function getPluginNamespaces(): array
    {
        $classNames = [];

        foreach ($this->getVendorAndPluginNames() as $vendorName => $vendorList) {
            foreach ($vendorList as $pluginName => $pluginPath) {
                $namespace = '\\' . $vendorName . '\\' . $pluginName;
                $namespace = Str::normalizeClassName($namespace);
                $classNames[$namespace] = $pluginPath;
            }
        }

        return $classNames;
    }

    /**
     * Returns a 2 dimensional array of vendors and their plugins.
     * ['vendor' => ['author' => 'plugins/author/plugin']]
     */
    public function getVendorAndPluginNames(): array
    {
        $plugins = [];

        $dirPath = $this->app->pluginsPath();
        if (!File::isDirectory($dirPath)) {
            return $plugins;
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath, FilesystemIterator::FOLLOW_SYMLINKS)
        );
        $it->setMaxDepth(2);
        $it->rewind();

        while ($it->valid()) {
            if (($it->getDepth() > 1) && $it->isFile() && (strtolower($it->getFilename()) === "plugin.php")) {
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
     * Resolves a plugin identifier (Author.Plugin) from a plugin class name
     * (Author\Plugin) or PluginBase instance.
     */
    public function getIdentifier(PluginBase|string $plugin, bool $lower = false): string
    {
        $namespace = Str::normalizeClassName($plugin);
        if (strpos($namespace, '\\') === null) {
            return $namespace;
        }

        $parts = explode('\\', $namespace);
        $slice = array_slice($parts, 1, 2);
        $namespace = implode('.', $slice);

        return $lower ? strtolower($namespace) : $namespace;
    }

    /**
     * Resolves a plugin namespace (Author\Plugin) from a plugin class name
     * (Author\Plugin\Classes\Example), identifier (Author.Plugin), or
     * PluginBase instance.
     */
    public function getNamespace(PluginBase|string $plugin): string
    {
        if (is_string($plugin) && strpos($plugin, '.') !== null) {
            $parts = explode('.', $plugin);
            $slice = array_slice($parts, 0, 2);
            $namespace = implode('\\', $slice);

            return Str::normalizeClassName($namespace);
        }

        return Str::normalizeClassName($plugin);
    }

    /**
     * Normalizes the provided plugin identifier (author.plugin) and resolves
     * it case-insensitively to the normalized identifier (Author.Plugin)
     * Returns the provided identifier if a match isn't found
     */
    public function normalizeIdentifier(string $code): string
    {
        return $this->getNormalizedIdentifier($code);
    }

    /**
     * Returns the normalized identifier (i.e. Winter.Blog) from the provided
     * string or PluginBase instance.
     */
    public function getNormalizedIdentifier(PluginBase|string $plugin, bool $lower = false): string
    {
        $code = $this->getIdentifier($plugin);
        $identifier = $this->normalizedMap[strtolower($code)] ?? $code;
        return $lower ? strtolower($identifier) : $identifier;
    }

    /**
     * Spins over every plugin object and collects the results of the provided
     * method call. Results are cached in memory.
     */
    public function getRegistrationMethodValues(string $methodName): array
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
    // State Management (Disable, Enable, Freeze, Unfreeze)
    //

    public function getPluginFlags(PluginBase|string $plugin): array
    {
        $code = $this->getNormalizedIdentifier($plugin, true);
        return $this->pluginFlags[$code] ?? [];
    }

    /**
     * Sets the provided flag on the provided plugin
     */
    protected function flagPlugin(PluginBase|string $plugin, string $flag): void
    {
        $code = $this->getNormalizedIdentifier($plugin, true);
        $this->pluginFlags[$code][$flag] = true;
    }

    /**
     * Removes the provided flag from the provided plugin
     */
    protected function unflagPlugin(PluginBase|string $plugin, string $flag): void
    {
        // Remove the provided flag from the provided plugin
        $code = $this->getNormalizedIdentifier($plugin, true);
        unset($this->pluginFlags[$code][$flag]);

        // Remove the plugin from the pluginFlags property if it has no flags
        if (empty($this->pluginFlags[$code])) {
            unset($this->pluginFlags[$code]);
        }
    }

    /**
     * Loads all disabled plugins from the cached JSON file.
     */
    protected function loadDisabled(): void
    {
        // Check the config files for disabled plugins
        if (($configDisabled = Config::get('cms.disablePlugins')) && is_array($configDisabled)) {
            foreach ($configDisabled as $disabled) {
                $this->flagPlugin($disabled, static::DISABLED_BY_CONFIG);
            }
        }

        // Check the database for disabled plugins
        if (
            $this->app->hasDatabaseTable('system_plugin_versions')
        ) {
            $userDisabled = DB::table('system_plugin_versions')->where('is_disabled', 1)->lists('code') ?? [];
            foreach ($userDisabled as $code) {
                $this->flagPlugin($code, static::DISABLED_BY_USER);
            }
        }
    }

    /**
     * Determines if a plugin is disabled by looking at the meta information
     * or the application configuration.
     */
    public function isDisabled(PluginBase|string $plugin): bool
    {
        $code = $this->getNormalizedIdentifier($plugin, true);

        // @TODO: Limit this to only disabled flags if we add more than disabled flags
        return !empty($this->pluginFlags[$code]);
    }

    /**
     * Returns the plugin replacements defined in $this->replacementMap
     */
    public function getReplacementMap(): array
    {
        return $this->replacementMap;
    }

    /**
     * Returns the actively replaced plugins defined in $this->activeReplacementMap
     */
    public function getActiveReplacementMap(PluginBase|string|null $plugin = null): array|string|null
    {
        if ($plugin) {
            return $this->normalizedMap[
                $this->activeReplacementMap[$this->getNormalizedIdentifier($plugin, true)] ?? null
            ] ?? null;
        }

        $map = [];
        foreach ($this->activeReplacementMap as $key => $value) {
            $map[$this->normalizedMap[$key]] = $this->normalizedMap[$value];
        }

        return $map;
    }

    /**
     * Evaluates the replacement map to determine which replacements can actually
     * take effect
     */
    protected function detectPluginReplacements(): void
    {
        if (empty($this->replacementMap)) {
            return;
        }

        foreach ($this->replacementMap as $target => $replacement) {
            // If the replaced plugin isn't present then assume it can be replaced
            if (!isset($this->plugins[$target])) {
                continue;
            }

            // Only allow one of the replaced plugin or the replacing plugin to exist
            // at once depending on whether the version constraints are met or not
            if (
                $this->plugins[$replacement]->canReplacePlugin(
                    $this->normalizeIdentifier($target),
                    $this->plugins[$target]->getPluginVersion()
                )
            ) {
                // Set the plugin flags to disable the target plugin
                $this->flagPlugin($target, static::DISABLED_REPLACED);
                $this->unflagPlugin($replacement, static::DISABLED_REPLACEMENT_FAILED);

                // Register this plugin as actively replaced (i.e. both are present, replaced are disabled)
                $this->activeReplacementMap[$target] = $replacement;
            } else {
                // Set the plugin flags to disable the replacement plugin
                $this->flagPlugin($replacement, static::DISABLED_REPLACEMENT_FAILED);
                $this->unflagPlugin($target, static::DISABLED_REPLACED);

                // Remove the replacement alias to prevent redirection to a disabled plugin
                unset($this->replacementMap[$target]);
            }
        }
    }

    /**
     * Executes the plugin replacements defined in the activeReplacementMap property
     */
    protected function registerPluginReplacements(): void
    {
        foreach ($this->replacementMap as $target => $replacement) {
            list($target, $replacement) = array_map(
                fn($plugin) => $this->normalizeIdentifier($plugin),
                [$target, $replacement]
            );

            // Alias the replaced plugin to the replacing plugin
            $this->aliasPluginAs($replacement, $target);

            // Register namespace aliases for any replaced plugins
            $this->app->make(ClassLoader::class)->addNamespaceAliases([
                // class_alias() expects order to be $real, $alias
                $this->getNamespace($replacement) => $this->getNamespace($target),
            ]);
        }
    }

    /**
     * Registers namespace aliasing for multiple subsystems
     */
    protected function aliasPluginAs(string $namespace, string $alias): void
    {
        Lang::registerNamespaceAlias($namespace, $alias);
        Config::registerNamespaceAlias($namespace, $alias);
        Config::registerPackageFallback($namespace, $alias);
        SettingsManager::lazyRegisterOwnerAlias($namespace, $alias);
        NavigationManager::lazyRegisterOwnerAlias($namespace, $alias);
    }

    /**
     * Get the PluginVersion record for the provided plugin
     *
     * @throws InvalidArgumentException if unable to find the requested plugin record in the database
     */
    protected function getPluginRecord(PluginBase|string $plugin): PluginVersion
    {
        $plugin = $this->getNormalizedIdentifier($plugin);
        if (isset($this->pluginRecords[$plugin])) {
            return $this->pluginRecords[$plugin];
        }

        $record = PluginVersion::where('code', $plugin)->first();

        if (!$record) {
            throw new \InvalidArgumentException("$plugin was not found in the database.");
        }

        return $this->pluginRecords[$plugin] = $record;
    }

    /**
     * Flags the provided plugin as "frozen" (updates cannot be downloaded / installed)
     */
    public function freezePlugin(PluginBase|string $plugin): void
    {
        $record = $this->getPluginRecord($plugin);
        $record->is_frozen = true;
        $record->save();
    }

    /**
     * "Unfreezes" the provided plugin, allowing for updates to be performed
     */
    public function unfreezePlugin(PluginBase|string $plugin): void
    {
        $record = $this->getPluginRecord($plugin);
        $record->is_frozen = false;
        $record->save();
    }

    /**
     * Disables the provided plugin using the provided flag (defaults to static::DISABLED_BY_USER)
     */
    public function disablePlugin(PluginBase|string $plugin, string|bool $flag = self::DISABLED_BY_USER): bool
    {
        // $flag used to be (bool) $byUser
        if ($flag === true) {
            $flag = static::DISABLED_BY_USER;
        }

        // Flag the plugin as disabled
        $this->flagPlugin($plugin, $flag);

        // Updates the database record for the plugin if required
        if ($flag === static::DISABLED_BY_USER) {
            $record = $this->getPluginRecord($plugin);
            $record->is_disabled = true;
            $record->save();

            // Clear the cache so that the next request will regenerate the active flags
            $this->clearFlagCache();
        }

        // Clear the registration values cache
        $this->registrationMethodCache = [];

        return true;
    }

    /**
     * Enables the provided plugin using the provided flag (defaults to static::DISABLED_BY_USER)
     */
    public function enablePlugin(PluginBase|string $plugin, $flag = self::DISABLED_BY_USER): bool
    {
        // $flag used to be (bool) $byUser
        if ($flag === true) {
            $flag = static::DISABLED_BY_USER;
        }

        // Unflag the plugin as disabled
        $this->unflagPlugin($plugin, $flag);

        // Updates the database record for the plugin if required
        if ($flag === static::DISABLED_BY_USER) {
            $record = $this->getPluginRecord($plugin);
            $record->is_disabled = false;
            $record->save();

            // Clear the cache so that the next request will regenerate the active flags
            $this->clearFlagCache();
        }

        // Clear the registration values cache
        $this->registrationMethodCache = [];

        return true;
    }

    //
    // Dependencies
    //

    /**
     * Returns the plugin identifiers that are required by the supplied plugin.
     */
    public function getDependencies(PluginBase|string $plugin): array
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
     * Scans the system plugins to locate any dependencies that are not currently
     * installed. Returns an array of missing plugin codes keyed by the plugin that requires them.
     *
     *     ['Author.Plugin' => ['Required.Plugin1', 'Required.Plugin2']
     *
     *     PluginManager::instance()->findMissingDependencies();
     *
     */
    public function findMissingDependencies(): array
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
            if (!$plugin->checkDependencies($this)) {
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
    protected function sortByDependencies(): array
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
                    $depend = $this->getNormalizedIdentifier($depend, true);

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

    //
    // Management
    //

    /**
     * Completely roll back and delete a plugin from the system.
     */
    public function deletePlugin(string $id): void
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

            // Clear the registration values cache
            $this->registrationMethodCache = [];

            // Clear the plugin flag cache
            $this->clearFlagCache();
        }
    }

    /**
     * Tears down a plugin's database tables and rebuilds them.
     */
    public function refreshPlugin(string $id): void
    {
        $manager = UpdateManager::instance();
        $manager->rollbackPlugin($id);
        $manager->updatePlugin($id);
    }
}
