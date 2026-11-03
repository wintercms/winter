<?php

namespace Cms\Tests\Widgets;

use Backend\Tests\Fixtures\Models\UserFixture;
use Cms\Controllers\Index;
use Illuminate\Support\Facades\Request;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Exception\ApplicationException;

class AssetListPermissionTest extends PluginTestCase
{
    public function testOnUploadRejectsThemeMismatch(): void
    {
        $user = (new UserFixture)->withPermission('cms.manage_assets', true);
        $this->actingAs($user);
        $controller = new Index;

        $assetList = $controller->widget->assetList;
        $this->assertNotNull($assetList, 'assetList widget should be registered');

        // Simulate a request with a mismatched theme name
        Request::merge(['theme' => 'nonexistent-theme']);

        $this->expectException(ApplicationException::class);
        $assetList->onUpload();
    }
}
