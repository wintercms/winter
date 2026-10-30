<?php

namespace Backend\Tests\FormWidgets;

use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\FormWidgets\DatePicker;
use System\Tests\Bootstrap\PluginTestCase;

class DatePickerTest extends PluginTestCase
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

    /**
     * The bounds are passed to the picker as wall times, so a timestamp bound read as UTC moves the
     * limit by the UTC offset and can block a date the configuration allows.
     */
    public function testTimestampBoundsUseTheDefaultTimezone()
    {
        $widget = $this->makeWidget([
            'minDate' => static::TIMESTAMP,
            'maxDate' => static::TIMESTAMP,
        ]);

        $this->assertSame(static::LOCAL_DATETIME, $widget->minDate->format('Y-m-d H:i:s'));
        $this->assertSame(static::LOCAL_DATETIME, $widget->maxDate->format('Y-m-d H:i:s'));
    }

    public function testTimestampBoundsMatchTheirLocalDatetimeEquivalent()
    {
        $fromTimestamp = $this->makeWidget(['minDate' => static::TIMESTAMP]);
        $fromDatetime = $this->makeWidget(['minDate' => static::LOCAL_DATETIME]);

        $this->assertSame(
            $fromDatetime->minDate->format('Y-m-d H:i:s'),
            $fromTimestamp->minDate->format('Y-m-d H:i:s')
        );
        $this->assertSame($fromDatetime->minDate->getTimestamp(), $fromTimestamp->minDate->getTimestamp());
    }

    protected function makeWidget(array $config = []): DatePicker
    {
        return new DatePicker(new Controller(), new FormField('test', 'Test'), $config);
    }
}
