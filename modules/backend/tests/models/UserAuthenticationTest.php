<?php

namespace Backend\Tests\Models;

use Backend\Models\User;
use Backend\Models\UserRole;
use Backend\Models\UserThrottle;
use Backend\Facades\BackendAuth;
use Config;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Auth\AuthenticationException;
use Winter\Storm\Auth\AuthorizationException;
use Winter\Storm\Database\Model as Eloquent;

class UserAuthenticationTest extends PluginTestCase
{
    protected User $user;
    protected User $impersonator;
    protected User $impersonatee;

    public function setUp(): void
    {
        parent::setUp();

        Eloquent::unguarded(function () {
            $this->user = User::create([
                'first_name' => 'Login',
                'last_name' => 'User',
                'login' => 'loginuser',
                'email' => 'loginuser@test.com',
                'password' => 'Testing123!',
                'password_confirmation' => 'Testing123!',
                'permissions' => [],
                'is_activated' => true,
                'is_superuser' => false,
            ]);

            $this->impersonator = User::create([
                'first_name' => 'Imperson',
                'last_name' => 'Ator',
                'login' => 'impersonator',
                'email' => 'impersonator@test.com',
                'password' => 'Testing123!',
                'password_confirmation' => 'Testing123!',
                'permissions' => [
                    'backend.impersonate_users' => 1,
                    'backend.manage_users' => 1,
                ],
                'is_activated' => true,
                'is_superuser' => false,
            ]);

            $this->impersonatee = User::create([
                'first_name' => 'Imperson',
                'last_name' => 'Atee',
                'login' => 'impersonatee',
                'email' => 'impersonatee@test.com',
                'password' => 'Testing123!',
                'password_confirmation' => 'Testing123!',
                'permissions' => [
                    'backend.impersonate_users' => 1,
                    'test.secret' => 1,
                ],
                'is_activated' => true,
                'is_superuser' => false,
            ]);
        });
    }

    //
    // Login
    //

    public function testAuthenticateLogsTheUserIn()
    {
        $user = BackendAuth::authenticate([
            'login' => 'loginuser',
            'password' => 'Testing123!',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($this->user->id, $user->id);
        $this->assertTrue(BackendAuth::check());
        $this->assertFalse(BackendAuth::guest());
        $this->assertEquals($this->user->id, BackendAuth::getUser()->id);
        $this->assertNotNull(BackendAuth::getUser()->last_login);

        $persisted = session('admin_auth');
        $this->assertIsArray($persisted);
        $this->assertEquals($this->user->id, $persisted[0]);
    }

    public function testAuthenticateRejectsAnInvalidPassword()
    {
        try {
            BackendAuth::authenticate([
                'login' => 'loginuser',
                'password' => 'NotThePassword',
            ]);
            $this->fail('An invalid password should not authenticate');
        } catch (AuthenticationException $exception) {
            $this->assertStringContainsString('hashed credential', $exception->getMessage());
        }

        $this->assertFalse(BackendAuth::check());
        $this->assertEquals(1, (int) $this->user->throttle()->first()->attempts);
    }

    public function testAuthenticateRejectsAnUnknownLogin()
    {
        try {
            BackendAuth::authenticate([
                'login' => 'nobody',
                'password' => 'Testing123!',
            ]);
            $this->fail('An unknown login should not authenticate');
        } catch (AuthenticationException $exception) {
            $this->assertStringContainsString('A user was not found', $exception->getMessage());
        }

        $this->assertFalse(BackendAuth::check());
        $this->assertEquals(0, UserThrottle::count());
    }

    //
    // Throttling and banning
    //

    public function testThrottleSuspendsTheUserAfterTooManyAttempts()
    {
        Config::set('auth.throttle.attemptLimit', 3);

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                BackendAuth::authenticate([
                    'login' => 'loginuser',
                    'password' => 'NotThePassword',
                ]);
            } catch (AuthenticationException $exception) {
                // Expected, the credentials are invalid
            }
        }

        $throttle = $this->user->throttle()->first();
        $this->assertTrue((bool) $throttle->is_suspended);
        $this->assertTrue($this->user->isSuspended());

        try {
            BackendAuth::authenticate([
                'login' => 'loginuser',
                'password' => 'Testing123!',
            ]);
            $this->fail('A suspended user should not be able to authenticate');
        } catch (AuthenticationException $exception) {
            $this->assertStringContainsString('has been suspended', $exception->getMessage());
        }

        $this->assertFalse(BackendAuth::check());
    }

