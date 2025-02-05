<?php

namespace Cms\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use Cms\Classes\Theme;
use Config;
use Event;

class ThemeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.activeTheme', 'test');
        Config::set('cms.themesPath', '/modules/cms/tests/fixtures/themes');

        Event::flush('cms.theme.getActiveTheme');
        Theme::resetCache();
    }

    protected function countThemePages($path)
    {
        $result = 0;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $it->setMaxDepth(1);
        $it->rewind();

        while ($it->valid()) {
            if (!$it->isDot() && !$it->isDir() && $it->getExtension() == 'htm') {
                $result++;
            }

            $it->next();
        }

        return $result;
    }

    public function testGetPath()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestIncomplete('Need to fix Windows testing here');
        }

        $theme = Theme::load('test');

        $this->assertEquals(base_path('modules/cms/tests/fixtures/themes/test'), $theme->getPath());
    }

    public function testListPages()
    {
        $theme = Theme::load('test');

        $pageCollection = $theme->listPages();
        $pages = array_values($pageCollection->all());
        $this->assertIsArray($pages);

        $expectedPageNum = $this->countThemePages(base_path() . '/modules/cms/tests/fixtures/themes/test/pages');
        $this->assertCount($expectedPageNum, $pages);

        $this->assertInstanceOf('\Cms\Classes\Page', $pages[0]);
        $this->assertNotEmpty($pages[0]->url);
        $this->assertInstanceOf('\Cms\Classes\Page', $pages[1]);
        $this->assertNotEmpty($pages[1]->url);
    }

    public function testGetActiveTheme()
    {
        $activeTheme = Theme::getActiveTheme();

        $this->assertNotNull($activeTheme);
        $this->assertEquals('test', $activeTheme->getDirName());
    }

    public function testNoActiveTheme()
    {
        $this->expectException(\Winter\Storm\Exception\SystemException::class);
        $this->expectExceptionMessage('The active theme is not set.');

        Config::set('cms.activeTheme', null);
        Theme::getActiveTheme();
    }

    public function testApiTheme()
    {
        Event::flush('cms.theme.getActiveTheme');
        Event::listen('cms.theme.getActiveTheme', function () {
            return 'apitest';
        });

        $activeTheme = Theme::getActiveTheme();
        $this->assertNotNull($activeTheme);
        $this->assertEquals('apitest', $activeTheme->getDirName());
    }

    public function testChildThemeConfig()
    {
        Config::set('cms.activeTheme', 'childtest');

        $theme = Theme::getActiveTheme();
        $config = $theme->getConfig();

        $this->assertArrayHasKey('parent', $config);
        $this->assertEquals('test', $config['parent']);
    }

    public function testChildThemeAssetUrl()
    {
        Config::set('cms.activeTheme', 'childtest');

        $theme = Theme::getActiveTheme();

        $this->assertStringContainsString(
            'modules/cms/tests/fixtures/themes/test/assets/css/style1.css',
            $theme->assetUrl('assets/css/style1.css')
        );

        $this->assertStringContainsString(
            'modules/cms/tests/fixtures/themes/childtest/assets/css/style2.css',
            $theme->assetUrl('assets/css/style2.css')
        );
    }
}
