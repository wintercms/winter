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
                'url'         => 'http://rainlab.tld/blog/posts',
                'icon'        => 'icon-pencil',
                'permissions' => ['rainlab.blog.*'],
                'order'       => 500,

                'sideMenu' => [
                    'posts' => [
                        'label'       => 'Posts',
                        'icon'        => 'icon-copy',
                        'url'         => 'http://rainlab.tld/blog/posts',
                        'permissions' => ['rainlab.blog.access_posts']
                    ],
                    'categories' => [
                        'label'       => 'Categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => 'http://rainlab.tld/blog/categories',
                        'permissions' => ['rainlab.blog.access_categories']
                    ],
                ]
            ]
        ];
    }
}
