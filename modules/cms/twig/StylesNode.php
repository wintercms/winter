<?php namespace Cms\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;
use Twig\Attribute\YieldReady;

/**
 * Represents a "styles" node
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
#[YieldReady]
class StylesNode extends TwigNode
{
    public function __construct($lineno, $tag = 'styles')
    {
        parent::__construct([], [], $lineno);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param TwigCompiler $compiler A TwigCompiler instance
     */
    public function compile(TwigCompiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("yield \$this->env->getExtension('Cms\Twig\Extension')->assetsFunction('css');\n")
            ->write("yield \$this->env->getExtension('Cms\Twig\Extension')->displayBlock('styles');\n")
        ;
    }
}
