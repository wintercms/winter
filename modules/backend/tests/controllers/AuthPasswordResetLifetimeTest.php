<?php

namespace Backend\Tests\Controllers;

use Backend\Controllers\Auth;
use Backend\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Db;
use Winter\Storm\Support\Facades\Flash;
use Winter\Storm\Support\Facades\Mail;

/**
 * A Backend password reset code is a bearer credential delivered by mail, so how long it lives
 * and how often it can be replaced are both security properties of this flow.
 *
 * Two of them are asserted here:
 *
 * 1. A code is only usable inside the lifetime set by `auth.passwords.backend.expire`, and a code
 *    with no recorded issue time is never usable at all.
 * 2. The public restore form will not issue another code for an account inside the window set by
 *    `auth.passwords.backend.throttle`, so a repeat request neither mails anything nor replaces
 *    the code the account holder is already holding - while answering exactly as it would have.
 */
class AuthPasswordResetLifetimeTest extends PluginTestCase
{
    protected const PASSWORD = 'TestPassword1';

    protected User $alice;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);
        Config::set('app.url', 'http://localhost');
        Config::set('app.trustedHosts', false);

        // Trusted hosts are a static on the request class, so they outlive a single test and the
        // restore handler puts a pattern derived from app.url there
        HttpRequest::setTrustedHosts([]);

        // The restore handler mails inline and the number of messages is part of what is being
        // asserted. MailManager caches the transport when the mailer is first built and the facade
        // caches that mailer, so both are dropped after the config change.
        Config::set('mail.default', 'array');
        Mail::clearResolvedInstances();
        $this->app['mail.manager']->forgetMailers();

        Model::unguard();
        $this->alice = User::create([
            'first_name' => 'Alice',
            'last_name' => 'User',
            'login' => 'alice',
            'email' => 'alice@test.test',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
            'is_activated' => true,
        ]);
        Model::reguard();
    }

    public function tearDown(): void
    {
        Carbon::setTestNow();
        HttpRequest::setTrustedHosts([]);

        parent::tearDown();
    }

    /** Submits the public restore form for the given login, reporting everything it answers with. */
    protected function submitRestore(string $login = 'alice'): array
    {
        Flash::forget();

        Request::swap(HttpRequest::create('http://localhost/', 'POST', [
            'postback' => 1,
            'login' => $login,
        ]));

        try {
            $response = (new Auth)->restore_onSubmit();
            $thrown = null;
        } catch (\Throwable $ex) {
            $response = null;
            $thrown = get_class($ex) . ': ' . $ex->getMessage();
        }

        return [
            'exception' => $thrown,
            'status' => $response ? $response->getStatusCode() : null,
            'target' => $response ? $response->getTargetUrl() : null,
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ];
    }

    /** Submits the reset form with the given code, returning the exception message or null. */
    protected function submitReset(string $code, string $password): ?string
    {
        Flash::forget();

        Request::swap(HttpRequest::create('http://localhost/', 'POST', [
            'postback' => 1,
            'id' => $this->alice->getKey(),
            'code' => $code,
            'password' => $password,
            'password_confirmation' => $password,
        ]));

        try {
            (new Auth)->reset_onSubmit();

            return null;
        } catch (ApplicationException $ex) {
            return $ex->getMessage();
        }
    }

    /** How many messages the mailer has handed to a transport so far. */
    protected function sentCount(): int
    {
        return $this->app['mailer']->getSymfonyTransport()->messages()->count();
    }

    protected function storedCode(): ?string
    {
        return User::find($this->alice->getKey())->reset_password_code;
    }

    //
    // The re-issue window
    //

    public function testARepeatRestoreInsideTheWindowIssuesNoCodeAndSendsNoMail(): void
    {
        $this->submitRestore();
        $first = $this->storedCode();

        $this->assertNotEmpty($first);
        $this->assertSame(1, $this->sentCount());

        $this->submitRestore();

        $this->assertSame(1, $this->sentCount(), 'no second message must be sent');
        $this->assertSame($first, $this->storedCode(), 'the outstanding code must not be replaced');
    }

    /**
     * The point of leaving the code alone is that the link already in the account holder's inbox
     * still works, so that is asserted end to end through the reset handler.
     */
    public function testTheOutstandingCodeStillResetsThePasswordAfterARepeatRestore(): void
    {
        $this->submitRestore();
        $code = $this->storedCode();

        $this->submitRestore();

        $this->assertNull($this->submitReset($code, 'NewPassword1'));
        $this->assertTrue(User::find($this->alice->getKey())->checkPassword('NewPassword1'));
    }

    /**
     * Refusing to issue must not be observable: the form is public, so a response that differed
     * from the first one would report that this login has a code outstanding, and therefore that
     * the account exists.
     */
    public function testTheResponseToARepeatRestoreIsIdenticalToTheFirst(): void
    {
        $first = $this->submitRestore();
        $repeat = $this->submitRestore();
        $unknown = $this->submitRestore('nobody-here');

        $this->assertNull($first['exception']);
        $this->assertSame(
            ['If your account exists, an email has been sent to you with reset instructions.'],
            $first['success']
        );
        $this->assertSame($first, $repeat);
        $this->assertSame($first, $unknown);
    }

    public function testANewCodeIsIssuedOnceTheWindowHasElapsed(): void
    {
        $this->submitRestore();
        $first = $this->storedCode();

        Carbon::setTestNow(Carbon::now()->addSeconds(61));

        $this->submitRestore();

        $this->assertNotSame($first, $this->storedCode());
        $this->assertSame(2, $this->sentCount());
    }

    public function testTheWindowComesFromTheThrottleConfiguration(): void
    {
        Config::set('auth.passwords.backend.throttle', 600);

        $this->submitRestore();
        $first = $this->storedCode();

        // Past the shipped default of 60 seconds, inside the configured 600
        Carbon::setTestNow(Carbon::now()->addSeconds(120));

        $this->submitRestore();

        $this->assertSame($first, $this->storedCode());
        $this->assertSame(1, $this->sentCount());
    }

    public function testAWindowOfZeroAllowsAnImmediateReissue(): void
    {
        Config::set('auth.passwords.backend.throttle', 0);

        $this->submitRestore();
        $first = $this->storedCode();

        $this->submitRestore();

        $this->assertNotSame($first, $this->storedCode());
        $this->assertSame(2, $this->sentCount());
    }

    /**
     * The window belongs to the public form, not to the model: an administrator pressing the
     * reset button on a user they manage is a deliberate action and still issues a code every
     * time.
     */
    public function testIssuingACodeDirectlyIsNotSubjectToTheWindow(): void
    {
        $first = $this->alice->getResetPasswordCode();
        $second = $this->alice->getResetPasswordCode();

        $this->assertNotSame($first, $second);
        $this->assertSame($second, $this->storedCode());
    }

    //
    // The code lifetime
    //

    public function testACodeOlderThanItsLifetimeIsRefused(): void
    {
        $this->submitRestore();
        $code = $this->storedCode();

        Carbon::setTestNow(Carbon::now()->addMinutes(61));

        $this->assertSame(
            'Invalid password reset data supplied. Please try again!',
            $this->submitReset($code, 'NewPassword1')
        );
        $this->assertTrue(
            User::find($this->alice->getKey())->checkPassword(self::PASSWORD),
            'the password must be unchanged'
        );
    }

    public function testACodeInsideItsLifetimeStillResetsThePassword(): void
    {
        $this->submitRestore();
        $code = $this->storedCode();

        Carbon::setTestNow(Carbon::now()->addMinutes(59));

        $this->assertNull($this->submitReset($code, 'NewPassword1'));
        $this->assertTrue(User::find($this->alice->getKey())->checkPassword('NewPassword1'));
        $this->assertNull($this->storedCode(), 'a used code must not stay on the record');
    }

    public function testTheLifetimeComesFromTheExpireConfiguration(): void
    {
        Config::set('auth.passwords.backend.expire', 5);

        $this->submitRestore();
        $code = $this->storedCode();

        // Inside the shipped default of 60 minutes, past the configured 5
        Carbon::setTestNow(Carbon::now()->addMinutes(6));

        $this->assertSame(
            'Invalid password reset data supplied. Please try again!',
            $this->submitReset($code, 'NewPassword1')
        );
        $this->assertTrue(User::find($this->alice->getKey())->checkPassword(self::PASSWORD));
    }

    /**
     * A code written before the issue time was recorded has no known age, which is the state
     * every outstanding code is in immediately after the upgrade that adds the column. It must be
     * refused rather than treated as fresh.
     */
    public function testACodeWithNoRecordedIssueTimeIsRefused(): void
    {
        Db::table('backend_users')
            ->where('id', $this->alice->getKey())
            ->update([
                'reset_password_code' => 'legacy-code-with-no-issue-time',
                'reset_password_created_at' => null,
            ]);

        $this->assertSame(
            'Invalid password reset data supplied. Please try again!',
            $this->submitReset('legacy-code-with-no-issue-time', 'NewPassword1')
        );
        $this->assertTrue(User::find($this->alice->getKey())->checkPassword(self::PASSWORD));
    }

    public function testClearingTheResetPasswordAlsoClearsTheIssueTime(): void
    {
        $this->alice->getResetPasswordCode();

        $this->assertNotNull(User::find($this->alice->getKey())->reset_password_created_at);

        $this->alice->clearResetPassword();

        $fresh = User::find($this->alice->getKey());
        $this->assertNull($fresh->reset_password_code);
        $this->assertNull($fresh->reset_password_created_at);
    }
}
