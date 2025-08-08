<?php

namespace System\Classes\Extensions;

use Backend\Classes\NavigationManager;
use FilesystemIterator;
use Illuminate\Console\View\Components\Error;
use Illuminate\Console\View\Components\Info;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use System\Classes\ComposerManager;
use System\Classes\Extensions\Source\ExtensionSource;
use System\Classes\SettingsManager;
use System\Models\PluginVersion;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Foundation\Application;
use Winter\Storm\Foundation\Extension\WinterExtension;
use Winter\Storm\Packager\Composer;
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
class PluginManager extends ExtensionManager implements ExtensionManagerInterface
{
    /**
     * The application instance, since Plugins are an extension of a Service Provider
     */
    protected Application $app;

    /**
     * @var PluginVersionManager Handles versioning of plugins in the database.
     */
    protected PluginVersionManager $versionManager;

    /**
     * @var PluginBase[] Container array used for storing plugin information objects.
     */
    protected array $plugins = [];

    /**
     * @var array Array of plugin codes that contain any flags currently associated with the plugin
     */
    protected array $pluginFlags = [];

    /**
     * @var PluginVersion[] Local cache of loaded PluginVersion records keyed by plugin code
     */
    protected array $pluginRecords = [];

    /**
     * @var array A map of normalized plugin identifiers [lowercase.identifier => Normalized.Identifier]
     */
    protected array $normalizedMap = [];

    /**
     * @var array A map of plugin identifiers with their replacements [Original.Plugin => Replacement.Plugin]
     */
    protected array $replacementMap = [];

    /**
     * @var array A map of plugins that are currently replaced [Original.Plugin => Replacement.Plugin]
     */
    protected array $activeReplacementMap = [];

    /**
     * @var bool Flag to indicate that all plugins have had the register() method called by registerAll() being called on this class.
     */
    protected bool $registered = false;

    /**
     * @var bool Flag to indicate that all plugins have had the boot() method called by bootAll() being called on this class.
     */
    protected bool $booted = false;

    /**
     * @var array Cache of registration method results.
     */
    protected array $registrationMethodCache = [];

    /**
     * @var bool Prevent all plugins from registering or booting
     */
    public static bool $noInit = false;

    /**
     * Initializes the plugin manager
     * @throws SystemException
     */
    protected function init(): void
    {
        $this->app = App::make('app');

        // Define the version manager
        $this->versionManager = new PluginVersionManager($this);

        // Load the plugins from the filesystem and sort them by dependencies
        $this->loadPlugins();

        // Loads the plugin flags (disabled & replacement states) from the cache
        // regenerating them if required.
        $this->loadPluginFlags();

        // Register plugin replacements
        $this->registerPluginReplacements();
    }

    /**
     * Returns an array with all enabled plugins
     *
     * @return array [$code => $pluginObj]
     */
    public function list(): array
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
     * @throws SystemException
     * @throws ApplicationException
     */
    public function create(string $extension): WinterExtension
    {
        $this->renderComponent(Info::class, sprintf('Running command `create:plugin %s`.', $extension));

        Artisan::call('create:plugin', [
            'plugin' => $extension,
            '--uninspiring' => true,
        ], $this->getOutput());

        $this->renderComponent(Info::class, 'Reloading loaded plugins...');

        // Insure the in memory plugins match those on disk
        $this->loadPlugins();

        // Force a refresh of the plugin
        $this->refresh($extension);

        $this->renderComponent(Info::class, 'Plugin created successfully.');

        // Return an instance of the plugin
        return $this->findByIdentifier($extension);
    }

    /**
     * @throws ApplicationException
     * @throws SystemException
     */
    public function install(ExtensionSource|WinterExtension|string $extension): WinterExtension
    {
        // Insure the in memory plugins match those on disk
        $this->loadPlugins();

        // Get the plugin code from input and then update the plugin
        if (!($code = $this->resolveIdentifier($extension)) || $this->versionManager->updatePlugin($code) === false) {
            throw new ApplicationException('Unable to update plugin: ' . $code);
        }

        $this->renderComponent(Info::class, 'Plugin <fg=yellow>' . $code . '</> installed successfully.');

        // Return an instance of the plugin
        return $this->findByIdentifier($code);
    }

    public function isInstalled(ExtensionSource|WinterExtension|string $extension): bool
    {
        if (
            !($code = $this->resolveIdentifier($extension))
            || $this->versionManager->getCurrentVersion($code) === '0'
        ) {
            return false;
        }

        return true;
    }

    public function get(WinterExtension|ExtensionSource|string $extension): ?WinterExtension
    {
        if (!($code = $this->resolveIdentifier($extension))) {
            return null;
        }

        return $this->findByIdentifier($code);
    }

