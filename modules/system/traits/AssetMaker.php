<?php

namespace System\Traits;

use Url;
use Html;
use File;
use Event;
use System\Models\Parameter;
use System\Models\PluginVersion;
use System\Classes\CombineAssets;

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
     * @var array Collection of assets to display in the layout.
     */
    protected $assets = ['js'=>[], 'css'=>[], 'rss'=>[]];

    /**
     * @var string Specifies a path to the asset directory.
     */
    public $assetPath;

    /**
     * Ensures "first-come, first-served" applies to assets of the same priority level.
     */
    protected int $priorityFactor = 0;

    /**
     * Disables the use, and subequent broadcast, of assets. This is useful
     * to call during an AJAX request to speed things up. This method works
     * by specifically targeting the hasAssetsDefined method.
     * @return void
     */
    public function flushAssets()
    {
        $this->assets = ['js'=>[], 'css'=>[], 'rss'=>[]];
    }

    /**
     * Outputs `<link>` and `<script>` tags to load assets previously added
     * with addJs, addCss, & addRss method calls depending on the provided $type
     */
    public function makeAssets(string $type = null): ?string
    {
        if ($type != null) {
            $type = strtolower($type);
        }
        $result = null;
        $reserved = ['build', 'priority', 'preload'];

        $this->removeDuplicates();

        if ($type == null || $type == 'css') {
            foreach ($this->prioritiseAssets($this->assets['css']) as $asset) {
                if ($asset['attributes']['preload'] ?? false) {
                    $preloadAttributes = Html::attributes([
                        'rel' => 'preload',
                        'as' => 'style',
                        'href' => $this->getAssetEntryBuildPath($asset)
                    ]);
                    $attributes = Html::attributes(array_merge(
                        [
                            'rel'  => 'stylesheet',
                            'href' => $this->getAssetEntryBuildPath($asset)
                        ],
                    ));
                    $result .= '<link' . $preloadAttributes . '>' . PHP_EOL . '<link' . $attributes . '>' . PHP_EOL;
                } else {
                    $attributes = Html::attributes(array_merge(
                        [
                            'rel'  => 'stylesheet',
                            'href' => $this->getAssetEntryBuildPath($asset)
                        ],
                    ));

                    $result .= '<link' . $attributes . '>' . PHP_EOL;
                }
            }
        }

        if ($type == null || $type == 'rss') {
            foreach ($this->prioritiseAssets($this->assets['rss']) as $asset) {
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
            foreach ($this->prioritiseAssets($this->assets['js']) as $asset) {
                if ($asset['attributes']['preload'] ?? false) {
                    $preloadAttributes = Html::attributes([
                        'rel' => 'preload',
                        'as' => 'script',
                        'href' => $this->getAssetEntryBuildPath($asset)
                    ]);
                    $attributes = Html::attributes(array_merge(
                        [
                            'src' => $this->getAssetEntryBuildPath($asset)
                        ],
                    ));
                    $result .= '<link' . $preloadAttributes . '>' . PHP_EOL . '<script' . $attributes . '></script>' . PHP_EOL;
                } else {
                    $attributes = Html::attributes(array_merge(
                        [
                            'src' => $this->getAssetEntryBuildPath($asset)
                        ],
                        array_except($asset['attributes'], $reserved)
                    ));

                    $result .= '<script' . $attributes . '></script>' . PHP_EOL;
                }
            }
        }

        return $result;
    }

    /**
     * Adds JavaScript asset to the asset list. Call $this->makeAssets() in a view
     * to output corresponding markup.
     * @param array|string $name Specifies a path (URL) or an array of paths to the script(s).
     * @param array $attributes Adds extra HTML attributes to the asset link.
     * @return void
     */
    public function addJs($name, $attributes = [])
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
     * @param array|string $name Specifies a path (URL) or an array of paths to the stylesheet(s).
     * @param array $attributes Adds extra HTML attributes to the asset link.
     * @return void
     */
    public function addCss($name, $attributes = [])
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
     * @param string $name Specifies a path (URL) to the RSS channel
     * @param array $attributes Adds extra HTML attributes to the asset link.
     * @return void
     */
    public function addRss($name, $attributes = [])
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
     * Adds the provided asset to the internal asset collections
     *
     * @param string $type The type of the asset: 'js' || 'css' || 'rss'
     * @param string $path The path to the asset
     * @param array $attributes The attributes for the asset
     */
    protected function addAsset(string $type, string $path, array $attributes)
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
                $this->priorityFactor++;

                // Apply priority
                $attributes['priority'] = (!isset($attributes['priority']))
                    ? 100 + ($this->priorityFactor / 10000)
                    : intval($attributes['priority']) + ($this->priorityFactor / 10000);

                $this->assets[$type][] = ['path' => $path, 'attributes' => $attributes];
            }
        }
    }

    /**
     * Run the provided assets through the Asset Combiner
     * @param array $assets Collection of assets
     * @param string $localPath Prefix all assets with this path (optional)
     * @return string
     */
    public function combineAssets(array $assets, $localPath = '')
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
     * Assets will be prioritised based on their priority levels.
     *
     * @return array
     */
    public function getAssetPaths()
    {
        $this->removeDuplicates();

        $assets = [];
        foreach ($this->assets as $type => $collection) {
            $assets[$type] = [];
            foreach ($this->prioritiseAssets($collection) as $asset) {
                $assets[$type][] = $this->getAssetEntryBuildPath($asset);
            }
        }

        return $assets;
    }

    /**
     * Locates a file based on it's definition. If the file starts with
     * a forward slash, it will be returned in context of the application public path,
     * otherwise it will be returned in context of the asset path.
     * @param string $fileName File to load.
     * @param string $assetPath Explicitly define an asset path.
     * @return string Relative path to the asset file.
     */
    public function getAssetPath($fileName, $assetPath = null)
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
     * @return bool
     */
    public function hasAssetsDefined()
    {
        return count($this->assets, COUNT_RECURSIVE) > 3;
    }

    /**
     * Internal helper, attaches a build code to an asset path
     * @param  array $asset Stored asset array
     * @return string
     */
    protected function getAssetEntryBuildPath($asset)
    {
        $path = $asset['path'];
        if (isset($asset['attributes']['build'])) {
            $build = $asset['attributes']['build'];

            if ($build == 'core') {
                $build = 'v' . Parameter::get('system::core.build', 1);
            }
            elseif ($pluginVersion = PluginVersion::getVersion($build)) {
                $build = 'v' . $pluginVersion;
            }

            $path .= '?' . $build;
        }

        return $path;
    }

    /**
     * Internal helper, get asset scheme
     * @param string $asset Specifies a path (URL) to the asset.
     * @return string
     */
    protected function getAssetScheme($asset)
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
     * @return void
     */
    protected function removeDuplicates()
    {
        foreach ($this->assets as $type => &$collection) {
            $pathCache = [];
            foreach ($collection as $key => $asset) {
                if (!$path = array_get($asset, 'path')) {
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

    protected function getLocalPath(?string $relativePath)
    {
        $relativePath = File::symbolizePath((string) $relativePath);
        if (!starts_with($relativePath, [base_path()])) {
            $relativePath = base_path($relativePath);
        }
        return $relativePath;
    }

    /**
     * Prioritise assets based on a given "priority" level.
     */
    public function prioritizeAssets(array $assets): array
    {
        // Copy assets array so that the stored asset array is not modified.
        $sortedAssets = $assets;

        array_multisort(
            array_map(function ($item) {
                return $item['attributes']['priority'];
            }, $sortedAssets),
            SORT_NUMERIC,
            SORT_ASC,
            $sortedAssets
        );

        return $sortedAssets;
    }
}
