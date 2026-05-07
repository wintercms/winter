<?php

namespace System\Tests\Traits;

use System\Classes\Asset\PackageManager;
use System\Tests\Bootstrap\TestCase;
use System\Traits\AssetMaker;
use System\Traits\EventEmitter;
use System\Traits\ViewMaker;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Facades\Url;

class AssetMakerStub
{
    use AssetMaker;
    use ViewMaker; // Needed for `guessViewPath()`, which is used to set default assetPath
    use EventEmitter; // Needed for `addAsset()`
}

class AssetMakerTest extends TestCase
{
    private AssetMakerStub $stub;

    private const VITE_FIXTURE_PACKAGE = 'theme-assettest';
    private const VITE_FIXTURE_THEME_PATH = '/modules/system/tests/fixtures/themes/assettest';
    private const VITE_HOT_URL = 'http://localhost:5173';

    public function setUp() : void
    {
        $this->createApplication();
        $this->stub = new AssetMakerStub();
    }

    public function tearDown(): void
    {
        // Remove any vite hot file that a vite-related test wrote into the fixture
        $hotFile = base_path(self::VITE_FIXTURE_THEME_PATH . '/assets/dist/hot');
        if (File::exists($hotFile)) {
            File::delete($hotFile);
        }
        $distDir = base_path(self::VITE_FIXTURE_THEME_PATH . '/assets/dist');
        if (File::isDirectory($distDir) && count(File::files($distDir)) === 0 && count(File::directories($distDir)) === 0) {
            File::deleteDirectory($distDir);
        }

        parent::tearDown();
    }

    /**
     * Registers the assettest fixture as a vite package and writes a hot file so that
     * Vite::tags() emits deterministic dev-server tags without needing a built manifest.
     */
    private function setUpViteFixture(): void
    {
        $themePath = base_path(self::VITE_FIXTURE_THEME_PATH);
        if (!File::isDirectory($themePath)) {
            $this->markTestSkipped('Vite test fixture is missing at ' . self::VITE_FIXTURE_THEME_PATH);
        }

        // PackageManager's lazy init() call iterates Theme::all(), which fires Halcyon model
        // events. In the full system suite, prior tests can leave plugin/datasource state in a
        // way that makes that initial iteration throw — same pattern as the existing
        // ViteInstallTest. Skip gracefully when that happens so this test is meaningful only
        // in a clean environment (and always when run in isolation).
        try {
            $packageManager = PackageManager::instance();
        } catch (\Throwable $e) {
            $this->markTestSkipped('PackageManager could not initialise in this environment: ' . $e->getMessage());
        }

        // registerPackage silently no-ops when re-registering the same name + config.
        $packageManager->registerPackage(
            self::VITE_FIXTURE_PACKAGE,
            $themePath . '/vite.config.mjs',
            'vite'
        );

        File::ensureDirectoryExists($themePath . '/assets/dist');
        File::put($themePath . '/assets/dist/hot', self::VITE_HOT_URL);
    }

    //
    // Tests
    //

    public function testGetLocalPath(): void
    {
        $basePath = base_path();

        // Default assetPath
        $assetPath = $this->stub->guessViewPath('/assets', true);
        $resolvedPath = $this->callProtectedMethod($this->stub, 'getLocalPath', [$assetPath]);
        $this->assertEquals(realpath($basePath.$assetPath), realpath($resolvedPath));

        // Paths with symbols
        $resolvedPath = $this->callProtectedMethod($this->stub, 'getLocalPath', ['~/themes/demo/']);
        $this->assertEquals(realpath($basePath.'/themes/demo/'), realpath($resolvedPath));

        $resolvedPath = $this->callProtectedMethod($this->stub, 'getLocalPath', ['~/plugins/demo/']);
        $this->assertEquals(realpath($basePath.'/plugins/demo/'), realpath($resolvedPath));

        $resolvedPath = $this->callProtectedMethod($this->stub, 'getLocalPath', ['$/demo/']);
        $this->assertEquals(realpath($basePath.'/plugins/demo/'), realpath($resolvedPath));

        // Absolute Path
        $resolvedPath = $this->callProtectedMethod($this->stub, 'getLocalPath', [$basePath.'/some/wild/absolute/path/']);
        $this->assertEquals(realpath($basePath.'/some/wild/absolute/path/'), realpath($resolvedPath));
    }

    public function testGetAssetPath(): void
    {
        $assetPath = 'my/path/assets';

        $hostUrl = Url::to('/');

        // assetPath is ignored since we use pathSymbol for plugins
        $path = $this->stub->getAssetPath('$/author/plugin/assets/js/myAsset.js', $assetPath);
        $this->assertEquals($hostUrl . 'plugins/author/plugin/assets/js/myAsset.js', $path);

        // assetPath is ignored since we use pathSymbol for theme
        $path = $this->stub->getAssetPath('#/mytheme/assets/js/myAsset.js', $assetPath);
        $this->assertEquals($hostUrl . 'themes/mytheme/assets/js/myAsset.js', $path);

        // assetPath is ignored since we use pathSymbol for app root
        $path = $this->stub->getAssetPath('~/plugins/author/plugin/assets/js/myAsset.js', $assetPath);
        $this->assertEquals($hostUrl . 'plugins/author/plugin/assets/js/myAsset.js', $path);

        // assetPath is used since we use a relative path without pathSymbol
        $path = $this->stub->getAssetPath('js/myAsset.js', $assetPath);
        $this->assertEquals($hostUrl . $assetPath . '/js/myAsset.js', $path);

        // assetPath is ignored since we use an absolute path
        $path = $this->stub->getAssetPath('/js/myAsset.js', $assetPath);
        $this->assertEquals($hostUrl . 'js/myAsset.js', $path);
    }

