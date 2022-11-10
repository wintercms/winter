<?php namespace System\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Mail partials controller
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MailPartials extends Controller
{
    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
    ];

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['system.manage_mail_templates'];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Winter.System', 'system', 'settings');
        SettingsManager::setContext('Winter.System', 'mail_templates');
    }

    public function formBeforeSave($model)
    {
        $model->is_custom = 1;
    }
}
