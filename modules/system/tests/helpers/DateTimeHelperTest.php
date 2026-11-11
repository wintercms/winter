<?php

namespace System\Tests\Helpers;

use System\Helpers\DateTime as DateTimeHelper;
use System\Tests\Bootstrap\TestCase;
use System\Tests\Concerns\AssertsNoDeprecations;

class DateTimeHelperTest extends TestCase
{
    use AssertsNoDeprecations;

    public function testMomentFormat()
    {
        $this->assertSame('YYYY-MM-DD HH:mm', DateTimeHelper::momentFormat('Y-m-d H:i'));
        $this->assertSame('[d]D', DateTimeHelper::momentFormat('\dj'));
    }

    public function testMomentFormatWithoutFormat()
    {
        // Date pickers without a configured format pass null through.
        $this->assertSame('', $this->assertNoDeprecations(fn () => DateTimeHelper::momentFormat(null)));
    }
}
