<?php namespace Winter\Tester;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Winter Test Plugin',
            'description' => 'Test plugin used by unit tests.',
            'author' => 'Alexey Bobkov, Samuel Georges'
        ];
    }

    public function registerComponents()
    {
        return [
            'Winter\Tester\Components\Archive' => 'testArchive',
            'Winter\Tester\Components\Post' => 'testPost',
            'Winter\Tester\Components\MainMenu' => 'testMainMenu',
            'Winter\Tester\Components\ContentBlock' => 'testContentBlock',
            'Winter\Tester\Components\Comments' => 'testComments',
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'Winter\Tester\FormWidgets\Preview' => [
                'label' => 'Preview',
                'code'  => 'preview'
            ]
        ];
    }

    public function registerNavigation()
    {
        return [
            'blog' => [
                'label'       => 'Blog',
                'url'         => 'http://example.com/blog/posts',
                'icon'        => 'icon-pencil',
                'permissions' => ['winter.blog.*'],
                'order'       => 500,

                'sideMenu' => [
                    'posts' => [
                        'label'       => 'Posts',
                        'icon'        => 'icon-copy',
                        'url'         => 'http://example.com/blog/posts',
                        'permissions' => ['winter.blog.access_posts']
                    ],
                    'categories' => [
                        'label'       => 'Categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => 'http://example.com/blog/categories',
                        'permissions' => ['winter.blog.access_categories']
                    ],
                ]
            ]
        ];
    }

    public function registerValidationRules()
    {
        return [
            'uppercase' => function ($attribute, $value, $parameters, $validator) {
                return strtoupper($value) === $value;
            },
            'be_like_bob' => \Winter\Tester\Rules\BeLikeBobRule::class,
        ];
    }
}
