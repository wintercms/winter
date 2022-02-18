<?php namespace System\Twig;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

final class SpacelessTokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();
        $lineno = $token->getLine();

        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);
        $body = $this->parser->subparse([$this, 'decideSpacelessEnd'], true);
        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);

        return new SpacelessNode($body, $lineno, $this->getTag());
    }

    public function decideSpacelessEnd(Token $token)
    {
        return $token->test('endspaceless');
    }

    public function getTag()
    {
        return 'spaceless';
    }
}