    /**
     * Enables the provided plugin using the provided flag (defaults to static::DISABLED_BY_USER)
     */
    public function enable(WinterExtension|string $extension, string|bool $flag = self::DISABLED_BY_USER): ?bool
    {
        if (!($plugin = $this->get($extension))) {
            return null;
        }

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

    /**
     * Disables the provided plugin using the provided flag (defaults to static::DISABLED_BY_USER)
     */
    public function disable(WinterExtension|string $extension, string|bool $flag = self::DISABLED_BY_USER): ?bool
    {
        if (!($plugin = $this->get($extension))) {
            return null;
        }

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
     * @throws ApplicationException
     */
    public function update(WinterExtension|string|null $extension = null, bool $migrationsOnly = false): ?bool
    {
        $plugins = [];
        // If null, load all plugins
        if (!$extension) {
            $plugins = $this->list();
        }

        if (!$plugins) {
            if (!($resolved = $this->resolve($extension))) {
                throw new ApplicationException(
                    'Unable to resolve plugin: ' . (is_string($extension) ? $extension : $extension->getIdentifier())
                );
            }
            $plugins = [$resolved->getIdentifier() => $resolved];
        }

        foreach ($plugins as $code => $plugin) {
            $pluginName = Lang::get($plugin->pluginDetails()['name']);

            // Plugin record will be null if trying to update before install
            try {
                $pluginRecord = $this->getPluginRecord($plugin);
            } catch (\Throwable $e) {
                $pluginRecord = null;
            }

            if (!$migrationsOnly) {
                if (
                    !$pluginRecord?->is_frozen
                    && ($composerPackage = $plugin->getComposerPackageName())
                    && Composer::updateAvailable($composerPackage)
                ) {
                    $this->renderComponent(Info::class, sprintf(
                        'Performing composer update for %s (%s) plugin...',
                        $pluginName,
                        $code
                    ));

                    Preserver::instance()->store($plugin);
                    // @TODO: Make this not dry run
                    $update = Composer::update(dryRun: true, package: $composerPackage, withAllDependencies: true);

                    ($versions = $update->getUpgraded()[$composerPackage] ?? null)
                        ? $this->renderComponent(Info::class, sprintf(
                            'Updated plugin %s (%s) from v%s => v%s',
                            $pluginName,
                            $code,
                            $versions[0],
                            $versions[1]
                        ))
                        : $this->renderComponent(Error::class, sprintf(
                            'Failed to update plugin %s (%s)',
                            $pluginName,
                            $code
                        ));
                } elseif (false /* Detect if market */) {
                    Preserver::instance()->store($plugin);
                    // @TODO: Update files from market
                }
            }

            $this->renderComponent(Info::class, sprintf('Migrating %s (%s) plugin...', $pluginName, $code));
            $this->versionManager->updatePlugin($plugin);

            // Ensure any active aliases have their history migrated for replacing plugins
            $this->migratePluginReplacements();
        }

        return true;
    }

    /*
     * Replace plugins
     */
    public function migratePluginReplacements(): static
    {
        foreach ($this->list() as $code => $plugin) {
            if (!($replaces = $plugin->getReplaces())) {
                continue;
            }
            // @TODO: add full support for plugins replacing multiple plugins
            if (count($replaces) > 1) {
                throw new ApplicationException(Lang::get('system::lang.plugins.replace.multi_install_error'));
            }
            foreach ($replaces as $replace) {
                $this->versionManager->replacePlugin($plugin, $replace);
            }
        }

        return $this;
    }

    public function availableUpdates(WinterExtension|string|null $extension = null): ?array
    {
        $toCheck = $extension ? [$this->findByIdentifier($extension)] : $this->list();

        $composerUpdates = Composer::getAvailableUpdates();

        $updates = [];
        foreach ($toCheck as $plugin) {
            if ($plugin->getComposerPackageName()) {
                if (isset($composerUpdates[$plugin->getComposerPackageName()])) {
                    $updates[$plugin->getPluginIdentifier()] = [
                        'package_name' => $plugin->getComposerPackageName(),
                        'icon' => $plugin->pluginDetails()['icon'] ?? 'icon-puzzle-piece',
                        ...$composerUpdates[$plugin->getComposerPackageName()],
                    ];
                }
                continue;
            }
            // @TODO: Add market place support for updates
        }

        return $updates;
    }

    /**
     * Tears down a plugin's database tables and rebuilds them.
     * @throws ApplicationException
     */
    public function refresh(WinterExtension|ExtensionSource|string|null $extension = null): ?bool
    {
        if (!($code = $this->resolveIdentifier($extension))) {
            return null;
        }

        $this->rollback($code);
        $this->update($code);

        return true;
    }

    /**
     * @throws ApplicationException
     * @throws \Exception
     */
    public function rollback(WinterExtension|string|null $extension = null, ?string $targetVersion = null): ?PluginBase
    {
        if (!($code = $this->resolveIdentifier($extension))) {
            return null;
        }

        // Remove the plugin database and version
        if (
            !($plugin = $this->findByIdentifier($code))
            && $this->versionManager->purgePlugin($code)
        ) {
            $this->renderComponent(Info::class, sprintf('%s purged from database', $code));
            return $plugin;
        }

        if (!$plugin) {
            $this->renderComponent(Info::class, sprintf('%s was not found', $code));
            return null;
        }

        if ($targetVersion && !$this->versionManager->hasDatabaseVersion($plugin, $targetVersion)) {
            throw new ApplicationException(Lang::get('system::lang.updates.plugin_version_not_found'));
        }

        if ($this->versionManager->removePlugin($plugin, $targetVersion, true)) {
            $this->renderComponent(Info::class, sprintf('%s rolled back', $code));

            if ($currentVersion = $this->versionManager->getCurrentVersion($plugin)) {
                $this->renderComponent(Info::class, sprintf(
                    'Current Version: %s (%s)',
                    $currentVersion,
                    $this->versionManager->getCurrentVersionNote($plugin)
                ));
            }

            return $plugin;
        }

        $this->renderComponent(Error::class, sprintf('Unable to find plugin %s', $code));

        return null;
    }

    /**
     * Completely roll back and delete a plugin from the system.
     * @throws ApplicationException
     */
    public function uninstall(WinterExtension|string $extension, bool $noRollback = false, bool $preserveFiles = false): ?bool
    {
        if (!($code = $this->resolveIdentifier($extension))) {
            return null;
        }

        // Rollback plugin
        if (!$noRollback) {
            $this->rollback($code);
        }

        // Get the plugin object from its code
        $plugin = $this->findByIdentifier($code);
        if (!$plugin) {
            $this->renderComponent(Info::class, 'Unable to access plugin object: <fg=red>' . $code . '</>');
            return true;
        }

        // If the plugin was installed via composer, remove it
        if ($composerPackage = $plugin->getComposerPackageName()) {
            $this->renderComponent(
                Info::class,
                sprintf('Removing plugin: %s (%s) via composer', $code, $composerPackage)
            );
            Composer::remove($composerPackage);
        }

        // Delete from file system
        if (($pluginPath = $plugin->getPluginPath()) && File::exists($pluginPath)) {
            if (!$preserveFiles) {
                File::deleteDirectory($pluginPath);
            }

            // Clear the registration values cache
            $this->registrationMethodCache = [];

            // Clear the plugin flag cache
            $this->clearFlagCache();
        }

        $this->renderComponent(Info::class, 'Deleted plugin: <fg=red>' . $code . '</>');

        return true;
    }

    /**
     * Uninstall all plugins
     * @throws ApplicationException
     */
    public function tearDown(): static
    {
        foreach (array_reverse($this->getAllPlugins()) as $plugin) {
            $this->uninstall($plugin, preserveFiles: true);
        }

        return $this;
    }

    public function versionManager(): PluginVersionManager
    {
        return $this->versionManager;
    }

    /**
     * Finds all available plugins and loads them in to the $this->plugins array.
     * @throws SystemException
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

            /* @var PluginBase $className */
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
     * Finds all plugins in a given path by looking for valid Plugin.php files
     */
    public function findPluginsInPath(string $path): array
    {
        $pluginFiles = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'Plugin.php') {
                if ($code = $this->extractPluginCodeFromFile($file->getRealPath())) {
                    $pluginFiles[$code] = $file->getPathname();
                }
            }
        }

        return $pluginFiles;
    }

