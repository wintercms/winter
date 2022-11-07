<?php

namespace Cms\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use Cms\Classes\Controller;
use Cms\Classes\Page;
use Cms\Classes\Theme;

class PageTest extends TestCase
{
    public function testResolveMenuItem()
    {
        $theme = Theme::load('test');
        $controller = new Controller;

        $item = (object) [
            'type' => 'cms-page',
            'reference' => 'index',
        ];

        // Check to make sure that resolved menuItems for the provided URL are considered active
        // with or without a trailing slash
        $url = $controller->pageUrl($item->reference);
        $trailingUrl = $url . '/';

        $result = Page::resolveMenuItem($item, $url, $theme);
        $this->assertTrue($result['isActive']);

        $result = Page::resolveMenuItem($item, $trailingUrl, $theme);
        $this->assertTrue($result['isActive']);
    }
}
