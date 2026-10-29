<?php

namespace Backend\Tests\Fixtures\Models;

use Illuminate\Support\Facades\Schema;
use Winter\Storm\Database\Model;

/**
 * Self-contained event model fixture for Calendar widget tests.
 *
 * Owns its own table so the backend test suite has no dependency on any plugin. The columns
 * mirror the Calendar widget's default record mapping (recordStart/recordEnd/recordAllDay/…),
 * plus an `rrule` column used to exercise the recurrence pre-filter behaviour.
 */
class CalendarEventFixture extends Model
{
    public $table = 'backend_test_calendar_events';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Store the datetime columns as plain strings so the widget/EventData receive the exact
     * value that was written, keeping the date-range assertions deterministic.
     */
    protected $casts = [
        'all_day' => 'boolean',
    ];

    /**
     * Create the backing table if it does not already exist.
     */
    public static function migrateUp(): void
    {
        if (Schema::hasTable('backend_test_calendar_events')) {
            return;
        }

        Schema::create('backend_test_calendar_events', function ($table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->boolean('all_day')->default(false);
            $table->string('color')->nullable();
            $table->string('rrule')->nullable();
        });
    }

    /**
     * Drop the backing table.
     */
    public static function migrateDown(): void
    {
        Schema::dropIfExists('backend_test_calendar_events');
    }
}
