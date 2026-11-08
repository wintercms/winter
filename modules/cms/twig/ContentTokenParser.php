<?php namespace Cms\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Token as TwigToken;
use Twig\TokenParser\AbstractTokenParser as TwigTokenParser;
use Twig\Error\SyntaxError as TwigErrorSyntax;
use System\Twig\ParserCompatibility;

/**
 * Parser for the `{% content %}` Twig tag.
 *
 *     {% content "intro.htm" %}
 *
 *     {% content "intro.md" name='John' %}
 *
 *     {% content "intro/txt" name='John', year=2013 %}
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class ContentTokenParser extends TwigTokenParser
{
    use ParserCompatibility;

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

        $name = $this->parseTagExpression();
        $paramNames = [];
        $nodes = [$name];

        $end = false;
        while (!$end) {
            $current = $stream->next();

            if ($current->test(TwigToken::NAME_TYPE)) {
                $paramNames[] = $current->getValue();
                $stream->expect(TwigToken::OPERATOR_TYPE, '=');
                $nodes[] = $this->parseTagExpression();
            }
            elseif ($current->test(TwigToken::BLOCK_END_TYPE)) {
                $end = true;
            }
            else {
                throw new TwigErrorSyntax(
                    sprintf('Invalid syntax in the content tag. Line %s', $lineno),
                    $stream->getCurrent()->getLine(),
                    $stream->getSourceContext()
                );
            }
        }

        return new ContentNode($this->makeNodeList($nodes), $paramNames, $token->getLine());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'content';
    }
}
