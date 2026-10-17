<?php

namespace Backend\Tests\Controllers;

use Backend\Classes\AuthManager;
use Backend\Facades\BackendAuth;
use Backend\Models\User;
use Illuminate\Database\Eloquent\Model;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Support\Facades\Config;

class ImpersonationPermissionTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);
    }

    protected function makeUser(string $login, array $permissions): User
    {
        Model::unguard();
        $user = User::create([
            'first_name' => ucfirst($login),
            'last_name' => 'Fixture',
            'login' => $login,
            'email' => $login . '@test.test',
            'password' => 'DummyPassword123',
            'password_confirmation' => 'DummyPassword123',
            'is_activated' => true,
            'is_superuser' => false,
            'permissions' => $permissions,
        ]);
        Model::reguard();

        return $user;
    }

    public function testDeletedImpersonatorCannotExpandTargetPermissions(): void
    {
        $original = $this->makeUser('original', [
            'backend.manage_users' => 1,
            'backend.impersonate_users' => 1,
        ]);
        $target = $this->makeUser('target', [
            'backend.manage_users' => 1,
            'system.access_logs' => 1,
        ]);

        BackendAuth::login($original, false);

        $impersonate = $this->post(
            'backend/backend/users/update/' . $target->getKey(),
            [],
            [
                'X-WINTER-REQUEST-HANDLER' => 'onImpersonateUser',
                'X-Requested-With' => 'XMLHttpRequest',
            ]
        );
        $this->assertNotSame(403, $impersonate->getStatusCode());
        $this->assertSame($target->getKey(), BackendAuth::getUser()->getKey());
        $this->assertFalse(BackendAuth::getUser()->hasAccess('system.access_logs'));

        $delete = $this->post(
            'backend/backend/users/update/' . $original->getKey(),
            [],
            [
                'X-WINTER-REQUEST-HANDLER' => 'onDelete',
                'X-Requested-With' => 'XMLHttpRequest',
            ]
        );
        $this->assertNotSame(403, $delete->getStatusCode());
        $this->assertTrue(User::withTrashed()->findOrFail($original->getKey())->trashed());

        AuthManager::forgetInstance();
        $this->app->forgetInstance('backend.auth');
        BackendAuth::clearResolvedInstance('backend.auth');
        $auth = BackendAuth::getFacadeRoot();

        $freshTarget = $auth->getUser();
        $this->assertSame($target->getKey(), $freshTarget->getKey());
        $this->assertFalse($freshTarget->hasAccess('system.access_logs'));
        $this->assertFalse($freshTarget->hasAccess('system.access_logs'));
        $this->assertFalse($auth->isImpersonator());
        $this->assertNull($auth->getUser());
    }

    public function testDeletingImpersonatorDoesNotExpandCachedPermissionsInCurrentRequest(): void
    {
        $original = $this->makeUser('same-request-original', [
            'backend.manage_users' => 1,
            'backend.impersonate_users' => 1,
        ]);
        $target = $this->makeUser('same-request-target', [
            'backend.manage_users' => 1,
            'system.access_logs' => 1,
        ]);

        $auth = BackendAuth::getFacadeRoot();
        $auth->login($original, false);
        $auth->impersonate($target);

        $this->assertFalse($target->hasAccess('system.access_logs'));
        $this->assertTrue($original->delete());

        $this->assertFalse($target->hasAccess('system.access_logs'));
    }

    public function testReusedTargetCanStartExternalImpersonationAfterRevocation(): void
    {
        $original = $this->makeUser('reused-original', [
            'backend.manage_users' => 1,
            'backend.impersonate_users' => 1,
        ]);
        $target = $this->makeUser('reused-target', [
            'backend.manage_users' => 1,
            'system.access_logs' => 1,
        ]);

        $auth = BackendAuth::getFacadeRoot();
        $auth->login($original, false);
        $auth->impersonate($target);
        $this->assertTrue($original->delete());

        AuthManager::forgetInstance();
        $this->app->forgetInstance('backend.auth');
        BackendAuth::clearResolvedInstance('backend.auth');
        $auth = BackendAuth::getFacadeRoot();
        $reusedTarget = $auth->getUser();

        $this->assertSame($target->getKey(), $reusedTarget->getKey());
        $this->assertFalse($reusedTarget->hasAccess('system.access_logs'));
        $this->assertFalse($auth->isImpersonator());

        $reusedTarget->bindEvent('model.auth.beforeImpersonate', function ($impersonator) {
            return $impersonator === false;
        });
        $auth->impersonate($reusedTarget);

        $this->assertTrue($auth->isExternalImpersonation());
        $this->assertTrue($reusedTarget->hasAccess('system.access_logs'));
    }

    public function testExplicitExternalImpersonationRemainsSupported(): void
    {
        $target = $this->makeUser('external-target', ['system.access_logs' => 1]);
        $target->bindEvent('model.auth.beforeImpersonate', function ($impersonator) {
            return $impersonator === false;
        });

        $auth = BackendAuth::getFacadeRoot();
        $auth->impersonate($target);

        $this->assertTrue($auth->isImpersonator());
        $this->assertTrue($auth->isExternalImpersonation());
        $this->assertTrue($auth->getUser()->hasAccess('system.access_logs'));

        $auth->stopImpersonate();

        $this->assertFalse($auth->isImpersonator());
        $this->assertNull($auth->getUser());
    }
}
