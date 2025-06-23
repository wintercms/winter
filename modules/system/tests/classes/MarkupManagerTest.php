<?php

namespace System\Tests\Classes;

use System\Classes\MarkupManager;
use System\Tests\Bootstrap\TestCase;
use Twig\TwigFunction;
use Winter\Storm\Exception\SystemException;

class MarkupManagerTest extends TestCase
{
    protected $manager;
    protected $testFunctions;
    protected $testFunctionsWithOptions;

    private const OPTIONS = ['is_safe_callback' => ['html']];
    private const EMPTY_CALLABLE_MESSAGE = 'The markup function ([]) for emptyFunction is not callable.';
    private const INVALID_CALLABLE_MESSAGE = 'The markup function ({"callable":"not_a_function"}) for invalidFunction is not callable.';

    public function setUp() : void
    {
        parent::setUp();

        include_once base_path() . '/modules/system/tests/fixtures/plugins/winter/tester/Plugin.php';
        $this->manager = MarkupManager::instance();

        $this->testFunctions = $this->makeTestFunctionData(withOptions: false);
        $this->testFunctionsWithOptions = $this->makeTestFunctionData(withOptions: true);
    }

    private function makeTestFunctionData(bool $withOptions = false): array
    {
        $testFunction = function () {
            return 'testFunction return value';
        };
        $options = $withOptions ? self::OPTIONS : [];

        return [
            'testFunction1' => [
                'callable' => $testFunction,
                'options' => $options
            ],
            'testFunction2' => [
                $testFunction,
                'options' => $options
            ],
        ];
    }

    private function registerAndGetTwigFunctions(array $functions)
    {
        $this->manager->registerFunctions($functions);
        return self::callProtectedMethod($this->manager, 'makeTwigFunctions', []);
    }

    private function assertAllTwigFunctions(array $functions, string $message = '')
    {
        $this->assertIsArray($functions);
        $this->assertNotEmpty($functions);
        $this->assertContainsOnlyInstancesOf(
            TwigFunction::class,
            $functions,
            $message ?: "All elements must be TwigFunction instances"
        );
    }

    //
    // Tests
    //

    public function testIsWildCallable()
    {
        /*
         * Negatives
         */
        $callable = 'something';
        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable]);
        $this->assertFalse($result);

        $callable = ['Form', 'open'];
        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable]);
        $this->assertFalse($result);

        $callable = function () {
            return 'O, Hai!';
        };
        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable]);
        $this->assertFalse($result);

        /*
         * String
         */
        $callable = 'something_*';
        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable]);
        $this->assertTrue($result);

        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable, 'delicious']);
        $this->assertEquals('something_delicious', $result);

        /*
         * Array
         */
        $callable = ['Class', 'foo_*'];
        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable]);
        $this->assertTrue($result);

        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable, 'bar']);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertEquals('Class', $result[0]);
        $this->assertEquals('foo_bar', $result[1]);

        $callable = ['My*', 'method'];
        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable]);
        $this->assertTrue($result);

        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable, 'Class']);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertEquals('MyClass', $result[0]);
        $this->assertEquals('method', $result[1]);

        $callable = ['My*', 'my*'];
        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable]);
        $this->assertTrue($result);

        $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable, 'Food']);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertEquals('MyFood', $result[0]);
        $this->assertEquals('myFood', $result[1]);
    }

    public function testMakeTwigFunctionsHandlesCallableArrayWithOptions()
    {
        $functions = $this->registerAndGetTwigFunctions($this->testFunctionsWithOptions);
        $this->assertAllTwigFunctions($functions);
    }

    public function testMakeTwigFunctionsAppliesDefaultOptions()
    {
        $functions = $this->registerAndGetTwigFunctions($this->testFunctions);
        $this->assertAllTwigFunctions($functions);

        foreach ($functions as $function) {
            $options = self::getProtectedProperty($function, 'options');
            $this->assertArrayHasKey('is_safe', $options);
            $this->assertEquals(['html'], $options['is_safe']);
        }
    }

    public function testMakeTwigFunctionsHandlesEmptyCallableArray()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage(self::EMPTY_CALLABLE_MESSAGE);

        $this->manager->registerFunctions([
            'emptyFunction' => []
        ]);

        self::callProtectedMethod($this->manager, 'makeTwigFunctions', []);
    }

    public function testMakeTwigFunctionsHandlesInvalidCallable()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage(self::INVALID_CALLABLE_MESSAGE);

        $this->manager->registerFunctions([
            'invalidFunction' => [
                'callable' => 'not_a_function'
            ]
        ]);

        self::callProtectedMethod($this->manager, 'makeTwigFunctions', []);
    }
}
