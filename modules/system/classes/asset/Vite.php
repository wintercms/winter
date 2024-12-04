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
     * @return HtmlString
     *
     * @throws SystemException
     */
    public function __invoke($entrypoints, $package = null)
    {
        if (!$package) {
            throw new \InvalidArgumentException('A package must be passed');
        }

        // Normalise the package name
        $package = strtolower($package);

        if (!($compilableAssetPackage = PackageManager::instance()->getPackages('vite')[$package] ?? null)) {
            throw new SystemException('Unable to resolve package: ' . $package);
        }

        $this->useHotFile(base_path($compilableAssetPackage['path'] . '/assets/dist/hot'));
        return parent::__invoke($entrypoints, $compilableAssetPackage['path'] . '/assets/dist');
    }

    /**
     * Helper method to generate Vite tags for an entrypoint(s).
     *
     * @param string|array $entrypoints The list of entry points for Vite
     * @param string $package The package name of the plugin or theme
     *
     * @throws SystemException
     */
    public static function tags(array|string $entrypoints, string $package): HtmlString
    {
        return App::make(\Illuminate\Foundation\Vite::class)($entrypoints, $package);
    }
}
