<?php

namespace Backend\Tests\Controllers;

use Backend\Controllers\MyAccount;
use Backend\Models\User;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\Config;

/**
 * Regression coverage for GHSA-mpmw-f6h6-3g26.
 *
 * Every authenticated user can reach My Account, so FormController's record-scoped actions --
 * create, update, preview -- must not be routable here: each takes a caller-supplied key and
 * would resolve to any backend user. `$guarded` drops them from routing; `formExtendQuery()`
 * pins the lookup to the current user in case anything reaches the behavior anyway.
 */
class MyAccountSecurityTest extends PluginTestCase
{
    protected User $mallory;
    protected User $alice;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        Model::unguard();
        $this->mallory = $this->makeUser('mallory');
        $this->alice = $this->makeUser('alice');
        Model::reguard();

        $this->actingAs($this->mallory);
    }

    protected function makeUser(string $login): User
    {
        return User::create([
            'first_name' => ucfirst($login),
            'last_name' => 'User',
            'login' => $login,
            'email' => "{$login}@test.test",
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
        ]);
    }

    /** The behavior still supplies these for index() to call, but no URL may reach them. */
    public function testRecordScopedActionsAreNotRoutable(): void
    {
        $controller = new MyAccount;

        foreach (['create', 'update', 'preview'] as $action) {
            $this->assertTrue($controller->methodExists($action), "Precondition: {$action} exists");
            $this->assertFalse($controller->actionExists($action), "{$action} must not be routable");
        }

        $this->assertTrue($controller->actionExists('index'), 'index must still route');
    }

    public function testTheFormLookupIsPinnedToTheCurrentUser(): void
    {
        $controller = new MyAccount;

        $this->assertEquals(
            $this->mallory->getKey(),
            $controller->formFindModelObject($this->mallory->getKey())->getKey()
        );

        $this->expectException(ApplicationException::class);
        $controller->formFindModelObject($this->alice->getKey());
    }

    /** The reported request. */
    public function testPreviewOfAnotherUserIsNotServed(): void
    {
        $response = $this->get('backend/backend/myaccount/preview/' . $this->alice->getKey());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringNotContainsString('alice@test.test', $response->getContent());
    }

    /** Nothing legitimate regressed: routing, the form and the save all still work. */
    public function testMyAccountStillSavesTheCurrentUsersOwnRecord(): void
    {
        $response = $this->post('backend/backend/myaccount', [
            'User' => [
                'first_name' => 'Renamed',
                'last_name' => 'User',
                'login' => 'mallory',
                'email' => 'mallory@test.test',
            ],
        ], [
            'X-WINTER-REQUEST-HANDLER' => 'onSave',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Renamed', User::find($this->mallory->getKey())->first_name);
    }
}
