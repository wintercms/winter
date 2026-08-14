<?php

namespace System\Tests\Classes;

use Backend\Facades\Backend;
use Cms\Classes\Controller as CmsController;
use Cms\Classes\Theme;
use Cache;
use Config;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Event;
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
        Cache::flush();

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

    public function testCalculateResizedDimensionsMatchesDefaultResizer()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $imagePath = base_path('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png');

        $resizer = new \Winter\Storm\Database\Attach\Resizer($imagePath);
        $modes = ['exact', 'portrait', 'landscape', 'auto', 'fit', 'crop'];
        $reqWidth = 200;
        $reqHeight = 150;

        $stormGetDimensions = new \ReflectionMethod($resizer, 'getDimensions');
        $stormGetDimensions->setAccessible(true);
        $stormWidth = new \ReflectionProperty($resizer, 'width');
        $stormWidth->setAccessible(true);
        $stormHeight = new \ReflectionProperty($resizer, 'height');
        $stormHeight->setAccessible(true);
        $winterMethod = new \ReflectionMethod(ImageResizer::class, 'calculateResizedDimensions');
        $winterMethod->setAccessible(true);

        foreach ($modes as $mode) {
            $resizer->setOptions(['mode' => $mode]);
            $expected = $stormGetDimensions->invoke($resizer, $reqWidth, $reqHeight);
            $expected = ['width' => (int) $expected[0], 'height' => (int) $expected[1]];

            $calculated = $winterMethod->invoke(
                null,
                $stormWidth->getValue($resizer),
                $stormHeight->getValue($resizer),
                $reqWidth,
                $reqHeight,
                $mode
            );

            $this->assertSame($expected, $calculated, "Mode $mode output should match DefaultResizer");
        }
    }

    public function testFilterGetDimensionsReturnsFallbackForMissingImage()
    {
        $this->assertSame(['width' => 0, 'height' => 0], ImageResizer::filterGetDimensions(
            '/plugins/database/tester/assets/images/MISSING.png'
        ));
    }

    public function testFilterGetDimensionsReturnsOriginalWhenNoResizeRequested()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        $this->copyMedia();

        $url = URL::to(MediaLibrary::url('winter.png'));
        $dimensions = ImageResizer::filterGetDimensions($url);

        $this->assertSame(310, $dimensions['width']);
        $this->assertSame(310, $dimensions['height']);
    }

    public function testFilterGetDimensionsFromResizerUrl()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        $this->copyMedia();

        $imageResizer = new ImageResizer(
            URL::to(MediaLibrary::url('winter.png')),
            100,
            100
        );
        $resizerUrl = $imageResizer->getResizerUrl();

        $this->assertStringStartsWith('/resizer/', $resizerUrl);

        $dimensions = ImageResizer::filterGetDimensions($resizerUrl);

        $this->assertSame(100, $dimensions['width']);
        $this->assertSame(100, $dimensions['height']);
    }

    public function testFilterGetDimensionsFromAbsoluteResizerUrl()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        $this->copyMedia();

        Config::set('cms.linkPolicy', 'force');

        $imageResizer = new ImageResizer(
            URL::to(MediaLibrary::url('winter.png')),
            100,
            100
        );
        $resizerUrl = $imageResizer->getResizerUrl();

        $this->assertStringStartsWith('http', $resizerUrl);

        $dimensions = ImageResizer::filterGetDimensions($resizerUrl);

        $this->assertSame(100, $dimensions['width']);
        $this->assertSame(100, $dimensions['height']);
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

        foreach (glob(storage_path('app/media/*')) as $file) {
            unlink($file);
        }

        rmdir(storage_path('app/media'));
        rmdir(storage_path('app'));
    }
}
