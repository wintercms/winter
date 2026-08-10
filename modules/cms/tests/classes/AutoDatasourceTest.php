<?php

namespace Cms\Tests\Classes;

use Exception;
use System\Tests\Bootstrap\PluginTestCase;
use Cms\Classes\AutoDatasource;
use Winter\Storm\Database\MemoryCache;
use Winter\Storm\Database\Model;
use Winter\Storm\Halcyon\Datasource\DbDatasource;
use Winter\Storm\Halcyon\Datasource\FileDatasource;
use Winter\Storm\Support\Facades\DB;

class CmsThemeTemplateFixture extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public $table = 'cms_theme_templates';
}

class AutoDatasourceTest extends PluginTestCase
{
    /**
     * Array of model fixtures.
     *
     * @var array
     */
    public $fixtures = [];

    /**
     * AutoDatasource object.
     *
     * @var Cms\Classes\AutoDatasource;
     */
    public $datasource;

    public function setUp(): void
    {
        parent::setUp();

        $this->fixtures = [];

        // Create fixtures of template data
        $this->fixtures[] = CmsThemeTemplateFixture::create([
            'source' => 'test',
            'path' => 'partials/page-partial.htm',
            'content' => 'AutoDatasource partials/page-partial.htm',
            'file_size' => 40
        ]);

        $this->fixtures[] = CmsThemeTemplateFixture::create([
            'source' => 'test',
            'path' => 'partials/testpost/default.htm',
            'content' => 'AutoDatasource partials/testpost/default.htm',
            'file_size' => 44
        ]);

        $this->fixtures[] = CmsThemeTemplateFixture::create([
            'source' => 'test',
            'path' => 'partials/subdir/test.htm',
            'content' => 'AutoDatasource partials/subdir/test.htm',
            'file_size' => 39,
            'updated_at' => '2019-06-01 12:00:00'
        ]);

        $this->fixtures[] = CmsThemeTemplateFixture::create([
            'source' => 'test',
            'path' => 'partials/nesting/level2.htm',
            'content' => 'AutoDatasource partials/nesting/level2.htm',
            'file_size' => 42,
            'deleted_at' => '2019-01-01 00:00:00'
        ]);

        // Create AutoDatasource
        $this->datasource = new AutoDatasource([
            'database' => new DbDatasource('test', 'cms_theme_templates'),
            'filesystem' => new FileDatasource(
                base_path('modules/system/tests/fixtures/themes/test'),
                \App::make('files')
            ),
        ]);
    }

    public function tearDown(): void
    {
        foreach ($this->fixtures as $fixture) {
            $fixture->delete();
        }

        parent::tearDown();
    }

    public function testSelect()
    {
        $results = collect($this->datasource->select('partials'))
            ->keyBy('fileName')
            ->toArray();

        // Should be 14 partials in filesystem (tests/fixtures/themes/test), and 1 created directly in database.
        // 1 of the filesystem partials should be marked deleted in database.
        $this->assertCount(14, $results);

        // Database-only partial should be available
        $this->assertArrayHasKey('subdir/test.htm', $results);
        $this->assertEquals(
            'AutoDatasource partials/subdir/test.htm',
            $results['subdir/test.htm']['content']
        );

        // Two filesystem partials should be overriden by database
        $this->assertEquals(
            'AutoDatasource partials/page-partial.htm',
            $results['page-partial.htm']['content']
        );
        $this->assertEquals(
            'AutoDatasource partials/testpost/default.htm',
            $results['testpost/default.htm']['content']
        );

        // One filesystem partial should be marked deleted in database
        $this->assertArrayNotHasKey('nesting/level2.htm', $results);
    }

    public function testPathCacheValueShapes()
    {
        $pathCache = self::getProtectedProperty($this->datasource, 'pathCache');

        // Database records report their last modified time, deleted records report false
        $this->assertIsInt($pathCache[0]['partials/subdir/test.htm']);
        $this->assertEquals(
            strtotime('2019-06-01 12:00:00'),
            $pathCache[0]['partials/subdir/test.htm']
        );
        $this->assertFalse($pathCache[0]['partials/nesting/level2.htm']);

        // Filesystem records continue to report true so that their mtime is resolved live
        $this->assertTrue($pathCache[1]['partials/layout-partial.htm']);
    }

