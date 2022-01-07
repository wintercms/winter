<?php namespace Cms\Twig;

use Request;
use System\Models\Parameter;
use System\Classes\CombineAssets;
use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;

/**
 * Represents a "winterjs" node
 *
 * @package winter\wn-cms-module
 * @author Winter CMS
 */
class WinterJsNode extends TwigNode
{
    public function __construct(array $modules, $lineno, $tag = 'winterjs')
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
            'request' => 'framework-js-request',
            'attr' => 'framework-attr-request',
            'extras' => 'framework.extras',
        ];
        $basePath = Request::getBasePath() . '/modules/system/assets/js/framework-next/build/';

        // Add base script
        $compiler
            ->write("echo '<script src=\"${basePath}framework.js${cacheBust}\"></script>'.PHP_EOL;" . PHP_EOL);

        foreach ($modules as $module) {
            $moduleJs = $moduleMap[$module];
            $compiler
                ->write("echo '<script src=\"${basePath}${moduleJs}.js${cacheBust}\"></script>'.PHP_EOL;" . PHP_EOL);
        }
    }
}
