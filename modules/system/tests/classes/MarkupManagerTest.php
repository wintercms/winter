<?php

namespace System\Tests\Classes;

use System\Classes\MarkupManager;
use System\Tests\Bootstrap\TestCase;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Winter\Storm\Exception\SystemException;

class MarkupManagerTest extends TestCase
{
    protected $manager;
    protected $testCallableData;
    protected $testCallableDataWithOptions;

    private const OPTIONS = ['is_safe_callback' => ['html']];
    private const EMPTY_FUNCTION_MESSAGE = 'The markup function ([]) for emptyCallable is not callable.';
    private const INVALID_FUNCTION_MESSAGE = 'The markup function ({"callable":"not_a_callable"}) for invalidCallable is not callable.';
    private const EMPTY_FILTER_MESSAGE = 'The markup filter ([]) for emptyCallable is not callable.';
    private const INVALID_FILTER_MESSAGE = 'The markup filter ({"callable":"not_a_callable"}) for invalidCallable is not callable.';

    public function setUp() : void
    {
        parent::setUp();

        include_once base_path() . '/modules/system/tests/fixtures/plugins/winter/tester/Plugin.php';
        $this->manager = MarkupManager::instance();

        $this->testCallableData = $this->makeTestCallableData(withOptions: false);
        $this->testCallableDataWithOptions = $this->makeTestCallableData(withOptions: true);
    }

    private function makeTestCallableData(bool $withOptions = false): array
    {
        $testCallable = function () {
            return 'testCallable return value';
        };
        $options = $withOptions ? self::OPTIONS : [];

        return [
            'testCallable1' => [
                'callable' => $testCallable,
                'options' => $options
            ],
            'testCallable2' => [
                $testCallable,
                'options' => $options
            ],
        ];
    }

    private function registerAndGetTwigFunctions(array $functions)
    {
        $this->manager->registerFunctions($functions);
        return self::callProtectedMethod($this->manager, 'makeTwigFunctions', []);
    }

    private function registerAndGetTwigFilters(array $filters)
    {
        $this->manager->registerFilters($filters);
        return self::callProtectedMethod($this->manager, 'makeTwigFilters', []);
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

    private function assertAllTwigFilters(array $filters, string $message = '')
    {
        $this->assertIsArray($filters);
        $this->assertNotEmpty($filters);
        $this->assertContainsOnlyInstancesOf(
            TwigFilter::class,
            $filters,
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
        $functions = $this->registerAndGetTwigFunctions($this->testCallableDataWithOptions);
        $this->assertAllTwigFunctions($functions);
    }

    public function testMakeTwigFiltersHandlesCallableArrayWithOptions()
    {
        $filters = $this->registerAndGetTwigFilters($this->testCallableDataWithOptions);
        $this->assertAllTwigFilters($filters);
    }

    public function testMakeTwigFunctionsAppliesDefaultOptions()
    {
        $functions = $this->registerAndGetTwigFunctions($this->testCallableData);
        $this->assertAllTwigFunctions($functions);

        foreach ($functions as $function) {
            $options = self::getProtectedProperty($function, 'options');
            $this->assertArrayHasKey('is_safe', $options);
            $this->assertEquals(['html'], $options['is_safe']);
        }
    }

    public function testMakeTwigFiltersAppliesDefaultOptions()
    {
        $filters = $this->registerAndGetTwigFilters($this->testCallableData);
        $this->assertAllTwigFilters($filters);

        foreach ($filters as $filter) {
            $options = self::getProtectedProperty($filter, 'options');
            $this->assertArrayHasKey('is_safe', $options);
            $this->assertEquals(['html'], $options['is_safe']);
        }
    }

    public function testMakeTwigFunctionsHandlesEmptyCallableArray()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage(self::EMPTY_FUNCTION_MESSAGE);

        $this->manager->registerFunctions([
            'emptyCallable' => []
        ]);

        self::callProtectedMethod($this->manager, 'makeTwigFunctions', []);
    }

    public function testMakeTwigFiltersHandlesEmptyCallableArray()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage(self::EMPTY_FILTER_MESSAGE);

        $this->manager->registerFilters([
            'emptyCallable' => []
        ]);

        self::callProtectedMethod($this->manager, 'makeTwigFilters', []);
    }

    public function testMakeTwigFunctionsHandlesInvalidCallable()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage(self::INVALID_FUNCTION_MESSAGE);

        $this->manager->registerFunctions([
            'invalidCallable' => [
                'callable' => 'not_a_callable'
            ]
        ]);

        self::callProtectedMethod($this->manager, 'makeTwigFunctions', []);
    }

    public function testMakeTwigFiltersHandlesInvalidCallable()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage(self::INVALID_FILTER_MESSAGE);

        $this->manager->registerFilters([
            'invalidCallable' => [
                'callable' => 'not_a_callable'
            ]
        ]);

        self::callProtectedMethod($this->manager, 'makeTwigFilters', []);
    }
}
