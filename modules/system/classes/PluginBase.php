<?php namespace System\Classes;

use Str;
use File;
use Yaml;
use Backend;
use ReflectionClass;
use SystemException;
use Composer\Semver\Semver;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider as ServiceProviderBase;

/**
 * Plugin base class
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginBase extends ServiceProviderBase
{
    /**
     * @var \Winter\Storm\Foundation\Application The application instance.
     */
    protected $app;

    /**
     * @var boolean
     */
    protected $loadedYamlConfiguration = false;

    /**
     * @var string The absolute path to this plugin's directory, access with getPluginPath()
     */
    protected $path;

    /**
     * @var string The version of this plugin as reported by updates/version.yaml, access with getPluginVersion()
     */
    protected $version;

    /**
     * @var array Plugin dependencies
     */
    public $require = [];

    /**
     * @var boolean Determine if this plugin should have elevated privileges.
     */
    public $elevated = false;

    /**
     * @var boolean Determine if this plugin should be loaded (false) or not (true).
     */
    public $disabled = false;

    /**
     * Returns information about this plugin, including plugin name and developer name.
     *
     * @return array
     * @throws SystemException
     */
    public function pluginDetails()
    {
        $thisClass = get_class($this);

        $configuration = $this->getConfigurationFromYaml(sprintf('Plugin configuration file plugin.yaml is not '.
            'found for the plugin class %s. Create the file or override pluginDetails() '.
            'method in the plugin class.', $thisClass));

        if (!array_key_exists('plugin', $configuration)) {
            throw new SystemException(sprintf(
                'The plugin configuration file plugin.yaml should contain the "plugin" section: %s.',
                $thisClass
            ));
        }

        return $configuration['plugin'];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Registers CMS markup tags introduced by this plugin.
     *
     * @return array
     */
    public function registerMarkupTags()
    {
        return [];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        $configuration = $this->getConfigurationFromYaml();
        if (array_key_exists('navigation', $configuration)) {
            $navigation = $configuration['navigation'];

            if (is_array($navigation)) {
                array_walk_recursive($navigation, function (&$item, $key) {
                    if ($key === 'url') {
                        $item = Backend::url($item);
                    }
                });
            }

            return $navigation;
        }
    }

    /**
     * Registers back-end quick actions for this plugin.
     *
     * @return array
     */
    public function registerQuickActions()
    {
        $configuration = $this->getConfigurationFromYaml();
        if (array_key_exists('quickActions', $configuration)) {
            $quickActions = $configuration['quickActions'];

            if (is_array($quickActions)) {
                array_walk_recursive($quickActions, function (&$item, $key) {
                    if ($key === 'url') {
                        $item = Backend::url($item);
                    }
                });
            }

            return $quickActions;
        }
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        $configuration = $this->getConfigurationFromYaml();
        if (array_key_exists('permissions', $configuration)) {
            return $configuration['permissions'];
        }
    }

    /**
     * Registers any back-end configuration links used by this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        $configuration = $this->getConfigurationFromYaml();
        if (array_key_exists('settings', $configuration)) {
            return $configuration['settings'];
        }
    }

    /**
     * Registers scheduled tasks that are executed on a regular basis.
     *
     * @param Schedule $schedule
     * @return void
     */
    public function registerSchedule($schedule)
    {
    }

    /**
     * Registers any report widgets provided by this plugin.
     * The widgets must be returned in the following format:
     *
     *     return [
     *         'className1'=>[
     *             'label'    => 'My widget 1',
     *             'context' => ['context-1', 'context-2'],
     *         ],
     *         'className2' => [
     *             'label'    => 'My widget 2',
     *             'context' => 'context-1'
     *         ]
     *     ];
     *
     * @return array
     */
    public function registerReportWidgets()
    {
        return [];
    }

    /**
     * Registers any form widgets implemented in this plugin.
     * The widgets must be returned in the following format:
     *
     *     return [
     *         ['className1' => 'alias'],
     *         ['className2' => 'anotherAlias']
     *     ];
     *
     * @return array
     */
    public function registerFormWidgets()
    {
        return [];
    }

    /**
     * Registers custom back-end list column types introduced by this plugin.
     *
     * @return array
     */
    public function registerListColumnTypes()
    {
        return [];
    }

    /**
     * Registers any mail layouts implemented by this plugin.
     * The layouts must be returned in the following format:
     *
     *     return [
     *         'marketing'    => 'acme.blog::layouts.marketing',
     *         'notification' => 'acme.blog::layouts.notification',
     *     ];
     *
     * @return array
     */
    public function registerMailLayouts()
    {
        return [];
    }

    /**
     * Registers any mail templates implemented by this plugin.
     * The templates must be returned in the following format:
     *
     *     return [
     *         'acme.blog::mail.welcome',
     *         'acme.blog::mail.forgot_password',
     *     ];
     *
     * @return array
     */
    public function registerMailTemplates()
    {
        return [];
    }

    /**
     * Registers any mail partials implemented by this plugin.
     * The partials must be returned in the following format:
     *
     *     return [
     *         'tracking'  => 'acme.blog::partials.tracking',
     *         'promotion' => 'acme.blog::partials.promotion',
     *     ];
     *
     * @return array
     */
    public function registerMailPartials()
    {
        return [];
    }

    /**
     * Registers a new console (artisan) command
     *
     * @param string $key The command name
     * @param string|\Closure $command The command class or closure
     * @return void
     */
    public function registerConsoleCommand($key, $command)
    {
        $key = 'command.'.$key;
        $this->app->singleton($key, $command);
        $this->commands($key);
    }

    /**
     * Read configuration from YAML file
     *
     * @param string|null $exceptionMessage
     * @return array|bool
     * @throws SystemException
     */
    protected function getConfigurationFromYaml($exceptionMessage = null)
    {
        if ($this->loadedYamlConfiguration !== false) {
            return $this->loadedYamlConfiguration;
        }

        $reflection = new ReflectionClass(get_class($this));
        $yamlFilePath = dirname($reflection->getFileName()).'/plugin.yaml';

        if (!file_exists($yamlFilePath)) {
            if ($exceptionMessage) {
                throw new SystemException($exceptionMessage);
            }

            $this->loadedYamlConfiguration = [];
        }
        else {
            $this->loadedYamlConfiguration = Yaml::parseFile($yamlFilePath);
            if (!is_array($this->loadedYamlConfiguration)) {
                throw new SystemException(sprintf('Invalid format of the plugin configuration file: %s. The file should define an array.', $yamlFilePath));
            }
        }

        return $this->loadedYamlConfiguration;
    }

    /**
     * Gets list of plugins replaced by this plugin
     *
     * @param bool $includeConstraints Include version constraints in the results as the array values
     * @return array ['Author.Plugin'] or ['Author.Plugin' => 'self.version']
     */
    public function getReplaces($includeConstraints = false): array
    {
        $replaces = $this->pluginDetails()['replaces'] ?? null;

        if ($includeConstraints) {
            if (is_string($replaces)) {
                $replaces = [$replaces => 'self.version'];
            }
        } else {
            if (is_array($replaces)) {
                $replaces = array_keys($replaces);
            } elseif (is_string($replaces)) {
                $replaces = [$replaces];
            }
        }

        return is_array($replaces) ? $replaces : [];
    }

    /**
     * Check if the provided plugin & version can be replaced by this plugin
     *
     * @param string $pluginIdentifier
     * @param string $version
     * @return bool
     */
    public function canReplacePlugin(string $pluginIdentifier, string $version): bool
    {
        $replaces = $this->getReplaces(true);

        if (is_array($replaces) && in_array($pluginIdentifier, array_keys($replaces))) {
            $constraints = $replaces[$pluginIdentifier];
            if ($constraints === 'self.version') {
                $constraints = $this->getPluginVersion();
            }

            return Semver::satisfies($version, $constraints);
        }

        return false;
    }

    /**
     * Gets the identifier for this plugin
     *
     * @return string Identifier in format of Author.Plugin
     */
    public function getPluginIdentifier(): string
    {
        $namespace = Str::normalizeClassName(get_class($this));
        if (strpos($namespace, '\\') === null) {
            return $namespace;
        }

        $parts = explode('\\', $namespace);
        $slice = array_slice($parts, 1, 2);
        $namespace = implode('.', $slice);

        return $namespace;
    }

    /**
     * Returns the absolute path to this plugin's directory
     */
    public function getPluginPath(): string
    {
        if ($this->path) {
            return $this->path;
        }

        $reflection = new ReflectionClass($this);
        $this->path = File::normalizePath(dirname($reflection->getFileName()));

        return $this->path;
    }

    /**
     * Gets the current version of the plugin as reported by updates/version.yaml
     */
    public function getPluginVersion(): string
    {
        if (isset($this->version)) {
            return $this->version;
        }

        $versions = $this->getPluginVersions();
        if (empty($versions)) {
            return $this->version = (string) VersionManager::NO_VERSION_VALUE;
        }

        return $this->version = trim(key(array_slice($versions, -1, 1)));
    }

    /**
     * Gets the contents of the plugin's updates/version.yaml file and normalizes the results
     */
    public function getPluginVersions(bool $includeScripts = true): array
    {
        $path = $this->getPluginPath();
        $versionFile = $path . '/updates/version.yaml';
        if (!File::isFile($versionFile)) {
            return [];
        }

        $updates = Yaml::withProcessor(new VersionYamlProcessor, function ($yaml) use ($versionFile) {
            return (array) $yaml->parseFile($versionFile);
        });

        uksort($updates, function ($a, $b) {
            return version_compare($a, $b);
        });

        $versions = [];
        foreach ($updates as $version => $details) {
            if (!is_array($details)) {
                $details = [$details];
            }

            if (!$includeScripts) {
                // Filter out valid update scripts
                $details = array_values(array_filter($details, function ($string) use ($path) {
                    return !Str::endsWith($string, '.php') || !File::exists($path . '/updates/' . $string);
                }));
            }

            $versions[$version] = $details;
        }

        return $versions;
    }

    /**
     * Verifies the plugin's dependencies are present and enabled
     */
    public function checkDependencies(PluginManager $manager): bool
    {
        $required = $manager->getDependencies($this);
        if (empty($required)) {
            return true;
        }

        foreach ($required as $require) {
            $requiredPlugin = $manager->findByIdentifier($require);

            if (!$requiredPlugin || $manager->isDisabled($requiredPlugin)) {
                return false;
            }
        }

        return true;
    }
}
