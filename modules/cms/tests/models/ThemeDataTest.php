<?php

namespace Cms\Tests\Models;

use Cms\Classes\Theme;
use Cms\Models\ThemeData;
use Config;
use Event;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\MemoryCache;
use Winter\Storm\Support\Facades\DB;

class ThemeDataTest extends PluginTestCase
{
    /**
     * @var Theme A theme fixture that declares customization fields
     */
    protected $theme;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.activeTheme', 'themedata');
        Config::set('cms.themesPath', '/modules/cms/tests/fixtures/themes');

        Event::flush('cms.theme.getActiveTheme');
        Theme::resetCache();

        $this->theme = Theme::load('themedata');
    }

    public function tearDown(): void
    {
        ThemeData::flushCache('themedata');

        parent::tearDown();
    }

    /**
     * Counts the queries made against the theme data table while running the callback.
     */
    protected function countQueries(callable $callback): int
    {
        // Identical queries are deduplicated in memory for the lifetime of a request, which
        // would otherwise hide the query this cache is meant to avoid on the next request
        MemoryCache::instance()->flush();

        DB::connection()->flushQueryLog();
        DB::connection()->enableQueryLog();

        $callback();

        $queries = DB::connection()->getQueryLog();
        DB::connection()->disableQueryLog();

        return count(array_filter($queries, function ($query) {
            return str_contains($query['query'], 'cms_theme_data');
        }));
    }

    /**
     * Ensures the record exists and the persistent cache is populated, then drops only the
     * in memory cache, simulating the state at the start of a subsequent request.
     */
    protected function primeCache(): void
    {
        ThemeData::forTheme($this->theme);
        ThemeData::flushCache();

        ThemeData::forTheme($this->theme);
        ThemeData::flushCache();
    }

    public function testForThemeIsCachedAcrossRequests()
    {
        $this->primeCache();

        $queries = $this->countQueries(function () {
            ThemeData::forTheme($this->theme);
        });

        $this->assertEquals(0, $queries, 'Theme data should be served from the cache');
    }

    public function testForThemeCreatesRecordOnlyOnce()
    {
        ThemeData::forTheme($this->theme);
        ThemeData::flushCache();
        ThemeData::forTheme($this->theme);
        ThemeData::flushCache();
        ThemeData::forTheme($this->theme);

        $this->assertEquals(1, ThemeData::where('theme', 'themedata')->count());
    }

    public function testDefaultValuesAreAppliedToCachedRecords()
    {
        $this->primeCache();

        // Defaults are applied by afterFetch(), which must still run for cached rows
        $this->assertEquals('Winter', ThemeData::forTheme($this->theme)->site_name);
    }

    public function testAfterSaveInvalidatesCache()
    {
        $this->primeCache();

        $themeData = ThemeData::forTheme($this->theme);
        $themeData->site_name = 'Updated';
        $themeData->save();

        // Only drop the in memory cache; the persistent cache must have been invalidated
        // by afterSave(), otherwise theme customizations would not take effect
        ThemeData::flushCache();

        $this->assertEquals('Updated', ThemeData::forTheme($this->theme)->site_name);
    }

    public function testAfterDeleteInvalidatesCache()
    {
        $this->primeCache();

        $themeData = ThemeData::forTheme($this->theme);
        $themeData->site_name = 'Updated';
        $themeData->save();

        $this->theme->removeCustomData();
        ThemeData::flushCache();

        // A new record should be created rather than the deleted one being served
        $this->assertNull(ThemeData::forTheme($this->theme)->site_name);
        $this->assertEquals(1, ThemeData::where('theme', 'themedata')->count());
    }

    public function testDynamicAttributesSurviveTheCache()
    {
        $themeData = ThemeData::forTheme($this->theme);
        $themeData->site_name = 'From cache';
        $themeData->save();

        ThemeData::flushCache();
        ThemeData::forTheme($this->theme);
        ThemeData::flushCache();

        $queries = $this->countQueries(function () {
            // Dynamic attributes live in the jsonable `data` column and are expanded by
            // afterFetch(), which runs during hydration rather than during the query
            $this->assertEquals('From cache', ThemeData::forTheme($this->theme)->site_name);
        });

        $this->assertEquals(0, $queries);
    }
}
