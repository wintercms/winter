<?php

namespace System\Phpcs\Tests\Sniffs;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class NoGlobalAliasesSniffTest extends AbstractSniffUnitTest
{
    /**
     * Returns the lines where warnings should occur.
     *
     * @return array<int, int> Line numbers as keys, number of warnings as values.
     */
    public function getWarningList(): array
    {
        return [
            3 => 1, // Global alias Config
            4 => 1, // Global alias Lang
            5 => 1, // Global alias URL
            9 => 0, // Fully-qualified import (no error)
            10 => 1, // Global alias Yaml
            14 => 0, // Trait imported (no error)
            15 => 1, // Trait conflicts with alias
        ];
    }

    /**
     * Returns the lines where errors should occur.
     *
     * @return array<int, int> Line numbers as keys, number of errors as values.
     */
    public function getErrorList(): array
    {
        return [];
    }
}
