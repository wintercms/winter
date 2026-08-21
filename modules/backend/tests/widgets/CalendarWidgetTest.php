<?php

namespace Backend\Tests\Widgets;

use Backend\Tests\Fixtures\Models\CalendarEventFixture;
use Backend\Widgets\Calendar;
use Carbon\Carbon;
use Config;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;

/**
 * Coverage for the Calendar widget's record fetching, with an emphasis on the visible-window
 * date-range pre-filter and the recurrence opt-out that lets consumers expand recurring events
 * in the `backend.calendar.extendRecords` event.
 */
class CalendarWidgetTest extends PluginTestCase
{
    /**
     * Start of the visible window used throughout: 2026-03-01 00:00:00 UTC.
     */
    protected int $windowStart;

    /**
     * End of the visible window: 2026-04-01 00:00:00 UTC.
     */
    protected int $windowEnd;

    public function setUp(): void
    {
        parent::setUp();

        CalendarEventFixture::migrateUp();

        $this->windowStart = Carbon::parse('2026-03-01 00:00:00', 'UTC')->timestamp;
        $this->windowEnd = Carbon::parse('2026-04-01 00:00:00', 'UTC')->timestamp;
    }

    public function tearDown(): void
    {
        CalendarEventFixture::migrateDown();

        parent::tearDown();
    }

    /**
     * Builds a Calendar widget bound to the fixture model with the default record mapping.
     */
    protected function makeCalendarWidget(array $config = []): Calendar
    {
        $model = new CalendarEventFixture;

        $widget = new Calendar(null, array_merge([
            'alias' => 'calendar',
            'recordTitle' => 'name',
            'recordStart' => 'start_at',
            'recordEnd' => 'end_at',
            'recordAllDay' => 'all_day',
        ], $config));

        $widget->model = $model;

        return $widget;
    }

    /**
     * Seeds a single event and returns the created model.
     */
    protected function seedEvent(string $name, string $start, ?string $end = null, array $attributes = []): CalendarEventFixture
    {
        Model::unguard();
        $event = CalendarEventFixture::create(array_merge([
            'name' => $name,
            'start_at' => $start,
            'end_at' => $end,
            'all_day' => false,
        ], $attributes));
        Model::reguard();

        return $event;
    }

    /**
     * Returns the event titles from a getRecords() result payload.
     */
    protected function titlesFrom(array $result): array
    {
        return array_map(fn ($event) => $event['title'], $result['events']);
    }

    public function testDateRangeFilterLimitsRecordsToVisibleWindow()
    {
        $this->seedEvent('inside', '2026-03-10 09:00:00', '2026-03-10 10:00:00');
        $this->seedEvent('before', '2026-02-10 09:00:00', '2026-02-10 10:00:00');
        $this->seedEvent('after', '2026-04-10 09:00:00', '2026-04-10 10:00:00');
        $this->seedEvent('spanning', '2026-02-25 09:00:00', '2026-03-02 10:00:00');

        $widget = $this->makeCalendarWidget();
        $titles = $this->titlesFrom($widget->getRecords($this->windowStart, $this->windowEnd));

        sort($titles);
        $this->assertSame(['inside', 'spanning'], $titles);
    }

    public function testGetRecordsWithoutWindowReturnsEverything()
    {
        $this->seedEvent('inside', '2026-03-10 09:00:00', '2026-03-10 10:00:00');
        $this->seedEvent('before', '2026-02-10 09:00:00', '2026-02-10 10:00:00');

        $widget = $this->makeCalendarWidget();

        // No window supplied - the range filter is a no-op and every record is returned.
        $this->assertCount(2, $widget->getRecords()['events']);
    }

