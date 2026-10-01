<?php

namespace System\Tests\Classes;

use Backend\Facades\Backend;
use Cms\Classes\Controller as CmsController;
use Cms\Classes\Theme;
use Cache;
use File;
use Config;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Event;
use Storage;
use System\Classes\ImageResizer;
use System\Classes\MediaLibrary;
use System\Models\File as FileModel;
use System\Tests\Bootstrap\PluginTestCase;
use URL;
use Throwable;
use GdImage;
use ReflectionMethod;
use ReflectionProperty;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Database\Attach\Resizer as DefaultResizer;

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

        File::deleteDirectory(storage_path('temp/resizer-parity'));

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
     * Assert that the predicted dimensions match what the resizer actually produces
     *
     * The existing committed fixtures are square, and at a ratio of 1.0 every mode
     * agrees with every other: the round-versus-truncate divergence collapses and the
     * landscape and portrait branches of the auto calculation are unreachable. A green
     * suite over square sources therefore establishes nothing about the arithmetic, so
     * the sources here are generated at the ratios that actually exercise it.
     *
     * Sources are generated rather than committed because a dimension assertion does
     * not care about the picture, and a 3x7 fixture costs 200 bytes rather than 40 KiB.
     * Anything that needs to see detail -- a resampling quality assertion -- needs a
     * photographic source instead.
     *
     * Cases where the resizer itself refuses are compared too, by asserting that the
     * prediction does not claim a size. A harness that catches and skips them hides
     * exactly the degenerate boundaries this test exists to cover.
     *
     * @return void
     */
    public function testCalculateResizedDimensionsMatchesResizerOutput()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $modes = ['exact', 'portrait', 'landscape', 'auto', 'fit', 'crop'];
        $requests = [
            [200, 150], [100, 100], [150, 200], [50, 200], [200, 50],
            [0, 0], [0, 150], [200, 0], [1, 1], [0, 1], [1, 0],
            [1, 100], [100, 1], [1, 50], [50, 1], [37, 91], [640, 480],
        ];

        $compared = 0;
        $refused = 0;
        $failures = [];
        $refusedWithoutPrediction = [];
        $predictionTypeFailures = [];

        foreach ($this->createParityFixtures() as $fixture) {
            $size = getimagesize($fixture);
            $origWidth = $size[0];
            $origHeight = $size[1];

            foreach ($modes as $mode) {
                foreach ($requests as $request) {
                    [$reqWidth, $reqHeight] = $request;

                    $predicted = $this->predictDimensions($origWidth, $origHeight, $reqWidth, $reqHeight, $mode);

                    if (!is_int($predicted['width']) || !is_int($predicted['height'])) {
                        $predictionTypeFailures[] = sprintf(
                            '%s mode=%s req=%dx%d produced %s',
                            basename($fixture),
                            $mode,
                            $reqWidth,
                            $reqHeight,
                            gettype($predicted['width']) . '/' . gettype($predicted['height'])
                        );
                    }

                    $options = ['mode' => $mode];
                    if ($mode === 'crop') {
                        $options['offset'] = [0, 0];
                    }

                    $actual = $this->resizeAndMeasure($fixture, $reqWidth, $reqHeight, $options);

                    if ($actual === null) {
                        // The resizer refused to produce an image. The prediction must
                        // not claim a size for it.
                        $refused++;
                        if ($predicted['width'] > 0 || $predicted['height'] > 0) {
                            $refusedWithoutPrediction[] = sprintf(
                                'resizer refused but predicted %dx%d: %s mode=%s req=%dx%d',
                                $predicted['width'],
                                $predicted['height'],
                                basename($fixture),
                                $mode,
                                $reqWidth,
                                $reqHeight
                            );
                        }
                        continue;
                    }

                    $compared++;
                    if ($actual !== $predicted) {
                        $failures[] = sprintf(
                            '%s (%dx%d) mode=%s req=%dx%d: predicted %dx%d, produced %dx%d',
                            basename($fixture),
                            $origWidth,
                            $origHeight,
                            $mode,
                            $reqWidth,
                            $reqHeight,
                            $predicted['width'],
                            $predicted['height'],
                            $actual['width'],
                            $actual['height']
                        );
                    }
                }
            }
        }

        $this->assertGreaterThan(0, $compared, 'No comparisons were made; the fixtures or requests are empty.');
        $this->assertSame([], $predictionTypeFailures, 'Dimensions must be integers, not floats.');
        $this->assertSame([], $failures, "{$compared} comparisons, {$refused} refused by the resizer.");
        $this->assertSame([], $refusedWithoutPrediction, "{$refused} cases refused by the resizer.");
    }

    /**
     * A resized image has to report the same dimensions through the Twig filters as the
     * file the resizer route writes
     *
     * The cold path is covered by the parity test; this is the state every image is in
     * after its first browser load, where getUrl() returns the static path and the
     * resizer configuration is never written by the URL builder.
     *
     * @return void
     */
    public function testSteadyStateResizedUrlStillReportsDimensions()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        Config::set('cms.storage.resized', [
            'disk'   => 'test_local',
            'folder' => 'resized',
            // The local adapter's url() ignores a non-default root and returns
            // /storage/<folder>/..., so the configured public path has to match that
            // for a resized image to be recognised as the 'resized' source at all.
            'path'   => '/storage/resized',
        ]);

        // Deliberately not square: on a square source every mode predicts the same
        // thing and this test would pass even if the arithmetic were wrong.
        $imageResizer = new ImageResizer($this->createMediaImage('steady.png', 400, 300), 200, 150);
        $identifier = $imageResizer->getIdentifier();

        // Cold: the resizer filter returns a /resizer/ URL, and nothing has been
        // written yet, so the prediction has to come from the source dimensions
        $resizerUrl = $imageResizer->getResizerUrl();
        $cold = ImageResizer::filterGetDimensions($resizerUrl);
        $this->assertSame(200, $cold['width']);
        $this->assertSame(150, $cold['height']);

        // Produce the image the way the resizer route does
        ImageResizer::fromIdentifier($identifier)->resize();

        $produced = getimagesize(Storage::disk('test_local')->path($imageResizer->getPathToResizedImage()));
        $this->assertSame(200, $produced[0]);
        $this->assertSame(150, $produced[1]);

        // Steady state: the file exists, so getUrl() bypasses getResizerUrl() and
        // storeConfig() never runs again for this image
        // Steady state: the resizer filter now returns the static resized path, and
        // getUrl() bypasses getResizerUrl() so storeConfig() never runs again
        $this->assertTrue($imageResizer->isResized());
        $this->assertSame($imageResizer->getResizedUrl(), $imageResizer->getUrl());
        $steady = ImageResizer::filterGetDimensions($imageResizer->getUrl());

        $this->assertSame(
            ['width' => $produced[0], 'height' => $produced[1]],
            $steady,
            'A resized image must still report its dimensions after the first browser load.'
        );

        Storage::disk('test_local')->deleteDirectory('resized');
    }

    /**
     * A failed source read must not be cached as a zero dimension
     *
     * @return void
     */
    public function testFilterGetDimensionsDoesNotCacheAFailedRead()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        Config::set('cms.storage.resized', [
            'disk'   => 'test_local',
            'folder' => 'resized',
            'path'   => '/storage/resized',
        ]);

        $url = $this->createMediaImage('transient.png', 400, 300);
        $imageResizer = new ImageResizer($url, 200, 150);
        $identifier = $imageResizer->getIdentifier();
        $config = $imageResizer->getConfig();

        // Captured while the source still exists: getResizerUrl() resolves the
        // identifier, which reads the source's mtime.
        $resizerUrl = $imageResizer->getResizerUrl();

        // Seed the configuration directly so the assertion does not depend on
        // getUrl() having written it, then remove the source out from under it.
        Cache::put(ImageResizer::CACHE_PREFIX . $identifier, $config, now()->addDay());
        Storage::disk('test_local')->delete($config['image']['path']);

        $this->assertSame(
            ['width' => 0, 'height' => 0],
            ImageResizer::filterGetDimensions($resizerUrl),
            'An unmeasurable source reports unknown rather than a partial pair.'
        );

        // Restore the source. The failure must not have reached the source cache, so
        // the next read recovers with no manual cache invalidation at all.
        $this->createMediaImage('transient.png', 400, 300);

        $this->assertSame(
            ['width' => 200, 'height' => 150],
            ImageResizer::filterGetDimensions($resizerUrl),
            'A failed read must not become a cached zero dimension.'
        );
    }

    /**
     * The resizer configuration survives the request that consumes it
     *
     * The entry used to be evicted by fromIdentifier() so that a second visitor could
     * not race the first. That made any page holding a resizer URL stop resolving after
     * the first request, which is the case this whole change exists to fix.
     *
     * @return void
     */
    public function testResizerConfigSurvivesTheRequestThatConsumesIt()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        Config::set('cms.storage.resized', [
            'disk'   => 'test_local',
            'folder' => 'resized',
            'path'   => '/storage/resized',
        ]);

        $imageResizer = new ImageResizer($this->createMediaImage('shared.png', 400, 300), 200, 150);
        $identifier = $imageResizer->getIdentifier();
        $resizerUrl = $imageResizer->getResizerUrl();

        $this->assertTrue(
            Cache::has(ImageResizer::CACHE_PREFIX . $identifier),
            'The URL builder is expected to write the configuration.'
        );

        // A second visitor hits the same URL. The configuration is still resolvable,
        // so the dimensions still resolve too.
        ImageResizer::fromIdentifier($identifier)->resize();

        $this->assertTrue(
            Cache::has(ImageResizer::CACHE_PREFIX . $identifier),
            'The configuration must not be consumed by the request that uses it.'
        );

        $this->assertSame(
            ['width' => 200, 'height' => 150],
            ImageResizer::filterGetDimensions($resizerUrl),
            'A resizer URL must keep resolving after a previous request consumed it.'
        );

        Storage::disk('test_local')->deleteDirectory('resized');
    }

    /**
     * An EXIF orientation of 6 or 8 is reflected in the reported dimensions
     *
     * The resizer reports the displayed dimensions rather than the stored pixel
     * buffer, so a source tagged as rotated has to be reported swapped or every
     * portrait and landscape calculation is computed against the wrong orientation.
     *
     * GD cannot write an EXIF segment, so the fixture is assembled here rather than
     * committed. The structural assertions run everywhere; the orientation assertion
     * only runs where the exif extension is present, which is why it was added to the
     * CI extension list for this job.
     *
     * @return void
     */
    public function testExifOrientationIsReflectedInTheDimensions()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        Config::set('cms.storage.resized', [
            'disk'   => 'test_local',
            'folder' => 'resized',
            'path'   => '/storage/resized',
        ]);

        $url = $this->createRotatedJpeg('rotated.jpg', 400, 225, 6);
        $this->assertExifSegmentIsWellFormed($url, 6, 400, 225);

        $imageResizer = new ImageResizer($url, 200, 150);
        $resizerUrl = $imageResizer->getResizerUrl();
        $config = $imageResizer->getConfig();
        $identifier = $imageResizer->getIdentifier();
        Cache::put(ImageResizer::CACHE_PREFIX . $identifier, $config, now()->addDay());

        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('The exif extension is not available.');
        }

        // Stored 400x225, displayed as 225x400 once the orientation is applied.
        // Requesting 200x150 in auto mode against a portrait source produces
        // 84x150 (150 constrained by height, 225 * 150/400 wide). Without the swap
        // the source reads as a 400x225 landscape and the answer is 200x112 instead,
        // so this pair is what distinguishes the two.
        $dimensions = ImageResizer::filterGetDimensions($resizerUrl);

        $this->assertSame(
            ['width' => 84, 'height' => 150],
            $dimensions,
            'An image tagged with orientation 6 must be reported in its displayed orientation.'
        );
    }

    /**
     * A source on a symlinked disk is still measured correctly
     *
     * getConfig() rewrites a local disk's path prefix to its realpath so that atomic
     * deployments survive a changing base path, and the dimension reader has to
     * resolve the same file through that rewrite.
     *
     * @return void
     */
    public function testSymlinkedSourceIsMeasuredCorrectly()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        // The storage path has to be in place before the fixture is written, or the
        // fixture lands outside the disk root that is about to be configured.
        $this->setUpStorage();

        $real = storage_path('app/real-media');
        $link = storage_path('app/linked-media');

        if (!is_dir($real)) {
            mkdir($real, 0777, true);
        }

        $image = imagecreatetruecolor(400, 300);
        imagefill($image, 0, 0, imagecolorallocate($image, 40, 80, 120));
        imagepng($image, $real . DIRECTORY_SEPARATOR . 'linked.png');
        imagedestroy($image);

        if (is_link($link) || file_exists($link)) {
            unlink($link);
        }

        if (!@symlink($real, $link)) {
            $this->markTestSkipped('This platform does not allow creating symlinks here.');
        }

        Config::set('filesystems.disks.test_symlink', [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ]);

        Config::set('cms.storage.media', [
            'disk'   => 'test_symlink',
            'folder' => 'linked-media',
            // The local adapter's url() ignores a non-default root, so the configured
            // public path has to match what it produces for the source to be found.
            'path'   => '/storage/linked-media',
        ]);

        Config::set('cms.storage.resized', [
            'disk'   => 'test_local',
            'folder' => 'resized',
            'path'   => '/storage/resized',
        ]);

        // The available sources are memoised from the first call, so they have to be
        // dropped after repointing the media disk at a different one.
        ImageResizer::flushAvailableSources();

        $imageResizer = new ImageResizer('/storage/linked-media/linked.png', 200, 150);
        $resizerUrl = $imageResizer->getResizerUrl();
        $identifier = $imageResizer->getIdentifier();
        Cache::put(ImageResizer::CACHE_PREFIX . $identifier, $imageResizer->getConfig(), now()->addDay());

        $this->assertSame(
            ['width' => 200, 'height' => 150],
            ImageResizer::filterGetDimensions($resizerUrl),
            'A symlinked source must resolve to the same file the resizer would read.'
        );
    }

    /**
     * Changing the source invalidates the cached dimensions
     *
     * This is the invariant the whole cache lifetime rests on. The identifier is an HMAC
     * over the resized path, which hashes the configuration, which carries the source's
     * mtime -- so a changed source produces a different identifier and therefore a
     * different cache key. Without it, dropping the eviction in fromIdentifier() would
     * serve a stale size indefinitely.
     *
     * @return void
     */
    public function testSourceChangeInvalidatesCachedDimensions()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        Config::set('cms.storage.resized', [
            'disk'   => 'test_local',
            'folder' => 'resized',
            'path'   => '/storage/resized',
        ]);
        ImageResizer::flushAvailableSources();

        $url = $this->createMediaImage('changing.png', 400, 300);
        $path = storage_path('app/media/changing.png');

        $first = new ImageResizer($url, 200, 150);
        $firstUrl = $first->getResizerUrl();
        $firstIdentifier = $first->getIdentifier();

        $this->assertSame(
            ['width' => 200, 'height' => 150],
            ImageResizer::filterGetDimensions($firstUrl),
            'A 400x300 source asked for 200x150 resolves to 200x150.'
        );

        // A repeated read has to come from the cache and agree
        $this->assertSame(
            ['width' => 200, 'height' => 150],
            ImageResizer::filterGetDimensions($firstUrl),
            'A repeated read returns the same answer.'
        );

        // Replace the source with a different shape. The mtime is moved forward
        // explicitly because the timestamp is second-granularity on many filesystems, and
        // a rewrite inside the same second would legitimately keep the same identifier.
        //
        // Square, deliberately. A replacement sharing the original's aspect ratio would
        // make this assertion vacuous: auto mode preserves the ratio, so 600x450 against
        // a 400x300 original predicts exactly what the original predicted, and the test
        // would pass whether or not anything was recomputed.
        $replacement = imagecreatetruecolor(500, 500);
        imagefill($replacement, 0, 0, imagecolorallocate($replacement, 200, 40, 40));
        imagepng($replacement, $path);
        imagedestroy($replacement);
        touch($path, time() + 10);

        $second = new ImageResizer($url, 200, 150);

        $this->assertNotSame(
            $firstIdentifier,
            $second->getIdentifier(),
            'A changed source must produce a different identifier, or the cache cannot expire.'
        );

        // No arithmetic assertion here, deliberately. For a media source a changed mtime
        // produces a different identifier, so the second read hits a different cache key
        // and could not have served a stale entry even in principle -- an assertion about
        // recomputation would pass unconditionally. The staleness that can actually happen
        // needs the identifier to stay put while the source moves, which is the filemodel
        // case, covered by testSourceCacheRevalidatesWhenTheConfigurationMtimeMoves.
    }

    /**
     * The source entry re-validates when the configuration's mtime moves under a fixed key
     *
     * This is the case the identifier cannot catch. For a filemodel source the resized
     * path is the thumb filename -- attachment id and requested bounds, nothing about the
     * file's contents -- so the identifier does not move when the bytes do, and a cached
     * source entry would otherwise be trusted for ever. Here the situation is simulated
     * directly: the key is held fixed and the cached configuration is given a newer mtime,
     * which is exactly what a re-running storeConfig() would do.
     *
     * @return void
     */
    public function testSourceCacheRevalidatesWhenTheConfigurationMtimeMoves()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->setUpStorage();
        Config::set('cms.storage.resized', [
            'disk'   => 'test_local',
            'folder' => 'resized',
            'path'   => '/storage/resized',
        ]);
        ImageResizer::flushAvailableSources();

        $url = $this->createMediaImage('revalidate.png', 400, 300);
        $imageResizer = new ImageResizer($url, 200, 150);
        $identifier = $imageResizer->getIdentifier();
        $cacheKey = ImageResizer::CACHE_PREFIX . $identifier;
        $resizerUrl = $imageResizer->getResizerUrl();

        $this->assertSame(
            ['width' => 200, 'height' => 150],
            ImageResizer::filterGetDimensions($resizerUrl),
            'A 400x300 source asked for 200x150 resolves to 200x150.'
        );

        // Replace the file with a square one, and refresh the configuration's mtime
        // without moving the identifier.
        $path = storage_path('app/media/revalidate.png');
        $replacement = imagecreatetruecolor(500, 500);
        imagefill($replacement, 0, 0, imagecolorallocate($replacement, 200, 40, 40));
        imagepng($replacement, $path);
        imagedestroy($replacement);

        $config = Cache::get($cacheKey);
        $this->assertIsArray($config);
        $config['image']['mtime'] = time() + 120;
        Cache::put($cacheKey, $config, now()->addDay());

        // The key is unchanged, so this is the same entry. Only a source re-read can turn
        // the answer from 200x150 into 200x200.
        $this->assertSame(
            ['width' => 200, 'height' => 200],
            ImageResizer::filterGetDimensions($resizerUrl),
            'A source entry must be discarded when the configuration reports a newer mtime.'
        );
    }
    /**
     * An unrecognised mode falls back to the auto result, and says so
     *
     * This is the one branch where the prediction is knowingly a guess: a custom mode
     * means a custom resizer, and no amount of arithmetic recovers its output. The
     * parity sweep cannot cover it, because it compares against a resizer that would
     * throw on an unknown mode, so the fallback needs its own test. It is asserted
     * rather than assumed because the fallback is the only place the class reports a
     * size it cannot justify, and a silent change to a wrong number is exactly the kind
     * of guess this documents.
     *
     * @return void
     */
    public function testUnrecognisedModeFallsBackToAuto()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        // A non-square source, so auto and the other modes do not agree
        $sourceWidth = 400;
        $sourceHeight = 300;

        foreach ([[200, 150], [0, 150], [200, 0], [1, 0]] as $bounds) {
            [$reqWidth, $reqHeight] = $bounds;

            $this->assertSame(
                $this->predictDimensions($sourceWidth, $sourceHeight, $reqWidth, $reqHeight, 'auto'),
                $this->predictDimensions($sourceWidth, $sourceHeight, $reqWidth, $reqHeight, 'no-such-mode'),
                "An unrecognised mode must fall back to auto for {$reqWidth}x{$reqHeight}."
            );
        }

        // And the fallback is a real answer rather than a refusal: on this source and
        // these bounds the auto result is a positive size, not the 0x0 used to mean
        // "the resizer would refuse".
        $fallback = $this->predictDimensions($sourceWidth, $sourceHeight, 200, 150, 'no-such-mode');
        $this->assertGreaterThan(0, $fallback['width']);
        $this->assertGreaterThan(0, $fallback['height']);
    }
    /**
     * Unidentifiable input raises; only a resizer URL reports unknown
     *
     * Two different failures that look alike from the outside. A resizer URL has no
     * file behind it until the browser asks for one, so reporting unknown is the only
     * honest answer. A path that does not resolve to an image at all is a broken
     * template, and it used to raise -- a signal worth keeping, because a silently
     * rendered width="0" is much harder to notice than an exception.
     *
     * @return void
     */
    public function testUnidentifiableInputStillRaises()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $this->expectException(SystemException::class);
        $this->expectExceptionMessageMatches('/^Unable to process the provided image/');

        ImageResizer::filterGetDimensions('/plugins/database/tester/assets/images/MISSING.png');
    }

    /**
     * A resizer URL with no cached configuration reports unknown rather than raising
     *
     * @return void
     */
    public function testResizerUrlWithoutConfigurationReportsUnknown()
    {
        if (!in_array('Cms', Config::get('cms.loadModules', []))) {
            $this->markTestSkipped('The CMS module is not active.');
        }

        $identifier = str_repeat('a', 40);
        Cache::forget(ImageResizer::CACHE_PREFIX . $identifier);

        $this->assertSame(
            ['width' => 0, 'height' => 0],
            ImageResizer::filterGetDimensions('/resizer/' . $identifier . '/%252Fstorage%252Fapp%252Fresized%252Fnope.png'),
            'A resizer URL with nothing cached is unknown, not an error.'
        );
    }
    /**
     * Write a JPEG carrying an EXIF orientation tag
     *
     * The APP1 segment is assembled by hand: a little-endian TIFF header followed by a
     * single IFD0 entry for tag 0x0112, spliced in after the SOI marker.
     */
    protected function createRotatedJpeg(string $name, int $width, int $height, int $orientation): string
    {
        $directory = storage_path('app/media');

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 20, 60, 110));
        ob_start();
        imagejpeg($image, null, 92);
        $jpeg = ob_get_clean();
        imagedestroy($image);

        $tiff = "II" . "\x2A\x00"
            . "\x08\x00\x00\x00"
            . "\x01\x00"
            . "\x12\x01"
            . "\x03\x00"
            . "\x01\x00\x00\x00"
            . pack('v', $orientation) . "\x00\x00"
            . "\x00\x00\x00\x00";

        $payload = "Exif\x00\x00" . $tiff;
        $app1 = "\xFF\xE1" . pack('n', strlen($payload) + 2) . $payload;

        $path = $directory . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, substr($jpeg, 0, 2) . $app1 . substr($jpeg, 2));

        return Config::get('cms.storage.media.path') . '/' . $name;
    }

    /**
     * Assert that the spliced EXIF segment is where it should be and says what we meant
     *
     * Read back without the exif extension, so the fixture itself is proved well formed
     * on every platform even where the orientation cannot be exercised.
     */
    protected function assertExifSegmentIsWellFormed(string $url, int $expectedOrientation, int $width, int $height): void
    {
        $path = storage_path('app/media') . '/' . basename($url);
        $raw = file_get_contents($path);

        $this->assertSame("\xFF\xD8\xFF\xE1", substr($raw, 0, 4), 'SOI followed by an APP1 marker.');

        $declared = unpack('n', substr($raw, 4, 2))[1];
        $this->assertSame(substr($raw, 6, 6), "Exif\x00\x00", 'The segment identifies itself as Exif.');

        // The payload is "Exif\0\0" followed by the TIFF block, so the header starts
        // six bytes into the payload rather than at its first byte.
        $tiff = substr($raw, 6 + 6, $declared - 2 - 6);
        $this->assertSame('II', substr($tiff, 0, 2), 'Little-endian TIFF header.');
        $this->assertSame(42, unpack('v', substr($tiff, 2, 2))[1], 'TIFF magic number.');

        $ifdOffset = unpack('V', substr($tiff, 4, 4))[1];
        $this->assertSame(0x0112, unpack('v', substr($tiff, $ifdOffset + 2, 2))[1], 'The first IFD0 tag is Orientation.');
        $this->assertSame($expectedOrientation, unpack('v', substr($tiff, $ifdOffset + 10, 2))[1], 'The orientation value round-trips.');

        // The JPEG data after the segment has to still be decodable at the stored size
        $remainder = substr($raw, 2 + 2 + $declared);
        $size = getimagesizefromstring("\xFF\xD8" . $remainder);
        $this->assertNotFalse($size, 'The splice did not corrupt the image data.');
        $this->assertSame([$width, $height], [$size[0], $size[1]]);
    }

    /**
     * Place a generated image on the media disk and return its public URL
     */
    protected function createMediaImage(string $name, int $width, int $height): string
    {
        $directory = storage_path('app/media');

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 40, 80, 120));
        imagepng($image, $directory . DIRECTORY_SEPARATOR . $name);
        imagedestroy($image);

        return Config::get('cms.storage.media.path') . '/' . $name;
    }

    /**
     * Generate the source images used by the parity test
     *
     * @return array Absolute paths to the generated fixtures
     */
    protected function createParityFixtures(): array
    {
        $directory = storage_path('temp/resizer-parity');

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Sizes are chosen for the ratio, not the resolution, since the parity
        // assertion only depends on the ratio. The near-square case is the sharpest
        // one for the round-versus-truncate divergence.
        $sizes = [
            'landscape-101x64.png' => [101, 64],
            'portrait-64x101.png'  => [64, 101],
            'nearsquare-101x97.png' => [101, 97],
            'extreme-3x7.png'      => [3, 7],
        ];

        $fixtures = [];

        foreach ($sizes as $name => $size) {
            $path = $directory . DIRECTORY_SEPARATOR . $name;
            $image = imagecreatetruecolor($size[0], $size[1]);
            imagefill($image, 0, 0, imagecolorallocate($image, 40, 80, 120));
            imagepng($image, $path);
            imagedestroy($image);
            $fixtures[] = $path;
        }

        return $fixtures;
    }

    /**
     * Run the real resizer and measure what it produced
     *
     * @return array|null ['width' => int, 'height' => int], or null if the resizer refused
     */
    protected function resizeAndMeasure(string $path, int $width, int $height, array $options): ?array
    {
        try {
            $resizer = DefaultResizer::open($path)->resize($width, $height, $options);
        } catch (Throwable $ex) {
            return null;
        }

        $property = new ReflectionProperty($resizer, 'image');
        $property->setAccessible(true);
        $image = $property->getValue($resizer);

        if (!$image instanceof GdImage) {
            return null;
        }

        return ['width' => imagesx($image), 'height' => imagesy($image)];
    }

    /**
     * Ask the resizer class to predict the dimensions it will produce
     *
     * @return array ['width' => int, 'height' => int]
     */
    protected function predictDimensions(int $origWidth, int $origHeight, int $reqWidth, int $reqHeight, string $mode): array
    {
        $method = new ReflectionMethod(ImageResizer::class, 'calculateResizedDimensions');
        $method->setAccessible(true);

        return $method->invoke(null, $origWidth, $origHeight, $reqWidth, $reqHeight, $mode);
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

        // Anything this class creates outside of app/media has to go too, or the
        // rmdir() below fails and takes an unrelated test class down with it.
        File::deleteDirectory(storage_path('app/resized'));
        File::deleteDirectory(storage_path('app/real-media'));

        // The media files go first. A symlink left behind by a failed test would
        // outlive this class and confuse whichever one ran next -- but removing it is
        // the step most likely to fail, and on Windows it is: unlink() cannot remove a
        // directory symlink there, so an uncaught throw here used to abort the rest of
        // the cleanup and leave this class's media files on disk for the next class to
        // count. Windows wants rmdir(), Linux wants unlink(), so try both and suppress
        // both rather than choosing wrong on either platform.
        foreach (glob(storage_path('app/media/*')) as $file) {
            @unlink($file);
        }

        $link = storage_path('app/linked-media');
        if (is_link($link) || file_exists($link)) {
            @unlink($link);
            if (is_link($link) || file_exists($link)) {
                @rmdir($link);
            }
        }

        @rmdir(storage_path('app/media'));
        @rmdir(storage_path('app'));
    }
}
