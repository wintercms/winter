<?php

namespace Cms\Classes;

use Cms\Models\ThemeData;
use DirectoryIterator;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use System\Models\Parameter;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Halcyon\Datasource\DatasourceInterface;
use Winter\Storm\Halcyon\Datasource\DbDatasource;
use Winter\Storm\Halcyon\Datasource\FileDatasource;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Event;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Facades\Url;
use Winter\Storm\Support\Facades\Yaml;
use Winter\Storm\Support\Str;

/**
 * This class represents the CMS theme.
 * CMS theme is a directory that contains all CMS objects - pages, layouts, partials and asset files..
 * The theme parameters are specified in the theme.ini file in the theme root directory.
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class Theme extends CmsObject
{
    /**
     * @var string Specifies the theme directory name.
     */
    protected $dirName;

    /**
     * @var mixed Keeps the cached configuration file values.
     */
    protected $configCache;

    /**
     * @var mixed Active theme cache in memory
     */
    protected static $activeThemeCache = false;

    /**
     * @var mixed Edit theme cache in memory
     */
    protected static $editThemeCache = false;

    /**
     * @var array Allowable file extensions.
     */
    protected $allowedExtensions = ['yaml'];

    /**
     * @var string Default file extension.
     */
    protected $defaultExtension = 'yaml';

    const ACTIVE_KEY = 'cms::theme.active';
    const EDIT_KEY = 'cms::theme.edit';

    /**
     * Loads the theme.
     */
    public static function load($dirName, $file = null): ?static
    {
        $theme = new static;
        $theme->setDirName($dirName);
        $theme->registerHalcyonDatasource();
        if (App::runningInBackend()) {
            $theme->registerBackendLocalization();
        }

        return $theme;
    }

    /**
     * Returns the absolute theme path.
     */
    public function getPath(?string $dirName = null): string
    {
        if (!$dirName) {
            $dirName = $this->getDirName();
        }

        return themes_path($dirName);
    }

    /**
     * Sets the theme directory name.
     */
    public function setDirName(string $dirName): void
    {
        $this->dirName = $dirName;
    }

    /**
     * Returns the theme directory name.
     */
    public function getDirName(): string
    {
        return $this->dirName;
    }

    /**
     * Helper for {{ theme.id }} twig vars
     * Returns a unique string for this theme.
     */
    public function getId(): string
    {
        return snake_case(str_replace('/', '-', $this->getDirName()));
    }

    /**
     * Determines if a theme with given directory name exists
     */
    public static function exists(string $dirName): bool
    {
        $theme = static::load($dirName);
        $path = $theme->getPath();

        return File::isDirectory($path);
    }

    /**
     * Returns a list of pages in the theme.
     * This method is used internally in the routing process and in the back-end UI.
     */
    public function listPages(bool $skipCache = false): \Cms\Classes\CmsObjectCollection
    {
        return Page::listInTheme($this, $skipCache);
    }

    /**
     * Returns true if this theme is the chosen active theme.
     */
    public function isActiveTheme(): bool
    {
        $activeTheme = self::getActiveTheme();

        return $activeTheme && $activeTheme->getDirName() === $this->getDirName();
    }

    /**
     * Returns the active theme code.
     * By default the active theme is loaded from the cms.activeTheme parameter,
     * but this behavior can be overridden by the cms.theme.getActiveTheme event listener.
     * If the theme doesn't exist, returns null.
     */
    public static function getActiveThemeCode(): string
    {
        /**
         * @event cms.theme.getActiveTheme
         * Overrides the active theme code.
         *
         * If a value is returned from this halting event, it will be used as the active
         * theme code. Example usage:
         *
         *     Event::listen('cms.theme.getActiveTheme', function () {
         *         return 'mytheme';
         *     });
         *
         */
        $apiResult = Event::fire('cms.theme.getActiveTheme', [], true);
        if ($apiResult !== null) {
            return $apiResult;
        }

        // Load the active theme from the configuration
        $activeTheme = $configuredTheme = Config::get('cms.activeTheme');

        // Attempt to load the active theme from the cache before checking the database
        try {
            $cached = Cache::get(self::ACTIVE_KEY, null);
            if (
                is_array($cached)
                // Check if the configured theme has changed
                && $cached['config'] === $configuredTheme
            ) {
                return $cached['active'];
            }
        } catch (Exception $ex) {
            // Cache failed
        }

        // Check the database
        if (App::hasDatabase()) {
            try {
                $dbResult = Parameter::applyKey(self::ACTIVE_KEY)->value('value');
            } catch (Exception $ex) {
                $dbResult = null;
            }

            if ($dbResult !== null && static::exists($dbResult)) {
                $activeTheme = $dbResult;
            }
        }

        if (!strlen($activeTheme)) {
            throw new SystemException(Lang::get('cms::lang.theme.active.not_set'));
        }

        // Cache the results
        try {
            Cache::forever(self::ACTIVE_KEY, [
                'config' => $configuredTheme,
                'active' => $activeTheme,
            ]);
        } catch (Exception $ex) {
            // Cache failed
        }

        return $activeTheme;
    }

    /**
     * Returns the active theme object.
     * If the theme doesn't exist, returns null.
     */
    public static function getActiveTheme(): self
    {
        if (self::$activeThemeCache !== false) {
            return self::$activeThemeCache;
        }

        $theme = static::load(static::getActiveThemeCode());


        return self::$activeThemeCache = $theme;
    }

    /**
     * Sets the active theme in the database.
     * The active theme code is stored in the database and overrides the configuration cms.activeTheme parameter.
     */
    public static function setActiveTheme(string $code): void
    {
        self::resetCache();

        Parameter::set(self::ACTIVE_KEY, $code);

        /**
         * @event cms.theme.setActiveTheme
         * Fires when the active theme has been changed.
         *
         * If a value is returned from this halting event, it will be used as the active
         * theme code. Example usage:
         *
         *     Event::listen('cms.theme.setActiveTheme', function ($code) {
         *         \Log::info("Theme has been changed to $code");
         *     });
         *
         */
        Event::fire('cms.theme.setActiveTheme', compact('code'));
    }

    /**
     * Returns the edit theme code.
     * By default the edit theme is loaded from the cms.editTheme parameter,
     * but this behavior can be overridden by the cms.theme.getEditTheme event listeners.
     * If the edit theme is not defined in the configuration file, the active theme
     * is returned.
     *
     * @throws SystemException if the edit theme cannot be determined
     */
    public static function getEditThemeCode(): string
    {
        /**
         * @event cms.theme.getEditTheme
         * Overrides the edit theme code.
         *
         * If a value is returned from this halting event, it will be used as the edit
         * theme code. Example usage:
         *
         *     Event::listen('cms.theme.getEditTheme', function () {
         *         return "the-edit-theme-code";
         *     });
         *
         */
        $apiResult = Event::fire('cms.theme.getEditTheme', [], true);
        if ($apiResult !== null) {
            return $apiResult;
        }

        $editTheme = Config::get('cms.editTheme');
        if (!$editTheme) {
            $editTheme = static::getActiveThemeCode();
        }

        if (!strlen($editTheme)) {
            throw new SystemException(Lang::get('cms::lang.theme.edit.not_set'));
        }

        return $editTheme;
    }

    /**
     * Returns the edit theme.
     */
    public static function getEditTheme(): self
    {
        if (self::$editThemeCache !== false) {
            return self::$editThemeCache;
        }

        $theme = static::load(static::getEditThemeCode());


        return self::$editThemeCache = $theme;
    }

    /**
     * Returns an array of all themes.
     */
    public static function all(): array
    {
        $it = new DirectoryIterator(themes_path());
        $it->rewind();

        $result = [];
        foreach ($it as $fileinfo) {
            if (!$fileinfo->isDir() || $fileinfo->isDot()) {
                continue;
            }

            $theme = static::load($fileinfo->getFilename());

            $result[] = $theme;
        }

        return $result;
    }

    /**
     * Reads the theme.yaml file and returns the theme configuration values.
     */
    public function getConfig(): array
    {
        if ($this->configCache !== null) {
            return $this->configCache;
        }

        // Attempt to load the theme's config file from whatever datasources are available.
        $sources = [
            'filesystem' => new FileDatasource(themes_path($this->getDirName()), App::make('files'))
        ];
        if (static::databaseLayerEnabled()) {
            $sources['database'] = new DbDatasource($this->getDirName(), 'cms_theme_templates');
        }
        $data = (new AutoDatasource($sources))->selectOne('', 'theme', 'yaml');

        if (!$data) {
            return $this->configCache = [];
        }

        $config = Yaml::parse($data['content']) ?: [];

        /**
         * @event cms.theme.extendConfig
         * Extend basic theme configuration supplied by the theme by returning an array.
         *
         * Note if planning on extending form fields, use the `cms.theme.extendFormConfig`
         * event instead.
         *
         * Example usage:
         *
         *     Event::listen('cms.theme.extendConfig', function ($themeCode, &$config) {
         *          $config['name'] = 'Winter Theme';
         *          $config['description'] = 'Another great theme from Winter CMS';
         *     });
         *
         */
        Event::fire('cms.theme.extendConfig', [$this->getDirName(), &$config]);

        return $this->configCache = $config;
    }

    /**
     * Themes have a dedicated `form` option that provide form fields
     * for customization, this is an immutable accessor for that and
     * also an solid anchor point for extension.
     */
    public function getFormConfig(): array
    {
        $config = $this->getConfigArray('form');

        /**
         * @event cms.theme.extendFormConfig
         * Extend form field configuration supplied by the theme by returning an array.
         *
         * Note if you are planning on using `assetVar` to inject CSS variables from a
         * plugin registration file, make sure the plugin has elevated permissions.
         *
         * Example usage:
         *
         *     Event::listen('cms.theme.extendFormConfig', function ($themeCode, &$config) {
         *          array_set($config, 'tabs.fields.header_color', [
         *              'label'           => 'Header Colour',
         *              'type'            => 'colorpicker',
         *              'availableColors' => [#103141, #708598, #6cc551],
         *              'assetVar'        => 'header-bg',
         *              'tab'             => 'Global'
         *          ]);
         *     });
         *
         */
        Event::fire('cms.theme.extendFormConfig', [$this->getDirName(), &$config]);

        return $config;
    }

    /**
     * Generates an asset URL for the provided path within the theme, will use the parent theme
     * if the current theme does not actually have a directory on the filesystem (i.e. is virtual).
     */
    public function assetUrl(?string $path): string
    {
        $expiresAt = now()->addMinutes(Config::get('cms.urlCacheTtl', 10));
        $key = sprintf('winter.cms.%s.assetUrl.%s.%s', $this->dirName, request()->getSchemeAndHttpHost(), $path);
        return Cache::remember($key, $expiresAt, function () use ($path) {
            // Handle symbolized paths
            if ($path && File::isPathSymbol($path)) {
                return Url::asset(File::localToPublic(File::symbolizePath($path)));
            }

            $config = $this->getConfig();
            $themeDir = $this->getDirName();

            // If the active theme does not have a directory, then just check the parent theme
            if (!File::isDirectory(themes_path($this->getDirName())) && !empty($config['parent'])) {
                $themeDir = $config['parent'];
            }

            // Define a helper for constructing the URL
            $urlPath = function ($themeDir, $path) {
                $_url = Config::get('cms.themesPath', '/themes') . '/' . $themeDir;

                if ($path !== null) {
                    $_url .= '/' . $path;
                }

                return $_url;
            };

            $url = $urlPath($themeDir, $path);

            // If the file cannot be found in the theme, generate a url for the parent theme
            if (!File::exists(base_path($url)) && !empty($config['parent']) && $themeDir !== $config['parent']) {
                $parentUrl = $urlPath($config['parent'], $path);
                // If found in the parent, return it
                if (File::exists(base_path($parentUrl))) {
                    return Url::asset($parentUrl);
                }
            }

            // Default to returning the current theme's url
            return Url::asset($url);
        });
    }

    /**
     * Returns a value from the theme configuration file by its name.
     */
    public function getConfigValue(string $name, mixed $default = null): mixed
    {
        return array_get($this->getConfig(), $name, $default);
    }

    /**
     * Returns an array value from the theme configuration file by its name.
     * If the value is a string, it is treated as a YAML file and loaded.
     */
    public function getConfigArray(string $name): array
    {
        $result = array_get($this->getConfig(), $name, []);

        if (is_string($result)) {
            $fileName = File::symbolizePath($result);

            if (File::isLocalPath($fileName)) {
                $path = $fileName;
            }
            else {
                $path = $this->getPath().'/'.$result;
            }

            if (!File::exists($path)) {
                throw new ApplicationException('Path does not exist: '.$path);
            }

            $result = Yaml::parseFile($path);
        }

        return (array) $result;
    }

    /**
     * Writes to the theme.yaml file with the supplied array values.
     *
     * @throws ApplicationException if the theme.yaml file does not exist.
     */
    public function writeConfig(array $values = [], bool $overwrite = false): void
    {
        if (!$overwrite) {
            $values = $values + (array) $this->getConfig();
        }

        $path = $this->getPath().'/theme.yaml';
        if (!File::exists($path)) {
            throw new ApplicationException('Path does not exist: ' . $path);
        }

        $contents = Yaml::render($values);
        File::put($path, $contents);
        $this->configCache = $values;

        self::resetCache();
    }

    /**
     * Returns the theme preview image URL.
     * If the image file doesn't exist returns the placeholder image URL.
     */
    public function getPreviewImageUrl(): string
    {
        $previewPath = $this->getConfigValue('previewImage', 'assets/images/theme-preview.png');

        if (File::exists($this->getPath() . '/' . $previewPath)) {
            return Url::asset('themes/' . $this->getDirName() . '/' . $previewPath);
        }

        return Url::asset('modules/cms/assets/images/default-theme-preview.png');
    }

    /**
     * Resets any memory or cache involved with the active or edit theme.
     */
    public static function resetCache(bool $memoryOnly = false): void
    {
        self::$activeThemeCache = false;
        self::$editThemeCache = false;

        // Sometimes it may be desired to only clear the local cache of the active / edit themes instead of the persistent cache
        if (!$memoryOnly) {
            Cache::forget(self::ACTIVE_KEY);
            Cache::forget(self::EDIT_KEY);
        }
    }

    /**
     * Returns true if this theme has form fields that supply customization data.
     */
    public function hasCustomData(): bool
    {
        return (bool) $this->getConfigValue('form', false);
    }

    /**
     * Returns data specific to this theme
     */
    public function getCustomData(): ThemeData
    {
        return ThemeData::forTheme($this);
    }

    /**
     * Remove data specific to this theme
     */
    public function removeCustomData(): bool
    {
        if ($this->hasCustomData()) {
            return $this->getCustomData()->delete();
        }

        return true;
    }

    /**
     * Register the backend localizations provided by this theme and its ancestors.
     */
    public function registerBackendLocalization(): void
    {
        $langPath = $this->getPath() . '/lang';

        if (File::isDirectory($langPath)) {
            Lang::addNamespace('themes.' . $this->getDirName(), $langPath);
        }

        // Check the parent theme if present
        $config = $this->getConfig();
        if (!empty($config['parent'])) {
            $langPath = themes_path($config['parent'] . '/lang');
            if (File::isDirectory($langPath)) {
                Lang::addNamespace('themes.' . $config['parent'], $langPath);
            }
        }
    }

    /**
     * Checks to see if the database layer has been enabled
     */
    public static function databaseLayerEnabled(): bool
    {
        $enableDbLayer = Config::get('cms.databaseTemplates', false);
        if (is_null($enableDbLayer)) {
            $enableDbLayer = !Config::get('app.debug', false);
        }

        $hasDb = Cache::rememberForever('cms.databaseTemplates.hasTables', function () {
            return App::hasDatabaseTable('cms_theme_templates');
        });

        return $enableDbLayer && $hasDb;
    }

    /**
     * Ensures this theme is registered as a Halcyon datasource.
     */
    public function registerHalcyonDatasource(): void
    {
        $resolver = App::make('halcyon');
        if ($resolver->hasDatasource($this->dirName)) {
            return;
        }

        $sources = [];
        if (static::databaseLayerEnabled()) {
            $sources['database'] = new DbDatasource($this->dirName, 'cms_theme_templates');
        }

        $sources['filesystem'] = new FileDatasource($this->getPath(), App::make('files'));

        $config = $this->getConfig();
        if (!empty($config['parent'])) {
            if (static::databaseLayerEnabled()) {
                $sources['parent-database'] = new DbDatasource($config['parent'], 'cms_theme_templates');
            }

            $sources['parent-filesystem'] = new FileDatasource(themes_path($config['parent']), App::make('files'));
        }

        $datasource = count($sources) > 1
            ? new AutoDatasource($sources, 'halcyon-datasource-auto-' . $this->dirName)
            : array_shift($sources);

        $resolver->addDatasource($this->dirName, $datasource);

        /**
         * @event cms.theme.registerHalcyonDatasource
         * Fires immediately after the theme's Datasource has been registered.
         *
         * Allows for extension of the theme Halcyon Datasource, example usage:
         *
         *     use Cms\Classes\Theme;
         *     use Winter\Storm\Halcyon\Datasource\Resolver;
         *
         *     Event::listen('cms.theme.registerHalcyonDatasource', function (Theme $theme, Resolver $resolver) {
         *         $resolver->addDatasource($theme->getDirName(), new AutoDatasource([
         *             'theme' => $theme->getDatasource(),
         *             'example' => new ExampleDatasource(),
         *         ], 'example-autodatasource'));
         *     });
         *
         */
        Event::fire('cms.theme.registerHalcyonDatasource', [$this, $resolver]);
    }

    /**
     * Get the theme's datasource
     */
    public function getDatasource(): DatasourceInterface
    {
        $resolver = App::make('halcyon');
        return $resolver->datasource($this->getDirName());
    }

    /**
     * Implements the getter functionality.
     */
    public function __get($name)
    {
        if (in_array(strtolower($name), ['id', 'path', 'dirname', 'config', 'formconfig', 'previewimageurl'])) {
            $method = 'get'. ucfirst($name);
            return $this->$method();
        }

        if ($this->hasCustomData()) {
            return $this->getCustomData()->{$name};
        }

        return null;
    }

    /**
     * Determine if an attribute exists on the object.
     */
    public function __isset($key)
    {
        if (in_array(strtolower($key), ['id', 'path', 'dirname', 'config', 'formconfig', 'previewimageurl'])) {
            return true;
        }

        if ($this->hasCustomData()) {
            $theme = $this->getCustomData();
            return $theme->offsetExists($key);
        }

        return false;
    }
}
