<?php

use Cms\Classes\Controller;
use Cms\Classes\Page;
use Cms\Classes\Theme;

class PageTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

    }

    public function testResolveMenuItem()
    {
        $themeName = 'test';

        $theme = Theme::load($themeName);
        $controller = new Controller;

        $item = (object) [
            'type' => 'cms-page',
            'reference' => 'index',
        ];

        // fake current URL to be the same as menu item reference page with extra "/" at the end.
        $url = $controller->pageUrl($item->reference) . '/';

        $result = Page::resolveMenuItem($item, $url, $theme);

        $this->assertTrue($result['isActive']);
    }
}
