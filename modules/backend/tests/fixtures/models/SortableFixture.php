<?php

namespace Backend\Tests\Fixtures\Models;

use Illuminate\Support\Facades\Schema;
use Winter\Storm\Database\Model;
use Winter\Storm\Database\Traits\Sortable;

/**
 * Self-contained Sortable model fixture for list reordering tests.
 *
 * Owns its own table so the backend test suite has no dependency on any plugin.
 */
class SortableFixture extends Model
{
    use Sortable;

    public $table = 'backend_test_sortable_fixtures';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Create the backing table if it does not already exist.
     */
    public static function migrateUp(): void
    {
        if (Schema::hasTable('backend_test_sortable_fixtures')) {
            return;
        }

        Schema::create('backend_test_sortable_fixtures', function ($table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('label')->nullable();
            $table->integer('sort_order')->nullable();
        });
    }

    /**
     * Drop the backing table.
     */
    public static function migrateDown(): void
    {
        Schema::dropIfExists('backend_test_sortable_fixtures');
    }
}