    public function testSuccessfulLoginClearsFailedAttempts()
    {
        try {
            BackendAuth::authenticate([
                'login' => 'loginuser',
                'password' => 'NotThePassword',
            ]);
        } catch (AuthenticationException $exception) {
            // Expected, the credentials are invalid
        }

        $this->assertEquals(1, (int) $this->user->throttle()->first()->attempts);

        BackendAuth::authenticate([
            'login' => 'loginuser',
            'password' => 'Testing123!',
        ]);

        $throttle = $this->user->throttle()->first();
        $this->assertTrue(BackendAuth::check());
        $this->assertEquals(0, (int) $throttle->attempts);
        $this->assertFalse((bool) $throttle->is_suspended);
    }

    public function testBannedUserCannotAuthenticate()
    {
        BackendAuth::findThrottleByUserId($this->user->id)->ban();

        try {
            BackendAuth::authenticate([
                'login' => 'loginuser',
                'password' => 'Testing123!',
            ]);
            $this->fail('A banned user should not be able to authenticate');
        } catch (AuthenticationException $exception) {
            $this->assertStringContainsString('has been banned', $exception->getMessage());
        }

        $this->assertFalse(BackendAuth::check());
    }

    //
    // Logout
    //

    public function testLogoutDestroysTheSession()
    {
        BackendAuth::authenticate([
            'login' => 'loginuser',
            'password' => 'Testing123!',
        ]);

        $this->assertTrue(BackendAuth::check());
        $sessionId = session()->getId();

        BackendAuth::logout();

        $this->assertFalse(BackendAuth::check());
        $this->assertNull(BackendAuth::getUser());
        $this->assertNull(session('admin_auth'));
        $this->assertNotEquals($sessionId, session()->getId());
    }

    //
    // Account state
    //

    public function testSoftDeletedUserCannotAuthenticate()
    {
        $this->user->delete();

        try {
            BackendAuth::authenticate([
                'login' => 'loginuser',
                'password' => 'Testing123!',
            ]);
            $this->fail('A soft deleted user should not be able to authenticate');
        } catch (AuthenticationException $exception) {
            $this->assertStringContainsString('A user was not found', $exception->getMessage());
        }

        $this->assertFalse(BackendAuth::check());
    }

    public function testInactiveUserCannotLoginWhenActivationIsRequired()
    {
        $this->user->is_activated = false;
        $this->user->save();

        $manager = StrictActivationAuthManager::instance();

        try {
            $manager->login($this->user);
            $this->fail('An inactive user should not be able to login when activation is required');
        } catch (AuthenticationException $exception) {
            $this->assertStringContainsString('not activated', $exception->getMessage());
        }

        $this->assertFalse($manager->check());

        StrictActivationAuthManager::forgetInstance();
    }

    public function testBackendAuthDoesNotRequireActivation()
    {
        $this->user->is_activated = false;
        $this->user->save();

        BackendAuth::authenticate([
            'login' => 'loginuser',
            'password' => 'Testing123!',
        ]);

        $this->assertTrue(BackendAuth::check());
        $this->assertEquals($this->user->id, BackendAuth::getUser()->id);
    }

    //
    // Impersonation
    //

    public function testImpersonationSwitchesTheUserAndFiltersPermissions()
    {
        BackendAuth::authenticate([
            'login' => 'impersonator',
            'password' => 'Testing123!',
        ]);

        BackendAuth::impersonate($this->impersonatee);

        $this->assertTrue(BackendAuth::isImpersonator());
        $this->assertEquals($this->impersonatee->id, BackendAuth::getUser()->id);
        $this->assertEquals($this->impersonator->id, BackendAuth::getRealUser()->id);

        // Permissions the impersonator also has are kept
        $this->assertTrue(BackendAuth::getUser()->hasAccess('backend.impersonate_users'));

        // Permissions the impersonator does not have are filtered out
        $this->assertFalse(BackendAuth::getUser()->hasAccess('test.secret'));
    }

