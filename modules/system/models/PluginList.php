<?php

namespace System\Models;

use System\Classes\DetailedVersionedPackage;
use System\Facades\Composer;
use Winter\Storm\Database\Model;
use Winter\Storm\Database\Traits\ArraySource;

class PluginList extends Model
{
    use ArraySource;

    /**
     * @var array The accessors to append to the model's array form.
     */
    protected $appends = ['slug'];

    /**
     * Disables caching of this array source.
     */
    public bool $cacheArray = false;

    public function getRecords(): array
    {
        $records = [];
        $pluginVersions = PluginVersion::get();

        foreach ($pluginVersions as $plugin) {
            $records[strtolower($plugin->code)] = [
                'name' => $plugin->name,
                'composer' => false,
                'code' => strtolower($plugin->code),
                'description' => $plugin->description,
                'version' => $plugin->version,
                'author' => $plugin->author,
                'icon' => $plugin->icon,
                'iconSvg' => $plugin->iconSvg,
                'homepage' => $plugin->homepage,
                'orphaned' => $plugin->orphaned,
                'disabled' => $plugin->disabledBySystem || $plugin->disabledByConfig,
            ];
        }

        $composerPackages = Composer::show()->type('winter-plugin')->toDetailed();

        /** @var \System\Classes\DetailedPackage */
        foreach ($composerPackages as $package) {
            $records[$this->toPluginCode($package->getNamespace(), $package->getName())] = [
                'name' => $package->isWinterPlugin()
                    ? $package->getPluginName()
                    : $package->getPackageName(),
                'composer' => true,
                'code' => $this->toPluginCode($package->getNamespace(), $package->getName()),
                'description' => $package->getDescription(),
                'version' => ($package instanceof DetailedVersionedPackage) ? $package->getDisplayVersion() : null,
                'author' => $package->getAuthorList(),
                'icon' => null,
                'iconSvg' => null,
                'homepage' => $package->getHomepage(),
                'orphaned' => false,
                'disabled' => false,
            ];
        }

        return array_values($records);
    }

    protected function toPluginCode(string $namespace, string $name): string
    {
        $namespace = strtolower($namespace);

        $name = preg_replace('/^(wn|oc)\-/', '', strtolower($name));
        $name = preg_replace('/-plugin$/', '', $name);

        return $namespace . '.' . $name;
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