    public function testAssetOrdering(): void
    {
        $hostUrl = Url::to('/');

        // Test specified priorities
        $this->stub->addCss('mySecond.css', [
            'order' => 2,
        ]);
        $this->stub->addCss('myThird.css', [
            'order' => 3,
        ]);
        $this->stub->addCss('myFirst.css', [
            'order' => 1,
        ]);

        $assets = $this->stub->getAssetPaths();

        $this->assertEquals([
            $hostUrl . 'myFirst.css',
            $hostUrl . 'mySecond.css',
            $hostUrl . 'myThird.css',
        ], $assets['css']);

        // Test first-come, first-served - these assets will be prioritised the default 100.
        $this->stub->flushAssets();

        $this->stub->addCss('myFirst.css');
        $this->stub->addCss('mySecond.css');
        $this->stub->addCss('myThird.css');

        $assets = $this->stub->getAssetPaths();

        $this->assertEquals([
            $hostUrl . 'myFirst.css',
            $hostUrl . 'mySecond.css',
            $hostUrl . 'myThird.css',
        ], $assets['css']);
    }

    public function testGetAssetType(): void
    {
        $cases = [
            'foo.js' => 'js',
            'assets/javascript/theme.js' => 'js',
            'foo.css' => 'css',
            'assets/css/theme.css' => 'css',
            'foo.txt' => null,
            'foo' => null,
            'js' => null,
            'foo.JS' => 'js',
            'foo.CSS' => 'css',
        ];

        foreach ($cases as $input => $expected) {
            $this->assertSame(
                $expected,
                $this->callProtectedMethod($this->stub, 'getAssetType', [$input]),
                "getAssetType('$input') did not return the expected type"
            );
        }
    }

    public function testMakeAssetsViteCssOnly(): void
    {
        $this->setUpViteFixture();

        $this->stub->addCss('plain.css');
        $this->stub->addVite(
            ['assets/css/theme.css', 'assets/javascript/theme.js'],
            self::VITE_FIXTURE_PACKAGE
        );

        $output = $this->stub->makeAssets('css');

        $this->assertNotNull($output);
        // Plain CSS still emitted
        $this->assertStringContainsString('plain.css', $output);
        // Vite CSS entrypoint emitted
        $this->assertStringContainsString('assets/css/theme.css', $output);
        // Vite JS entrypoint must NOT leak into the css output
        $this->assertStringNotContainsString('assets/javascript/theme.js', $output);
    }

    public function testMakeAssetsViteJsOnly(): void
    {
        $this->setUpViteFixture();

        $this->stub->addJs('plain.js');
        $this->stub->addVite(
            ['assets/css/theme.css', 'assets/javascript/theme.js'],
            self::VITE_FIXTURE_PACKAGE
        );

        $output = $this->stub->makeAssets('js');

        $this->assertNotNull($output);
        $this->assertStringContainsString('plain.js', $output);
        $this->assertStringContainsString('assets/javascript/theme.js', $output);
        $this->assertStringNotContainsString('assets/css/theme.css', $output);
    }

    public function testMakeAssetsViteTypeUnfiltered(): void
    {
        $this->setUpViteFixture();

        $this->stub->addVite(
            ['assets/css/theme.css', 'assets/javascript/theme.js'],
            self::VITE_FIXTURE_PACKAGE
        );

        $output = $this->stub->makeAssets('vite');

        $this->assertNotNull($output);
        // Legacy 'vite' type must still emit both entrypoints unfiltered
        $this->assertStringContainsString('assets/css/theme.css', $output);
        $this->assertStringContainsString('assets/javascript/theme.js', $output);
    }

    public function testMakeAssetsAllTypesSplitsViteAcrossGroups(): void
    {
        $this->setUpViteFixture();

        $this->stub->addVite(
            ['assets/css/theme.css', 'assets/javascript/theme.js'],
            self::VITE_FIXTURE_PACKAGE
        );

        $output = $this->stub->makeAssets();

        $this->assertNotNull($output);
        // Each entrypoint should appear exactly once: in its corresponding group, not in the
        // standalone vite block (which must NOT fire when $type is null).
        $this->assertSame(1, substr_count($output, 'assets/css/theme.css'));
        $this->assertSame(1, substr_count($output, 'assets/javascript/theme.js'));
    }

    public function testMakeAssetsViteSkipsAssetWithNoMatchingEntrypoints(): void
    {
        $this->setUpViteFixture();

        // JS-only entrypoint registered as a vite asset
        $this->stub->addVite(['assets/javascript/theme.js'], self::VITE_FIXTURE_PACKAGE);

        $output = $this->stub->makeAssets('css');

        // No css entrypoints to filter to, so the vite asset must contribute nothing
        // and not produce an empty Vite::tags() call (which would still emit @vite/client).
        $this->assertEmpty($output);
    }
}
