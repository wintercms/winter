<?php

namespace Winter\Tester\Components;

use Cms\Classes\CodeBase;
use Cms\Classes\ComponentBase;

class Categories extends ComponentBase
{
    public function __construct(CodeBase $cmsObject = null, $properties = [])
    {
        parent::__construct($cmsObject, $properties);
    }

    public function componentDetails()
    {
        return [
            'name' => 'Blog Categories Dummy Component',
            'description' => 'Displays the list of categories in the blog.',
        ];
    }

    public function posts()
    {
        return [
            ['title' => 'Lorum ipsum', 'content' => 'Post Content #1'],
            ['title' => 'La Playa Nudista', 'content' => 'Second Post Content'],
        ];
    }

    public function onTestAjax()
    {
        $this->page['var'] = 'page';
    }
}
