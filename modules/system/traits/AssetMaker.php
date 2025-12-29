<?php

namespace System\Traits;

use System\Classes\CombineAssets;
use System\Classes\PluginManager;
use System\Classes\Asset\Vite;
use System\Models\Parameter;
use System\Models\PluginVersion;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Support\Facades\Event;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Facades\Html;
use Winter\Storm\Support\Facades\Url;

/**
 * Asset Maker Trait
 * Adds asset based methods to a class
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
trait AssetMaker
{
    /**
     * Collection of assets to display in the layout.
     */
    protected array $assets = ['js' => [], 'css' => [], 'rss' => [], 'vite' => []];

    /**
     * @var string Specifies a path to the asset directory.
     */
    public $assetPath;

    /**
     * Ensures "first-come, first-served" applies to assets of the same ordering.
     */
    protected int $orderFactor = 0;

    /**
     * Disables the use, and subequent broadcast, of assets. This is useful
     * to call during an AJAX request to speed things up. This method works
     * by specifically targeting the hasAssetsDefined method.
     */
    public function flushAssets(): void
    {
        $this->assets = ['js' => [], 'css' => [], 'rss' => [], 'vite' => []];
    }

    /**
     * Outputs `<link>` and `<script>` tags to load assets previously added
     * with addJs, addCss, & addRss method calls depending on the provided $type
     */
    public function makeAssets(?string $type = null): ?string
    {
        if ($type != null) {
            $type = strtolower($type);
        }
        $result = null;
        $reserved = ['build', 'order'];

        $this->removeDuplicates();

        if ($type == null || $type == 'css') {
            foreach ($this->orderAssets($this->assets['css']) as $asset) {
                $attributes = Html::attributes(array_merge(
                    [
                        'rel'  => 'stylesheet',
                        'href' => $this->getAssetEntryBuildPath($asset)
                    ],
                    array_except($asset['attributes'], $reserved)
                ));

                $result .= '<link' . $attributes . '>' . PHP_EOL;
            }
        }

        if ($type == null || $type == 'rss') {
            foreach ($this->orderAssets($this->assets['rss']) as $asset) {
                $attributes = Html::attributes(array_merge(
                    [
                        'rel'   => 'alternate',
                        'href'  => $this->getAssetEntryBuildPath($asset),
                        'title' => 'RSS',
                        'type'  => 'application/rss+xml'
                    ],
                    array_except($asset['attributes'], $reserved)
                ));

                $result .= '<link' . $attributes . '>' . PHP_EOL;
            }
        }

        if ($type == null || $type == 'js') {
            foreach ($this->orderAssets($this->assets['js']) as $asset) {
                $attributes = Html::attributes(array_merge(
                    [
                        'src' => $this->getAssetEntryBuildPath($asset)
                    ],
                    array_except($asset['attributes'], $reserved)
                ));

                $result .= '<script' . $attributes . '></script>' . PHP_EOL;
            }
        }

        if ($type == null || $type == 'vite') {
            foreach ($this->assets['vite'] as $asset) {
                $result .= Vite::tags($asset['attributes']['entrypoints'], $asset['path']);
            }
        }

        return $result;
    }

    /**
     * Adds JavaScript asset to the asset list. Call $this->makeAssets() in a view
     * to output corresponding markup.
     * @param string|array $name When an array of paths are provided they will be passed to the Asset Combiner
     * @param array|string $attributes When a string is provided it will be used as the 'build' attribute value
     */
    public function addJs(string|array $name, array|string $attributes = []): void
    {
        if (is_array($name)) {
            $name = $this->combineAssets($name, $this->getLocalPath($this->assetPath));
        }

        // Alias october* assets to winter.*
        if (str_contains($name, 'js/october')) {
            $winterPath = str_replace('js/october', 'js/winter', $name);
            if (file_exists(base_path(ltrim(parse_url($winterPath, PHP_URL_PATH), '/')))) {
                $name = $winterPath;
            }
        }

        $jsPath = $this->getAssetPath($name);

        if (isset($this->controller)) {
            $this->controller->addJs($jsPath, $attributes);
        }

        if (is_string($attributes)) {
            $attributes = ['build' => $attributes];
        }

        $jsPath = $this->getAssetScheme($jsPath);

        $this->addAsset('js', $jsPath, $attributes);
    }

    /**
     * Adds StyleSheet asset to the asset list. Call $this->makeAssets() in a view
     * to output corresponding markup.
     * @param string|array $name When an array of paths are provided they will be passed to the Asset Combiner
     * @param array|string $attributes When a string is provided it will be used as the 'build' attribute value
     */
    public function addCss(string|array $name, array|string $attributes = []): void
    {
        if (is_array($name)) {
            $name = $this->combineAssets($name, $this->getLocalPath($this->assetPath));
        }

        $cssPath = $this->getAssetPath($name);

        if (isset($this->controller)) {
            $this->controller->addCss($cssPath, $attributes);
        }

        if (is_string($attributes)) {
            $attributes = ['build' => $attributes];
        }

        $cssPath = $this->getAssetScheme($cssPath);

        $this->addAsset('css', $cssPath, $attributes);
    }

    /**
     * Adds an RSS link asset to the asset list. Call $this->makeAssets() in a view
     * to output corresponding markup.
     */
    public function addRss(string $name, array|string $attributes = []): void
    {
        $rssPath = $this->getAssetPath($name);

        if (isset($this->controller)) {
            $this->controller->addRss($rssPath, $attributes);
        }

        if (is_string($attributes)) {
            $attributes = ['build' => $attributes];
        }

        $rssPath = $this->getAssetScheme($rssPath);

        $this->addAsset('rss', $rssPath, $attributes);
    }

    /**
     * Adds Vite tags
     * @param string|array $entrypoints The list of entry points for Vite
     * @param ?string $package The package name of the plugin or theme
     */
    public function addVite(array|string $entrypoints, ?string $package = null): void
    {
        if (!is_array($entrypoints)) {
            $entrypoints = [$entrypoints];
        }

        // If package was not set, attempt to guess
        if (is_null($package)) {
            $caller = get_called_class();
            if (!($plugin = PluginManager::instance()->findByNamespace($caller))) {
                throw new SystemException('Unable to determine vite package from namespace: ' . $caller);
            }
            // Set package to the plugin id
            $package = $plugin->getPluginIdentifier();
        }

        if (isset($this->controller)) {
            $this->controller->addVite($entrypoints, $package);
        }

        $this->addAsset('vite', $package, [
            'entrypoints' => $entrypoints
        ]);
    }

    /**
     * Adds the provided asset to the internal asset collections
     */
    protected function addAsset(string $type, string $path, array $attributes): void
    {
        if (!in_array($path, $this->assets[$type])) {
            /**
             * @event system.assets.beforeAddAsset
             * Provides an opportunity to inspect or modify an asset.
             *
             * The parameters provided are:
             * string `$type`: The type of the asset being added
             * string `$path`: The path to the asset being added
             * array `$attributes`: The array of attributes for the asset being added.
             *
             * All the parameters are provided by reference for modification.
             * This event is also a halting event, so returning false will prevent the
             * current asset from being added. Note that duplicates are filtered out
             * before the event is fired.
             *
             * Example usage:
             *
             *     Event::listen('system.assets.beforeAddAsset', function (string &$type, string &$path, array &$attributes) {
             *         if (in_array($path, $blockedAssets)) {
             *             return false;
             *         }
             *     });
             *
             * Or
             *
             *     $this->bindEvent('assets.beforeAddAsset', function (string &$type, string &$path, array &$attributes) {
             *         $attributes['special_cdn_flag'] = false;
             *     });
             *
             */
            if (
                // Fire local event if exists
                (
                    method_exists($this, 'fireEvent') &&
                    ($this->fireEvent('assets.beforeAddAsset', [&$type, &$path, &$attributes], true) !== false)
                ) &&
                // Fire global event
                (Event::fire('system.assets.beforeAddAsset', [&$type, &$path, &$attributes], true) !== false)
            ) {
                $this->orderFactor++;

                // Apply ordering
                $attributes['order'] = (!isset($attributes['order']))
                    ? 500 + ($this->orderFactor / 10000)
                    : intval($attributes['order']) + ($this->orderFactor / 10000);

                $this->assets[$type][] = ['path' => $path, 'attributes' => $attributes];
            }
        }
    }

    /**
     * Run the provided assets through the Asset Combiner
     */
    public function combineAssets(array $assets, string $localPath = ''): string
    {
        // Short circuit if no assets actually provided
        if (empty($assets)) {
            return '';
        }
        $assetPath = !empty($localPath) ? $localPath : $this->assetPath;
        return Url::to(CombineAssets::combine($assets, $assetPath));
    }

    /**
     * Returns an array of all registered asset paths.
     *
     * Assets will be prioritized based on their defined ordering.
     */
    public function getAssetPaths(): array
    {
        $this->removeDuplicates();

        $assets = [];
        foreach ($this->assets as $type => $collection) {
            $assets[$type] = [];
            foreach ($this->orderAssets($collection) as $asset) {
                $assets[$type][] = $this->getAssetEntryBuildPath($asset);
            }
        }

        return $assets;
    }

    /**
     * Returns the URL to the provided asset. If the provided fileName is a relative path
     * without a leading slash it will be assumbed to be relative to the asset path.
     */
    public function getAssetPath(string $fileName, ?string $assetPath = null): string
    {
        if (starts_with($fileName, ['//', 'http://', 'https://'])) {
            return $fileName;
        }

        if (!$assetPath) {
            $assetPath = $this->assetPath;
        }

        // Process absolute or symbolized paths
        $publicPath = File::localToPublic(File::symbolizePath($fileName));
        if ($publicPath) {
            $fileName = $publicPath;
        }

        if (substr($fileName, 0, 1) == '/' || $assetPath === null) {
            return Url::asset($fileName);
        }

        return Url::asset($assetPath . '/' . $fileName);
    }

    /**
     * Returns true if assets any have been added.
     */
    public function hasAssetsDefined(): bool
    {
        return count($this->assets, COUNT_RECURSIVE) > 3;
    }

    /**
     * Internal helper, attaches a build code to an asset path
     */
    protected function getAssetEntryBuildPath(array $asset): string
    {
        $path = $asset['path'];
        if (isset($asset['attributes']['build'])) {
            $build = $asset['attributes']['build'];

            if ($build == 'core') {
                $build = 'v' . Parameter::get('system::core.build', 1);
            } elseif ($pluginVersion = PluginVersion::getVersion($build)) {
                $build = 'v' . $pluginVersion;
            }

            $path .= '?' . $build;
        }

        return $path;
    }

    /**
     * Internal helper, get asset scheme
     */
    protected function getAssetScheme(string $asset): string
    {
        if (starts_with($asset, ['//', 'http://', 'https://'])) {
            return $asset;
        }

        if (substr($asset, 0, 1) == '/') {
            $asset = Url::asset($asset);
        }

        return $asset;
    }

    /**
     * Removes duplicate assets from the entire collection.
     */
    protected function removeDuplicates(): void
    {
        foreach ($this->assets as $type => &$collection) {
            $pathCache = [];
            foreach ($collection as $key => $asset) {
                if (!$path = array_get($asset, 'path')) {
                    continue;
                }

                if ($type === 'vite') {
                    // If handling vite, ensure that each entrypoint is considered it's own path within a package
                    foreach ($asset['attributes']['entrypoints'] ?? [] as $index => $entrypoint) {
                        $vitePath = $path . '|' . $entrypoint;
                        // If we detect a duplicate, remove it
                        if (isset($pathCache[$vitePath])) {
                            array_forget($collection[$key]['attributes']['entrypoints'], $index);
                            // If all entry points have been removed from an asset, then remove it
                            if (empty($collection[$key]['attributes']['entrypoints'])) {
                                unset($collection[$key]);
                            }

                            continue;
                        }

                        $pathCache[$vitePath] = true;
                    }

                    continue;
                }

                if (isset($pathCache[$path])) {
                    array_forget($collection, $key);
                    continue;
                }

                $pathCache[$path] = true;
            }
        }
    }

    protected function getLocalPath(?string $relativePath): string
    {
        $relativePath = File::symbolizePath((string) $relativePath);
        if (!starts_with($relativePath, [base_path()])) {
            $relativePath = base_path($relativePath);
        }
        return $relativePath;
    }

    /**
     * Prioritize assets based on the given order.
     */
    public function orderAssets(array $assets): array
    {
        // Copy assets array so that the stored asset array is not modified.
        $sortedAssets = $assets;

        array_multisort(
            array_map(function ($item) {
                return $item['attributes']['order'];
            }, $sortedAssets),
            SORT_NUMERIC,
            SORT_ASC,
            $sortedAssets
        );

        return $sortedAssets;
    }
}