    public function testLastModifiedIsServedFromPathCacheWithoutQuerying()
    {
        // The duplicate query cache would otherwise mask a query issued by this call
        MemoryCache::instance()->flush();

        DB::connection()->flushQueryLog();
        DB::connection()->enableQueryLog();

        $mtime = $this->datasource->lastModified('partials', 'subdir/test', 'htm');

        $queries = DB::connection()->getQueryLog();
        DB::connection()->disableQueryLog();

        $this->assertEquals(strtotime('2019-06-01 12:00:00'), $mtime);
        $this->assertCount(0, $queries, 'lastModified() should not query the database');
    }

    public function testLastModifiedFallsBackToFilesystemDatasource()
    {
        $path = base_path('modules/system/tests/fixtures/themes/test/partials/layout-partial.htm');

        $this->assertEquals(
            filemtime($path),
            $this->datasource->lastModified('partials', 'layout-partial', 'htm')
        );
    }

    public function testLastModifiedIsStableForNullUpdatedAt()
    {
        $this->fixtures[] = CmsThemeTemplateFixture::create([
            'source' => 'test',
            'path' => 'partials/no-timestamp.htm',
            'content' => 'AutoDatasource partials/no-timestamp.htm',
            'file_size' => 40,
            'updated_at' => null,
        ]);

        $this->datasource->populateCache(true);

        $pathCache = self::getProtectedProperty($this->datasource, 'pathCache');
        $cached = $pathCache[0]['partials/no-timestamp.htm'];

        // updated_at is nullable, and Carbon::parse(null) resolves to "now". The value is
        // resolved once, when the path cache is built, so lastModified() reports it
        // consistently rather than returning a different result on every call.
        // Note this does not make the Halcyon cache usable for such records: selectOne()
        // still resolves their mtime live, so the two disagree and the cache is busted on
        // every request. That is pre-existing and not addressed here.
        $this->assertIsInt($cached);
        $this->assertEquals($cached, $this->datasource->lastModified('partials', 'no-timestamp', 'htm'));
    }

    public function testRecordsWithAnEpochTimestampRemainAvailable()
    {
        $this->fixtures[] = CmsThemeTemplateFixture::create([
            'source' => 'test',
            'path' => 'partials/epoch.htm',
            'content' => 'AutoDatasource partials/epoch.htm',
            'file_size' => 33,
            'updated_at' => '1970-01-01 00:00:00',
        ]);

        $this->datasource->populateCache(true);

        // The path cache carries modification times, but every consumer of it tests the
        // value for truthiness. A timestamp of 0 must therefore not be stored as-is, or
        // this live record would read as deleted and disappear.
        $listed = collect($this->datasource->select('partials', ['columns' => ['fileName']]))
            ->pluck('fileName')
            ->all();

        $this->assertContains('epoch.htm', $listed);
        $this->assertSame(
            'AutoDatasource partials/epoch.htm',
            $this->datasource->selectOne('partials', 'epoch', 'htm')['content']
        );
    }

    public function testLastModifiedThrowsForDeletedPath()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('partials/nesting/level2.htm is deleted');

        $this->datasource->lastModified('partials', 'nesting/level2', 'htm');
    }

    public function testLastModifiedReflectsUpdatesMadeThroughTheDatasource()
    {
        $before = $this->datasource->lastModified('partials', 'subdir/test', 'htm');

        $this->datasource->update('partials', 'subdir/test', 'htm', 'Updated content');

        $after = $this->datasource->lastModified('partials', 'subdir/test', 'htm');

        // Editing a template must take effect immediately, without clearing the cache
        $this->assertGreaterThan($before, $after);
        $this->assertEquals(
            'Updated content',
            $this->datasource->selectOne('partials', 'subdir/test', 'htm')['content']
        );
    }
}
