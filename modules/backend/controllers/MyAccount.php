<?php

namespace Backend\Controllers;

use Backend\Behaviors\FormController;
use Backend\Classes\Controller;
use Backend\Facades\BackendAuth;
use Backend\Facades\BackendMenu;
use System\Classes\SettingsManager;

/**
 * My Account controller
 *
 * Allows any authenticated backend user to manage their own account settings.
 * Isolated from the Users controller to prevent privilege escalation via
 * handler dispatch on a controller with degraded permissions.
 *
 * @package winter\wn-backend-module
 * @author Winter CMS
 */
class MyAccount extends Controller
{
    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        FormController::class,
    ];

    /**
     * @var array Permissions required to view this page.
     *      Empty array — any logged-in user can access their own account.
     */
    public $requiredPermissions = [];

    /**
     * @var string HTML body tag class
     */
    public $bodyClass = 'compact-container';

    public $formLayout = 'sidebar';

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Winter.System', 'system', 'users');
        SettingsManager::setContext('Winter.Backend', 'myaccount');
    }

    /**
     * My Account page
     */
    public function index()
    {
        $this->pageTitle = 'backend::lang.myaccount.menu_label';
        return $this->asExtension('FormController')->update($this->user->id, 'myaccount');
    }

    /**
     * Save handler for the My Account form
     */
    public function index_onSave()
    {
        $result = $this->asExtension('FormController')->update_onSave($this->user->id, 'myaccount');

        /*
         * If the password or login name has been updated, reauthenticate the user
         */
        $loginChanged = $this->user->login != post('User[login]');
        $passwordChanged = strlen(post('User[password]'));
        if ($loginChanged || $passwordChanged) {
            BackendAuth::login($this->user->reload(), true);
        }

        return $result;
    }
}
