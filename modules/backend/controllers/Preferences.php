<?php namespace Backend\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\Backend;
use Backend\Facades\BackendMenu;
use Backend\Models\Preference as PreferenceModel;
use Illuminate\Support\Facades\Lang;
use System\Classes\SettingsManager;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Facades\Flash;

/**
 * Editor Settings controller
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 *
 */
class Preferences extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
    ];

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['backend.manage_preferences'];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addJs('/modules/backend/assets/js/preferences/preferences.js', 'core');

        BackendMenu::setContext('Winter.System', 'system', 'mysettings');
        SettingsManager::setContext('Winter.Backend', 'preferences');
    }

    public function index()
    {
        $this->pageTitle = 'backend::lang.backend_preferences.menu_label';
        $this->asExtension('FormController')->update();
    }

    /**
     * Remove the code editor tab if there is no permission.
     */
    public function formExtendFields($form)
    {
        if (!$this->user->hasAccess('backend.manage_own_editor')) {
            $form->removeTab('backend::lang.backend_preferences.code_editor');
        }
    }

    public function index_onSave()
    {
        return $this->asExtension('FormController')->update_onSave();
    }

    public function index_onResetDefault()
    {
        $model = $this->formFindModelObject();
        $model->resetDefault();

        Flash::success(Lang::get('backend::lang.form.reset_success'));

        return Backend::redirect('backend/preferences');
    }

    public function formFindModelObject()
    {
        return PreferenceModel::instance();
    }
}
