<?php

namespace Cms\Tests\Classes;

use Cms\Classes\CodeParser;
use Cms\Classes\Layout;
use Cms\Classes\Theme;
use System\Tests\Bootstrap\TestCase;

/**
 * rebuild() writes a randomly named class to a fixed path, so require_once loads only the first
 * rebuild of a given object in a process. Later rebuilds, including ones written by another
 * process, left their class undeclared.
 */
class CodeParserRebuildTest extends TestCase
{
    public function tearDown(): void
    {
        // The parser's request cache is static and would otherwise reach the next test.
        (new \ReflectionProperty(CodeParser::class, 'cache'))->setValue(null, []);

        parent::tearDown();
    }

    protected function makeParser(): CodeParser
    {
        $layout = Layout::load(Theme::load('test'), 'php-parser-test.htm');

        $this->assertNotEmpty($layout, 'The fixture layout the parser tests share is missing.');

        return new CodeParser($layout);
    }

    public function testRebuildingTwiceInOneProcessLeavesTheClassLoadable()
    {
        $parser = $this->makeParser();

        $first = $this->rebuild($parser);
        $this->assertTrue(class_exists($first), 'The first rebuild must be loadable.');

        $second = $this->rebuild($parser);

        $this->assertNotSame(
            $first,
            $second,
            'Each rebuild should generate a new class name.'
        );
        $this->assertTrue(
            class_exists($second),
            'The second rebuild should be loadable.'
        );
    }

    public function testSourceLoadsARebuildWrittenByAnotherProcess()
    {
        $parser = $this->makeParser();
        $reflection = new \ReflectionObject($parser);

        $first = $this->rebuild($parser);
        $path = $reflection->getMethod('getCacheFilePath')->invoke($parser);

        // Another process rebuilds the same object: same path, new class name.
        $second = 'Cms' . str_replace('.', '', uniqid('', true)) . '_' . md5(mt_rand()) . 'Class';
        file_put_contents($path, str_replace($first, $second, file_get_contents($path)));
        $reflection->getMethod('storeCachedInfo')->invoke($parser, [
            'filePath' => $path,
            'className' => $second,
            'source' => 'cache',
            'offset' => 0,
        ]);

        $this->assertInstanceOf($second, $parser->source(null, null, null));
    }

    /**
     * Invokes the protected rebuild() against the path the parser itself would use.
     */
    protected function rebuild(CodeParser $parser): string
    {
        $reflection = new \ReflectionObject($parser);
        $path = $reflection->getMethod('getCacheFilePath')->invoke($parser);

        return $reflection->getMethod('rebuild')->invoke($parser, $path);
    }
}
