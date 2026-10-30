<?php

namespace Cms\Tests\Controllers;

use Backend\Models\User;
use Cms\Classes\Theme;
use Cms\Controllers\ThemeOptions;
use Cms\Models\ThemeData;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Event;

/**
 * Theme Options confines holders of `cms.manage_theme_options` to the active theme, and did so by
 * interposing `getDirName()` in each inherited FormController handler it overrode. `update_onDelete`
 * was never overridden, so the behaviour's own handler ran and resolved the posted id against the
 * whole of `cms_theme_data`.
 *
 * The restriction now lives on the form query itself, so it applies to every handler the behaviour
 * serves rather than to the three this controller happens to redeclare.
 */
class ThemeOptionsSecurityTest extends PluginTestCase
{
    protected User $publisher;
    protected User $developer;
    protected ThemeData $activeThemeData;
    protected ThemeData $otherThemeData;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('app.debug', true);
        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);
        Config::set('cms.themesPath', '/modules/cms/tests/fixtures/themes');
        Config::set('cms.activeTheme', 'themedata');

        Event::flush('cms.theme.getActiveTheme');
        Theme::resetCache();

        Model::unguard();

        // Holds the lesser permission only: "Configure customization options for the active theme"
        $this->publisher = $this->makeUser('publisher', ['cms.manage_theme_options' => 1]);

        // Holds the permission that also allows managing themes other than the active one
        $this->developer = $this->makeUser('developer', [
            'cms.manage_theme_options' => 1,
            'cms.manage_themes' => 1,
        ]);

        $this->activeThemeData = ThemeData::create([
            'theme' => 'themedata',
            'data' => ['site_name' => 'Active theme'],
        ]);

        $this->otherThemeData = ThemeData::create([
            'theme' => 'test',
            'data' => ['site_name' => 'Another theme'],
        ]);

        Model::reguard();

        ThemeData::flushCache('themedata');
        ThemeData::flushCache('test');
    }

    public function tearDown(): void
    {
        ThemeData::flushCache('themedata');
        ThemeData::flushCache('test');

        parent::tearDown();
    }

    protected function makeUser(string $login, array $permissions): User
    {
        return User::create([
            'first_name' => ucfirst($login),
            'last_name' => 'User',
            'login' => $login,
            'email' => "{$login}@test.test",
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'permissions' => $permissions,
        ]);
    }

    /**
     * The reported request: the delete handler of the update action, pointed at a row belonging to a
     * theme this account may not manage.
     */
    public function testTheDeleteHandlerCannotReachAnotherThemesData(): void
    {
        $this->actingAs($this->publisher);

        $response = $this->post('backend/cms/themeoptions/update/' . $this->otherThemeData->getKey(), [], [
            'X-WINTER-REQUEST-HANDLER' => 'onDelete',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString(
            'Form record with an ID of ' . $this->otherThemeData->getKey() . ' could not be found',
            $response->getContent()
        );
        $this->assertNotNull(
            ThemeData::find($this->otherThemeData->getKey()),
            'The other theme\'s customization data was deleted'
        );
    }

    /**
     * The same account may still delete the data of the theme it is confined to, which is what
     * "Reset to default" on its own page already does.
     */
    public function testTheDeleteHandlerStillReachesTheActiveTheme(): void
    {
        $this->actingAs($this->publisher);

        $response = $this->post('backend/cms/themeoptions/update/' . $this->activeThemeData->getKey(), [], [
            'X-WINTER-REQUEST-HANDLER' => 'onDelete',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNull(ThemeData::find($this->activeThemeData->getKey()));
    }

    /**
     * A user who may manage every theme is not confined by the scope at all.
     */
    public function testAThemeManagerMayStillReachAnyThemesData(): void
    {
        $this->actingAs($this->developer);

        $response = $this->post('backend/cms/themeoptions/update/' . $this->otherThemeData->getKey(), [], [
            'X-WINTER-REQUEST-HANDLER' => 'onDelete',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNull(ThemeData::find($this->otherThemeData->getKey()));
    }

    /**
     * The scope is on the query every handler resolves through, not on a list of handler overrides.
     */
    public function testTheFormLookupIsScopedForTheLesserPermission(): void
    {
        $this->actingAs($this->publisher);
        $controller = new ThemeOptions;

        $this->assertEquals(
            $this->activeThemeData->getKey(),
            $controller->formFindModelObject($this->activeThemeData->getKey())->getKey()
        );

        $this->expectException(ApplicationException::class);
        $controller->formFindModelObject($this->otherThemeData->getKey());
    }

    /**
     * Nothing legitimate regressed: the sanctioned save handler still writes the active theme.
     */
    public function testTheActiveThemesOptionsCanStillBeSaved(): void
    {
        $this->actingAs($this->publisher);

        $response = $this->post('backend/cms/themeoptions/update', [
            'ThemeData' => ['site_name' => 'Renamed by publisher'],
        ], [
            'X-WINTER-REQUEST-HANDLER' => 'onSave',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            'Renamed by publisher',
            ThemeData::find($this->activeThemeData->getKey())->site_name
        );
    }
}
