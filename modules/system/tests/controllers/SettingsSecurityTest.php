<?php

namespace System\Tests\Controllers;

use Backend\Models\User;
use Backend\Models\UserRole;
use System\Classes\SettingsManager;
use System\Models\MailSetting;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;

/**
 * The Settings controller declares no $requiredPermissions, so a setting item's own
 * `permissions` are the only thing protecting it. SettingsManager filters those items out as
 * it loads them, but that filtering is skipped when items are registered directly on the
 * manager instead of through registerCallback() -- loadItems() never runs, so nothing is
 * filtered. findSettingItem() checks the item itself so the controller does not depend on the
 * manager having taken that path.
 */
class SettingsSecurityTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        SettingsManager::forgetInstance();

        // Settings models are cached both in a static instance map and in the query cache,
        // and neither is reset between tests in the same process.
        MailSetting::clearInternalCache();
        \Cache::flush();
    }

    protected function makeUser(string $login, array $permissions): User
    {
        Model::unguard();
        $role = UserRole::create([
            'name' => $login,
            'code' => $login,
            'permissions' => $permissions,
        ]);
        $user = User::create([
            'first_name' => ucfirst($login),
            'last_name' => 'User',
            'login' => $login,
            'email' => "{$login}@test.test",
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'role_id' => $role->id,
        ]);
        Model::reguard();

        return $user;
    }

    /** registerBackendSettings() is gated behind runningInBackend(), which is false here. */
    protected function registerCoreSettingItems(): void
    {
        $provider = new \System\ServiceProvider($this->app);
        $method = new \ReflectionMethod($provider, 'registerBackendSettings');
        $method->setAccessible(true);
        $method->invoke($provider);
    }

    /** Registered directly on the instance, so loadItems() -- and its filtering -- never runs. */
    protected function registerUnfilteredSettingItem(): void
    {
        SettingsManager::instance()->registerSettingItems('Acme.Test', [
            'protected' => [
                'label' => 'Protected',
                'class' => MailSetting::class,
                'permissions' => ['system.manage_mail_settings'],
            ],
        ]);
    }

    protected function save(string $uri, string $address)
    {
        return $this->post('backend/system/settings/update/' . $uri, [
            'MailSetting' => ['send_mode' => 'smtp', 'smtp_address' => $address],
        ], [
            'X-WINTER-REQUEST-HANDLER' => 'onSave',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    public function testAnUnfilteredItemIsRefusedWithoutItsPermission(): void
    {
        $this->registerUnfilteredSettingItem();
        $this->actingAs($this->makeUser('peon', ['cms.manage_pages' => 1]));

        $this->save('acme/test/protected', 'attacker.example.com');

        MailSetting::clearInternalCache();
        $this->assertNotEquals('attacker.example.com', MailSetting::instance()->smtp_address);
    }

    public function testAnUnfilteredItemIsServedToAUserHoldingItsPermission(): void
    {
        $this->registerUnfilteredSettingItem();
        $this->actingAs($this->makeUser('mailadmin', ['system.manage_mail_settings' => 1]));

        $response = $this->save('acme/test/protected', 'legit.example.com');

        $this->assertEquals(200, $response->getStatusCode());
        MailSetting::clearInternalCache();
        $this->assertEquals('legit.example.com', MailSetting::instance()->smtp_address);
    }

    /** Nothing legitimate regressed on the normal registerCallback() path. */
    public function testCoreMailSettingsStillSaveForAPermittedUser(): void
    {
        $this->registerCoreSettingItems();
        $this->actingAs($this->makeUser('mailadmin2', ['system.manage_mail_settings' => 1]));

        $response = $this->save('winter/system/mail_settings', 'legit.example.com');

        $this->assertEquals(200, $response->getStatusCode());
        MailSetting::clearInternalCache();
        $this->assertEquals('legit.example.com', MailSetting::instance()->smtp_address);
    }

    public function testCoreMailSettingsAreRefusedForAnUnprivilegedUser(): void
    {
        $this->registerCoreSettingItems();
        $this->actingAs($this->makeUser('peon2', ['cms.manage_pages' => 1]));

        $this->save('winter/system/mail_settings', 'attacker.example.com');

        MailSetting::clearInternalCache();
        $this->assertNotEquals('attacker.example.com', MailSetting::instance()->smtp_address);
    }
}
