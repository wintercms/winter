<?php namespace Cms\Twig;

use Twig\Compiler;
use Twig\Compiler as TwigCompiler;

/**
 * Extends the twig's native macro node to inject appropriate context
 *
 * @package winter\wn-cms-module
 * @author Romain BILLOIR
 */
class MacroNode extends \Twig\Node\MacroNode
{
    /**
     * Compiles the node to PHP.
     *
     * @param TwigCompiler $compiler A TwigCompiler instance
     */
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('public function macro_%s(', $this->getAttribute('name')))
        ;

        $count = \count($this->getNode('arguments'));
        $pos = 0;
        foreach ($this->getNode('arguments') as $name => $default) {
            $compiler
                ->raw('$__'.$name.'__ = ')
                ->subcompile($default)
            ;

            if (++$pos < $count) {
                $compiler->raw(', ');
            }
        }

        if ($count) {
            $compiler->raw(', ');
        }

        $compiler
            ->raw('...$__varargs__')
            ->raw(")\n")
            ->write("{\n")
            ->indent()
            ->write("\$macros = \$this->macros;\n")
            ->write("\$context = \$this->env->mergeGlobals(array_merge(\n")
            ->indent()
            ->write("['this' => \$this->env->getExtension('Cms\Twig\Extension')->getFrontendContext()],\n")
            ->write("[\n")
        ;

        foreach ($this->getNode('arguments') as $name => $default) {
            $compiler
                ->write('')
                ->string($name)
                ->raw(' => $__'.$name.'__')
                ->raw(",\n")
            ;
        }

        $compiler
            ->write('')
            ->string(self::VARARGS_NAME)
            ->raw(' => ')
        ;

        $compiler
            ->raw("\$__varargs__,\n")
            ->outdent()
            ->write("]));\n\n")
            ->write("\$blocks = [];\n\n")
        ;
        if ($compiler->getEnvironment()->isDebug()) {
            $compiler->write("ob_start();\n");
        } else {
            $compiler->write("ob_start(function () { return ''; });\n");
        }
        $compiler
            ->write("try {\n")
            ->indent()
            ->subcompile($this->getNode('body'))
            ->raw("\n")
            ->write("return ('' === \$tmp = ob_get_contents()) ? '' : new Markup(\$tmp, \$this->env->getCharset());\n")
            ->outdent()
            ->write("} finally {\n")
            ->indent()
            ->write("ob_end_clean();\n")
            ->outdent()
            ->write("}\n")
            ->outdent()
            ->write("}\n\n")
        ;
    }
}
