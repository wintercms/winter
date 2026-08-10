<?php

namespace Backend\Tests\Fixtures\Models;

use Backend\Models\User;
use Illuminate\Support\Facades\Schema;
use Winter\Storm\Database\Model;

/**
 * Self-contained model fixture holding a belongsToMany relation to Backend\Models\User
 * with editable pivot data.
 *
 * Owns its own tables so the backend test suite has no dependency on any plugin.
 */
class PivotRelationFixture extends Model
{
    public $table = 'backend_test_pivot_relation_fixtures';

    protected $guarded = [];

    public $timestamps = false;

    public $belongsToMany = [
        'users' => [
            User::class,
            'table' => 'backend_test_pivot_relation_users',
            'key' => 'fixture_id',
            'otherKey' => 'user_id',
            'pivot' => ['is_default'],
        ],
    ];

    /**
     * Create the backing tables if they do not already exist.
     */
    public static function migrateUp(): void
    {
        if (!Schema::hasTable('backend_test_pivot_relation_fixtures')) {
            Schema::create('backend_test_pivot_relation_fixtures', function ($table) {
                $table->increments('id');
                $table->string('name')->nullable();
            });
        }

        if (!Schema::hasTable('backend_test_pivot_relation_users')) {
            Schema::create('backend_test_pivot_relation_users', function ($table) {
                $table->integer('fixture_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->boolean('is_default')->default(false);
                $table->primary(['fixture_id', 'user_id']);
            });
        }
    }

    /**
     * Drop the backing tables.
     */
    public static function migrateDown(): void
    {
        Schema::dropIfExists('backend_test_pivot_relation_users');
        Schema::dropIfExists('backend_test_pivot_relation_fixtures');
    }
}
