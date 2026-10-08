<?php namespace System\Twig\SecurityPolicy;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Twig\Sandbox\SecurityNotAllowedMethodError;

/**
 * SafeSession is a session proxy that is safe to use in a Twig sandbox.
 *
 * The CMS controller hands `this.session` to every template, and the security policy is
 * not given method arguments, so on its own it can only restrict which methods are
 * called, never which keys they touch. Some keys are not scratch space: `admin_auth*`
 * holds the backend authentication state, `_*` holds Laravel's own state including the CSRF
 * token, and `widget.*` is fed to `unserialize()` by `Backend\Traits\SessionMaker`. This
 * proxy rejects writes to those keys; reads are left alone because a template can only ever
 * reach the session of the visitor that is already rendering it.
 *
 * @package winter\wn-system-module
 */
class SafeSession
{
    use ForwardsCalls;
    use ResolvesNamedArguments;

    /**
     * @var \Illuminate\Session\SessionManager|\Illuminate\Session\Store The wrapped session.
     */
    protected $session;

    /**
     * @var array<string, string[]> Methods that take the session key to write as their first
     * argument, against the parameter names the session store declares for it — so a template
     * may also pass the key by name. Lower case keys, since PHP method dispatch is case
     * insensitive.
     */
    protected $writeMethods = [
        'put' => ['key'],
        'forget' => ['keys'],
        'pull' => ['key'],
    ];

    /**
     * @var string[] Key prefixes that are reserved for authentication, CSRF and other
     * framework or backend state, and so may not be written from a template.
     */
    protected $reservedKeyPrefixes = [
        // Laravel internals (_token, _flash, _previous, _old_input, ...)
        '_',
        // Backend\Classes\AuthManager
        'admin_auth',
        // Winter\Storm\Auth\Manager
        'winter_auth',
        // Illuminate guard keys (login_web_*, login_admin_*, ...)
        'login_',
        // Laravel password rehash keys
        'password_hash_',
        // Backend\Traits\SessionMaker widget state, which is consumed by unserialize()
        'widget.',
    ];

    /**
     * Constructor
     */
    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * Forward all calls to the session, rejecting writes to a reserved key first.
     */
    public function __call($method, $parameters)
    {
        // PHP method dispatch is case insensitive, so normalise before matching
        $normalized = strtolower($method);

        if (array_key_exists($normalized, $this->writeMethods)) {
            $this->checkKeyAllowed(
                $normalized,
                $this->resolveArgument($parameters, 0, $this->writeMethods[$normalized])
            );
        }

        return $this->forwardCallTo($this->session, $method, $parameters);
    }

    /**
     * Whether the given key is the container of a reserved dotted prefix.
     *
     * The session store resolves a key with Arr::get()/Arr::has(), which are dot aware, so a
     * value written to `widget` is read back under `widget.<anything>`. Reserving the prefix
     * without reserving the container it hangs from would leave the same state writable one
     * level up.
     *
     * @param string $key
     */
    protected function isReservedContainer(string $key): bool
    {
        foreach ($this->reservedKeyPrefixes as $prefix) {
            if (!Str::endsWith($prefix, '.')) {
                continue;
            }

            if ($key === rtrim($prefix, '.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Throws if the given key, or any key of a given array, is reserved.
     *
     * @param string $method
     * @param mixed $key
     * @throws SecurityNotAllowedMethodError
     */
    protected function checkKeyAllowed(string $method, $key): void
    {
        if (is_array($key)) {
            // forget() takes a list of keys, put() takes a key => value map
            $keys = $method === 'forget' ? $key : array_keys($key);

            foreach ($keys as $arrayKey) {
                $this->checkKeyAllowed($method, $arrayKey);
            }
            return;
        }

        if (!is_string($key)) {
            return;
        }

        if (Str::startsWith($key, $this->reservedKeyPrefixes) || $this->isReservedContainer($key)) {
            throw new SecurityNotAllowedMethodError(
                sprintf('Writing to the reserved session key "%s" is blocked.', $key),
                static::class,
                $method
            );
        }
    }
}
