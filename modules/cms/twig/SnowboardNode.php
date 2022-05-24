<?php namespace Cms\Twig;

use Url;
use Config;
use Request;
use System\Models\Parameter;
use System\Classes\CombineAssets;
use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;

/**
 * Represents a "snowboard" node
 *
 * @package winter\wn-cms-module
 * @author Winter CMS
 */
class SnowboardNode extends TwigNode
{
    /**
     * @var bool Indicates if the base Snowboard framework is already loaded, in case of multiple uses of this tag.
     */
    public static $baseLoaded = false;

    public function __construct(array $modules, $lineno, $tag = 'snowboard')
    {
        parent::__construct([], ['modules' => $modules], $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param TwigCompiler $compiler A TwigCompiler instance
     */
    public function compile(TwigCompiler $compiler)
    {
        $build = Parameter::get('system::core.build', 'winter');
        $cacheBust = '?v=' . $build;
        $modules = $this->getAttribute('modules');

        $compiler
            ->addDebugInfo($this)
            ->write("\$_minify = ".CombineAssets::class."::instance()->useMinify;" . PHP_EOL);

        $moduleMap = [
            'base' => (Config::get('app.debug', false) === true) ? 'snowboard.base.debug' : 'snowboard.base',
            'vendor' => 'snowboard.vendor',
            'request' => 'snowboard.request',
            'attr' => 'snowboard.data-attr',
            'extras' => 'snowboard.extras',
        ];
        $manifestPath = Request::getBasePath() . '/modules/system/assets/js/build/manifest.js';
        $basePath = Request::getBasePath() . '/modules/system/assets/js/snowboard/build/';

        if (!static::$baseLoaded) {
            // Add manifest and vendor files
            $compiler
                ->write("echo '<script data-module=\"snowboard-manifest\" src=\"${manifestPath}${cacheBust}\"></script>'.PHP_EOL;" . PHP_EOL);
            $vendorJs = $moduleMap['vendor'];
            $compiler
                ->write("echo '<script data-module=\"snowboard-vendor\" src=\"${basePath}${vendorJs}.js${cacheBust}\"></script>'.PHP_EOL;" . PHP_EOL);

            // Add base script
            $baseJs = $moduleMap['base'];
            $baseUrl = Url::to('/');
            $compiler
                ->write("echo '<script data-module=\"snowboard-base\" data-base-url=\"${baseUrl}\" src=\"${basePath}${baseJs}.js${cacheBust}\"></script>'.PHP_EOL;" . PHP_EOL);

            static::$baseLoaded = true;
        }

        foreach ($modules as $module) {
            $moduleJs = $moduleMap[$module];
            $compiler
                ->write("echo '<script data-module=\"${module}\" src=\"${basePath}${moduleJs}.js${cacheBust}\"></script>'.PHP_EOL;" . PHP_EOL);
        }
    }
}
