<?php

namespace System\Tests\Classes;

use Backend\Facades\Backend;
use Cms\Classes\Controller as CmsController;
use Cms\Classes\Theme;
use Config;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Event;
use File;
use Storage;
use System\Classes\ImageResizer;
use System\Classes\MediaLibrary;
use System\Models\File as FileModel;
use System\Tests\Bootstrap\PluginTestCase;
use URL;
use Winter\Storm\Exception\SystemException;

class ImageResizerTest extends PluginTestCase
{
    use ArraySubsetAsserts;

    protected $originalThemesPath = '';

    public function setUp(): void
    {
        parent::setUp();

        $this->originalThemesPath = Config::get('cms.themesPath');
        Config::set('cms.themesPath', '/modules/system/tests/fixtures/themes');

        Config::set('cms.activeTheme', 'test');
        Event::flush('cms.theme.getActiveTheme');
        Theme::resetCache();
    }

    public function tearDown(): void
    {
        $this->removeMedia();

        Config::set('cms.themesPath', $this->originalThemesPath);

        ImageResizer::flushAvailableSources();
        parent::tearDown();
    }

    /**
     * Tests configuration through the constructor as well as events.
     *
     * @return void
     */
    public function testConfiguration()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        // Resize with default options
        $imageResizer = new ImageResizer(
            (new CmsController())->themeUrl('assets/images/winter.png'),
            100,
            100
        );
        self::assertArraySubset([
            'width' => 100,
            'height' => 100,
            'options' => [
                'mode' => 'auto',
                'offset' => [0, 0],
                'sharpen' => 0,
                'interlace' => false,
                'quality' => 90,
                'extension' => 'png',
            ],
        ], $imageResizer->getConfig());

        // Resize with customised options
        $imageResizer = new ImageResizer(
            (new CmsController())->themeUrl('assets/images/winter.png'),
            150,
            120,
            [
                'mode' => 'fit',
                'offset' => [2, 2],
                'sharpen' => 23,
                'interlace' => true,
                'quality' => 73,
                'extension' => 'jpg'
            ]
        );
        self::assertArraySubset([
            'width' => 150,
            'height' => 120,
            'options' => [
                'mode' => 'fit',
                'offset' => [2, 2],
                'sharpen' => 23,
                'interlace' => true,
                'quality' => 73,
                'extension' => 'jpg'
            ],
        ], $imageResizer->getConfig());

        // Resize with an customised defaults
        Event::listen('system.resizer.getDefaultOptions', function (&$options) {
            $options = array_merge($options, [
                'mode' => 'fit',
                'offset' => [2, 2],
                'sharpen' => 23,
                'interlace' => true,
                'quality' => 73,
            ]);
        });

        $imageResizer = new ImageResizer(
            (new CmsController())->themeUrl('assets/images/winter.png'),
            100,
            100,
            []
        );
        self::assertArraySubset([
            'width' => 100,
            'height' => 100,
            'options' => [
                'mode' => 'fit',
                'offset' => [2, 2],
                'sharpen' => 23,
                'interlace' => true,
                'quality' => 73,
                'extension' => 'png',
            ],
        ], $imageResizer->getConfig());

        Event::forget('system.resizer.getDefaultOptions');

        // Resize with a falsey height specified
        $imageResizer = new ImageResizer(
            (new CmsController())->themeUrl('assets/images/winter.png'),
            100,
            false
        );
        self::assertArraySubset([
            'width' => 100,
            'height' => 0,
        ], $imageResizer->getConfig());

        $imageResizer = new ImageResizer(
            (new CmsController())->themeUrl('assets/images/winter.png'),
            100,
            null
        );
        self::assertArraySubset([
            'width' => 100,
            'height' => 0,
        ], $imageResizer->getConfig());

        // Resize with a falsey width specified
        $imageResizer = new ImageResizer(
            (new CmsController())->themeUrl('assets/images/winter.png'),
            '',
            100
        );
        self::assertArraySubset([
            'width' => 0,
            'height' => 100,
        ], $imageResizer->getConfig());

