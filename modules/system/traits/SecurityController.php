<?php namespace System\Traits;

use Crypt;
use Config;
use Request;
use Session;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Winter\Storm\Foundation\Http\Middleware\CheckForTrustedHost;

/**
 * Security Controller Trait
 * Adds cross-site scripting protection methods to a controller based class
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
trait SecurityController
{
    /**
     * Adds anti-CSRF cookie.
     * Adds a cookie with a token for CSRF checks to the response.
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    protected function makeXsrfCookie()
    {
        $config = Config::get('session');

        return new Cookie(
            'XSRF-TOKEN',
            Session::token(),
            Carbon::now()->addMinutes((int) $config['lifetime'])->getTimestamp(),
            $config['path'],
            $config['domain'],
            $config['secure'],
            false,
            false,
            $config['same_site'] ?? null
        );
    }

    /**
     * Checks the request data / headers for a valid CSRF token.
     *
     * @return bool Returns false if a valid token is not found or cms.enableCsrfProtection is set to false
     */
    protected function verifyCsrfToken()
    {
        if (!Config::get('cms.enableCsrfProtection', true)) {
            return true;
        }

        $token = Request::input('_token') ?: Request::header('X-CSRF-TOKEN');

        if (!$token && $header = Request::header('X-XSRF-TOKEN')) {
            $token = Crypt::decrypt($header, false);
        }

        if (!strlen($token) || !strlen(Session::token())) {
            return false;
        }

        return hash_equals(
            Session::token(),
            $token
        );
    }

    /**
     * Checks if the back-end should force a secure protocol (HTTPS) enabled by config.
     * @return bool
     */
    protected function verifyForceSecure()
    {
        if (Request::secure() || Request::ajax()) {
            return true;
        }

        return !Config::get('cms.backendForceSecure', false);
    }

    /**
     * Applies trusted host verification to the current request even when the `app.trustedHosts`
     * configuration has it disabled.
     *
     * Use this before generating an absolute URL that is delivered out of band, such as the
     * Backend password reset link, so the host in that URL is one the application is configured
     * for rather than whatever the request asked for. When `app.trustedHosts` is enabled the
     * verification has already been applied by
     * \Winter\Storm\Foundation\Http\Middleware\CheckForTrustedHost.
     *
     * @return void
     */
    protected function forceTrustedHosts()
    {
        if (Config::get('app.trustedHosts', false) !== false) {
            return;
        }

        $hosts = CheckForTrustedHost::processTrustedHosts(true);

        if (count($hosts)) {
            Request::setTrustedHosts($hosts);

            // Trigger the host validation logic
            Request::getHost();
        }
    }
}
