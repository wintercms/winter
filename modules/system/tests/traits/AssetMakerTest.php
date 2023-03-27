<?php

namespace System\Tests\Traits;

use System\Tests\Bootstrap\TestCase;
use System\Traits\AssetMaker;
use System\Traits\ViewMaker;

class AssetMakerStub
{
    use AssetMaker;
    use ViewMaker; // Needed for guessViewPath(), which is used to set default assetPath
}

class AssetMakerTest extends TestCase
{
    private $stub;

    public function setUp() : void
    {
        $this->createApplication();
        $this->stub = new AssetMakerStub();
    }

    //
    // Tests
    //

    public function testGetLocalPath()
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

    public function testGetAssetPath()
    {
        $assetPath = 'my/path/assets';

        // assetPath is ignored since we use pathSymbol for plugins
        $path = $this->stub->getAssetPath('$/author/plugin/assets/js/myAsset.js', $assetPath);
        $this->assertEquals('http://localhost/plugins/author/plugin/assets/js/myAsset.js', $path);

        // assetPath is ignored since we use pathSymbol for theme
        $path = $this->stub->getAssetPath('#/mytheme/assets/js/myAsset.js', $assetPath);
        $this->assertEquals('http://localhost/themes/mytheme/assets/js/myAsset.js', $path);

        // assetPath is ignored since we use pathSymbol for app root
        $path = $this->stub->getAssetPath('~/plugins/author/plugin/assets/js/myAsset.js', $assetPath);
        $this->assertEquals('http://localhost/plugins/author/plugin/assets/js/myAsset.js', $path);

        // assetPath is used since we use a relative path without pathSymbol
        $path = $this->stub->getAssetPath('js/myAsset.js', $assetPath);
        $this->assertEquals('http://localhost/' . $assetPath . '/js/myAsset.js', $path);

        // assetPath is ignored since we use an absolute path
        $path = $this->stub->getAssetPath('/js/myAsset.js', $assetPath);
        $this->assertEquals('http://localhost/js/myAsset.js', $path);
    }
}
