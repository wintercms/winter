<?php namespace Cms\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;
use Twig\Attribute\YieldReady;
use Twig\Node\CaptureNode;

/**
 * Represents a put node
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
#[YieldReady]
class PutNode extends TwigNode
{
    public function __construct(TwigNode $body, $name, $endType, $lineno, $tag = 'put')
    {
        parent::__construct(['body' => $body], ['name' => $name, 'endType' => $endType], $lineno);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param TwigCompiler $compiler A TwigCompiler instance
     */
    public function compile(TwigCompiler $compiler)
    {
        $isOverwrite = strtolower((string) $this->getAttribute('endType')) == 'overwrite';
        $contentsVar = '$'.$compiler->getVarName();

        // Capture the body rather than relying on the block's output buffer, which a yielded
        // body never reaches when Twig runs with "use_yield".
        $capture = new CaptureNode($this->getNode('body'), $this->getTemplateLine());
        $capture->setAttribute('raw', true);

        $compiler
            ->addDebugInfo($this)
            ->write("\$this->env->getExtension('Cms\Twig\Extension')->startBlock(")
            ->raw("'".$this->getAttribute('name')."'")
            ->raw(");\n")
            ->write($contentsVar.' = ')
            ->subcompile($capture)
            ->raw("\n")
            ->addDebugInfo($this)
            ->write("\$this->env->getExtension('Cms\Twig\Extension')->endBlock(")
            ->raw($isOverwrite ? 'false' : 'true')
            ->raw(', '.$contentsVar.");\n")
            ->write('unset('.$contentsVar.");\n")
        ;
    }
}
