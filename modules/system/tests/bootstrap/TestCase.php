<?php

namespace System\Tests\Bootstrap;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\Assert;
use ReflectionClass;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app['cache']->setDefaultDriver('array');
        $app->setLocale('en');

        // Set random encryption key
        $app['config']->set('app.key', bin2hex(random_bytes(16)));

        // Override the Kernel call method to prevent symfony shell verbosity breaking scripts.
        // @see: https://github.com/symfony/symfony/pull/53632
        // @see: https://github.com/symfony/symfony/pull/24425
        $app->bind(Kernel::class, function (Application $app) {
            return new class($app, $app->make(Dispatcher::class)) extends \Winter\Storm\Foundation\Console\Kernel
            {
                public function call($command, array $parameters = [], $outputBuffer = null)
                {
                    $result = parent::call($command, $parameters, $outputBuffer);

                    $shellVerbosity = 0;

                    if (\function_exists('putenv')) {
                        @putenv('SHELL_VERBOSITY=' . $shellVerbosity);
                    }

                    $_ENV['SHELL_VERBOSITY'] = $_SERVER['SHELL_VERBOSITY'] = $shellVerbosity;

                    return $result;
                }
            };
        });

        return $app;
    }

    //
    // Helpers
    //

    protected static function callProtectedMethod($object, $name, $params = [])
    {
        $className = get_class($object);
        $class = new ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $params);
    }

    public static function getProtectedProperty($object, $name)
    {
        $className = get_class($object);
        $class = new ReflectionClass($className);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    public static function setProtectedProperty($object, $name, $value)
    {
        $className = get_class($object);
        $class = new ReflectionClass($className);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property->setValue($object, $value);
    }

    /**
     * Stub for `assertFileNotExists`
     */
    public static function assertFileNotExists(string $filename, string $message = ''): void
    {
        Assert::assertFileDoesNotExist($filename, $message);
    }

    /**
     * Stub for `assertRegExp`
     */
    public static function assertRegExp(string $pattern, string $string, string $message = ''): void
    {
        Assert::assertMatchesRegularExpression($pattern, $string, $message);
    }

    /**
     * Stub for `assertObjectHasAttribute`
     */
    public static function assertObjectHasAttribute(string $propertyName, $object, string $message = ''): void
    {
        Assert::assertObjectHasProperty($propertyName, $object, $message);
    }
}
