<?php

namespace System\Tests\Controllers;

use Backend\Models\User;
use Backend\Models\UserRole;
use Cms\Models\MaintenanceSetting;
use System\Controllers\MailBrandSettings;
use System\Models\MailBrandSetting;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\DB;

/**
 * Mail brand customization is a single-record settings screen: it is reached through index(), which
 * calls the form behaviour's create() with the settings singleton. The behaviour's record-scoped
 * actions -- create, update, preview -- were routable all the same, and the update action's handlers
 * resolve a caller-supplied key through formFindModelObject(). Every settings model shares the
 * `system_settings` table, so that key reached another feature's settings row, guarded by another
 * permission.
 *
 * $guarded drops those actions from routing, the same remedy MyAccount carries for the same defect.
 */
class MailBrandSettingsSecurityTest extends PluginTestCase
{
    protected int $maintenanceSettingId;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('app.debug', true);
        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        MailBrandSetting::clearInternalCache();
        \Cache::flush();

        // Registered behind runningInBackend(), false under PHPUnit; without it the settings screen
        // cannot render and a 200 here would mean nothing.
        $this->invokeProviderMethod(new \Backend\ServiceProvider($this->app), 'registerBackendWidgets');
        $this->invokeProviderMethod(new \System\ServiceProvider($this->app), 'registerBackendSettings');

        // A settings row belonging to another feature, guarded by cms.manage_themes
        $maintenance = MaintenanceSetting::instance();
        $maintenance->is_enabled = true;
        $maintenance->save();

        $this->maintenanceSettingId = (int) DB::table('system_settings')
            ->where('item', 'cms_maintenance_settings')
            ->value('id');

        $this->actingAsMailManager();
    }

    public function tearDown(): void
    {
        MailBrandSetting::instance()->resetDefault();
        MailBrandSetting::clearInternalCache();
        \Cache::flush();

        parent::tearDown();
    }

    protected function invokeProviderMethod($provider, string $method): void
    {
        $reflection = new \ReflectionMethod($provider, $method);
        $reflection->setAccessible(true);
        $reflection->invoke($provider);
    }

    /**
     * Holds "Manage mail templates" and nothing else, as a custom role or the per-user permission
     * checkboxes on the Administrators form would grant it.
     */
    protected function actingAsMailManager(): void
    {
        Model::unguard();
        $role = UserRole::create([
            'name' => 'Mail manager',
            'code' => 'mail_manager',
            'permissions' => ['system.manage_mail_templates' => 1],
        ]);
        $user = User::create([
            'first_name' => 'Mail',
            'last_name' => 'Manager',
            'login' => 'mailmanager',
            'email' => 'mailmanager@test.test',
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'role_id' => $role->id,
        ]);
        Model::reguard();

        $this->actingAs($user);
    }

    /** index() calls them, but no URL may reach them. */
    public function testRecordScopedActionsAreNotRoutable(): void
    {
        $controller = new MailBrandSettings;

        foreach (['create', 'update', 'preview'] as $action) {
            $this->assertTrue($controller->methodExists($action), "Precondition: {$action} exists");
            $this->assertFalse($controller->actionExists($action), "{$action} must not be routable");
        }

        $this->assertTrue($controller->actionExists('index'), 'index must still route');
    }

    /** The reported request. */
    public function testTheDeleteHandlerCannotReachAnotherFeaturesSettings(): void
    {
        $response = $this->post('backend/system/mailbrandsettings/update/' . $this->maintenanceSettingId, [], [
            'X-WINTER-REQUEST-HANDLER' => 'onDelete',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(
            1,
            DB::table('system_settings')->where('id', $this->maintenanceSettingId)->count(),
            'Another feature\'s settings row was deleted'
        );
        $this->assertHandlerWasNotServed($response, 'onDelete');
    }

    public function testTheSaveHandlerCannotReachAnotherFeaturesSettings(): void
    {
        $response = $this->post('backend/system/mailbrandsettings/update/' . $this->maintenanceSettingId, [
            'MailBrandSetting' => ['body_bg' => '#abcdef'],
        ], [
            'X-WINTER-REQUEST-HANDLER' => 'onSave',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        // SettingsModel::beforeSave() stamps its own code onto whatever row it was handed, so a save
        // through this screen would retitle the maintenance row as the mail brand settings.
        $this->assertEquals(
            'cms_maintenance_settings',
            DB::table('system_settings')->where('id', $this->maintenanceSettingId)->value('item'),
            'Another feature\'s settings row was taken over'
        );
        $this->assertHandlerWasNotServed($response, 'onSave');
    }

    public function testTheRecordScopedActionsAreNotServed(): void
    {
        foreach (['create', 'update/' . $this->maintenanceSettingId, 'preview/' . $this->maintenanceSettingId] as $path) {
            $this->assertEquals(
                404,
                $this->get('backend/system/mailbrandsettings/' . $path)->getStatusCode(),
                "{$path} must not be served"
            );
        }
    }

    /**
     * With the action gone the handler has nowhere to be dispatched from, which is what the backend
     * reports rather than a permission error.
     */
    protected function assertHandlerWasNotServed($response, string $handler): void
    {
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString(
            "AJAX handler '{$handler}' was not found.",
            $response->getContent()
        );
    }

    /** Nothing legitimate regressed: the screen renders and still saves its own settings. */
    public function testTheMailBrandScreenStillWorks(): void
    {
        $this->assertEquals(200, $this->get('backend/system/mailbrandsettings')->getStatusCode());

        $response = $this->post('backend/system/mailbrandsettings', [
            'MailBrandSetting' => ['body_bg' => '#abcdef'],
        ], [
            'X-WINTER-REQUEST-HANDLER' => 'onSave',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        MailBrandSetting::clearInternalCache();
        $this->assertEquals('#abcdef', MailBrandSetting::get('body_bg'));
    }
}
