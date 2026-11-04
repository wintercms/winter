<?php

namespace Backend\Tests\Widgets;

use ApplicationException;
use Backend\Classes\Controller as BackendController;
use Backend\Widgets\MediaManager;
use Config;
use File;
use Request;
use System\Classes\ImageResizer;
use System\Tests\Bootstrap\PluginTestCase;
use System\Tests\Concerns\MigratesDatabaseOnce;
use Winter\Storm\Exception\SystemException;

/**
 * Covers the media path handling of the Media Manager's thumbnail and crop source handlers.
 *
 * Both handlers take a path from the request and hand it to the image resizer, so the path
 * has to be validated by the handler and confined to its source folder by the resizer in
 * the same way that the neighbouring handlers of this widget already do.
 */
class MediaManagerThumbnailPathTest extends PluginTestCase
{
    use MigratesDatabaseOnce;

    public function setUp(): void
    {
        parent::setUp();

        $this->app->useStoragePath(base_path('storage/temp'));

        Config::set('filesystems.disks.test_local', [
            'driver' => 'local',
            'root'   => storage_path('app'),
            'url'    => '/storage/temp/app',
        ]);
        Config::set('cms.storage.media', [
            'disk'   => 'test_local',
            'folder' => 'media',
            'path'   => '/storage/temp/app/media',
        ]);
        Config::set('cms.storage.resized', [
            'disk'   => 'test_local',
            'folder' => 'resized',
            'path'   => '/storage/temp/app/resized',
        ]);

        ImageResizer::flushAvailableSources();

        File::makeDirectory(storage_path('app/media/nested folder'), 0777, true, true);
        File::makeDirectory(storage_path('app/resized'), 0777, true, true);
        File::makeDirectory(storage_path('app/uploads/protected/abc'), 0777, true, true);

        foreach ([
            'winter.png',
            'winter space.png',
            'wîntér ünicode.png',
            'nested folder/winter.png',
        ] as $mediaItem) {
            File::copy(
                base_path('modules/system/tests/fixtures/media/winter.png'),
                storage_path('app/media/' . $mediaItem)
            );
        }

        File::copy(
            base_path('modules/system/tests/fixtures/media/winter.png'),
            storage_path('app/uploads/protected/abc/hidden.png')
        );
    }

    public function tearDown(): void
    {
        File::deleteDirectory(storage_path('app/media'));
        File::deleteDirectory(storage_path('app/uploads'));
        File::deleteDirectory(storage_path('app/resized'));

        ImageResizer::flushAvailableSources();

        parent::tearDown();
    }

    /**
     * @dataProvider refusedThumbnailPathProvider
     */
    public function testGenerateThumbnailsRefusesPathsOutsideTheMediaLibrary(string $path)
    {
        Request::merge(['batch' => [$this->thumbnailRequest($path)]]);

        $result = $this->makeManager()->onGenerateThumbnails();

        $this->assertCount(1, $result['generatedThumbnails']);
        $this->assertBrokenThumbnail($result['generatedThumbnails'][0]['markup']);
    }

    /**
     * Reported to the caller rather than rendered broken: a single path has no batch to spare.
     *
     * @dataProvider refusedThumbnailPathProvider
     */
    public function testTheSidebarPreviewRefusesPathsOutsideTheMediaLibrary(string $path)
    {
        Request::merge(['path' => $path, 'lastModified' => 1]);

        $this->expectException(ApplicationException::class);

        $this->makeManager()->onGetSidebarThumbnail();
    }

    /**
     * The browser requests thumbnails in batches, so one unusable path in a batch must not
     * cost the other items in the same batch their thumbnails.
     */
    public function testOneRefusedPathInABatchStillThumbnailsTheRestOfTheBatch()
    {
        Request::merge(['batch' => [
            $this->thumbnailRequest('/winter.png'),
            $this->thumbnailRequest('/../uploads/protected/abc/hidden.png'),
            $this->thumbnailRequest('/nested folder/winter.png'),
        ]]);

        $result = $this->makeManager()->onGenerateThumbnails();

        $this->assertCount(3, $result['generatedThumbnails']);
        $this->assertResizerThumbnail($result['generatedThumbnails'][0]['markup']);
        $this->assertBrokenThumbnail($result['generatedThumbnails'][1]['markup']);
        $this->assertResizerThumbnail($result['generatedThumbnails'][2]['markup']);
    }

