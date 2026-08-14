<?php

namespace Cms\Tests\Classes;

use Cms\Classes\CodeParser;
use Cms\Classes\Layout;
use Cms\Classes\Theme;
use System\Tests\Bootstrap\TestCase;

/**
 * CodeParser writes a generated class to a path derived from the object, and names that class with a
 * value that is random per rebuild:
 *
 *     $uniqueName = str_replace('.', '', uniqid('', true)) . '_' . md5(mt_rand());
 *     $className  = 'Cms' . $uniqueName . 'Class';
 *
 * Loading it with require_once therefore only works once per process, because require_once keys on the
 * path. A process that serves a single request never notices. A worker does: the second rebuild of the
 * same object writes a new class name to an already-included path, require_once does nothing, and the
 * class is never declared —
 *
 *     Error: Class "Cms<hash>Class" not found
 *       Cms\Classes\CodeParser->source()
 *       Cms\Classes\Controller->initCustomObjects()
 *
 * which also disabled handleCorruptCache(), whose entire job is to rebuild after finding the cache
 * unusable. It rebuilt, then could not load what it had rebuilt.
 */
class CodeParserWorkerTest extends TestCase
{
    protected function makeParser(): CodeParser
    {
        $layout = Layout::load(Theme::load('test'), 'php-parser-test.htm');

        $this->assertNotEmpty($layout, 'The fixture layout the parser tests share is missing.');

        return new CodeParser($layout);
    }

    /**
     * Rebuilding twice in one process must leave the second class loadable, which is what a worker
     * serving two requests against the same page does after the request cache is cleared between them.
     */
    public function testRebuildingTwiceInOneProcessLeavesTheClassLoadable()
    {
        $parser = $this->makeParser();

        $first = $this->rebuild($parser);
        $this->assertTrue(class_exists($first), 'The first rebuild must be loadable.');

        $second = $this->rebuild($parser);

        $this->assertNotSame(
            $first,
            $second,
            'Each rebuild generates a random class name, so the two must differ, otherwise this test '
            . 'would pass for the wrong reason.'
        );
        $this->assertTrue(
            class_exists($second),
            'The second rebuild was not loadable. require_once keys on the path, and the path is the '
            . 'same for both rebuilds, so it silently did nothing the second time.'
        );
    }

    /**
     * Invokes the protected rebuild() against the path the parser itself would use.
     */
    protected function rebuild(CodeParser $parser): string
    {
        $reflection = new \ReflectionObject($parser);

        $path = $reflection->getMethod('getCacheFilePath');
        $path->setAccessible(true);

        $rebuild = $reflection->getMethod('rebuild');
        $rebuild->setAccessible(true);

        return $rebuild->invoke($parser, $path->invoke($parser));
    }
}
