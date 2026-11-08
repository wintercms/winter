<?php namespace System\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;
use Twig\Attribute\YieldReady;
use Twig\Node\CaptureNode;

/**
 * Represents a partial node
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
#[YieldReady]
class MailPartialNode extends TwigNode
{
    public function __construct(TwigNode $nodes, $paramNames, $body, $lineno, $tag = 'partial')
    {
        $nodes = ['nodes' => $nodes];

        if ($body) {
            $nodes['body'] = $body;
        }

        parent::__construct($nodes, ['names' => $paramNames], $lineno);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param TwigCompiler $compiler A TwigCompiler instance
     */
    public function compile(TwigCompiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler->write("\$context['__system_partial_params'] = [];\n");

        if ($this->hasNode('body')) {
            $capture = new CaptureNode($this->getNode('body'), $this->getTemplateLine());
            $capture->setAttribute('raw', true);

            $compiler
                ->addDebugInfo($this)
                ->write("\$context['__system_partial_params']['body'] = ")
                ->subcompile($capture)
                ->raw("\n");
        }

        for ($i = 1; $i < count($this->getNode('nodes')); $i++) {
            $compiler->write("\$context['__system_partial_params']['".$this->getAttribute('names')[$i-1]."'] = ");
            $compiler->subcompile($this->getNode('nodes')->getNode($i));
            $compiler->write(";\n");
        }

        $compiler
            ->write("yield \System\Classes\MailManager::instance()->renderPartial(")
            ->subcompile($this->getNode('nodes')->getNode(0))
            ->write(", \$context['__system_partial_params']")
            ->write(");\n")
        ;

        $compiler->write("unset(\$context['__system_partial_params']);\n");
    }
}
