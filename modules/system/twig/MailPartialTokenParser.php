<?php namespace System\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Node\Nodes;
use Twig\Token as TwigToken;
use Twig\TokenParser\AbstractTokenParser as TwigTokenParser;
use Twig\Error\SyntaxError as TwigErrorSyntax;

/**
 * Parser for the `{% partial %}` Twig tag.
 *
 *     {% partial "sidebar" %}
 *
 *     {% partial "sidebar" name='John' %}
 *
 *     {% partial "sidebar" name='John', year=2013 %}
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MailPartialTokenParser extends TwigTokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param TwigToken $token A TwigToken instance
     * @return TwigNode A TwigNode instance
     */
    public function parse(TwigToken $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $name = $this->parser->parseExpression();
        $paramNames = [];
        $nodes = [$name];
        $hasBody = false;
        $body = null;

        $end = false;
        while (!$end) {
            $current = $stream->next();

            if (
                $current->test(TwigToken::NAME_TYPE, 'body') &&
                !$stream->test(TwigToken::OPERATOR_TYPE, '=')
            ) {
                $hasBody = true;
                $current = $stream->next();
            }

            if ($current->test(TwigToken::NAME_TYPE)) {
                $paramNames[] = $current->getValue();
                $stream->expect(TwigToken::OPERATOR_TYPE, '=');
                $nodes[] = $this->parser->parseExpression();
            }
            elseif ($current->test(TwigToken::BLOCK_END_TYPE)) {
                $end = true;
            }
            else {
                throw new TwigErrorSyntax(
                    sprintf('Invalid syntax in the partial tag. Line %s', $lineno),
                    $stream->getCurrent()->getLine(),
                    $stream->getSourceContext()
                );
            }
        }

        if ($hasBody) {
            $body = $this->parser->subparse([$this, 'decidePartialEnd'], true);
            $stream->expect(TwigToken::BLOCK_END_TYPE);
        }

        return new MailPartialNode(new Nodes($nodes), $paramNames, $body, $token->getLine());
    }

    public function decidePartialEnd(TwigToken $token)
    {
        return $token->test('endpartial');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'partial';
    }
}
