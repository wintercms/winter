<?php

namespace Backend\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use Backend\Classes\NavigationManager;

class NavigationManagerTest extends TestCase
{
    public function testRegisterMenuItems()
    {
        $manager = NavigationManager::instance();
        $items = $manager->listMainMenuItems();
        $this->assertArrayNotHasKey('WINTER.TEST.DASHBOARD', $items);

        $manager->registerMenuItems('Winter.Test', [
            'dashboard' => [
                'label' => 'Dashboard',
                'icon' => 'icon-dashboard',
                'url' => 'http://example.com',
                'order' => 100
            ]
        ]);

        $items = $manager->listMainMenuItems();
        $this->assertArrayHasKey('WINTER.TEST.DASHBOARD', $items);

        $item = $items['WINTER.TEST.DASHBOARD'];
        $this->assertObjectHasProperty('code', $item);
        $this->assertObjectHasProperty('label', $item);
        $this->assertObjectHasProperty('icon', $item);
        $this->assertObjectHasProperty('url', $item);
        $this->assertObjectHasProperty('owner', $item);
        $this->assertObjectHasProperty('order', $item);
        $this->assertObjectHasProperty('permissions', $item);
        $this->assertObjectHasProperty('sideMenu', $item);

        $this->assertEquals('dashboard', $item->code);
        $this->assertEquals('Dashboard', $item->label);
        $this->assertEquals('icon-dashboard', $item->icon);
        $this->assertEquals('http://example.com', $item->url);
        $this->assertEquals(100, $item->order);
        $this->assertEquals('Winter.Test', $item->owner);
    }

    public function testListMainMenuItems()
    {
        $manager = NavigationManager::instance();
        $items = $manager->listMainMenuItems();

        $this->assertArrayHasKey('WINTER.TESTER.BLOG', $items);
    }

    public function testListSideMenuItems()
    {
        $manager = NavigationManager::instance();

        $items = $manager->listSideMenuItems();
        $this->assertEmpty($items);

        $manager->setContext('Winter.Tester', 'blog');

        $items = $manager->listSideMenuItems();
        $this->assertIsArray($items);
        $this->assertArrayHasKey('posts', $items);
        $this->assertArrayHasKey('categories', $items);

        $this->assertIsObject($items['posts']);
        $this->assertObjectHasProperty('code', $items['posts']);
        $this->assertObjectHasProperty('owner', $items['posts']);
        $this->assertEquals('posts', $items['posts']->code);
        $this->assertEquals('Winter.Tester', $items['posts']->owner);

        $this->assertObjectHasProperty('permissions', $items['posts']);
        $this->assertIsArray($items['posts']->permissions);
        $this->assertCount(1, $items['posts']->permissions);

        $this->assertObjectHasProperty('order', $items['posts']);
        $this->assertObjectHasProperty('order', $items['categories']);
        $this->assertEquals(100, $items['posts']->order);
        $this->assertEquals(200, $items['categories']->order);
    }

    public function testAddMainMenuItems()
    {
        $manager = NavigationManager::instance();
        $manager->addMainMenuItems('Winter.Tester', [
            'print' => [
                'label' => 'Print',
                'icon' => 'icon-print',
                'url' => 'javascript:window.print()'
            ]
        ]);

        $items = $manager->listMainMenuItems();

        $this->assertIsArray($items);
        $this->assertArrayHasKey('WINTER.TESTER.PRINT', $items);

        $item = $items['WINTER.TESTER.PRINT'];
        $this->assertEquals('print', $item->code);
        $this->assertEquals('Print', $item->label);
        $this->assertEquals('icon-print', $item->icon);
        $this->assertEquals('javascript:window.print()', $item->url);
        $this->assertEquals(500, $item->order);
        $this->assertEquals('Winter.Tester', $item->owner);
    }

    public function testAddMainMenuItemsWithAlias()
    {
        $manager = NavigationManager::instance();
        $manager->addMainMenuItems('Winter.Tester', [
            'print' => [
                'label' => 'Print',
                'icon' => 'icon-print',
                'url' => 'javascript:window.print()'
            ]
        ]);

        $manager->registerOwnerAlias('Winter.Tester', 'Alias.Tester');

        $item = $manager->getMainMenuItem('Alias.Tester', 'print');

        $this->assertEquals('print', $item->code);
        $this->assertEquals('Print', $item->label);
        $this->assertEquals('icon-print', $item->icon);
        $this->assertEquals('javascript:window.print()', $item->url);
        $this->assertEquals(500, $item->order);
        $this->assertEquals('Winter.Tester', $item->owner);
    }

    public function testRemoveMainMenuItem()
    {
        $manager = NavigationManager::instance();
        $manager->addMainMenuItems('Winter.Tester', [
            'close' => [
                'label' => 'Close',
                'icon' => 'icon-times',
                'url' => 'javascript:window.close()'
            ]
        ]);

        $items = $manager->listMainMenuItems();
        $this->assertArrayHasKey('WINTER.TESTER.CLOSE', $items);

        $manager->removeMainMenuItem('Winter.Tester', 'close');

        $items = $manager->listMainMenuItems();
        $this->assertArrayNotHasKey('WINTER.TESTER.CLOSE', $items);
    }