    /**
     * Such an item is listed by the library but cannot be renamed, moved, previewed or
     * cropped either, so it gets the broken thumbnail rather than costing the batch.
     */
    public function testAnItemOutsideTheLibraryPathRulesOnlyLosesItsOwnThumbnail()
    {
        File::copy(
            base_path('modules/system/tests/fixtures/media/winter.png'),
            storage_path('app/media/winter+plus.png')
        );

        Request::merge(['batch' => [
            $this->thumbnailRequest('/winter+plus.png'),
            $this->thumbnailRequest('/winter.png'),
        ]]);

        $result = $this->makeManager()->onGenerateThumbnails();

        $this->assertCount(2, $result['generatedThumbnails']);
        $this->assertBrokenThumbnail($result['generatedThumbnails'][0]['markup']);
        $this->assertResizerThumbnail($result['generatedThumbnails'][1]['markup']);
    }

    public static function refusedThumbnailPathProvider(): array
    {
        return [
            'relative segment' => ['/../uploads/protected/abc/hidden.png'],
            'nested relative segments' => ['/nested folder/../../uploads/protected/abc/hidden.png'],
            'relative segment at the end' => ['/nested folder/..'],
            'backslash separators' => ['\\..\\uploads\\protected\\abc\\hidden.png'],
            'dot and relative segments' => ['/./../uploads/protected/abc/hidden.png'],
            'absolute url' => ['https://localhost/storage/temp/app/uploads/protected/abc/hidden.png'],
            // The vector branch of the thumbnail markup does not use the resizer at all, so
            // it has to be covered separately from the raster cases above
            'vector file' => ['/../uploads/protected/abc/hidden.svg'],
        ];
    }

    /**
     * Whichever of the handler and the resizer refuses it first.
     */
    public function testARefusedThumbnailPathPublishesNothing()
    {
        Request::merge([
            'batch' => [$this->thumbnailRequest('/../uploads/protected/abc/hidden.png')],
        ]);

        try {
            $markup = $this->makeManager()->onGenerateThumbnails()['generatedThumbnails'][0]['markup'];

            // If a resizer URL was still produced, follow it so that anything it would
            // write is written before the assertion below runs
            if (preg_match('#/resizer/([a-f0-9]{40})/([^"]+)#', $markup, $matches)) {
                $this->get("/resizer/{$matches[1]}/{$matches[2]}");
            }
        } catch (ApplicationException $ex) {
            // The path was refused before a resizer URL could be generated
        }

        $this->assertEmpty(
            File::allFiles(storage_path('app/resized')),
            'A refused thumbnail path left a file in the resized folder'
        );
    }

    /**
     * Invalidation case.
     *
     * @dataProvider acceptedThumbnailPathProvider
     */
    public function testGenerateThumbnailsStillResizesMediaItems(string $path, string $expectedPrefix)
    {
        Request::merge(['batch' => [$this->thumbnailRequest($path)]]);

        $result = $this->makeManager()->onGenerateThumbnails();

        $this->assertEquals('thumbnail', $result['generatedThumbnails'][0]['id']);
        $this->assertResizerThumbnail($result['generatedThumbnails'][0]['markup']);

        preg_match('#/resizer/([a-f0-9]{40})/([^"]+)#', $result['generatedThumbnails'][0]['markup'], $matches);

        $this->get("/resizer/{$matches[1]}/{$matches[2]}")->assertStatus(301);

        $published = File::allFiles(storage_path('app/resized'));
        $this->assertCount(1, $published);
        $this->assertStringStartsWith($expectedPrefix, $published[0]->getFilename());
    }

    public static function acceptedThumbnailPathProvider(): array
    {
        return [
            'library root' => ['/winter.png', 'winter_resized_'],
            'name with a space' => ['/winter space.png', 'winter space_resized_'],
            'name with non-latin characters' => ['/wîntér ünicode.png', 'wîntér ünicode_resized_'],
            'nested folder' => ['/nested folder/winter.png', 'winter_resized_'],
        ];
    }

    /**
     * @dataProvider refusedCropSourceProvider
     */
    public function testCropRefusesSourcesOutsideTheirSourceFolder(string $sourceUrl)
    {
        $this->mergeCropRequest($sourceUrl, '/winter.png');

        try {
            $this->makeManager()->onCropImage();
        } catch (SystemException $ex) {
            // The resizer refuses to resolve an image outside the matched source folder
        }

        $this->assertEmpty(
            File::glob(storage_path('app/media/winter_cropped.*')),
            'A refused crop source was written into the media library'
        );
    }

