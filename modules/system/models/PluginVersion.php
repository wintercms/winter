<?php namespace System\Models;

use Lang;
use Model;
use System\Classes\PluginManager;

/**
 * Stores information about current plugin versions.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginVersion extends Model
{
    public $table = 'system_plugin_versions';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var bool Disable model timestamps.
     */
    public $timestamps = false;

    /**
     * @var array Cache store for version information
     */
    protected static $versionCache;

    /**
     * @var bool Plugin has been disabled by a missing dependency.
     */
    public $disabledBySystem = false;

    /**
     * @var bool Plugin has been disabled by the user or configuration.
     */
    public $disabledByConfig = false;

    /**
     * @var bool If true, plugin exists in the database but not the filesystem.
     */
    public $orphaned = false;

    /**
     * @var string Plugin name, sourced from plugin details
     */
    public $name;

    /**
     * @var string Plugin description, sourced from plugin details
     */
    public $description;

    /**
     * @var string Plugin author, sourced from plugin details
     */
    public $author;

    /**
     * @var string Plugin icon, sourced from plugin details
     */
    public $icon;

    /**
     * @var string Plugin homepage, sourced from plugin details
     */
    public $homepage;

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = ['slug'];

    /**
     * After the model is populated
     */
    public function afterFetch()
    {
        /*
         * Override the database columns with the plugin details
         * found in the plugin registration file.
         */
        $manager = PluginManager::instance();
        $pluginObj = $manager->findByIdentifier($this->code);

        if ($pluginObj) {
            $pluginInfo = $pluginObj->pluginDetails();
            foreach ($pluginInfo as $attribute => $info) {
                if (property_exists($this, $attribute)) {
                    $this->{$attribute} = Lang::get($info);
                }
            }

            $activeFlags = $manager->getPluginFlags($pluginObj);
            if (!empty($activeFlags)) {
                foreach ($activeFlags as $flag => $enabled) {
                    if (in_array($flag, [
                        PluginManager::DISABLED_MISSING,
                        PluginManager::DISABLED_REPLACED,
                        PluginManager::DISABLED_REPLACEMENT_FAILED,
                        PluginManager::DISABLED_MISSING_DEPENDENCIES,
                    ])) {
                        $this->disabledBySystem = true;
                    }

                    if ($flag === PluginManager::DISABLED_BY_CONFIG) {
                        $this->disabledByConfig = true;
                    }
                }
            }
        }
        else {
            $this->name = $this->code;
            $this->description = Lang::get('system::lang.plugins.unknown_plugin');
            $this->orphaned = true;
        }
    }

    /**
     * Returns true if the plugin should be updated by the system.
     */
    public function getIsUpdatableAttribute(): bool
    {
        return !$this->is_disabled && !$this->disabledBySystem && !$this->disabledByConfig;
    }

    /**
     * Only include enabled plugins
     * @param $query
     * @return QueryBuilder
     */
    public function scopeApplyEnabled($query)
    {
        return $query->where('is_disabled', '!=', 1);
    }

    /**
     * Returns the current version for a plugin
     */
    public static function getVersion(string $pluginCode): ?string
    {
        if (self::$versionCache === null) {
            self::$versionCache = self::lists('version', 'code');
        }

        return self::$versionCache[$pluginCode] ?? null;
    }

    /**
     * Provides the slug attribute.
     */
    public function getSlugAttribute(): string
    {
        return self::makeSlug($this->code);
    }

    /**
     * Generates a slug for the plugin.
     */
    public static function makeSlug(string $code): string
    {
        return strtolower(str_replace('.', '-', $code));
    }
}
