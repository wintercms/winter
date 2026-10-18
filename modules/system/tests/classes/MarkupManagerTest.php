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

    /**
     * Generates a not-callable error message.
     *
     * @param string $type     Either 'function' or 'filter'
     * @param string $details  The details string (e.g., '[]' or '{"callable":"not_a_callable"}')
     * @param string $name     The name of the callable
     * @return string
     */
    public static function notCallableExceptionMessage(string $type, string $details, string $name): string
    {
        return sprintf('The markup %s (%s) for %s is not callable.', $type, $details, $name);
    }

    private function expectExceptionForEmptyCallable(string $type)
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage(self::notCallableExceptionMessage($type, '[]', 'emptyCallable'));
        if ($type === TwigFunction::class) {
            $this->manager->registerFunctions(['emptyCallable' => []]);
            self::callProtectedMethod($this->manager, 'makeTwigFunctions', []);
        } else {
            $this->manager->registerFilters(['emptyCallable' => []]);
            self::callProtectedMethod($this->manager, 'makeTwigFilters', []);
        }
    }

    private function expectExceptionForInvalidCallable(string $type)
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage(self::notCallableExceptionMessage($type, '{"callable":"not_a_callable"}', 'invalidCallable'));
        if ($type === TwigFunction::class) {
            $this->manager->registerFunctions(['invalidCallable' => ['callable' => 'not_a_callable']]);
            self::callProtectedMethod($this->manager, 'makeTwigFunctions', []);
        } else {
            $this->manager->registerFilters(['invalidCallable' => ['callable' => 'not_a_callable']]);
            self::callProtectedMethod($this->manager, 'makeTwigFilters', []);
        }
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

    private function assertAllTwigInstances(array $items, string $class, string $message = '')
    {
        $this->assertIsArray($items);
        $this->assertNotEmpty($items);
        $this->assertContainsOnlyInstancesOf($class, $items, $message ?: "All elements must be instances of $class");
    }

    //
    // Tests
    //

    public function testIsWildCallable()
    {
        /*
         * Negatives
         */
        $negatives = [
            'something',
            ['Form', 'open'],
            function () {
                return 'O, Hai!';
            },
        ];
        foreach ($negatives as $callable) {
            $result = self::callProtectedMethod($this->manager, 'isWildCallable', [$callable]);
            $this->assertFalse($result);
        }

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
        $this->assertAllTwigInstances($functions, TwigFunction::class);
    }

    public function testMakeTwigFiltersHandlesCallableArrayWithOptions()
    {
        $filters = $this->registerAndGetTwigFilters($this->testCallableDataWithOptions);
        $this->assertAllTwigInstances($filters, TwigFilter::class);
    }

    public function testMakeTwigFunctionsAppliesDefaultOptions()
    {
        $functions = $this->registerAndGetTwigFunctions($this->testCallableData);
        $this->assertAllTwigInstances($functions, TwigFunction::class);
        foreach ($functions as $function) {
            $options = self::getProtectedProperty($function, 'options');
            $this->assertArrayHasKey('is_safe', $options);
            $this->assertEquals(['html'], $options['is_safe']);
        }
    }

    public function testMakeTwigFiltersAppliesDefaultOptions()
    {
        $filters = $this->registerAndGetTwigFilters($this->testCallableData);
        $this->assertAllTwigInstances($filters, TwigFilter::class);
        foreach ($filters as $filter) {
            $options = self::getProtectedProperty($filter, 'options');
            $this->assertArrayHasKey('is_safe', $options);
            $this->assertEquals(['html'], $options['is_safe']);
        }
    }

    public function testMakeTwigFunctionsHandlesEmptyCallableArray()
    {
        $this->expectExceptionForEmptyCallable(TwigFunction::class);
    }

    public function testMakeTwigFiltersHandlesEmptyCallableArray()
    {
        $this->expectExceptionForEmptyCallable(TwigFilter::class);
    }

    public function testMakeTwigFunctionsHandlesInvalidCallable()
    {
        $this->expectExceptionForInvalidCallable(TwigFunction::class);
    }

    public function testMakeTwigFiltersHandlesInvalidCallable()
    {
        $this->expectExceptionForInvalidCallable(TwigFilter::class);
    }
}
