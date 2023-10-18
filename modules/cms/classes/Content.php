<?php

namespace Cms\Classes;

use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Facades\Markdown;

/**
 * The CMS content file class.
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class Content extends CmsCompoundObject
{
    /**
     * @var string The container name associated with the model, eg: pages.
     */
    protected $dirName = 'content';

    /**
     * @var array Allowable file extensions.
     */
    protected $allowedExtensions = ['htm', 'txt', 'md'];

    /**
     * @var array List of attribute names which are not considered "settings".
     */
    protected $purgeable = ['parsedMarkup'];

    /**
     * Initializes the object properties from the cached data. The extra data
     * set here becomes available as attributes set on the model after fetch.
     * @param array $item The cached data array.
     */
    public static function initCacheItem(&$item)
    {
        $item['parsedMarkup'] = (new static($item))->parseMarkup();
    }

    /**
     * Returns a default value for parsedMarkup attribute.
     * @return string
     */
    public function getParsedMarkupAttribute()
    {
        if (array_key_exists('parsedMarkup', $this->attributes)) {
            return $this->attributes['parsedMarkup'];
        }

        return $this->attributes['parsedMarkup'] = $this->parseMarkup();
    }

    /**
     * Parses the content markup according to the file type.
     * @return string
     */
    public function parseMarkup()
    {
        $extension = strtolower(File::extension($this->fileName));

        switch ($extension) {
            case 'txt':
                $result = htmlspecialchars($this->markup);
                break;
            case 'md':
                $result = $this->parseMarkdownMarkup($this->markup);
                break;
            default:
                $result = $this->markup;
        }

        return $result;
    }

    /**
     * Parse Markdown content files.
     *
     * This method replaces curly variables in the content temporarily while Markdown rendering takes place, to
     * circumvent the escaping that Commonmark does on curly brackets in links.
     */
    protected function parseMarkdownMarkup(string $markup): string
    {
        /**
         * Replace variables temporarily for Markdown parsing, as the Commonmark library escapes curly brackets in
         * links.
         * @see https://github.com/wintercms/winter/issues/992
         */
        $variables = [];
        $markup = preg_replace_callback('/\{([^\n \}]+)\}/', function (array $matches) use (&$variables) {
            $signature = hash('sha256', $matches[1]);
            if (!array_key_exists($signature, $variables)) {
                $variables[$signature] = $matches[1];
            }
            return ".=VAR={$signature}=.";
        }, $markup);

        $markup = Markdown::parse($markup);

        foreach ($variables as $signature => $variable) {
            $markup = str_replace(".=VAR={$signature}=.", "{{$variable}}", $markup);
        }

        return $markup;
    }
}
