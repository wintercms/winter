<?php namespace System\Classes;

use Str;
use Winter\Storm\Parse\Processor\YamlProcessor;

/**
 * "version.yaml" pre-processor class.
 *
 * Post-v3.x versions of the Symonfy/Yaml package use more recent versions of YAML spec, which breaks common
 * implementations of our version file format. To maintain compatibility, this class will pre-process YAML
 * contents from these files to work with Symfony/Yaml 4.0+.
 *
 * @author Winter CMS
 */
class VersionYamlProcessor extends YamlProcessor
{
    /**
     * @inheritDoc
     */
    public function preprocess($text)
    {
        $lines = preg_split('/[\n\r]+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($lines as $num => &$line) {
            // Ensure that !!! lines are quoted
            // @TODO: This is a brittle workaround, identify the possible cases where this can be a problem
            //        i.e. quotes inside the message, message part of multi-message update, etc; and resolve them
            // if (Str::contains($line, ': !!!')) {
            //     $line = Str::replace(': !!!', ': "!!!', $line);
            //     $line .= '"';
            // }

            // continue;



            // Surround array keys with quotes if not already
            $line = preg_replace_callback('/^\s*([\'"]{0}[^\'"\n\r:]+[\'"]{0})\s*:/m', function ($matches) {
                return '"' . trim($matches[1]) . '":';
            }, rtrim($line));

            // Add quotes around any unquoted text following an array key
            // specifically to ensure usage of !!! in unquoted comments does not fail
            $line = preg_replace('/^\s*([^\n\r\-:]+)\s*: +(?![\'"\s])(.*)/m', '$1: "$2"', $line);

            // If this line is the continuance of a multi-line string, remove the quote from the previous line and
            // continue the quote
            if (
                preg_match('/^(?!\s*(-|(.*?):\s*))([^\n\r]+)([^"]$)/m', $line)
                && substr($lines[$num - 1], -1) === '"'
            ) {
                $lines[$num - 1] = substr($lines[$num - 1], 0, -1);
                $line .= '"';
            }

            // Add quotes around any unquoted array items
            $line = preg_replace('/^(\s*-\s*)(?![\'" ])(.*)/m', '$1"$2"', $line);
        }

        return implode("\n", $lines);
    }

    /**
     * @inheritDoc
     */
    public function process($parsed)
    {
        return $parsed;
    }
}
