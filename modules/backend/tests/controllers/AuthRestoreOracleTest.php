<?php

namespace Backend\Tests\Controllers;

use Backend\Controllers\Auth;
use Backend\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Flash;
use Winter\Storm\Support\Facades\Mail;

/**
 * The public restore form's response must not depend on whether the submitted login matches
 * an existing account.
 *
 * The form is for signed-out users and has no notion of an acting user, but nothing stopped an
 * authenticated session reaching it, and the user model it calls does apply an authorization
 * check when one is present. Its response therefore has to be asserted with a session as well
 * as without, for both a login that matches an account and one that does not.
 */
class AuthRestoreOracleTest extends PluginTestCase
{
    protected User $alice;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);
        Config::set('app.url', 'http://localhost');
        // The known-login branch sends the reset mail inline, and the response it produces is
        // exactly what is being compared, so nothing must reach a real transport
        Mail::fake();
        HttpRequest::setTrustedHosts([]);

        $this->alice = $this->makeUser('alice');
    }

    protected function makeUser(string $login): User
    {
        Model::unguard();
        $user = User::create([
            'first_name' => ucfirst($login),
            'last_name' => 'User',
            'login' => $login,
            'email' => "{$login}@test.test",
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
        ]);
        Model::reguard();

        return $user;
    }

    protected function submitRestore(string $login): array
    {
        Flash::forget();

        Request::swap(HttpRequest::create('http://localhost/', 'POST', [
            'postback' => 1,
            'login' => $login,
        ]));

        try {
            (new Auth)->restore_onSubmit();
            $thrown = null;
        } catch (\Throwable $ex) {
            $thrown = get_class($ex) . ': ' . $ex->getMessage();
        }

        return [
            'exception' => $thrown,
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ];
    }

    /** Control: as a guest, a known and an unknown login are already indistinguishable. */
    public function testGuestRestoreResponseIsIdenticalForKnownAndUnknownLogins(): void
    {
        $known = $this->submitRestore('alice');
        $unknown = $this->submitRestore('nobody-here');

        $this->assertNull($known['exception']);
        $this->assertSame($known, $unknown);
    }

    /** Invalidation test: the guest flow still issues a code for a real account. */
    public function testGuestRestoreStillIssuesACodeForAKnownAccount(): void
    {
        $this->submitRestore('alice');

        $this->assertNotEmpty(User::find($this->alice->getKey())->reset_password_code);
    }

    public function testAuthenticatedRestoreDoesNotRevealAccountExistence(): void
    {
        $mallory = $this->makeUser('mallory');
        $this->assertFalse($mallory->hasAccess('backend.manage_users'), 'Precondition: no user management access');

        $this->actingAs($mallory);

        $known = $this->submitRestore('alice');
        $unknown = $this->submitRestore('nobody-here');

        $this->assertNull($known['exception'], 'A known login must not raise where an unknown one does not');
        $this->assertSame($known, $unknown);
    }

    /**
     * `restore_onSubmit` is also reachable as the `onSubmit` AJAX handler for the restore
     * action, and Controller::run() dispatches AJAX handlers before the action method - so a
     * guard placed only in restore() would leave this route open.
     */
    public function testAuthenticatedRestoreAjaxHandlerDoesNotRevealAccountExistence(): void
    {
        $this->actingAs($this->makeUser('mallory'));

        $responses = [];

        foreach (['alice', 'nobody-here'] as $login) {
            $request = HttpRequest::create('http://localhost/backend/backend/auth/restore', 'POST', [
                'login' => $login,
            ]);
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
            $request->headers->set('X-WINTER-REQUEST-HANDLER', 'onSubmit');
            Request::swap($request);

            $response = (new Auth)->run('restore', []);

            $responses[$login] = [
                'status' => $response->getStatusCode(),
                'content' => (string) $response->getContent(),
            ];
        }

        $this->assertSame($responses['alice'], $responses['nobody-here']);
    }

    /**
     * The reset URL carries the account's numeric id. Neither the reset page nor a reset
     * submission answers differently for an id that exists and one that does not, so the id in
     * the link is not itself an enumeration oracle. Kept as a control, since it is the property
     * the original report described.
     */
    public function testResetEndpointIsIdenticalForKnownAndUnknownUserIds(): void
    {
        $this->assertNull(\BackendAuth::findUserById(999999), 'Precondition: id 999999 is absent');

        $render = [];

        foreach ([$this->alice->getKey(), 999999] as $id) {
            Flash::forget();
            Request::swap(HttpRequest::create('http://localhost/', 'GET'));

            $controller = new Auth;
            $controller->reset($id, 'bogus-code');

            $render[$id] = [
                'flash' => Flash::all(),
                'code' => $controller->vars['code'],
            ];
        }

        $this->assertSame($render[$this->alice->getKey()]['flash'], $render[999999]['flash']);
    }
}
