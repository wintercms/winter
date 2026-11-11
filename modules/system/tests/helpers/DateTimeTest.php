<?php

namespace System\Tests\Helpers;

use System\Helpers\DateTime as DateTimeHelper;
use System\Tests\Bootstrap\TestCase;

class DateTimeTest extends TestCase
{
    /**
     * 2023-11-14 23:30:00 UTC, which is 2023-11-15 00:30:00 in Europe/Brussels.
     * The two fall on different calendar days, so a timezone slip changes the date as well as the time.
     */
    const TIMESTAMP = 1700004600;
    const LOCAL_DATETIME = '2023-11-15 00:30:00';

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

    public function testMakeCarbonReadsTimestampsInTheDefaultTimezone()
    {
        $date = DateTimeHelper::makeCarbon(static::TIMESTAMP);

        $this->assertSame(static::LOCAL_DATETIME, $date->format('Y-m-d H:i:s'));
        $this->assertSame(static::TIMESTAMP, $date->getTimestamp());
    }

    /**
     * A timestamp and the local datetime it represents must produce the same Carbon instance, so that a
     * value stored as a timestamp renders the same as one stored as a datetime string.
     */
    public function testMakeCarbonReadsATimestampAndItsLocalDatetimeIdentically()
    {
        $fromTimestamp = DateTimeHelper::makeCarbon(static::TIMESTAMP);
        $fromDatetime = DateTimeHelper::makeCarbon(static::LOCAL_DATETIME);

        $this->assertSame($fromDatetime->format('Y-m-d H:i:s'), $fromTimestamp->format('Y-m-d H:i:s'));
        $this->assertSame($fromDatetime->getTimestamp(), $fromTimestamp->getTimestamp());
    }

    public function testTimeTenseReadsTimestampsInTheDefaultTimezone()
    {
        $this->assertSame('15 Nov 2023 at 00:30', DateTimeHelper::timeTense(static::TIMESTAMP));
        $this->assertSame(
            DateTimeHelper::timeTense(static::LOCAL_DATETIME),
            DateTimeHelper::timeTense(static::TIMESTAMP)
        );
    }
}
