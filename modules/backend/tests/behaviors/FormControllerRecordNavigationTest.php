<?php

namespace Backend\Tests\Behaviors;

use Backend\Behaviors\FormController;
use System\Tests\Bootstrap\TestCase;

/**
 * Unit coverage for FormController record navigation.
 *
 * The previous/next/current/total math is a pure, database-agnostic helper
 * (`resolveRecordPosition`) that resolves a record's position within an already
 * ordered set of keys. Keeping it free of SQL is what lets the navigation work
 * identically across every database driver Winter supports — the ordered keys
 * are read once via a portable `pluck`, and the position is worked out here in
 * PHP rather than with driver-specific window functions or session variables.
 */
class FormControllerRecordNavigationTest extends TestCase
{
    public function testEmptySet(): void
    {
        $this->assertSame(
            ['previous' => null, 'next' => null, 'current' => null, 'total' => 0],
            FormController::resolveRecordPosition([], 5)
        );
    }

    public function testSingleRecordThatIsCurrent(): void
    {
        $this->assertSame(
            ['previous' => null, 'next' => null, 'current' => 1, 'total' => 1],
            FormController::resolveRecordPosition([5], 5)
        );
    }

    public function testFirstRecordHasNextButNoPrevious(): void
    {
        $this->assertSame(
            ['previous' => null, 'next' => 6, 'current' => 1, 'total' => 3],
            FormController::resolveRecordPosition([5, 6, 7], 5)
        );
    }

    public function testMiddleRecordHasBothNeighbours(): void
    {
        $this->assertSame(
            ['previous' => 5, 'next' => 7, 'current' => 2, 'total' => 3],
            FormController::resolveRecordPosition([5, 6, 7], 6)
        );
    }

    public function testLastRecordHasPreviousButNoNext(): void
    {
        $this->assertSame(
            ['previous' => 6, 'next' => null, 'current' => 3, 'total' => 3],
            FormController::resolveRecordPosition([5, 6, 7], 7)
        );
    }

    public function testCurrentKeyNotInSetYieldsNoPosition(): void
    {
        $this->assertSame(
            ['previous' => null, 'next' => null, 'current' => null, 'total' => 3],
            FormController::resolveRecordPosition([5, 6, 7], 99)
        );
    }

    public function testKeysAreComparedLoosely(): void
    {
        // Database drivers may return keys as strings while the current model
        // key is an integer (or vice versa); comparison must not be type-strict.
        $this->assertSame(
            ['previous' => '5', 'next' => '7', 'current' => 2, 'total' => 3],
            FormController::resolveRecordPosition(['5', '6', '7'], 6)
        );
    }

    public function testNonNumericKeysAreSupported(): void
    {
        // Works for UUID / string primary keys, not just auto-increment integers.
        $this->assertSame(
            ['previous' => 'aaa', 'next' => 'ccc', 'current' => 2, 'total' => 3],
            FormController::resolveRecordPosition(['aaa', 'bbb', 'ccc'], 'bbb')
        );
    }

    public function testGapsInKeysArePreserved(): void
    {
        $this->assertSame(
            ['previous' => 3, 'next' => 40, 'current' => 3, 'total' => 4],
            FormController::resolveRecordPosition([1, 3, 12, 40], 12)
        );
    }
}
