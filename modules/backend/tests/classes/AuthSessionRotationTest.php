<?php

namespace Backend\Tests\Classes;

use Backend\Classes\AuthManager;
use Backend\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;

/**
 * Covers the session identifier across a backend sign in: it moves to a new identifier, the
 * session contents and the sign-in flows that depend on them keep working, and the identifier
 * the session had beforehand no longer addresses it.
 */
class AuthSessionRotationTest extends PluginTestCase
{
    protected const PASSWORD = 'TestPassword1';

    protected const SIGNIN = '/backend/backend/auth/signin';

    protected User $user;
    protected User $other;

    public function setUp(): void
    {
        parent::setUp();

        Model::unguard();
        $this->user = User::create([
            'first_name' => 'Rotation',
            'last_name' => 'Tester',
            'login' => 'rotationtester',
            'email' => 'rotationtester@test.test',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
            'is_activated' => true,
            'is_superuser' => true,
        ]);
        $this->other = User::create([
            'first_name' => 'Other',
            'last_name' => 'Tester',
            'login' => 'othertester',
            'email' => 'othertester@test.test',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
            'is_activated' => true,
        ]);
        Model::reguard();

        Session::start();
    }

    public function tearDown(): void
    {
        AuthManager::forgetInstance();

        parent::tearDown();
    }

    /**
     * Builds the auth manager the way the next request would: nothing cached in memory, the
     * identity rebuilt from whatever the session currently holds.
     */
    protected function freshAuth(): AuthManager
    {
        AuthManager::forgetInstance();
        $this->app->forgetInstance('backend.auth');

        return $this->app->make('backend.auth');
    }

    protected function signIn(bool $remember = false): AuthManager
    {
        $auth = $this->freshAuth();
        $auth->authenticate(['login' => 'rotationtester', 'password' => self::PASSWORD], $remember);

        return $auth;
    }

    /**
     * Emulates the next request arriving with the given session identifier: the session contents
     * are re-read from the handler rather than carried over in memory.
     */
    protected function requestWithSessionId(string $id): void
    {
        Session::save();
        Session::flush();
        Session::setId($id);
        Session::start();
    }

    public function testSignInMovesTheSessionToANewIdentifier(): void
    {
        $before = Session::getId();

        $this->signIn();

        $this->assertNotSame($before, Session::getId());
    }

    public function testSignInStillAuthenticates(): void
    {
        $this->signIn();

        $this->assertTrue($this->freshAuth()->check(), 'a legitimate sign in must authenticate');
        $this->assertSame($this->user->getKey(), $this->freshAuth()->getUser()->getKey());
    }

    public function testTheIdentifierFromBeforeSignInDoesNotAddressTheSession(): void
    {
        $before = Session::getId();

        $this->signIn();
        $this->assertTrue($this->freshAuth()->check(), 'Precondition: the sign in authenticated');

        $this->requestWithSessionId($before);

        $this->assertNull(Session::get('admin_auth'));
        $this->assertFalse($this->freshAuth()->check());
    }

    public function testTheIdentifierFromAfterSignInAddressesTheSession(): void
    {
        $this->signIn();
        $after = Session::getId();

        $this->requestWithSessionId($after);

        $this->assertIsArray(Session::get('admin_auth'));
        $this->assertTrue($this->freshAuth()->check());
    }

    public function testSignInKeepsTheSessionContents(): void
    {
        Session::put('url.intended', 'http://localhost/backend/backend/users');
        Session::flash('example', 'kept');
        $token = Session::token();

        $this->signIn();

        $this->assertSame('http://localhost/backend/backend/users', Session::get('url.intended'));
        $this->assertSame('kept', Session::get('example'));
        $this->assertSame($token, Session::token(), 'the CSRF token must survive the sign in');
    }

    public function testARememberedSignInSurvivesTheSessionBeingDropped(): void
    {
        $this->signIn(true);

        $remembered = Cookie::queued('admin_auth');
        $this->assertNotNull($remembered, 'Precondition: signing in with remember queues the cookie');

        // A browser restart: the session cookie is gone, the remember cookie is not.
        Session::flush();
        Session::setId(null);
        Session::start();
        Request::swap(HttpRequest::create('/', 'GET', [], ['admin_auth' => $remembered->getValue()]));

        $auth = $this->freshAuth();

        $this->assertTrue($auth->check());
        $this->assertTrue($auth->viaRemember());
        $this->assertSame($this->user->getKey(), $auth->getUser()->getKey());
    }

    public function testImpersonationStartsAndStopsAroundASignedInSession(): void
    {
        $this->signIn();
        $signedIn = Session::getId();

        $auth = $this->freshAuth();
        $auth->impersonate($this->other);

        $this->assertSame($signedIn, Session::getId(), 'impersonation must not move the session');
        $this->assertSame($this->other->getKey(), $auth->getUser()->getKey());
        $this->assertTrue($auth->isImpersonator());
        $this->assertSame($this->user->getKey(), $auth->getRealUser()->getKey());

        $auth = $this->freshAuth();
        $this->assertTrue($auth->check(), 'the impersonated session must survive the next request');
        $this->assertSame($this->other->getKey(), $auth->getUser()->getKey());

        $auth = $this->freshAuth();
        $auth->stopImpersonate();

        $this->assertSame($signedIn, Session::getId());
        $this->assertFalse($auth->isImpersonator());
        $this->assertSame($this->user->getKey(), $auth->getUser()->getKey());
        $this->assertSame($this->user->getKey(), $this->freshAuth()->getUser()->getKey());
    }

    public function testSignOutStillInvalidatesTheSession(): void
    {
        $this->signIn();
        $signedIn = Session::getId();

        $this->freshAuth()->logout();

        $this->assertNotSame($signedIn, Session::getId());
        $this->assertFalse($this->freshAuth()->check());
    }

    public function testTheSignInFormPostIsAcceptedAndIssuesANewSessionCookie(): void
    {
        $form = $this->get(self::SIGNIN);
        $form->assertStatus(200);

        $this->assertTrue(
            (bool) preg_match('/name="_token"[^>]*value="([^"]+)"/', $form->getContent(), $matches),
            'the sign-in form renders a token field'
        );

        $sessionCookie = null;
        foreach ($form->headers->getCookies() as $cookie) {
            if ($cookie->getName() === 'winter_session') {
                $sessionCookie = $cookie->getValue();
            }
        }
        $this->assertNotNull($sessionCookie, 'the sign-in page issues a session cookie');
        $beforeId = Session::getId();

        // Post the form back the way the browser would: same cookie, same token.
        Session::flush();
        AuthManager::forgetInstance();
        $this->app->forgetInstance('backend.auth');
        $this->unencryptedCookies = ['winter_session' => $sessionCookie];

        $post = $this->post(self::SIGNIN, [
            '_token' => $matches[1],
            'postback' => '1',
            'login' => 'rotationtester',
            'password' => self::PASSWORD,
        ]);

        $post->assertStatus(302);
        $this->assertStringEndsWith('/backend/backend', (string) $post->headers->get('Location'));
        $this->assertNotSame($beforeId, Session::getId());
        $this->assertTrue($this->app->make('backend.auth')->check());
    }
}
