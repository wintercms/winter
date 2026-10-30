<?php

namespace Backend\Tests\Controllers;

use Backend\Models\User;
use Backend\Models\UserRole;
use Illuminate\Http\Request as HttpRequest;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Foundation\Http\Middleware\CheckForTrustedHost;
use Winter\Storm\Support\Facades\Config;

/**
 * The backend password reset flow mails an absolute link, so the host that link is built from
 * must come from the application configuration rather than from the request that triggered it.
 *
 * `Backend\Controllers\Auth::restore_onSubmit()` has always applied trusted host verification to
 * its own link generation, regardless of the `app.trustedHosts` setting. The administrator
 * triggered reset in `Backend\Controllers\Users::update_onManualPasswordReset()` mails the same
 * kind of link and has to behave the same way, so both entry points are asserted here together
 * with the normal request each of them has to keep serving.
 */
class UsersPasswordResetTest extends PluginTestCase
{
    protected User $manager;
    protected User $victim;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);
        Config::set('app.url', 'http://winter.test');
        Config::set('app.trustedHosts', false);

        // Trusted hosts are a static on the request class, so they outlive a single test
        HttpRequest::setTrustedHosts([]);

        // Winter\Storm\Foundation\Bootstrap\RegisterWinter pins the generator's root URL to
        // app.url while running in console, which the test suite always is. Releasing it is what
        // makes URL generation here behave the way it does under a web request, which is the
        // behaviour these tests are about.
        $this->app['url']->forceRootUrl(null);

        // The flow mails inline and the mail body is what is being asserted, so the message has
        // to be captured in process. MailManager caches the transport when the mailer is first
        // built and the facade caches that mailer, so both are dropped after the config change.
        Config::set('mail.default', 'array');
        \Winter\Storm\Support\Facades\Mail::clearResolvedInstances();
        $this->app['mail.manager']->forgetMailers();

        Model::unguard();
        $this->manager = $this->makeUser('manager', ['backend.manage_users' => 1]);
        $this->victim = $this->makeUser('victim', ['cms.manage_pages' => 1]);
        Model::reguard();
    }

    public function tearDown(): void
    {
        // The handlers put a pattern derived from app.url on the request class, which is static,
        // so it would reject the default host every later test in the run uses
        HttpRequest::setTrustedHosts([]);

        parent::tearDown();
    }

    protected function makeUser(string $login, array $permissions): User
    {
        $role = UserRole::create([
            'name' => $login,
            'code' => $login,
            'permissions' => $permissions,
        ]);

        return User::create([
            'first_name' => ucfirst($login),
            'last_name' => 'User',
            'login' => $login,
            'email' => "{$login}@test.test",
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'role_id' => $role->id,
        ]);
    }

    /** Every message the mailer handed to a transport during the test, as raw MIME. */
    protected function sentMessages(): array
    {
        return $this->app['mailer']->getSymfonyTransport()->messages()
            ->map(function ($sent) {
                return $sent->getOriginalMessage()->toString();
            })
            ->all();
    }

    /**
     * The administrator triggered reset, as an AJAX handler on the user form, served for the
     * given host.
     */
    protected function manualReset(string $origin, array $headers = [])
    {
        return $this->post($origin . '/backend/backend/users/update/' . $this->victim->getKey(), [], $headers + [
            'X-WINTER-REQUEST-HANDLER' => 'onManualPasswordReset',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    /** The public restore form, submitted for the given host. */
    protected function publicRestore(string $origin)
    {
        return $this->post($origin . '/backend/backend/auth/restore', [
            'postback' => 1,
            'login' => $this->victim->login,
        ]);
    }

    /**
     * A request served for a host the application is not configured for must not produce a reset
     * link for that host.
     */
    public function testManualResetDoesNotBuildTheLinkFromTheRequestHost(): void
    {
        $this->actingAs($this->manager);

        $this->manualReset('http://not-this-site.test');

        $this->assertCount(0, $this->sentMessages(), 'No message was sent');
        $this->assertNull(
            User::find($this->victim->getKey())->reset_password_code,
            'No reset code was issued'
        );
    }

    /**
     * With `app.trustedProxies` open, which is the documented setup for a CDN or load balancer,
     * the effective host comes from the forwarded header rather than from `Host`. That host has
     * to be held to the same requirement.
     */
    public function testManualResetDoesNotBuildTheLinkFromAForwardedHost(): void
    {
        Config::set('app.trustedProxies', '*');

        $this->actingAs($this->manager);

        $this->manualReset('http://winter.test', ['X-Forwarded-Host' => 'not-this-site.test']);

        $this->assertCount(0, $this->sentMessages(), 'No message was sent');
        $this->assertNull(
            User::find($this->victim->getKey())->reset_password_code,
            'No reset code was issued'
        );
    }

    /** The same requirement on the public restore form, which has always been covered. */
    public function testPublicRestoreDoesNotBuildTheLinkFromTheRequestHost(): void
    {
        $this->publicRestore('http://not-this-site.test');

        $this->assertCount(0, $this->sentMessages(), 'No message was sent');
        $this->assertNull(
            User::find($this->victim->getKey())->reset_password_code,
            'No reset code was issued'
        );
    }

    /** A normal request still issues a code and mails a link to the configured host. */
    public function testManualResetStillMailsALinkForTheConfiguredHost(): void
    {
        $this->actingAs($this->manager);

        $response = $this->manualReset('http://winter.test');

        $this->assertEquals(200, $response->getStatusCode());

        $code = User::find($this->victim->getKey())->reset_password_code;
        $this->assertNotEmpty($code);

        $messages = $this->sentMessages();
        $this->assertCount(1, $messages);
        $this->assertStringContainsString('victim@test.test', $messages[0]);
        $this->assertStringContainsString(
            'http://winter.test/backend/backend/auth/reset/' . $this->victim->getKey() . '/' . $code,
            quoted_printable_decode($messages[0])
        );
    }

    /** The same, through the public restore form. */
    public function testPublicRestoreStillMailsALinkForTheConfiguredHost(): void
    {
        $response = $this->publicRestore('http://winter.test');

        $this->assertEquals(302, $response->getStatusCode());

        $code = User::find($this->victim->getKey())->reset_password_code;
        $this->assertNotEmpty($code);

        $messages = $this->sentMessages();
        $this->assertCount(1, $messages);
        $this->assertStringContainsString(
            'http://winter.test/backend/backend/auth/reset/' . $this->victim->getKey() . '/' . $code,
            quoted_printable_decode($messages[0])
        );
    }

    /** The `www` form of the configured host is part of the derived pattern and still serves. */
    public function testManualResetServesTheWwwFormOfTheConfiguredHost(): void
    {
        $this->actingAs($this->manager);

        $response = $this->manualReset('http://www.winter.test');

        $this->assertEquals(200, $response->getStatusCode());

        $code = User::find($this->victim->getKey())->reset_password_code;
        $this->assertNotEmpty($code);

        $this->assertStringContainsString(
            'http://www.winter.test/backend/backend/auth/reset/' . $this->victim->getKey() . '/' . $code,
            quoted_printable_decode($this->sentMessages()[0])
        );
    }

    /**
     * An explicit `app.trustedHosts` list is what a multi domain install uses, and the flow has
     * to keep serving every host on it rather than only the one in `app.url`.
     *
     * CheckForTrustedHost skips itself while running unit tests, so the list it would have put on
     * the request is applied here instead, which is what the handler sees in production.
     */
    public function testManualResetServesAnyConfiguredTrustedHost(): void
    {
        Config::set('app.trustedHosts', ['winter.test', 'second.test']);
        HttpRequest::setTrustedHosts(
            CheckForTrustedHost::processTrustedHosts(Config::get('app.trustedHosts'))
        );

        $this->actingAs($this->manager);

        $response = $this->manualReset('http://second.test');

        $this->assertEquals(200, $response->getStatusCode());

        $code = User::find($this->victim->getKey())->reset_password_code;
        $this->assertNotEmpty($code);

        $this->assertStringContainsString(
            'http://second.test/backend/backend/auth/reset/' . $this->victim->getKey() . '/' . $code,
            quoted_printable_decode($this->sentMessages()[0])
        );
    }
}