    public static function refusedCropSourceProvider(): array
    {
        return [
            'relative segment' => ['/storage/temp/app/media/../uploads/protected/abc/hidden.png'],
            'encoded segment' => ['/storage/temp/app/media/%2e%2e/uploads/protected/abc/hidden.png'],
            'encoded separator' => ['/storage/temp/app/media/..%2Fuploads/protected/abc/hidden.png'],
            'backslash separators' => ['/storage/temp/app/media\\..\\uploads/protected/abc/hidden.png'],
            'nested relative segments' => [
                '/storage/temp/app/media/nested folder/../../uploads/protected/abc/hidden.png',
            ],
            'scheme prefixed' => ['file:///storage/temp/app/media/../uploads/protected/abc/hidden.png'],
            'local file system source' => ['/modules/../storage/temp/app/uploads/protected/abc/hidden.png'],
        ];
    }

    /**
     * Invalidation case.
     *
     * @dataProvider acceptedCropSourceProvider
     */
    public function testCropStillCropsMediaItems(string $sourceUrl, string $path, string $expectedPath)
    {
        $this->mergeCropRequest($sourceUrl, $path);

        $result = $this->makeManager()->onCropImage();

        // Media library paths are always forward slashed, but deduplicatePath() rebuilds
        // them with DIRECTORY_SEPARATOR
        $this->assertEquals($expectedPath, '/' . trim(str_replace('\\', '/', $result['path']), '/'));
        $this->assertFileExists(storage_path('app/media' . $expectedPath));
    }

    public static function acceptedCropSourceProvider(): array
    {
        return [
            'library root' => [
                '/storage/temp/app/media/winter.png',
                '/winter.png',
                '/winter_cropped.png',
            ],
            'name with a space' => [
                '/storage/temp/app/media/winter space.png',
                '/winter space.png',
                '/winter space_cropped.png',
            ],
            'name with non-latin characters' => [
                '/storage/temp/app/media/wîntér ünicode.png',
                '/wîntér ünicode.png',
                '/wîntér ünicode_cropped.png',
            ],
            'nested folder' => [
                '/storage/temp/app/media/nested folder/winter.png',
                '/nested folder/winter.png',
                '/nested folder/winter_cropped.png',
            ],
        ];
    }

    protected function mergeCropRequest(string $sourceUrl, string $path): void
    {
        Request::merge([
            'img' => $sourceUrl,
            'path' => $path,
            'selection' => ['x' => 0, 'y' => 0, 'w' => 32, 'h' => 32],
            'selectionMode' => MediaManager::SELECTION_MODE_NORMAL,
            'selectionWidth' => '32',
            'selectionHeight' => '32',
        ]);
    }

    protected function assertResizerThumbnail(string $markup): void
    {
        $this->assertMatchesRegularExpression('#<img src="/resizer/[a-f0-9]{40}/#', $markup);
    }

    protected function assertBrokenThumbnail(string $markup): void
    {
        $this->assertStringNotContainsString('/resizer/', $markup, 'a refused path was given a resizer URL');
        $this->assertStringContainsString('icon-chain-broken', $markup);
    }

    protected function thumbnailRequest(string $path): array
    {
        return [
            'id' => 'thumbnail',
            'path' => $path,
            'width' => 100,
            'height' => 100,
            'lastModified' => 1,
        ];
    }

    /**
     * These four hand `path` to setCurrentFolder(string $path), so a request that omits it,
     * or sends something that is not a string, acts on the library root rather than raising.
     * See wintercms/winter#1582.
     *
     * @dataProvider folderHandlerProvider
     */
    public function testAFolderHandlerWithoutAUsablePathActsOnTheLibraryRoot(string $handler, array $data)
    {
        foreach ([[], ['path' => ''], ['path' => '   '], ['path' => ['x']], ['path' => ['a' => 'b']]] as $path) {
            Request::replace($data + $path);

            $manager = $this->makeManager();
            $manager->$handler();

            $this->assertSame(
                '/',
                self::callProtectedMethod($manager, 'getCurrentFolder'),
                sprintf('%s should act on the library root for %s', $handler, json_encode($path))
            );
        }
    }

    /**
     * @dataProvider folderHandlerProvider
     */
    public function testAFolderHandlerStillHonoursThePathItIsGiven(string $handler, array $data)
    {
        Request::replace($data + ['path' => '/nested folder']);

        $manager = $this->makeManager();
        $manager->$handler();

        $this->assertSame('/nested folder', self::callProtectedMethod($manager, 'getCurrentFolder'));
    }

    public static function folderHandlerProvider(): array
    {
        return [
            'onGoToFolder' => ['onGoToFolder', ['clearCache' => 0]],
            'onChangeView' => ['onChangeView', ['view' => 'grid']],
            'onSetFilter' => ['onSetFilter', ['filter' => 'all']],
            'onSetSorting' => ['onSetSorting', ['sortBy' => 'title', 'sortDirection' => 'asc']],
        ];
    }

    protected function makeManager(): MediaManager
    {
        return new MediaManager(new BackendController(), 'manager');
    }
}
