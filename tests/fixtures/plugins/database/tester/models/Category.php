<?php namespace Database\Tester\Models;

use Model;

class Category extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'database_tester_categories';

    public $belongsToMany = [
        'posts' => [
            'Database\Tester\Models\Post',
            'table' => 'database_tester_categories_posts',
            'pivot' => ['category_name', 'post_name']
        ]
    ];

    public function getCustomNameAttribute()
    {
        return $this->name.' (#'.$this->id.')';
    }
}


class CategorySimple extends Category
{
    use \Winter\Storm\Database\Traits\SimpleTree;
}

class CategoryNested extends Category
{
    use \Winter\Storm\Database\Traits\NestedTree;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'database_tester_categories_nested';
}
