<?php namespace System\Twig;

use Twig\Compiler;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;

/**
 * Represents a spaceless node.
 *
 * It removes spaces between HTML tags.
 *
 * Removed in Twig 3.0, but retained in Winter CMS for compatibility.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SpacelessNode extends Node implements NodeOutputInterface
{
    public function __construct(Node $body, int $lineno, string $tag = 'spaceless')
    {
        parent::__construct(['body' => $body], [], $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
        ;
        if ($compiler->getEnvironment()->isDebug()) {
            $compiler->write("ob_start();\n");
        } else {
            $compiler->write("ob_start(function () { return ''; });\n");
        }
        $compiler
            ->subcompile($this->getNode('body'))
            ->write("echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));\n")
        ;
    }
}
