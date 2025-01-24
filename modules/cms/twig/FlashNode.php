<?php namespace Cms\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;

/**
 * Represents a flash node
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class FlashNode extends TwigNode
{
    public function __construct($name, TwigNode $body, $lineno, $tag = 'flash')
    {
        parent::__construct(['body' => $body], ['name' => $name], $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param TwigCompiler $compiler A TwigCompiler instance
     */
    public function compile(TwigCompiler $compiler)
    {
        $attrib = $this->getAttribute('name');

        $compiler
            ->write('$_type = isset($context["type"]) ? $context["type"] : null;')
            ->write('$_message = isset($context["message"]) ? $context["message"] : null;')
        ;

        if ($attrib == 'all') {
            $compiler
                ->addDebugInfo($this)
                ->write('foreach (Flash::all() as $type => $message) {'.PHP_EOL)
                ->indent()
                    ->write('$context["type"] = $type;')
                    ->write('$context["message"] = $message;')
                    ->subcompile($this->getNode('body'))
                ->outdent()
                ->write('}'.PHP_EOL)
            ;
        }
        else {
            $compiler
                ->addDebugInfo($this)
                ->write('$context["type"] = ')
                ->string($attrib)
                ->write(';')
                ->write('foreach (Flash::')
                ->raw($attrib)
                ->write('() as $message) {'.PHP_EOL)
                ->indent()
                    ->write('$context["message"] = $message;')
                    ->subcompile($this->getNode('body'))
                ->outdent()
                ->write('}'.PHP_EOL)
            ;
        }

        $compiler
            ->write('$context["type"] = $_type;')
            ->write('$context["message"] = $_message;')
        ;
    }
}
