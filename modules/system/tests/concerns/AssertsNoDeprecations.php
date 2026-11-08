<?php

namespace System\Tests\Concerns;

/**
 * Fails a test when the code under test raises a deprecation. Twig and Symfony raise theirs
 * silenced with "@", so PHPUnit's own deprecation handling never sees them.
 */
trait AssertsNoDeprecations
{
    /**
     * Runs the callback and returns its result, failing if any deprecation not matching $ignore was raised.
     *
     * @param string[] $ignore Substrings of deprecation messages that are expected and allowed.
     */
    protected function assertNoDeprecations(callable $callback, array $ignore = []): mixed
    {
        $deprecations = [];

        set_error_handler(function (int $level, string $message, string $file, int $line) use (&$deprecations, $ignore) {
            foreach ($ignore as $expected) {
                if (str_contains($message, $expected)) {
                    return true;
                }
            }

            $deprecations[] = sprintf('%s (%s:%d)', $message, $file, $line);
            return true;
        }, E_DEPRECATED | E_USER_DEPRECATED);

        try {
            $result = $callback();
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], array_values(array_unique($deprecations)), 'Unexpected deprecations were raised.');

        return $result;
    }
}
