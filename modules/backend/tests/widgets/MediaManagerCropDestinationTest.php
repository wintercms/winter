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
use Winter\Storm\Filesystem\Definitions as FileDefinitions;

/**
 * Covers the destination path handling of the Media Manager's Crop & Insert handler.
 *
 * The requested destination selects the folder and the file name to store the crop under.
 * The extension comes from the image the resizer produced, and the result is held to the
 * same extension allowlist that uploading and renaming a media item are held to.
 */
class MediaManagerCropDestinationTest extends PluginTestCase
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
        File::copy(
            base_path('modules/system/tests/fixtures/media/winter.png'),
            storage_path('app/media/winter.png')
        );
    }

    public function tearDown(): void
    {
        File::deleteDirectory(storage_path('app/media'));
        File::deleteDirectory(storage_path('app/resized'));

        ImageResizer::flushAvailableSources();

        parent::tearDown();
    }

    /**
     * The extension of the stored crop comes from the image the resizer produced, not from
     * the request.
     *
     * @dataProvider requestedDestinationProvider
     */
    public function testCropStoresTheProducedImageFormat(string $destination, string $expectedPath)
    {
        $result = $this->crop('/storage/temp/app/media/winter.png', $destination);

        $this->assertNoScriptExtensionsInMediaLibrary();
        $this->assertEquals($expectedPath, $this->normalize($result['path']));
        $this->assertFileExists(storage_path('app/media' . $expectedPath));
    }

    public function requestedDestinationProvider(): array
    {
        return [
            'script extension' => ['/nested folder/report.php', '/nested folder/report_cropped.png'],
            'uppercase script extension' => ['/report.PHP', '/report_cropped.png'],
            'double extension' => ['/report.png.php', '/report.png_cropped.png'],
            'script extension before an image one' => ['/report.php.png', '/report.php_cropped.png'],
            'server parsed extension' => ['/report.phtml', '/report_cropped.png'],
            'no extension at all' => ['/report', '/report_cropped.png'],
            'trailing dot' => ['/report.', '/report_cropped.png'],
            'extension of a different image format' => ['/report.gif', '/report_cropped.png'],
            'name with a space' => ['/annual report.php', '/annual report_cropped.png'],
            'name with non-latin characters' => ['/rapporté.php', '/rapporté_cropped.png'],
            'folder name containing a dot' => ['/nested folder/a.b.php', '/nested folder/a.b_cropped.png'],
        ];
    }

    /**
     * The uploader accepts a client supplied extension without checking it against the
     * detected MIME type, and the resizer picks its decoder from the MIME type but its
     * encoder from the path extension, so GIF bytes stored under a ".png" name come back
     * out of a crop as a PNG. The stored destination must describe what was produced.
     */
    public function testCropOfMismatchedImageBytesStoresTheProducedFormat()
    {
        $source = storage_path('app/media/nested folder/mismatched.png');
        imagegif(imagecreatetruecolor(32, 32), $source);

        $result = $this->crop(
            '/storage/temp/app/media/nested folder/mismatched.png',
            '/nested folder/mismatched.php'
        );

        $this->assertNoScriptExtensionsInMediaLibrary();
        $this->assertEquals('/nested folder/mismatched_cropped.png', $this->normalize($result['path']));
        $this->assertEquals(
            'image/png',
            mime_content_type(storage_path('app/media/nested folder/mismatched_cropped.png'))
        );
    }

    /**
     * The destination is held to the same extension allowlist as uploading and renaming,
     * which is configurable through cms.fileDefinitions.defaultExtensions.
     */
    public function testCropRefusesADestinationOutsideTheExtensionAllowlist()
    {
        Config::set('cms.fileDefinitions.defaultExtensions', ['gif']);

        $this->expectException(ApplicationException::class);

        try {
            $this->crop('/storage/temp/app/media/winter.png', '/winter.png');
        } finally {
            $this->assertFileDoesNotExist(storage_path('app/media/winter_cropped.png'));
        }
    }

    /**
     * Out of the box the allowlist covers every format the resizer can produce, so stock
     * configuration never refuses the crop of an image the Media Manager can open. A site
     * that has narrowed the list will find crops to the formats it removed refused, in the
     * same way that uploading and renaming them are already refused.
     */
    public function testTheStockExtensionAllowlistCoversEveryFormatTheResizerProduces()
    {
        $this->assertNull(
            Config::get('cms.fileDefinitions.defaultExtensions'),
            'This test describes the behaviour of the unconfigured allowlist'
        );

        $allowed = array_map('strtolower', FileDefinitions::get('defaultExtensions'));

        foreach (['avif', 'gif', 'jpeg', 'jpg', 'png', 'webp'] as $extension) {
            $this->assertContains($extension, $allowed);
        }
    }

    /**
     * @dataProvider refusedDestinationProvider
     */
    public function testCropRefusesInvalidDestinations($destination)
    {
        $this->expectException(ApplicationException::class);

        $this->crop('/storage/temp/app/media/winter.png', $destination);
    }

    public function refusedDestinationProvider(): array
    {
        return [
            'relative segment' => ['/../resized/escaped.png'],
            'relative segment in the middle' => ['/nested folder/../../resized/escaped.png'],
            'backslash separators' => ['\\..\\resized\\escaped.png'],
            'absolute url' => ['https://localhost/winter.png'],
            'empty' => [''],
            'whitespace only' => ["  \t "],
            'not a string' => [['/winter.png']],
        ];
    }

    /**
     * The image to crop is named by its URL, so that the resizer resolves it through its
     * configured sources. The resizer will also take a disk and a path directly, for a
     * caller that already holds both, and a request is not such a caller.
     *
     * @dataProvider refusedSourceProvider
     */
    public function testCropRefusesASourceThatIsNotAUrl($source)
    {
        $this->expectException(ApplicationException::class);

        try {
            $this->crop($source, '/winter.png');
        } finally {
            $this->assertFileDoesNotExist(storage_path('app/media/winter_cropped.png'));
        }
    }

    public function refusedSourceProvider(): array
    {
        return [
            'disk and path pair' => [[
                'disk' => 'test_local',
                'path' => 'media/winter.png',
                'source' => 'media',
            ]],
            'list of urls' => [['/storage/temp/app/media/winter.png']],
            'empty' => [''],
            'whitespace only' => ["  \t "],
        ];
    }

    /**
     * Invalidation case: ordinary cropping must keep working, in the library root and in a
     * nested folder, for names containing spaces and non-latin characters, and repeated
     * crops must keep deduplicating rather than overwriting.
     */
    public function testCropStillWritesLegitimateImages()
    {
        $result = $this->crop('/storage/temp/app/media/winter.png', '/winter.png');

        $this->assertEquals('/winter_cropped.png', $this->normalize($result['path']));
        $this->assertEquals('winter_cropped.png', basename($this->normalize($result['path'])));
        $this->assertEquals('winter_cropped.png', $result['title']);
        $this->assertEquals([32, 32], array_slice(
            getimagesize(storage_path('app/media/winter_cropped.png')),
            0,
            2
        ));

        $again = $this->crop('/storage/temp/app/media/winter.png', '/winter.png');
        $this->assertEquals('/winter_cropped_1.png', $this->normalize($again['path']));

        foreach ([
            'nested folder/winter.png' => '/nested folder/winter_cropped.png',
            'winter space.png' => '/winter space_cropped.png',
            'wîntér ünicode.png' => '/wîntér ünicode_cropped.png',
        ] as $mediaItem => $expectedPath) {
            File::copy(
                base_path('modules/system/tests/fixtures/media/winter.png'),
                storage_path('app/media/' . $mediaItem)
            );

            $cropped = $this->crop(
                '/storage/temp/app/media/' . $mediaItem,
                '/' . $mediaItem
            );

            $this->assertEquals($expectedPath, $this->normalize($cropped['path']));
            $this->assertFileExists(storage_path('app/media' . $expectedPath));
        }
    }

    protected function crop($sourceUrl, $destination): array
    {
        Request::merge([
            'img' => $sourceUrl,
            'path' => $destination,
            'selection' => ['x' => 0, 'y' => 0, 'w' => 32, 'h' => 32],
            'selectionMode' => MediaManager::SELECTION_MODE_NORMAL,
            'selectionWidth' => '32',
            'selectionHeight' => '32',
        ]);

        return (new MediaManager(new BackendController(), 'manager'))->onCropImage();
    }

    /**
     * Media library paths are always forward slashed, but deduplicatePath() rebuilds them
     * with DIRECTORY_SEPARATOR
     */
    protected function normalize(string $path): string
    {
        return '/' . trim(str_replace('\\', '/', $path), '/');
    }

    protected function assertNoScriptExtensionsInMediaLibrary(): void
    {
        foreach (File::allFiles(storage_path('app/media')) as $file) {
            $this->assertNotContains(
                strtolower($file->getExtension()),
                ['php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'phtml', 'phar'],
                sprintf('%s was written to the media library', $file->getFilename())
            );
        }
    }
}
