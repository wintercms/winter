<?php

namespace Backend\Tests\Helpers;

use System\Tests\Bootstrap\TestCase;
use Backend\Helpers\Backend;
use Backend\Helpers\Exception\DecompileException;

class BackendHelperTest extends TestCase
{
    public function testDecompileAssets()
    {
        $backendHelper = new Backend;
        $assets = $backendHelper->decompileAsset('modules/backend/tests/fixtures/assets/compilation.js');

        $this->assertCount(2, $assets);
        $this->assertStringContainsString('file1.js', $assets[0]);
        $this->assertStringContainsString('file2.js', $assets[1]);
    }

    public function testDecompileMissingFile()
    {
        $this->expectException(DecompileException::class);

        $backendHelper = new Backend;
        $assets = $backendHelper->decompileAsset('modules/backend/tests/fixtures/assets/missing.js');
    }
}
