<?php

namespace Backend\Tests\Fixtures\Models;

use Illuminate\Support\Facades\Schema;
use System\Models\File;
use Winter\Storm\Database\Model;
use Winter\Storm\Database\Traits\HasSortableRelations;

/**
 * Self-contained model fixture for the relation behavior tests: a sortable hasMany relation to
 * itself, plus a file attachment so the relation's forms carry a nested form widget with AJAX
 * handlers of its own.
 *
 * Owns its own table so the backend test suite has no dependency on any plugin.
 */
class RelationBehaviorFixture extends Model
{
    use HasSortableRelations;

    public $table = 'backend_test_relation_behavior_fixtures';

    protected $guarded = [];

    public $timestamps = false;

    public $sortableRelations = ['children' => 'sort_order'];

    public $hasMany = [
        'children' => [self::class, 'key' => 'parent_id', 'order' => 'sort_order asc'],
    ];

    public $hasOne = [
        'primaryChild' => [self::class, 'key' => 'parent_id'],
    ];

    public $attachOne = [
        'thumb' => File::class,
    ];

    /**
     * Create the backing table if it does not already exist.
     */
    public static function migrateUp(): void
    {
        if (Schema::hasTable('backend_test_relation_behavior_fixtures')) {
            return;
        }

        Schema::create('backend_test_relation_behavior_fixtures', function ($table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('sort_order')->nullable();
        });
    }

    /**
     * Drop the backing table.
     */
    public static function migrateDown(): void
    {
        Schema::dropIfExists('backend_test_relation_behavior_fixtures');
    }
}
