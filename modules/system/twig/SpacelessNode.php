<?php namespace System\Twig;

use Twig\Compiler;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;
use Twig\Attribute\YieldReady;
use Twig\Node\CaptureNode;

/**
 * Represents a spaceless node.
 *
 * It removes spaces between HTML tags.
 *
 * Removed in Twig 3.0, but retained in Winter CMS for compatibility.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class SpacelessNode extends Node implements NodeOutputInterface
{
    public function __construct(Node $body, int $lineno, string $tag = 'spaceless')
    {
        parent::__construct(['body' => $body], [], $lineno);
    }

    public function compile(Compiler $compiler)
    {
        $contentsVar = '$'.$compiler->getVarName();

        $capture = new CaptureNode($this->getNode('body'), $this->getTemplateLine());
        $capture->setAttribute('raw', true);

        $compiler
            ->addDebugInfo($this)
            ->write($contentsVar.' = ')
            ->subcompile($capture)
            ->raw("\n")
            ->write("yield trim(preg_replace('/>\s+</', '><', ".$contentsVar."));\n")
            ->write('unset('.$contentsVar.");\n")
        ;
    }
}
