<?php namespace Database\Tester\Models;

use Model;

/**
 * Role Model
 */
class Role extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'database_tester_roles';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        'authors' => [
            'Database\Tester\Models\User',
            'table' => 'database_tester_authors_roles'
        ],
    ];

    /**
     * Restricts the query to the roles the supplied owner may be given, standing in for the
     * per-tenant query scope a deployment points a relation's `manage.scope` option at.
     */
    public function scopeAssignableTo($query, $owner)
    {
        return $query->where('description', 'tenant-' . $owner->getKey());
    }
}
