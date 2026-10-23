<?php

namespace System\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use System\Classes\MarkupManager;

class MarkupManagerTest extends TestCase
{

    public function setUp() : void
    {
        parent::setUp();

        include_once base_path() . '/modules/system/tests/fixtures/plugins/winter/tester/Plugin.php';
    }

    //
    // Tests
    //

    public function testIsWildCallable()
    {
        $manager = MarkupManager::instance();

        /*
         * Negatives
         */
        $callable = 'something';
        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable]);
        $this->assertFalse($result);

        $callable = ['Form', 'open'];
        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable]);
        $this->assertFalse($result);

        $callable = function () {
            return 'O, Hai!';
        };
        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable]);
        $this->assertFalse($result);

        /*
         * String
         */
        $callable = 'something_*';
        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable]);
        $this->assertTrue($result);

        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable, 'delicious']);
        $this->assertEquals('something_delicious', $result);

        /*
         * Array
         */
        $callable = ['Class', 'foo_*'];
        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable]);
        $this->assertTrue($result);

        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable, 'bar']);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertEquals('Class', $result[0]);
        $this->assertEquals('foo_bar', $result[1]);

        $callable = ['My*', 'method'];
        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable]);
        $this->assertTrue($result);

        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable, 'Class']);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertEquals('MyClass', $result[0]);
        $this->assertEquals('method', $result[1]);

        $callable = ['My*', 'my*'];
        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable]);
        $this->assertTrue($result);

        $result = self::callProtectedMethod($manager, 'isWildCallable', [$callable, 'Food']);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertEquals('MyFood', $result[0]);
        $this->assertEquals('myFood', $result[1]);
    }

    /**
     * A variadic parameter declares every position from its own onwards, so a callable one
     * is not covered by checking the index it was recorded under alone.
     *
     * @dataProvider variadicCallableArgumentsProvider
     */
    public function testAStringReachingAVariadicCallableParameterIsRefused(array $arguments)
    {
        $this->expectException(\Twig\Sandbox\SecurityNotAllowedMethodError::class);

        self::callProtectedMethod(MarkupManager::instance(), 'checkWildcardMethod', [
            [VariadicCallableProbe::class, 'run'],
            $arguments,
        ]);
    }

    public function variadicCallableArgumentsProvider()
    {
        return [
            'first position' => [['system']],
            'behind a null' => [[null, 'system']],
            'behind two nulls' => [[null, null, 'system']],
        ];
    }

    public function testAVariadicCallableParameterStillAcceptsNoCallbackAtAll()
    {
        self::callProtectedMethod(MarkupManager::instance(), 'checkWildcardMethod', [
            [VariadicCallableProbe::class, 'run'],
            [null, null],
        ]);

        $this->assertTrue(true, 'the guard must not refuse a call that supplies no callback');
    }
}

/**
 * Stands in for a plugin registering a wildcard extension against a method whose callable
 * parameter is both nullable and variadic. Nothing in core declares that shape.
 */
class VariadicCallableProbe
{
    public static function run(?callable ...$fns): int
    {
        return count($fns);
    }
}