    public function testRemoveMainMenuItemByAlias()
    {
        $manager = NavigationManager::instance();
        $manager->addMainMenuItems('Winter.Tester', [
            'close' => [
                'label' => 'Close',
                'icon' => 'icon-times',
                'url' => 'javascript:window.close()'
            ]
        ]);

        $manager->registerOwnerAlias('Winter.Tester', 'Alias.Tester');

        $items = $manager->listMainMenuItems();
        $this->assertArrayHasKey('WINTER.TESTER.CLOSE', $items);

        $manager->removeMainMenuItem('Alias.Tester', 'close');

        $items = $manager->listMainMenuItems();
        $this->assertArrayNotHasKey('WINTER.TESTER.CLOSE', $items);
    }

    public function testAddSideMenuItems()
    {
        $manager = NavigationManager::instance();
        $manager->listMainMenuItems();

        $manager->addSideMenuItems('Winter.Tester', 'blog', [
            'foo' => [
                'label' => 'Bar',
                'icon' => 'icon-derp',
                'url' => 'http://google.com',
                'permissions' => [
                    'winter.tester.access_foo',
                    'winter.tester.access_bar'
                ]
            ]
        ]);

        $manager->setContext('Winter.Tester', 'blog');
        $items = $manager->listSideMenuItems();

        $this->assertIsArray($items);
        $this->assertArrayHasKey('foo', $items);

        $this->assertIsObject($items['foo']);
        $this->assertObjectHasProperty('code', $items['foo']);
        $this->assertObjectHasProperty('owner', $items['foo']);
        $this->assertObjectHasProperty('order', $items['foo']);

        $this->assertEquals(-1, $items['foo']->order);
        $this->assertEquals('foo', $items['foo']->code);
        $this->assertEquals('Winter.Tester', $items['foo']->owner);

        $this->assertObjectHasProperty('permissions', $items['foo']);
        $this->assertIsArray($items['foo']->permissions);
        $this->assertCount(2, $items['foo']->permissions);
        $this->assertContains('winter.tester.access_foo', $items['foo']->permissions);
        $this->assertContains('winter.tester.access_bar', $items['foo']->permissions);
    }

    public function testAddSideMenuItemsWithAlias()
    {
        $manager = NavigationManager::instance();
        $manager->listMainMenuItems();

        $manager->addSideMenuItems('Winter.Tester', 'blog', [
            'foo' => [
                'label' => 'Bar',
                'icon' => 'icon-derp',
                'url' => 'http://google.com',
                'permissions' => [
                    'winter.tester.access_foo',
                    'winter.tester.access_bar'
                ]
            ]
        ]);

        $manager->registerOwnerAlias('Winter.Tester', 'Alias.Tester');
        $manager->setContext('Alias.Tester', 'blog');

        $items = $manager->listSideMenuItems();

        $this->assertTrue(is_array($items));
        $this->assertArrayHasKey('foo', $items);

        $this->assertTrue(is_object($items['foo']));
        $this->assertObjectHasProperty('code', $items['foo']);
        $this->assertObjectHasProperty('owner', $items['foo']);
        $this->assertObjectHasProperty('order', $items['foo']);

        $this->assertEquals(-1, $items['foo']->order);
        $this->assertEquals('foo', $items['foo']->code);
        $this->assertEquals('Winter.Tester', $items['foo']->owner);

        $this->assertObjectHasProperty('permissions', $items['foo']);
        $this->assertTrue(is_array($items['foo']->permissions));
        $this->assertCount(2, $items['foo']->permissions);
        $this->assertContains('winter.tester.access_foo', $items['foo']->permissions);
        $this->assertContains('winter.tester.access_bar', $items['foo']->permissions);
    }

    public function testRemoveSideMenuItem()
    {
        $manager = NavigationManager::instance();
        $manager->listMainMenuItems();

        $manager->addSideMenuItems('Winter.Tester', 'blog', [
            'bar' => [
                'label' => 'Bar',
                'icon' => 'icon-bars',
                'url' => 'http://yahoo.com'
            ]
        ]);

        $manager->setContext('Winter.Tester', 'blog');

        $items = $manager->listSideMenuItems();
        $this->assertArrayHasKey('bar', $items);

        $manager->removeSideMenuItem('Winter.Tester', 'blog', 'bar');

        $items = $manager->listSideMenuItems();
        $this->assertArrayNotHasKey('bar', $items);
    }

    public function testRemoveSideMenuItemByAlias()
    {
        $manager = NavigationManager::instance();
        $manager->listMainMenuItems();

        $manager->addSideMenuItems('Winter.Tester', 'blog', [
            'bar' => [
                'label' => 'Bar',
                'icon' => 'icon-bars',
                'url' => 'http://yahoo.com'
            ]
        ]);

        $manager->registerOwnerAlias('Winter.Tester', 'Alias.Tester');
        $manager->setContext('Alias.Tester', 'blog');

        $items = $manager->listSideMenuItems();
        $this->assertArrayHasKey('bar', $items);

        $manager->removeSideMenuItem('Alias.Tester', 'blog', 'bar');

        $items = $manager->listSideMenuItems();
        $this->assertArrayNotHasKey('bar', $items);
    }
}
