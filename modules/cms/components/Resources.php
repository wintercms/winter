<?php namespace Cms\Components;

use File;
use Cms\Classes\ComponentBase;
use System\Classes\CombineAssets;

/**
 * Resources component
 */
class Resources extends ComponentBase
{
    /**
     * @var string The default JavaScript directory
     */
    public $jsDir = 'js';

    /**
     * @var string The default CSS directory
     */
    public $cssDir = 'css';

    /**
     * @var string The default LESS directory
     */
    public $lessDir = 'less';

    /**
     * @var string The default SASS directory
     */
    public $sassDir = 'sass';

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'           => 'Resources',
            'description'    => 'Easily reference theme assets for inclusion on a page.',
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        return [
            'js' => [
                'title'             => 'JavaScript',
                'description'       => 'JavaScript file(s) in the assets/js folder',
                'type'              => 'stringList',
                'showExternalParam' => false
            ],
            'less' => [
                'title'             => 'LESS',
                'description'       => 'LESS file(s) in the assets/less folder',
                'type'              => 'stringList',
                'showExternalParam' => false
            ],
            'sass' => [
                'title'             => 'SASS',
                'description'       => 'SASS file(s) in the assets/sass folder',
                'type'              => 'stringList',
                'showExternalParam' => false
            ],
            'css' => [
                'title'             => 'CSS',
                'description'       => 'Stylesheet file(s) in the assets/css folder',
                'type'              => 'stringList',
                'showExternalParam' => false
            ],
            'vars' => [
                'title'             => 'Variables',
                'description'       => 'Page variables name(s) and value(s)',
                'type'              => 'dictionary',
                'showExternalParam' => false
            ]
        ];
    }

    public function init()
    {
        $this->assetPath = $this->guessAssetPath();
        $this->jsDir = $this->guessAssetDirectory(['js', 'javascript'], $this->jsDir);
        $this->sassDir = $this->guessAssetDirectory(['sass', 'scss'], $this->sassDir);
    }

    public function onRun()
    {
        /*
         * JavaScript
         */
        $js = [];
        if ($assets = $this->property('js')) {
            $js = array_filter(array_map([$this, 'prefixJs'], (array) $assets));
        }

        /*
         * LESS
         */
        $less = [];
        if ($assets = $this->property('less')) {
            $less = array_filter(array_map([$this, 'prefixLess'], (array) $assets));
        }

        /*
         * SASS
         */
        $sass = [];
        if ($assets = $this->property('sass')) {
            $sass = array_filter(array_map([$this, 'prefixSass'], (array) $assets));
        }

        /*
         * CSS
         */
        $css = [];
        if ($assets = $this->property('css')) {
            $css = array_filter(array_map([$this, 'prefixCss'], (array) $assets));
        }

        if (count($js)) {
            $this->addJs(CombineAssets::combine($js, $this->assetPath));
        }

        if (count($less)) {
            $this->addCss(CombineAssets::combine($less, $this->assetPath));
        }

        if (count($sass)) {
            $this->addCss(CombineAssets::combine($sass, $this->assetPath));
        }

        if (count($css)) {
            $this->addCss(CombineAssets::combine($css, $this->assetPath));
        }

        /*
         * Variables
         */
        if ($vars = $this->property('vars')) {
            foreach ((array) $vars as $key => $value) {
                $this->page[$key] = $value;
            }
        }
    }

    protected function prefixJs($value)
    {
        return $this->prefixPath($this->jsDir, $value);
    }

    protected function prefixCss($value)
    {
        return $this->prefixPath($this->cssDir, $value);
    }

    protected function prefixLess($value)
    {
        return $this->prefixPath($this->lessDir, $value);
    }

    protected function prefixSass($value)
    {
        return $this->prefixPath($this->sassDir, $value);
    }

    /**
     * Prefixes a property value with the asset subdirectory the property is documented to
     * name a file in, and drops a value that walks out of it.
     *
     * These properties are written by whoever can author a CMS template, and the value
     * was previously concatenated exactly as given, so a `..` in one of them named a file
     * outside the theme for the combiner to read and serve back. A subdirectory below the
     * asset directory is still allowed; only a parent reference is not.
     *
     * @param string $directory
     * @param string $value
     * @return string|null
     */
    protected function prefixPath($directory, $value)
    {
        $value = trim($value);

        if (in_array('..', explode('/', str_replace('\\', '/', $value)), true)) {
            return null;
        }

        return $directory.'/'.$value;
    }

    protected function guessAssetDirectory(array $possible, $default = null)
    {
        foreach ($possible as $option) {
            if (File::isDirectory($this->assetPath.'/'.$option)) {
                return $option;
            }
        }

        return $default;
    }

    protected function guessAssetPath()
    {
        $baseTheme = themes_path().'/'.$this->getTheme()->getDirName();

        if (File::isDirectory($baseTheme.'/assets')) {
            return $baseTheme.'/assets';
        }

        return $baseTheme.'/resources';
    }
}
