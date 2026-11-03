<?php

namespace System\Classes\Asset;

use Illuminate\Foundation\Vite as LaravelVite;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;
use Winter\Storm\Exception\SystemException;

class Vite extends LaravelVite
{
    /**
     * Generate Vite tags for an entrypoint(s).
     *
     * @param string|array $entrypoints The list of entry points for Vite
     * @param string|null $package The package name of the plugin or theme
     * @param string|null $buildDirectory The Vite build directory
     *
     * @return HtmlString
     *
     * @throws SystemException
     */
    public function __invoke($entrypoints, $package = null, ?string $buildDirectory = null)
    {
        if (!$package) {
            throw new \InvalidArgumentException('A package must be passed');
        }

        $compilableAssetPackage = static::resolvePackage($package);

        $this->useHotFile(base_path($compilableAssetPackage['path'] . '/assets/dist/hot'));
        return parent::__invoke($entrypoints, $compilableAssetPackage['path'] . ($buildDirectory ?? '/assets/dist'));
    }

    /**
     * @throws SystemException if the package could not be found
     */
    protected static function resolvePackage(string $package): array
    {
        // Normalise the package name
        $package = strtolower($package);

        if (!($compilableAssetPackage = PackageManager::instance()->getPackages('vite', true)[$package] ?? null)) {
            throw new SystemException('Unable to resolve package: ' . $package);
        }

        return $compilableAssetPackage;
    }

    /**
     * Helper method to generate Vite tags for an entrypoint(s).
     *
     * @param string|array $entrypoints The list of entry points for Vite
     * @param string $package The package name of the plugin or theme
     * @param string|null $buildDirectory The Vite build directory
     *
     * @throws SystemException
     */
    public static function tags(array|string $entrypoints, string $package, ?string $buildDirectory = null): HtmlString
    {
        return App::make(\Illuminate\Foundation\Vite::class)($entrypoints, $package, $buildDirectory);
    }

    /**
     * Helper method to generate Vite React Refresh tag.
     *
     * @param string $package The package name of the plugin or theme
     * @param string|null $buildDirectory The Vite build directory
     *
     * @throws SystemException
     */
    public static function reactRefreshTag(string $package, ?string $buildDirectory = null): ?HtmlString
    {
        $compilableAssetPackage = static::resolvePackage($package);
        return App::make(\Illuminate\Foundation\Vite::class)
            ->useHotFile(base_path($compilableAssetPackage['path'] . '/assets/dist/hot'))
            ->useBuildDirectory($compilableAssetPackage['path'] . ($buildDirectory ?? '/assets/dist'))
            ->reactRefresh();
    }
}
