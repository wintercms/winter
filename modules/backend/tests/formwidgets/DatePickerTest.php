<?php

namespace Backend\Tests\FormWidgets;

use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\FormWidgets\DatePicker;
use System\Tests\Bootstrap\PluginTestCase;

class DatePickerTest extends PluginTestCase
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

    public function testTimestampBoundsUseTheDefaultTimezone()
    {
        // 2023-11-14 23:30:00 UTC, which is 2023-11-15 00:30:00 in Europe/Brussels
        $widget = $this->makeWidget([
            'minDate' => 1700004600,
            'maxDate' => 1700004600,
        ]);

        $this->assertSame('2023-11-15 00:30:00', $widget->minDate->format('Y-m-d H:i:s'));
        $this->assertSame('2023-11-15 00:30:00', $widget->maxDate->format('Y-m-d H:i:s'));
    }

    public function testTimestampBoundsMatchTheirStringEquivalent()
    {
        $fromTimestamp = $this->makeWidget(['minDate' => 1700004600]);
        $fromString = $this->makeWidget(['minDate' => '2023-11-15 00:30:00']);

        $this->assertSame(
            $fromString->minDate->format('Y-m-d H:i:s'),
            $fromTimestamp->minDate->format('Y-m-d H:i:s')
        );
        $this->assertSame($fromString->minDate->getTimestamp(), $fromTimestamp->minDate->getTimestamp());
    }

    protected function makeWidget(array $config = []): DatePicker
    {
        return new DatePicker(new Controller(), new FormField('test', 'Test'), $config);
    }
}
