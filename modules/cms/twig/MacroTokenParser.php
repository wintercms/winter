<?php

namespace Cms\Twig;

use App;
use Twig\Error\SyntaxError;
use Twig\Node\BodyNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Duplication of the twig's native macro token parser to use the Winter's MacroNode
 *
 * @package winter\wn-cms-module
 * @author Romain BILLOIR
 */
class MacroTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $name = $stream->expect(/* Token::NAME_TYPE */ 5)->getValue();

        $arguments = $this->parser->getExpressionParser()->parseArguments(true, true);

        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);
        $this->parser->pushLocalScope();
        $body = $this->parser->subparse([$this, 'decideBlockEnd'], true);
        if ($token = $stream->nextIf(/* Token::NAME_TYPE */ 5)) {
            $value = $token->getValue();

            if ($value != $name) {
                throw new SyntaxError(sprintf('Expected endmacro for macro "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }
        $this->parser->popLocalScope();
        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);

        $this->parser->setMacro($name, new MacroNode($name, new BodyNode([$body]), $arguments, $lineno, $this->getTag()));

        return new Node();
    }

    public function decideBlockEnd(Token $token): bool
    {
        return $token->test('endmacro');
    }

    public function getTag(): string
    {
        return 'macro';
    }
}
