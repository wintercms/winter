<?php

namespace System\Classes\Asset;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;
use InvalidArgumentException;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Support\Collection;
use Winter\Storm\Support\Facades\Url;
use Winter\Storm\Support\Str;

class Mix
{
    /**
     * The preloaded assets.
     *
     * @var array
     */
    protected array $preloadedAssets = [];

    /**
     * The cached manifest files.
     *
     * @var array
     */
    protected static array $manifests = [];

    /**
     * Get the preloaded assets.
     *
     * @return array
     */
    public function preloadedAssets(): array
    {
        return $this->preloadedAssets;
    }

    /**
     * Generate Mix tags for an entrypoint(s).
     *
     * @param array|string $entrypoints The list of entry points for Mix
     * @param string|null $package The package name of the plugin or theme
     * @param string|null $manifestPath
     * @return HtmlString|string
     * @throws SystemException
     */
    public function __invoke(array|string $entrypoints, ?string $package = null, ?string $manifestPath = null): HtmlString|string
    {
        if (!$package) {
            throw new InvalidArgumentException('A package must be passed');
        }

        // Normalise the package name
        $package = strtolower($package);

        $manifestPath ??= 'mix-manifest.json';

        if (!($compilableAssetPackage = PackageManager::instance()->getPackages('mix')[$package] ?? null)) {
            throw new SystemException('Unable to resolve package: ' . $package);
        }

        $manifestPath = public_path($compilableAssetPackage['path'] . Str::start($manifestPath, '/'));

        if (!isset(static::$manifests[$manifestPath])) {
            if (!is_file($manifestPath)) {
                throw new Exception("The Mix manifest does not exist.");
            }

            static::$manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = static::$manifests[$manifestPath];

        $entrypoints = collect($entrypoints)
            ->map(fn($path) => Str::start($path, '/'));

        $tags = collect();
        $preloads = collect();

        foreach ($entrypoints as $entrypoint) {
            if (!isset($manifest[$entrypoint])) {
                throw new Exception("Unable to locate file in Mix manifest: $entrypoint");
            }

            $preloads->push([
                $entrypoint,
                Url::asset($compilableAssetPackage['path'] . $manifest[$entrypoint]),
            ]);

            $tags->push($this->makeTagForEntrypoint($entrypoint, Url::asset($compilableAssetPackage['path'] . $manifest[$entrypoint])));
        }

        [$stylesheets, $scripts] = $tags->unique()->partition(fn($tag) => str_starts_with($tag, '<link'));

        $preloads = $preloads->unique()
            ->sortByDesc(fn($args) => $this->isCssPath($args[0]))
            ->map(fn($args) => $this->makePreloadTagForEntrypoint(...$args));

        return new HtmlString($preloads->join('') . $stylesheets->join('') . $scripts->join(''));
    }

    /**
     * Helper method to generate Mix tags for an entrypoint(s).
     *
     * @param array|string $entrypoints The list of entry points for Mix
     * @param string $package The package name of the plugin or theme
     * @param string|null $manifestPath The relative path to the mix-manifest.json file from the package path
     * @return HtmlString|string
     *
     * @throws SystemException
     */
    public static function tags(array|string $entrypoints, string $package, ?string $manifestPath = null): HtmlString|string
    {
        return App::make(static::class)($entrypoints, $package, $manifestPath);
    }

    /**
     * Make a preload tag for the given entrypoint.
     *
     * @param $src
     * @param $url
     * @return string
     */
    protected function makePreloadTagForEntrypoint($src, $url): string
    {
        $attributes = $this->resolvePreloadTagAttributes($src, $url);

        $this->preloadedAssets[$url] = $this->parseAttributes(
            Collection::make($attributes)->forget('href')->all()
        );

        return '<link ' . implode(' ', $this->parseAttributes($attributes)) . ' />';
    }

    /**
     * Make tag for the given entrypoint.
     *
     * @param $src
     * @param $url
     * @return string
     */
    protected function makeTagForEntrypoint($src, $url): string
    {
        return $this->makeTag($src, $url);
    }

    /**
     * Generate an appropriate tag for the given URL.
     *
     * @param string $src
     * @param string $url
     * @return string
     */
    protected function makeTag(string $src, string $url): string
    {
        if ($this->isCssPath($src)) {
            return $this->makeStylesheetTagWithAttributes($url, []);
        }

        return $this->makeScriptTagWithAttributes($url, []);
    }

    /**
     * Generate a link tag with attributes for the given URL.
     *
     * @param string $url
     * @param array $attributes
     * @return string
     */
    protected function makeStylesheetTagWithAttributes(string $url, array $attributes): string
    {
        $attributes = $this->parseAttributes(array_merge([
            'rel' => 'stylesheet',
            'href' => $url,
        ], $attributes));

        return '<link ' . implode(' ', $attributes) . ' />';
    }

    /**
     * Generate a script tag with attributes for the given URL.
     *
     * @param string $url
     * @param array $attributes
     * @return string
     */
    protected function makeScriptTagWithAttributes(string $url, array $attributes): string
    {
        $attributes = $this->parseAttributes(array_merge([
            'src' => $url,
        ], $attributes));

        return '<script ' . implode(' ', $attributes) . '></script>';
    }

    /**
     * Determines whether the given path is a CSS file.
     *
     * @param string $path
     * @return bool
     */
    protected function isCssPath(string $path): bool
    {
        return Str::endsWith($path, '.css');
    }

    /**
     * Resolve the attributes for the entrypoints generated preload tag.
     *
     * @param string $src
     * @param string $url
     * @return array
     */
    protected function resolvePreloadTagAttributes(string $src, string $url): array
    {
        return [
            'rel' => 'preload',
            'as' => $this->isCssPath($src) ? 'style' : 'script',
            'href' => $url,
        ];
    }

    /**
     * Parse the attributes into key="value" strings.
     *
     * @param array $attributes
     * @return array
     */
    protected function parseAttributes(array $attributes): array
    {
        return Collection::make($attributes)
            ->reject(fn($value, $key) => in_array($value, [false, null], true))
            ->flatMap(fn($value, $key) => $value === true ? [$key] : [$key => $value])
            ->map(fn($value, $key) => is_int($key) ? $value : $key . '="' . $value . '"')
            ->values()
            ->all();
    }
}
