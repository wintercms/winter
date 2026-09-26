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
        $this->assertStringNotContainsString(
            '/resizer/',
            $result['generatedThumbnails'][0]['markup'],
            'A path outside the media library was given a resizer URL'
        );
        $this->assertStringContainsString('icon-chain-broken', $result['generatedThumbnails'][0]['markup']);
    }

    /**
     * The sidebar preview takes a single path and reports an unusable one to the caller,
     * which is how every other path bearing handler of this widget behaves.
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
        $this->assertMatchesRegularExpression(
            '#<img src="/resizer/[a-f0-9]{40}/#',
            $result['generatedThumbnails'][0]['markup']
        );
        $this->assertStringNotContainsString('/resizer/', $result['generatedThumbnails'][1]['markup']);
        $this->assertMatchesRegularExpression(
            '#<img src="/resizer/[a-f0-9]{40}/#',
            $result['generatedThumbnails'][2]['markup']
        );
    }

    /**
     * A media item whose name falls outside the media library's own path rules is listed by
     * the library but cannot be renamed, moved, previewed in the sidebar or cropped. Its
     * grid thumbnail is now the same broken thumbnail the widget already renders for an
     * item it cannot resize, and it does not cost its neighbours theirs.
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
        $this->assertStringContainsString('icon-chain-broken', $result['generatedThumbnails'][0]['markup']);
        $this->assertMatchesRegularExpression(
            '#<img src="/resizer/[a-f0-9]{40}/#',
            $result['generatedThumbnails'][1]['markup']
        );
    }

    public function refusedThumbnailPathProvider(): array
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
     * A path the handler refuses must not leave a resized file behind, whichever of the
     * handler and the resizer refuses it first.
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
     * Invalidation case: ordinary media items must still get a thumbnail, including items
     * in nested folders and items with spaces or non-latin characters in their names.
     *
     * @dataProvider acceptedThumbnailPathProvider
     */
    public function testGenerateThumbnailsStillResizesMediaItems(string $path, string $expectedPrefix)
    {
        Request::merge(['batch' => [$this->thumbnailRequest($path)]]);

        $result = $this->makeManager()->onGenerateThumbnails();

        $this->assertEquals('thumbnail', $result['generatedThumbnails'][0]['id']);
        $this->assertMatchesRegularExpression(
            '#<img src="/resizer/[a-f0-9]{40}/#',
            $result['generatedThumbnails'][0]['markup']
        );

        preg_match('#/resizer/([a-f0-9]{40})/([^"]+)#', $result['generatedThumbnails'][0]['markup'], $matches);

        $this->get("/resizer/{$matches[1]}/{$matches[2]}")->assertStatus(301);

        $published = File::allFiles(storage_path('app/resized'));
        $this->assertCount(1, $published);
        $this->assertStringStartsWith($expectedPrefix, $published[0]->getFilename());
    }

    public function acceptedThumbnailPathProvider(): array
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
        Request::merge([
            'img' => $sourceUrl,
            'path' => '/winter.png',
            'selection' => ['x' => 0, 'y' => 0, 'w' => 32, 'h' => 32],
            'selectionMode' => MediaManager::SELECTION_MODE_NORMAL,
            'selectionWidth' => '32',
            'selectionHeight' => '32',
        ]);

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

    public function refusedCropSourceProvider(): array
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
     * Invalidation case: an ordinary crop of a media item must still work, including items
     * in nested folders and items with spaces or non-latin characters in their names.
     *
     * @dataProvider acceptedCropSourceProvider
     */
    public function testCropStillCropsMediaItems(string $sourceUrl, string $path, string $expectedPath)
    {
        Request::merge([
            'img' => $sourceUrl,
            'path' => $path,
            'selection' => ['x' => 0, 'y' => 0, 'w' => 32, 'h' => 32],
            'selectionMode' => MediaManager::SELECTION_MODE_NORMAL,
            'selectionWidth' => '32',
            'selectionHeight' => '32',
        ]);

        $result = $this->makeManager()->onCropImage();

        // Media library paths are always forward slashed, but deduplicatePath() rebuilds
        // them with DIRECTORY_SEPARATOR
        $this->assertEquals($expectedPath, '/' . trim(str_replace('\\', '/', $result['path']), '/'));
        $this->assertFileExists(storage_path('app/media' . $expectedPath));
    }

    public function acceptedCropSourceProvider(): array
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

    protected function makeManager(): MediaManager
    {
        return new MediaManager(new BackendController(), 'manager');
    }
}
