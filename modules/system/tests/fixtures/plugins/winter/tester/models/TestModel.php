<?php namespace Winter\Tester\Models;

use Model;

class TestModel extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'winter_tester_test_model';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => TestUser::class
    ];

    public $hasOne = [
        'phone' => TestPhone::class
    ];

    public $morphTo = [
        'taggable' => []
    ];

    public $rules = [
        'uint' => 'required',
        'range' => 'required|integer|between:1,10',
    ];
}

class TestUser extends Model
{
}

class TestPhone extends Model
{
}
