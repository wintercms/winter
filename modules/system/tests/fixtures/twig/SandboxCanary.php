<?php

namespace System\Tests\Fixtures\Twig;

/**
 * Counts every constructor call and every callable dispatch that the Twig sandbox lets through,
 * so a security test can assert on what actually ran rather than on which exception was thrown.
 */
class SandboxCanary
{
    /**
     * @var int Number of times the sandbox allowed this class to be instantiated.
     */
    public static $instantiations = 0;

    /**
     * @var int Number of times the sandbox allowed a callable naming this class to be invoked.
     */
    public static $invocations = 0;

    public function __construct(...$arguments)
    {
        static::$instantiations++;
    }

    /**
     * Named as a string callable, e.g. `beforeQuery('...\SandboxCanary::invoke')`.
     */
    public static function invoke(...$arguments)
    {
        static::$invocations++;
    }

    public static function reset(): void
    {
        static::$instantiations = 0;
        static::$invocations = 0;
    }
}
