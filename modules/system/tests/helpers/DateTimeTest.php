<?php

namespace System\Tests\Helpers;

use System\Helpers\DateTime as DateTimeHelper;
use System\Tests\Bootstrap\TestCase;

class DateTimeTest extends TestCase
{
    /**
     * @var string The default timezone in place before the test ran.
     */
    protected $originalTimezone;

    public function setUp(): void
    {
        parent::setUp();

        $this->originalTimezone = date_default_timezone_get();

        date_default_timezone_set('Europe/Brussels');
    }

    public function tearDown(): void
    {
        date_default_timezone_set($this->originalTimezone);

        parent::tearDown();
    }

    public function testMakeCarbonUsesTheDefaultTimezoneForTimestamps()
    {
        // 2023-11-14 23:30:00 UTC, which is 2023-11-15 00:30:00 in Europe/Brussels
        $date = DateTimeHelper::makeCarbon(1700004600);

        $this->assertSame('Europe/Brussels', $date->getTimezone()->getName());
        $this->assertSame('2023-11-15 00:30:00', $date->format('Y-m-d H:i:s'));
        $this->assertSame(1700004600, $date->getTimestamp());
    }

    public function testMakeCarbonTreatsEveryInputAsTheSameInstant()
    {
        $fromTimestamp = DateTimeHelper::makeCarbon(1700004600);
        $fromDateTime = DateTimeHelper::makeCarbon('2023-11-15 00:30:00');

        $this->assertSame($fromDateTime->format('Y-m-d H:i:s'), $fromTimestamp->format('Y-m-d H:i:s'));
        $this->assertSame($fromDateTime->getTimestamp(), $fromTimestamp->getTimestamp());
    }

    public function testTimeTenseUsesTheDefaultTimezoneForTimestamps()
    {
        $this->assertStringContainsString('00:30', DateTimeHelper::timeTense(1700004600));
    }
}
