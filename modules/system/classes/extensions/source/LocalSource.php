<?php

namespace System\Classes\Extensions\Source;

use Cms\Classes\ThemeManager;
use Illuminate\Support\Facades\File;
use System\Classes\Extensions\PluginManager;
use System\Traits\InteractsWithZip;
use Winter\Storm\Exception\ApplicationException;

class LocalSource extends ExtensionSource
{
    /**
     * @throws ApplicationException
     */
    public function __construct(
        string $type,
        ?string $code = null,
        ?string $composerPackage = null,
        ?string $path = null
    ) {
        parent::__construct(static::SOURCE_LOCAL, $type, $code, $composerPackage, $path);
    }

    /**
     * @throws ApplicationException if the provided path doesn't exist
     */
    public static function fromZip(string $path): array
    {
        if (!File::exists($path)) {
            throw new ApplicationException("$path doesn't exist");
        }

        $dir = temp_path(time());

        if (!File::exists($dir)) {
            File::makeDirectory($dir);
        }

        (new class {
            use InteractsWithZip;
        })->extractArchive($path, $dir);

        $plugins = PluginManager::instance()->findPluginsInPath($dir);
        $themes = ThemeManager::instance()->findThemesInPath($dir);

        if (!count($plugins) && !count($themes)) {
            throw new ApplicationException('Could not detect any plugins or themes in zip');
        }

        $sources = [];

        foreach ($plugins as $code => $path) {
            $sources[] = new static(static::TYPE_PLUGIN, code: $code, path: $path);
        }

        foreach ($themes as $code => $path) {
            $sources[] = new static(static::TYPE_THEME, code: $code, path: $path);
        }

        return $sources;
    }
}