    public function testStopImpersonateRestoresTheImpersonator()
    {
        BackendAuth::authenticate([
            'login' => 'impersonator',
            'password' => 'Testing123!',
        ]);

        BackendAuth::impersonate($this->impersonatee);
        BackendAuth::stopImpersonate();

        $this->assertFalse(BackendAuth::isImpersonator());
        $this->assertEquals($this->impersonator->id, BackendAuth::getUser()->id);
        $this->assertEquals($this->impersonator->id, BackendAuth::getRealUser()->id);
        $this->assertTrue(BackendAuth::getUser()->hasAccess('backend.manage_users'));
    }

    public function testCannotImpersonateSuperuser()
    {
        Eloquent::unguarded(function () {
            $this->impersonatee->is_superuser = true;
            $this->impersonatee->save();
        });

        BackendAuth::authenticate([
            'login' => 'impersonator',
            'password' => 'Testing123!',
        ]);

        $this->expectException(AuthorizationException::class);
        BackendAuth::impersonate($this->impersonatee);
    }

    public function testCannotImpersonateWithoutTheImpersonatePermission()
    {
        Eloquent::unguarded(function () {
            $this->impersonator->permissions = [];
            $this->impersonator->save();
        });

        BackendAuth::authenticate([
            'login' => 'impersonator',
            'password' => 'Testing123!',
        ]);

        $this->expectException(AuthorizationException::class);
        BackendAuth::impersonate($this->impersonatee);
    }

    //
    // Permissions
    //

    public function testRolePermissionsAreInherited()
    {
        $role = UserRole::create([
            'code' => 'test-role',
            'name' => 'Test Role',
            'description' => 'Role used by the authentication tests',
            'permissions' => [
                'backend.manage_users' => 1,
                'test.*' => 1,
            ],
        ]);

        Eloquent::unguarded(function () use ($role) {
            $this->user->role_id = $role->id;
            $this->user->permissions = ['test.own' => 1];
            $this->user->save();
        });

        $permissions = $this->user->getMergedPermissions();

        $this->assertArrayHasKey('backend.manage_users', $permissions);
        $this->assertArrayHasKey('test.own', $permissions);
        $this->assertTrue($this->user->hasAccess('backend.manage_users'));
        $this->assertTrue($this->user->hasAccess('test.own'));
        $this->assertTrue($this->user->hasAccess('test.something'));
        $this->assertTrue($this->user->hasAccess('test.missing'));
        $this->assertFalse($this->user->hasAccess('nothing.here'));
    }

    public function testSuperUserHasAccessToEverything()
    {
        Eloquent::unguarded(function () {
            $this->user->is_superuser = true;
            $this->user->save();
        });

        $this->assertTrue($this->user->isSuperUser());
        $this->assertTrue($this->user->hasAccess('nothing.here'));
        $this->assertTrue($this->user->hasAccess(['nothing.here', 'nothing.there']));
    }

    //
    // Registration
    //

    public function testRegisterCreatesAnActivatedUser()
    {
        $user = BackendAuth::register([
            'first_name' => 'New',
            'last_name' => 'User',
            'login' => 'newuser',
            'email' => 'newuser@test.com',
            'password' => 'Testing123!',
            'password_confirmation' => 'Testing123!',
        ], true);

        $this->assertEquals('newuser', $user->login);
        $this->assertTrue(BackendAuth::hasUser());
        $this->assertEquals($user->id, BackendAuth::getUser()->id);

        $stored = User::where('login', 'newuser')->first();
        $this->assertNotNull($stored);
        $this->assertTrue((bool) $stored->is_activated);
    }
}

/**
 * Authentication manager that requires users to be activated, mirroring the default for the base
 * manager. The backend manager sets `$requireActivation` to false so administrators can log in.
 */
class StrictActivationAuthManager extends \Backend\Classes\AuthManager
{
    protected $requireActivation = true;

    protected $sessionKey = 'strict_auth';
}
