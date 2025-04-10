<?php

namespace System\Tests\Traits;

use System\Tests\Bootstrap\TestCase;
use System\Traits\AssetMaker;
use System\Traits\EventEmitter;
use System\Traits\ViewMaker;
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

    public function setUp() : void
    {
        $this->createApplication();
        $this->stub = new AssetMakerStub();
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
}
