<?php namespace System\Twig;

use Twig\Node\Node;
use Twig\Node\Nodes;

/**
 * Version-safe wrappers for Twig parser APIs that changed after the minimum supported Twig
 * version (3.14), so token parsers avoid the deprecated APIs on newer Twig without breaking older.
 *
 * @package winter\wn-system-module
 */
trait ParserCompatibility
{
    /**
     * Parses an expression; Parser::parseExpression() replaced the ExpressionParser in Twig 3.21.
     */
    protected function parseTagExpression(): Node
    {
        if (method_exists($this->parser, 'parseExpression')) {
            return $this->parser->parseExpression();
        }

        return $this->parser->getExpressionParser()->parseExpression();
    }

    /**
     * Wraps a list of nodes; instantiating Node directly is deprecated since Twig 3.15 (which added Nodes).
     */
    protected function makeNodeList(array $nodes, int $lineno = 0): Node
    {
        if (class_exists(Nodes::class)) {
            return new Nodes($nodes, $lineno);
        }

        return new Node($nodes, [], $lineno);
    }
}
