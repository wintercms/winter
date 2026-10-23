<?php

namespace Backend\Tests\Widgets;

use Backend\Widgets\Calendar\Classes\EventData;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Winter\Storm\Exception\ApplicationException;

/**
 * Unit coverage for the EventData value object that shapes records into the payload
 * FullCalendar consumes.
 */
class EventDataTest extends TestCase
{
    public function testDetectsAllDayFromDateOnlyStrings()
    {
        $event = new EventData(['title' => 'Holiday', 'start' => '2026-03-10']);

        $this->assertTrue($event->allDay);
        $this->assertSame('2026-03-10', $event->toArray()['start']);
    }

    public function testTreatsDatetimeStringsAsTimed()
    {
        $event = new EventData(['title' => 'Meeting', 'start' => '2026-03-10 09:00:00'], new DateTimeZone('UTC'));

        $this->assertFalse($event->allDay);
        $this->assertSame('2026-03-10T09:00:00+00:00', $event->toArray()['start']);
    }

    public function testExplicitAllDayOverridesDetection()
    {
        $event = new EventData([
            'title' => 'All day meeting',
            'start' => '2026-03-10 09:00:00',
            'allDay' => true,
        ]);

        $this->assertTrue($event->allDay);
        // An explicit allDay event is emitted date-only regardless of the source time.
        $this->assertSame('2026-03-10', $event->toArray()['start']);
    }

    public function testForcesConfiguredTimezoneOnTimedEvents()
    {
        // Tokyo has no DST, so the offset is unambiguous.
        $event = new EventData(['title' => 'Standup', 'start' => '2026-03-10 09:00:00'], new DateTimeZone('Asia/Tokyo'));

        $this->assertSame('2026-03-10T09:00:00+09:00', $event->toArray()['start']);
    }

    public function testAllDayEventsIgnoreTimezone()
    {
        // A date-only value must not be shifted across a day boundary by the timezone.
        $event = new EventData(['title' => 'Holiday', 'start' => '2026-03-10'], new DateTimeZone('Asia/Tokyo'));

        $this->assertTrue($event->allDay);
        $this->assertSame('2026-03-10', $event->toArray()['start']);
    }

    public function testIncludesEndWhenProvidedAndOmitsWhenNot()
    {
        $withEnd = new EventData([
            'title' => 'Meeting',
            'start' => '2026-03-10 09:00:00',
            'end' => '2026-03-10 10:00:00',
        ], new DateTimeZone('UTC'));
        $this->assertSame('2026-03-10T10:00:00+00:00', $withEnd->toArray()['end']);

        $withoutEnd = new EventData(['title' => 'Meeting', 'start' => '2026-03-10 09:00:00'], new DateTimeZone('UTC'));
        $this->assertArrayNotHasKey('end', $withoutEnd->toArray());
    }

    public function testPassesThroughAdditionalProperties()
    {
        $event = new EventData([
            'title' => 'Meeting',
            'start' => '2026-03-10 09:00:00',
            'id' => 42,
            'url' => 'https://example.test/events/42',
            'color' => '#ff0000',
            'tooltip' => 'Weekly sync',
        ], new DateTimeZone('UTC'));

        $array = $event->toArray();
        $this->assertSame(42, $array['id']);
        $this->assertSame('https://example.test/events/42', $array['url']);
        $this->assertSame('#ff0000', $array['color']);
        $this->assertSame('Weekly sync', $array['tooltip']);
        $this->assertSame('Meeting', $array['title']);
    }

    public function testRequiresTitle()
    {
        $this->expectException(ApplicationException::class);
        new EventData(['start' => '2026-03-10 09:00:00']);
    }

    public function testRequiresStart()
    {
        $this->expectException(ApplicationException::class);
        new EventData(['title' => 'Meeting']);
    }
}
