<?php

namespace Backend\Tests\Behaviors;

use Backend\Classes\Controller;
use Backend\Tests\Fixtures\Models\CalendarEventFixture;
use Backend\Tests\Fixtures\Models\UserFixture;
use Backend\Widgets\Calendar as CalendarWidget;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * A minimal controller wired up with the CalendarController behavior and an inline config
 * array (no YAML/view directory required).
 */
class CalendarTestController extends Controller
{
    public $implement = [\Backend\Behaviors\CalendarController::class];

    public $calendarConfig = [
        'modelClass' => CalendarEventFixture::class,
        'searchList' => ['columns' => ['name' => ['label' => 'Name', 'searchable' => true]]],
        'recordTitle' => 'name',
        'recordStart' => 'start_at',
        'recordEnd' => 'end_at',
        'recordAllDay' => 'all_day',
        'initialView' => 'week',
        'firstDay' => 1,
    ];
}

/**
 * A controller wired with a toolbar (search) so the search-linking path in makeCalendar()
 * is exercised.
 */
class CalendarSearchController extends Controller
{
    public $implement = [\Backend\Behaviors\CalendarController::class];

    public $calendarConfig = [
        'modelClass' => CalendarEventFixture::class,
        'searchList' => ['columns' => ['name' => ['label' => 'Name', 'searchable' => true]]],
        'toolbar' => [
            'search' => ['prompt' => 'Search events'],
        ],
    ];
}

/**
 * A controller missing the required `modelClass` config key.
 */
class CalendarInvalidController extends Controller
{
    public $implement = [\Backend\Behaviors\CalendarController::class];

    public $calendarConfig = [
        'searchList' => ['columns' => []],
    ];
}

/**
 * Coverage for the CalendarController behavior's config loading and widget wiring.
 */
class CalendarControllerTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        CalendarEventFixture::migrateUp();

        $this->actingAs(new UserFixture);
    }

    public function tearDown(): void
    {
        CalendarEventFixture::migrateDown();

        parent::tearDown();
    }

    public function testMakeCalendarBuildsWidgetBoundToModelAndConfig()
    {
        $controller = new CalendarTestController;
        $widget = $controller->makeCalendar();

        $this->assertInstanceOf(CalendarWidget::class, $widget);
        $this->assertInstanceOf(CalendarEventFixture::class, $widget->model);

        // Config values flow through to the widget.
        $this->assertSame('start_at', $widget->recordStart);
        $this->assertSame('end_at', $widget->recordEnd);
        $this->assertSame('week', $widget->initialView);
        $this->assertSame(1, $widget->firstDay);
    }

    public function testCalendarCreateModelObjectReturnsAFreshModel()
    {
        $controller = new CalendarTestController;

        $first = $controller->calendarCreateModelObject();
        $second = $controller->calendarCreateModelObject();

        $this->assertInstanceOf(CalendarEventFixture::class, $first);
        $this->assertNotSame($first, $second);
    }

    public function testMissingModelClassThrows()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/modelClass/');

        new CalendarInvalidController;
    }

    public function testToolbarSearchWiringDoesNotBreakMakeCalendar()
    {
        $controller = new CalendarSearchController;

        // Exercises initToolbar()'s search widget construction and the search.submit binding.
        $widget = $controller->makeCalendar();

        $this->assertInstanceOf(CalendarWidget::class, $widget);
    }
}
