<?php namespace Backend\Controllers;

use ApplicationException;
use Backend;
use BackendAuth;
use Backend\Classes\Controller;
use Config;
use Exception;
use Flash;
use Mail;
use Request;
use ValidationException;
use Validator;
use Winter\Storm\Foundation\Http\Middleware\CheckForTrustedHost;

/**
 * Authentication controller
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 *
 */
class Auth extends Controller
{
    /**
     * @var array Public controller actions
     */
    protected $publicActions = ['index', 'signin', 'signout', 'restore', 'reset'];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->layout = 'auth';
    }

    /**
     * Default route, redirects to signin.
     */
    public function index()
    {
        return Backend::redirect('backend/auth/signin');
    }

    /**
     * Displays the log in page.
     */
    public function signin()
    {
        if (BackendAuth::user()) {
            return Backend::redirect('backend');
        }

        $this->bodyClass = 'signin';

        // Clear Cache and any previous data to fix invalid security token issue
        $this->setResponseHeader('Cache-Control', 'no-cache, no-store, must-revalidate');

        try {
            if (post('postback')) {
                return $this->signin_onSubmit();
            }

            $this->bodyClass .= ' preload';
        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }
    }

    public function signin_onSubmit()
    {
        $rules = [
            'login'    => 'required|between:2,255',
            'password' => 'required|between:4,255'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        if (is_null($remember = Config::get('cms.backendForceRemember', true))) {
            $remember = (bool) post('remember');
        }

        // Authenticate user
        $user = BackendAuth::authenticate([
            'login' => post('login'),
            'password' => post('password')
        ], $remember);

        // Redirect to the intended page after successful sign in
        return Backend::redirectIntended('backend');
    }

    /**
     * Logs out a backend user.
     */
    public function signout()
    {
        if (BackendAuth::isImpersonator()) {
            BackendAuth::stopImpersonate();
        } else {
            BackendAuth::logout();
        }

        // Add HTTP Header 'Clear Site Data' to purge all sensitive data upon signout
        if (Request::secure()) {
            $this->setResponseHeader('Clear-Site-Data', 'cache, cookies, storage, executionContexts');
        }

        return Backend::redirect('backend');
    }

    /**
     * Request a password reset verification code.
     */
    public function restore()
    {
        $this->bodyClass = 'restore';

        try {
            if (post('postback')) {
                return $this->restore_onSubmit();
            }
        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }
    }

    /**
     * Submits the restore form.
     */
    public function restore_onSubmit()
    {
        // Force Trusted Host verification on password reset link generation
        // regardless of config to protect against host header poisoning
        $trustedHosts = Config::get('app.trustedHosts', false);
        if ($trustedHosts === false) {
            $hosts = CheckForTrustedHost::processTrustedHosts(true);

            if (count($hosts)) {
                Request::setTrustedHosts($hosts);

                // Trigger the host validation logic
                Request::getHost();
            }
        }

        $rules = [
            'login' => 'required|between:2,255'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $user = BackendAuth::findUserByLogin(post('login'));

        if ($user) {
            $code = $user->getResetPasswordCode();
            $link = Backend::url('backend/auth/reset/' . $user->id . '/' . $code);

            $data = [
                'name' => $user->full_name,
                'link' => $link,
            ];

            Mail::send('backend::mail.restore', $data, function ($message) use ($user) {
                $message->to($user->email, $user->full_name)->subject(trans('backend::lang.account.password_reset'));
            });
        }

        Flash::success(trans('backend::lang.account.restore_success'));

        return Backend::redirect('backend/auth/signin');
    }

    /**
     * Reset backend user password using verification code.
     */
    public function reset($userId = null, $code = null)
    {
        $this->bodyClass = 'reset';

        try {
            if (post('postback')) {
                return $this->reset_onSubmit();
            }

            if (!$userId || !$code) {
                throw new ApplicationException(trans('backend::lang.account.reset_error'));
            }
        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }

        $this->vars['code'] = $code;
        $this->vars['id'] = $userId;
    }

    /**
     * Submits the reset form.
     */
    public function reset_onSubmit()
    {
        if (!post('id') || !post('code')) {
            throw new ApplicationException(trans('backend::lang.account.reset_error'));
        }

        $rules = [
            'password' => 'required|between:4,255'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $code = post('code');
        $user = BackendAuth::findUserById(post('id'));

        if (!$user->checkResetPasswordCode($code)) {
            throw new ApplicationException(trans('backend::lang.account.reset_error'));
        }

        if (!$user->attemptResetPassword($code, post('password'))) {
            throw new ApplicationException(trans('backend::lang.account.reset_fail'));
        }

        $user->clearResetPassword();

        Flash::success(trans('backend::lang.account.reset_success'));

        return Backend::redirect('backend/auth/signin');
    }
}
