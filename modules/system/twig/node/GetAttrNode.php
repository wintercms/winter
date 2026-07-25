<?php namespace System\Twig\Node;

use System\Twig\SecurityPolicy;
use Twig\Compiler;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;
use Twig\Source;
use Twig\Template;

/**
 * GetAttrNode replaces Twig's GetAttrExpression so that, in sandbox mode, the object a method
 * is called on is first cast to a sandbox-safe proxy (see SecurityPolicy::castMethodObjectToSafeObject).
 * This is the only way to neutralise callable-passthrough on collections/paginators, because the
 * security policy is not given method arguments.
 *
 * The compile() logic is a faithful copy of the parent (Twig 3.22) so array access, the sandbox
 * ARRAY_LIKE_CLASSES fast-path, strict-variable handling and the `defined` test all behave
 * identically; only the getAttribute call target is swapped for customGetAttribute().
 *
 * @package winter\wn-system-module
 */
class GetAttrNode extends GetAttrExpression
{
    /**
     * @inheritDoc
     */
    public function __construct(array $nodes = [], array $attributes = [], int $lineno = 0)
    {
        // Skip GetAttrExpression::__construct() (it requires positional child nodes); the node
        // visitor supplies fully-formed $nodes/$attributes copied from the original expression.
        Node::__construct($nodes, $attributes, $lineno);
    }

    /**
     * @inheritDoc
     */
    public function compile(Compiler $compiler): void
    {
        $env = $compiler->getEnvironment();
        $arrayAccessSandbox = false;

        // optimize array calls
        if (
            $this->getAttribute('optimizable')
            && (!$env->isStrictVariables() || $this->getAttribute('ignore_strict_check'))
            && !$this->isDefinedTestEnabled()
            && Template::ARRAY_CALL === $this->getAttribute('type')
        ) {
            $var = '$'.$compiler->getVarName();
            $compiler
                ->raw('(('.$var.' = ')
                ->subcompile($this->getNode('node'))
                ->raw(') && is_array(')
                ->raw($var);

            if (!$env->hasExtension(SandboxExtension::class)) {
                $compiler
                    ->raw(') || ')
                    ->raw($var)
                    ->raw(' instanceof ArrayAccess ? (')
                    ->raw($var)
                    ->raw('[')
                    ->subcompile($this->getNode('attribute'))
                    ->raw('] ?? null) : null)')
                ;

                return;
            }

            $arrayAccessSandbox = true;

            $compiler
                ->raw(') || ')
                ->raw($var)
                ->raw(' instanceof ArrayAccess && in_array(')
                ->raw($var.'::class')
                ->raw(', \\Twig\\Extension\\CoreExtension::ARRAY_LIKE_CLASSES, true) ? (')
                ->raw($var)
                ->raw('[')
                ->subcompile($this->getNode('attribute'))
                ->raw('] ?? null) : ')
            ;
        }

        // Different from the parent: call our customGetAttribute() so the receiver is cast first.
        $compiler->raw(static::class.'::customGetAttribute($this->env, $this->source, ');

        if ($this->getAttribute('ignore_strict_check')) {
            $this->getNode('node')->setAttribute('ignore_strict_check', true);
        }

        $compiler
            ->subcompile($this->getNode('node'))
            ->raw(', ')
            ->subcompile($this->getNode('attribute'))
        ;

        if ($this->hasNode('arguments')) {
            $compiler->raw(', ')->subcompile($this->getNode('arguments'));
        } else {
            $compiler->raw(', []');
        }

        $compiler->raw(', ')
            ->repr($this->getAttribute('type'))
            ->raw(', ')->repr($this->isDefinedTestEnabled())
            ->raw(', ')->repr($this->getAttribute('ignore_strict_check'))
            ->raw(', ')->repr($env->hasExtension(SandboxExtension::class))
            ->raw(', ')->repr($this->getNode('node')->getTemplateLine())
            ->raw(')')
        ;

        if ($arrayAccessSandbox) {
            $compiler->raw(')');
        }
    }

    /**
     * customGetAttribute wraps CoreExtension::getAttribute, casting the object to a safe proxy
     * before a method-invoking access when the sandbox is active.
     *
     * The cast fires on METHOD_CALL and on any access that carries arguments (so the built-in
     * `attribute()` function, which compiles to an ANY_CALL, is also covered). Property/relation
     * access with no arguments is left untouched, so collections stay iterable.
     */
    public static function customGetAttribute(
        Environment $env,
        Source $source,
        $object,
        $item,
        array $arguments = [],
        $type = /* Template::ANY_CALL */ 'any',
        $isDefinedTest = false,
        $ignoreStrictCheck = false,
        $sandboxed = false,
        int $lineno = -1
    ) {
        if (
            $sandboxed
            && ($type === Template::METHOD_CALL || $arguments)
            && $env->hasExtension(SandboxExtension::class)
        ) {
            $policy = $env->getExtension(SandboxExtension::class)->getSecurityPolicy();
            if ($policy instanceof SecurityPolicy) {
                $object = $policy->castMethodObjectToSafeObject($object);
            }
        }

        return CoreExtension::getAttribute(
            $env,
            $source,
            $object,
            $item,
            $arguments,
            $type,
            $isDefinedTest,
            $ignoreStrictCheck,
            $sandboxed,
            $lineno
        );
    }
}
