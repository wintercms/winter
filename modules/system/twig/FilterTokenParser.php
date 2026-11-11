<?php namespace System\Twig;

use Twig\ExpressionParser\Infix\FilterExpressionParser;
use Twig\Node\BlockNode;
use Twig\Node\Expression\BlockReferenceExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Filters a section of a template by applying filters.
 *
 *   {% filter upper %}
 *      This text becomes uppercase
 *   {% endfilter %}
 *
 * @deprecated since Twig 2.9, to be removed in 3.0 (use the "apply" tag instead)
 */
final class FilterTokenParser extends AbstractTokenParser
{
    /**
     * @var int Disambiguates block names for filter tags on the same template line.
     */
    private static int $blockSalt = 0;

    public function parse(Token $token)
    {
        @trigger_error('The "filter" tag is deprecated since Twig 2.9, use the "apply" tag instead.', E_USER_DEPRECATED);

        // Parser::getVarName() is deprecated since Twig 3.15, so name the block ourselves.
        $name = '__internal_filter_' . substr(sha1(
            $this->parser->getStream()->getSourceContext()->getName() . ':' . $token->getLine() . ':' . static::$blockSalt++
        ), 0, 16);
        $ref = new BlockReferenceExpression(new ConstantExpression($name, $token->getLine()), null, $token->getLine());

        $filter = $this->parseFilters($ref);
        $this->parser->getStream()->expect(/* Token::BLOCK_END_TYPE */ 3);

        $body = $this->parser->subparse([$this, 'decideBlockEnd'], true);
        $this->parser->getStream()->expect(/* Token::BLOCK_END_TYPE */ 3);

        $block = new BlockNode($name, $body, $token->getLine());
        $this->parser->setBlock($name, $block);

        return new PrintNode($filter, $token->getLine());
    }

    /**
     * Parses the filter chain applied to $node, as Twig's own "apply" tag does.
     */
    private function parseFilters(Node $node): Node
    {
        $op = $this->parser->getEnvironment()->getExpressionParsers()->getByClass(FilterExpressionParser::class);
        while (true) {
            $node = $op->parse($this->parser, $node, $this->parser->getCurrentToken());
            if (!$this->parser->getStream()->test(Token::OPERATOR_TYPE, '|')) {
                break;
            }
            $this->parser->getStream()->next();
        }

        return $node;
    }

    public function decideBlockEnd(Token $token)
    {
        return $token->test('endfilter');
    }

    public function getTag()
    {
        return 'filter';
    }
}