    /**
     * Attempt to extract the plugin's code from a file
     *
     * @param string $filePath
     * @return string|null
     */
    public function extractPluginCodeFromFile(string $filePath): ?string
    {
        if (!preg_match('/namespace (.+?);/', file_get_contents($filePath), $match)) {
            return null;
        }

        $code = str_replace('\\', '.', $match[1]);

        return str_contains($code, '.') ? $code : null;
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
        $plugins = $this->list();

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
            throw new InvalidArgumentException("$plugin was not found in the database.");
        }

        return $this->pluginRecords[$plugin] = $record;
    }

    /**
     * Flags the provided plugin as "frozen" (updates cannot be downloaded / installed)
     */
    public function freezePlugin(PluginBase|string $plugin): void
    {
        if (!$plugin instanceof PluginVersion) {
            $plugin = $this->get($plugin);
        }

        if ($package = $plugin->getComposerPackageName()) {
            $version = $plugin->getComposerPackageVersion();
            if (empty($version)) {
                throw new ApplicationException("Unable to determine package version for $package");
            }

            Composer::pin($package, $version);
        }

        $record = $this->getPluginRecord($plugin);
        $record->is_frozen = true;
        $record->save();
    }

    /**
     * "Unfreezes" the provided plugin, allowing for updates to be performed
     * @TODO: Will prevent future plugins from being installed if the version has been updated in composer.json
     * but the composer update hasn't been run yet as there's now a mismatch between the lock and the composer.json
     * file. We could either solve by making unfreezing auto update at the same time, or by making install plugins
     * pass -W (with all depdencies); which we might need to allow anyways?
     */
    public function unfreezePlugin(PluginBase|string $plugin): void
    {
        if (!$plugin instanceof PluginVersion) {
            $plugin = $this->get($plugin);
        }

        if ($package = $plugin->getComposerPackageName()) {
            Composer::pin($package);
        }

        $record = $this->getPluginRecord($plugin);
        $record->is_frozen = false;
        $record->save();
    }

    /**
     * Get a list of warnings about the current system status
     * Warns when plugins are missing dependencies and when replaced plugins are still present on the system.
     */
    public function getWarnings(): array
    {
        $warnings = [];
        $missingDependencies = $this->findMissingDependencies();

        if (!empty($missingDependencies)) {
            $this->clearFlagCache();
        }

        foreach ($missingDependencies as $pluginCode => $plugin) {
            foreach ($plugin as $missingPluginCode) {
                $warnings[] = Lang::get('system::lang.updates.update_warnings_plugin_missing', [
                    'code' => '<strong>' . $missingPluginCode . '</strong>',
                    'parent_code' => '<strong>' . $pluginCode . '</strong>'
                ]);
            }
        }

        $replacementMap = $this->getReplacementMap();
        foreach ($replacementMap as $alias => $plugin) {
            if ($this->getActiveReplacementMap($alias)) {
                $warnings[] = Lang::get('system::lang.updates.update_warnings_plugin_replace', [
                    'plugin' => '<strong>' . $plugin . '</strong>',
                    'alias' => '<strong>' . $alias . '</strong>'
                ]);
            }
        }

        return $warnings;
    }

    /**
     * Get a list of plugin replacement notices.
     */
    public function getPluginReplacementNotices(): array
    {
        $notices = [];
        foreach ($this->getReplacementMap() as $alias => $plugin) {
            if ($this->getActiveReplacementMap($alias)) {
                $notices[$plugin] = Lang::get('system::lang.updates.update_warnings_plugin_replace_cli', [
                    'alias' => '<info>' . $alias . '</info>'
                ]);
            }
        }

        return $notices;
    }

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

    public function resolveIdentifier(ExtensionSource|WinterExtension|string $extension): ?string
    {
        if (is_string($extension)) {
            return $this->getNormalizedIdentifier($extension);
        }
        if ($extension instanceof ExtensionSource) {
            return $this->getNormalizedIdentifier($extension->getCode());
        }
        if ($extension instanceof WinterExtension) {
            return $extension->getPluginIdentifier();
        }

        return null;
    }

    /**
     * @param WinterExtension|string|null $extension
     * @return array<string, WinterExtension>
     * @throws ApplicationException
     */
    protected function getPluginList(WinterExtension|string|null $extension = null): array
    {
        if (!$extension) {
            return $this->list();
        }

        if (!($resolved = $this->resolve($extension))) {
            throw new ApplicationException('Unable to locate extension');
        }

        return [$resolved->getIdentifier() => $resolved];
    }

    /**
     * Returns an array with all enabled plugins
     *
     * @return array [$code => $pluginObj]
     * @deprecated
     */
    public function getPlugins(): array
    {
        return $this->list();
    }

    /**
     * Tears down a plugin's database tables and rebuilds them.
     * @deprecated
     */
    public function refreshPlugin(string $id): void
    {
        $this->refresh($id);
    }

    /**
     * Completely roll back and delete a plugin from the system.
     * @deprecated
     */
    public function deletePlugin(string $id): void
    {
        $this->uninstall($id);
    }

    /**
     * Disables the provided plugin using the provided flag (defaults to static::DISABLED_BY_USER)
     * @deprecated
     */
    public function disablePlugin(PluginBase|string $plugin, string|bool $flag = self::DISABLED_BY_USER): ?bool
    {
        return $this->disable($plugin, $flag);
    }

    /**
     * Enables the provided plugin using the provided flag (defaults to static::DISABLED_BY_USER)
     * @deprecated
     */
    public function enablePlugin(PluginBase|string $plugin, $flag = self::DISABLED_BY_USER): ?bool
    {
        return $this->enable($plugin, $flag);
    }
}