    public function testDateRangeFilterCanBeDisabledViaConfig()
    {
        $this->seedEvent('inside', '2026-03-10 09:00:00', '2026-03-10 10:00:00');
        $this->seedEvent('before', '2026-02-10 09:00:00', '2026-02-10 10:00:00');
        $this->seedEvent('after', '2026-04-10 09:00:00', '2026-04-10 10:00:00');

        $widget = $this->makeCalendarWidget(['applyDateRangeFilter' => false]);
        $titles = $this->titlesFrom($widget->getRecords($this->windowStart, $this->windowEnd));

        // With the built-in filter disabled the widget no longer constrains to the window;
        // the consumer is responsible for any windowing (e.g. in extendQuery).
        sort($titles);
        $this->assertSame(['after', 'before', 'inside'], $titles);
    }

    public function testRecurringMasterOutsideWindowIsDroppedByDefault()
    {
        // A monthly recurring master whose base start sits well before the visible window.
        $this->seedEvent('recurring', '2026-01-01 09:00:00', '2026-01-01 10:00:00', [
            'rrule' => 'FREQ=MONTHLY;COUNT=12',
        ]);

        $widget = $this->makeCalendarWidget();

        // Without an opt-out the master is filtered out at the database level before a consumer
        // ever sees it - this is the behaviour the recurrence opt-out exists to work around.
        $this->assertCount(0, $widget->getRecords($this->windowStart, $this->windowEnd)['events']);
    }

    public function testExtendQueryListenerCanDisableFilterSoRecurrenceExpandsInExtendRecords()
    {
        $this->seedEvent('recurring', '2026-01-01 09:00:00', '2026-01-01 10:00:00', [
            'rrule' => 'FREQ=MONTHLY;COUNT=12',
        ]);

        $widget = $this->makeCalendarWidget();

        // A consumer that expands recurrence server-side turns off the built-in window filter
        // so the master row survives the query...
        $widget->bindEvent('calendar.extendQueryBefore', function () use ($widget) {
            $widget->setApplyDateRangeFilter(false);
        });

        // ...then expands the surviving master into concrete occurrences for the window.
        $widget->bindEvent('calendar.extendRecords', function (&$records, $startTime, $endTime) {
            $this->assertGreaterThan(0, $records->count(), 'Recurring master should survive the query');

            $occurrences = collect();
            foreach ($records as $master) {
                $occurrence = new CalendarEventFixture([
                    'name' => $master->name,
                    'start_at' => Carbon::createFromTimestamp($startTime, 'UTC')->addDays(9)->setTime(9, 0)->format('Y-m-d H:i:s'),
                    'end_at' => Carbon::createFromTimestamp($startTime, 'UTC')->addDays(9)->setTime(10, 0)->format('Y-m-d H:i:s'),
                    'all_day' => false,
                ]);
                $occurrences->push($occurrence);
            }

            return $occurrences;
        });

        $result = $widget->getRecords($this->windowStart, $this->windowEnd);

        $this->assertCount(1, $result['events']);
        $this->assertSame('recurring', $result['events'][0]['title']);
        $this->assertStringStartsWith('2026-03-10', $result['events'][0]['start']);
    }

    public function testTimezoneDefaultsToApplicationTimezone()
    {
        Config::set('app.timezone', 'America/Toronto');

        $widget = $this->makeCalendarWidget();

        $this->assertSame('America/Toronto', $widget->getTimezone());
    }

    public function testTimezoneConfigControlsEventOutputOffset()
    {
        $this->seedEvent('meeting', '2026-03-10 09:00:00', '2026-03-10 10:00:00');

        // Tokyo has no DST, so the offset is unambiguous.
        $tokyo = $this->makeCalendarWidget(['timezone' => 'Asia/Tokyo']);
        $tokyoEvent = $tokyo->getRecords($this->windowStart, $this->windowEnd)['events'][0];
        $this->assertSame('2026-03-10T09:00:00+09:00', $tokyoEvent['start']);

        $utc = $this->makeCalendarWidget(['timezone' => 'UTC']);
        $utcEvent = $utc->getRecords($this->windowStart, $this->windowEnd)['events'][0];
        $this->assertSame('2026-03-10T09:00:00+00:00', $utcEvent['start']);
    }
}
