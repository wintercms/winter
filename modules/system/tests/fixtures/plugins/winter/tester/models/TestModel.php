<?php namespace Winter\Tester\Models;

use Model;

class TestModel extends Model
{
    use \Winter\Storm\Database\Traits\Sortable;

    public $table = 'winter_tester_test_model';

    public $jsonable = [
        'data',
    ];

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
