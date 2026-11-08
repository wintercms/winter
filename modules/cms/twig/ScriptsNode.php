<?php namespace Cms\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;
use Twig\Attribute\YieldReady;

/**
 * Represents a "scripts" node
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
#[YieldReady]
class ScriptsNode extends TwigNode
{
    public function __construct($lineno, $tag = 'scripts')
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
            ->write("yield \$this->env->getExtension('Cms\Twig\Extension')->assetsFunction('js');\n")
            ->write("yield \$this->env->getExtension('Cms\Twig\Extension')->displayBlock('scripts');\n")
        ;
    }
}
