<?php

namespace Backend\Tests;

use Backend\Classes\BackendController;
use Backend\Models\User as BackendUser;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;

/**
 * Shared scaffolding for the tests that drive a backend controller or widget directly.
 */
abstract class BackendTestCase extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Controllers read their action from this static, which another test's routed
        // request will have left set; these are driven directly, with no page action.
        BackendController::$action = null;
        BackendController::$params = [];

        Model::unguard();
    }

    public function tearDown(): void
    {
        Model::reguard();

        parent::tearDown();
    }

    /**
     * Widgets read the authenticated backend user while rendering.
     */
    protected function actAsBackendUser(string $login): BackendUser
    {
        $user = BackendUser::create([
            'first_name' => 'Backend',
            'last_name' => 'Tester',
            'login' => $login,
            'email' => $login . '@test.com',
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'is_superuser' => true,
        ]);

        $this->actingAs($user, 'backend');

        return $user;
    }

    protected function postData(array $data): void
    {
        request()->setMethod('POST');
        request()->request->replace($data);
    }
}
