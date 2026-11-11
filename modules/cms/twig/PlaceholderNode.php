<?php namespace Cms\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;
use Twig\Attribute\YieldReady;
use Twig\Node\CaptureNode;

/**
 * Represents a placeholder node
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
#[YieldReady]
class PlaceholderNode extends TwigNode
{
    public function __construct($name, $paramValues, $body, $lineno, $tag = 'placeholder')
    {
        $nodes = [];

        if ($body) {
            $nodes['default'] = $body;
        }

        $attributes = $paramValues;
        $attributes['name'] = $name;

        parent::__construct($nodes, $attributes, $lineno);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param TwigCompiler $compiler A TwigCompiler instance
     */
    public function compile(TwigCompiler $compiler)
    {
        $hasBody = $this->hasNode('default');
        $varId = '__placeholder_'.$this->getAttribute('name').'_default_contents';
        $compiler
            ->addDebugInfo($this)
            ->write("\$context[")
            ->raw("'".$varId."'")
            ->raw("] = null;");

        if ($hasBody) {
            $capture = new CaptureNode($this->getNode('default'), $this->getTemplateLine());
            $capture->setAttribute('raw', true);

            $compiler
                ->addDebugInfo($this)
                ->write("\$context[")
                ->raw("'".$varId."'")
                ->raw("] = ")
                ->subcompile($capture)
                ->raw("\n");
        }

        $isText = $this->hasAttribute('type') && $this->getAttribute('type') == 'text';

        $compiler->addDebugInfo($this);
        if (!$isText) {
            $compiler->write("yield \$this->env->getExtension('Cms\Twig\Extension')->displayBlock(");
        }
        else {
            $compiler->write("yield \$this->env->getRuntime(\\Twig\\Runtime\\EscaperRuntime::class)->escape(\$this->env->getExtension('Cms\Twig\Extension')->displayBlock(");
        }

        $compiler
            ->raw("'".$this->getAttribute('name')."', ")
            ->raw("\$context[")
            ->raw("'".$varId."'")
            ->raw("]")
            ->raw(")");

        if (!$isText) {
            $compiler->raw(";\n");
        }
        else {
            $compiler->raw(");\n");
        }

        $compiler
            ->addDebugInfo($this)
            ->write("unset(\$context[")
            ->raw("'".$varId."'")
            ->raw("]);");
    }
}
