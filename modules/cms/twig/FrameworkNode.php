<?php namespace Cms\Twig;

use System\Models\Parameter;
use System\Classes\CombineAssets;
use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;
use Url;

/**
 * Represents a "framework" node
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class FrameworkNode extends TwigNode
{
    public function __construct($name, $lineno, $tag = 'framework')
    {
        parent::__construct([], ['name' => $name], $lineno, $tag);
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
        $attrib = $this->getAttribute('name');
        $includeExtras = strtolower(trim($attrib)) === 'extras';

        $compiler
            ->addDebugInfo($this)
            ->write("\$_minify = ".CombineAssets::class."::instance()->useMinify;" . PHP_EOL);

        $basePath = rtrim(Url::asset(''), '/');

        if ($includeExtras) {
            $compiler
                ->write("if (\$_minify) {" . PHP_EOL)
                ->indent()
                    ->write("echo '<script src=\"{$basePath}/modules/system/assets/js/framework.combined-min.js$cacheBust\"></script>'.PHP_EOL;" . PHP_EOL)
                ->outdent()
                ->write("}" . PHP_EOL)
                ->write("else {" . PHP_EOL)
                ->indent()
                    ->write("echo '<script src=\"{$basePath}/modules/system/assets/js/framework.js$cacheBust\"></script>'.PHP_EOL;" . PHP_EOL)
                    ->write("echo '<script src=\"{$basePath}/modules/system/assets/js/framework.extras.js$cacheBust\"></script>'.PHP_EOL;" . PHP_EOL)
                ->outdent()
                ->write("}" . PHP_EOL)
                ->write("echo '<link rel=\"stylesheet\" property=\"stylesheet\" href=\"{$basePath}/modules/system/assets/css/framework.extras'.(\$_minify ? '-min' : '').'.css$cacheBust\">'.PHP_EOL;" . PHP_EOL)
            ;
        }
        else {
            $compiler->write("echo '<script src=\"{$basePath}/modules/system/assets/js/framework'.(\$_minify ? '-min' : '').'.js$cacheBust\"></script>'.PHP_EOL;" . PHP_EOL);
        }

        $compiler->write('unset($_minify);' . PHP_EOL);
    }
}
