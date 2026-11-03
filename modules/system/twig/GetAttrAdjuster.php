<?php namespace System\Twig;

use System\Twig\Node\GetAttrNode;
use Twig\Environment;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * GetAttrAdjuster swaps every attribute-access node (GetAttrExpression) for the custom
 * GetAttrNode, so that method calls in sandbox mode route through
 * SecurityPolicy::castMethodObjectToSafeObject. This is what enables the SafeCollection /
 * SafePaginator protections. Only exact GetAttrExpression instances are swapped (not the
 * replacement GetAttrNode), so the traversal never re-wraps.
 *
 * @package winter\wn-system-module
 */
class GetAttrAdjuster implements NodeVisitorInterface
{
    /**
     * @inheritDoc
     */
    public function enterNode(Node $node, Environment $env): Node
    {
        if (get_class($node) !== GetAttrExpression::class) {
            return $node;
        }

        $nodes = [
            'node' => $node->getNode('node'),
            'attribute' => $node->getNode('attribute'),
        ];

        if ($node->hasNode('arguments')) {
            $nodes['arguments'] = $node->getNode('arguments');
        }

        $isDefinedTest = $node->isDefinedTestEnabled();

        $attributes = [
            'type' => $node->getAttribute('type'),
            'ignore_strict_check' => $node->getAttribute('ignore_strict_check'),
            'optimizable' => $node->getAttribute('optimizable'),
        ];

        $getAttrNode = new GetAttrNode($nodes, $attributes, $node->getTemplateLine());

        if ($isDefinedTest) {
            $getAttrNode->enableDefinedTest();
        }

        return $getAttrNode;
    }

    /**
     * @inheritDoc
     */
    public function leaveNode(Node $node, Environment $env): ?Node
    {
        return $node;
    }

    /**
     * @inheritDoc
     */
    public function getPriority()
    {
        return 0;
    }
}
