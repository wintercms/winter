<?php

namespace Cms\Tests\Controllers;

use ApplicationException;
use Backend\Tests\Fixtures\Models\UserFixture;
use Cms\Controllers\Themes;
use Illuminate\Http\Response;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * Theme import writes theme files, including their PHP code sections, so it belongs to
 * `cms.manage_themes`. The controller admits `cms.manage_theme_options` as well, which the
 * Publisher role holds, so the import handlers have to say which of the two they need.
 */
class ThemesPermissionTest extends PluginTestCase
{
    public function testThemeImportIsRefusedWithoutTheManageThemesPermission(): void
    {
        $this->actingAs((new UserFixture)->withPermission('cms.manage_theme_options', true));
        $controller = new Themes;

        foreach (['index_onLoadImportForm', 'index_onImport'] as $handler) {
            $response = $controller->$handler();

            $this->assertInstanceOf(Response::class, $response, $handler);
            $this->assertSame(403, $response->getStatusCode(), $handler);
        }
    }

    public function testThemeImportIsAllowedWithTheManageThemesPermission(): void
    {
        $this->actingAs((new UserFixture)->withPermission('cms.manage_themes', true));
        $controller = new Themes;

        // Past the permission check, the handler goes on to resolve the theme the request names,
        // and this request names none.
        $this->expectException(ApplicationException::class);
        $controller->index_onLoadImportForm();
    }
}
