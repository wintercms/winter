<?php

namespace Backend\Tests\Fixtures\Models;

use Illuminate\Support\Facades\Schema;
use Winter\Storm\Database\Model;

/**
 * Self-contained model fixture for list selection tests.
 *
 * Owns its own table so the backend test suite has no dependency on any plugin, and carries
 * a searchable column and a column worth filtering on, which is what a selection resolved
 * from the list's active query needs in order to be tested at all.
 */
class ListSelectionFixture extends Model
{
    public $table = 'backend_test_selection_fixtures';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Create the backing table if it does not already exist.
     */
    public static function migrateUp(): void
    {
        if (Schema::hasTable('backend_test_selection_fixtures')) {
            return;
        }

        Schema::create('backend_test_selection_fixtures', function ($table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->integer('sort_order')->nullable();
        });
    }

    /**
     * Drop the backing table.
     */
    public static function migrateDown(): void
    {
        Schema::dropIfExists('backend_test_selection_fixtures');
    }

    /**
     * Seeds `$perCategory` records in each of the two categories, with names that are
     * distinctive enough to search for a single record.
     *
     * @return void
     */
    public static function seed(int $perCategory = 15, array $categories = ['alpha', 'beta']): void
    {
        $rows = [];

        foreach ($categories as $category) {
            for ($i = 1; $i <= $perCategory; $i++) {
                $rows[] = [
                    'name' => sprintf('%s record %03d', $category, $i),
                    'category' => $category,
                    'sort_order' => $i,
                ];
            }
        }

        // Chunked to stay well within SQLite's bound-parameter limit.
        foreach (array_chunk($rows, 100) as $chunk) {
            static::insert($chunk);
        }
    }
}
