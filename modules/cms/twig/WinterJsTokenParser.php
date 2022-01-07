<?php namespace Cms\Twig;

use Twig\Token as TwigToken;
use Twig\TokenParser\AbstractTokenParser as TwigTokenParser;

/**
 * Parser for the `{% winterjs %}` Twig tag.
 *
 * @package winter\wn-cms-module
 * @author Winter CMS
 */
class WinterJsTokenParser extends TwigTokenParser
{
    /**
     * @inheritDoc
     */
    public function parse(TwigToken $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $modules = [];

        do {
            $token = $stream->next();

            if ($token->getType() === TwigToken::NAME_TYPE) {
                $modules[] = $token->getValue();
            }
        } while ($token->getType() !== TwigToken::BLOCK_END_TYPE);

        // Filter out invalid types
        $modules = array_filter(
            array_map(function ($item) {
                return strtolower($item);
            }, $modules),
            function ($item) {
                return in_array($item, ['request', 'attr', 'extras', 'all']);
            }
        );

        if (in_array('all', $modules)) {
            $modules = [
                'request',
                'attr',
                'extras',
            ];
        }

        return new WinterJsNode($modules, $lineno, $this->getTag());
    }

    /**
     * @inheritDoc
     */
    public function getTag()
    {
        return 'winterjs';
    }
}