        $imageResizer = new ImageResizer(
            (new CmsController())->themeUrl('assets/images/winter.png'),
            "0",
            100
        );
        self::assertArraySubset([
            'width' => 0,
            'height' => 100,
        ], $imageResizer->getConfig());
    }

    /**
     * Tests URLs for sources that can be accessed via URL.
     *
     * @return void
     */
    public function testURLSources()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        // Theme URL (absolute URL)
        $this->setUpStorage();
        $this->copyMedia();

        $imageResizer = new ImageResizer(
            (new CmsController())->themeUrl('assets/images/winter.png'),
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Theme URL (relative URL)
        $this->setUpStorage();
        $this->copyMedia();

        $imageResizer = new ImageResizer(
            '/modules/system/tests/fixtures/themes/test/assets/images/winter.png',
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Media URL (absolute URL)
        $this->setUpStorage();
        $this->copyMedia();

        $imageResizer = new ImageResizer(
            URL::to(MediaLibrary::url('winter.png')),
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Media URL (relative URL)
        $this->setUpStorage();
        $this->copyMedia();

        $imageResizer = new ImageResizer(
            MediaLibrary::url('winter.png'),
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Media URL (absolute URL)
        $this->setUpStorage();
        $this->copyMedia();

        $imageResizer = new ImageResizer(
            URL::to(MediaLibrary::url('winter.png')),
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Plugin URL (relative URL)
        $imageResizer = new ImageResizer(
            '/modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png',
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Plugin URL (absolute URL)
        $imageResizer = new ImageResizer(
            URL::to('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'),
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Module URL (relative URL)
        $imageResizer = new ImageResizer(
            '/modules/backend/assets/images/favicon.png',
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Module URL (absolute URL)
        $imageResizer = new ImageResizer(
            Backend::skinAsset('assets/images/favicon.png'),
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // URL for a FileModel instance (absolute URL)
        $fileModel = new FileModel();
        $fileModel->fromFile(base_path('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'));
        $fileModel->save();

        $imageResizer = new ImageResizer(
            FileModel::first()->getPath(),
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Remove FileModel instance
        $fileModel->delete();

        // URL of a FileModel instance (relative URL)
        $fileModel = new FileModel();
        $fileModel->fromFile(base_path('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'));
        $fileModel->save();

        $imageResizer = new ImageResizer(
            str_replace(url('') . '/', '/', FileModel::first()->getPath()),
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);
    }

    public function testDirectSources()
    {
        // FileModel instance itself
        $fileModel = new FileModel();
        $fileModel->fromFile(base_path('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'));
        $fileModel->save();

        $imageResizer = new ImageResizer(
            $fileModel,
            100,
            100
        );
        $this->assertEquals('png', $imageResizer->getConfig()['options']['extension']);

        // Remove FileModel instance
        $fileModel->delete();
    }

    public function testInvalidInputPath()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessageMatches('/^Unable to process the provided image/');

        $imageResizer = new ImageResizer(
            '/plugins/database/tester/assets/images/MISSING.png',
            100,
            100
        );
    }

    public function testInvalidInputFileModel()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessageMatches('/^Unable to process the provided image/');

        $imageResizer = new ImageResizer(
            FileModel::first(),
            100,
            100
        );
    }

    public function testSpaceInFilename()
    {
        // Media URL with space
        $this->setUpStorage();
        $this->copyMedia();

        $imageResizer = new ImageResizer(
            URL::to(MediaLibrary::url('winter space.png')),
            100,
            100
        );

        $this->assertStringContainsString('winter%20space', $imageResizer->getResizedUrl(), 'Resized URLs are not properly URL encoded');
    }

    public function testGetResizedUrl()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $imageResizer = new ImageResizer((new CmsController())->themeUrl('assets/images/winter.png'));

        Config::set('cms.linkPolicy', 'force');
        $url = $imageResizer->getResizedUrl();
        $this->assertTrue(starts_with($url, 'http'));

        Config::set('cms.linkPolicy', 'detect');
        $url = $imageResizer->getResizedUrl();
        $this->assertTrue(starts_with($url, Config::get('cms.storage.resized.path', '/storage/tests/app/resized')));
    }

    public function testGetResizerUrl()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $imageResizer = new ImageResizer((new CmsController())->themeUrl('assets/images/winter.png'));

        Config::set('cms.linkPolicy', 'force');
        $url = $imageResizer->getResizerUrl();
        $this->assertTrue(starts_with($url, 'http'));

        Config::set('cms.linkPolicy', 'detect');
        $url = $imageResizer->getResizerUrl();
        $this->assertTrue(starts_with($url, '/resizer/'));

        // test dots' double-encoding
        // @see https://github.com/wintercms/winter/pull/1493
        $this->assertTrue(ends_with($url, '%252Epng'));

        // Verify the encoded URL round-trips through the resizer route's decoding and
        // signature verification. A fresh instance is required as the identifier is
        // cached on first generation and the link policy has changed since then. The
        // router decodes the parameter once before it reaches getValidResizedUrl().
        $imageResizer = new ImageResizer((new CmsController())->themeUrl('assets/images/winter.png'));
        [$identifier, $encodedUrl] = array_slice(explode('/', $imageResizer->getResizerUrl()), 2);
        $this->assertSame(
            $imageResizer->getResizedUrl(),
            ImageResizer::getValidResizedUrl($identifier, rawurldecode($encodedUrl))
        );
    }

    public function testResizerRedirect()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        $this->copyMedia();
        Config::set('cms.storage.resized', [
            'disk'   => 'test_local',
            'folder' => 'resized',
            'path'   => '/storage/temp/app/resized',
        ]);

        $imageResizer = new ImageResizer((new CmsController())->themeUrl('assets/images/winter.png'), 50, 50);

        // The resizer route responds with a permanent redirect as a resizer URL can
        // only ever target the resized URL embedded and signed within it, and this
        // also exercises the full round-trip of the double-encoded URL parameter
        // through the actual router
        $response = $this->get($imageResizer->getResizerUrl());
        $response->assertStatus(301);
        $response->assertRedirect($imageResizer->getResizedUrl());

        // Clean up the generated image
        Storage::disk('test_local')->deleteDirectory('resized');
    }

    /**
     * The disk path built for a matched source must stay inside that source's folder.
     *
     * @dataProvider containedSourcePathProvider
     */
    public function testRefusesPathsOutsideTheMatchedSource(string $url)
    {
        $this->setUpStorage();
        $this->copyMedia();
        $this->copyOutOfRootImage();

        // The target resolves to an existing, readable file on the media source's own
        // disk, so only the containment check can refuse it
        $this->assertTrue(
            Storage::disk('test_local')->exists('media/../uploads/protected/abc/hidden.png')
        );

        $this->expectException(SystemException::class);
        ImageResizer::normalizeImage($url);
    }

    public function containedSourcePathProvider(): array
    {
        return [
            'relative segment' => ['/storage/temp/app/media/../uploads/protected/abc/hidden.png'],
            'encoded segment' => ['/storage/temp/app/media/%2e%2e/uploads/protected/abc/hidden.png'],
            'encoded separator' => ['/storage/temp/app/media/..%2Fuploads/protected/abc/hidden.png'],
            'double encoded segment' => ['/storage/temp/app/media/%252e%252e/uploads/protected/abc/hidden.png'],
            'backslash separators' => ['/storage/temp/app/media\\..\\uploads/protected/abc/hidden.png'],
            'nested segments' => ['/storage/temp/app/media/nested/../../uploads/protected/abc/hidden.png'],
            'dot and relative segments' => ['/storage/temp/app/media/./../uploads/protected/abc/hidden.png'],
            'trailing space in segment' => ['/storage/temp/app/media/.. /../uploads/protected/abc/hidden.png'],
            'scheme prefixed' => ['file:///storage/temp/app/media/../uploads/protected/abc/hidden.png'],
            'protocol relative' => ['//localhost/storage/temp/app/media/../uploads/protected/abc/hidden.png'],
            'local file system source' => ['/modules/../storage/temp/app/uploads/protected/abc/hidden.png'],
            'local file system source, nested' => ['/modules/system/tests/fixtures/../../../../storage/temp/app/uploads/protected/abc/hidden.png'],
            'differently cased source folder' => ['/storage/temp/app/MEDIA/../uploads/protected/abc/hidden.png'],
            'source folder name prefix' => ['/storage/temp/app/media-archive/../uploads/protected/abc/hidden.png'],
        ];
    }

    /**
     * A relative segment is refused whether or not it would have resolved back inside the
     * matched source folder. The remainder is handed to a disk that is not necessarily a
     * local one, and each filesystem adapter resolves relative segments its own way, so
     * the path that reaches the disk is kept free of them entirely. This mirrors the media
     * library's own path validation, which refuses the segment outright as well.
     */
    public function testRefusesARelativeSegmentThatWouldHaveStayedInsideTheSource()
    {
        $this->setUpStorage();
        $this->copyMedia();

        File::makeDirectory(storage_path('app/media/nested folder'), 0777, true, true);
        File::copy(
            base_path('modules/system/tests/fixtures/media/winter.png'),
            storage_path('app/media/nested folder/winter.png')
        );

        // The local adapter resolves this one back inside the media folder, so only the
        // containment check refuses it
        $this->assertTrue(
            Storage::disk('test_local')->exists('media/nested folder/../winter.png')
        );

        $this->expectException(SystemException::class);
        ImageResizer::normalizeImage('/storage/temp/app/media/nested folder/../winter.png');
    }

    /**
     * The containment check must not refuse the paths it is guarding.
     */
    public function testAcceptsPathsInsideTheMatchedSource()
    {
        $this->setUpStorage();
        $this->copyMedia();

        File::makeDirectory(storage_path('app/media/nested folder'), 0777, true, true);
        File::copy(
            base_path('modules/system/tests/fixtures/media/winter.png'),
            storage_path('app/media/nested folder/winter.png')
        );
        File::copy(
            base_path('modules/system/tests/fixtures/media/winter.png'),
            storage_path('app/media/wîntér ünicode.png')
        );

        $expected = [
            'winter.png' => 'media/winter.png',
            // Media item names may contain spaces, dots and non-latin characters
            'winter space.png' => 'media/winter space.png',
            'wîntér ünicode.png' => 'media/wîntér ünicode.png',
            // Media items may live in nested folders
            'nested folder/winter.png' => 'media/nested folder/winter.png',
            // A current directory segment cannot leave the source folder
            './winter.png' => 'media/./winter.png',
        ];

        foreach ($expected as $mediaPath => $diskPath) {
            $image = ImageResizer::normalizeImage(MediaLibrary::url($mediaPath));

            $this->assertEquals('media', $image['source'], $mediaPath);
            $this->assertEquals($diskPath, $image['path'], $mediaPath);
        }

        // Sources backed by the local file system resolve the same way
        $image = ImageResizer::normalizeImage('/modules/system/tests/fixtures/media/winter.png');

        $this->assertEquals('modules', $image['source']);
        $this->assertEquals('modules/system/tests/fixtures/media/winter.png', $image['path']);
    }

    protected function copyOutOfRootImage()
    {
        $uploadPath = storage_path('app/uploads/protected/abc');

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        copy(
            base_path('modules/system/tests/fixtures/media/winter.png'),
            $uploadPath . DIRECTORY_SEPARATOR . 'hidden.png'
        );
    }

    protected function setUpStorage()
    {
        $this->app->useStoragePath(base_path('storage/temp'));

        Config::set('filesystems.disks.test_local', [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ]);

        Config::set('cms.storage.media', [
            'disk'   => 'test_local',
            'folder' => 'media',
            'path'   => '/storage/temp/app/media',
        ]);
    }

    protected function copyMedia()
    {
        $mediaPath = storage_path('app/media');

        if (!is_dir($mediaPath)) {
            mkdir($mediaPath, 0777, true);
        }

        foreach (glob(base_path('modules/system/tests/fixtures/media/*')) as $file) {
            $path = pathinfo($file);
            copy($file, $mediaPath . DIRECTORY_SEPARATOR . $path['basename']);
        }
    }

    protected function removeMedia()
    {
        if ($this->app->storagePath() !== base_path('storage/temp')) {
            return;
        }

        File::deleteDirectory(storage_path('app/media'));
        File::deleteDirectory(storage_path('app/uploads'));
        rmdir(storage_path('app'));
    }
}
