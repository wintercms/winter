<?php namespace System\Controllers;

use App;
use Lang;
use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use System\Models\EventLog;

/**
 * Event Logs controller
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class EventLogs extends Controller
{
    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
    ];

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['system.access_logs'];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Winter.System', 'system', 'settings');
        SettingsManager::setContext('Winter.System', 'event_logs');
    }

    public function index_onRefresh()
    {
        return $this->listRefresh();
    }

    public function index_onEmptyLog()
    {
        EventLog::truncate();
        Flash::success(Lang::get('system::lang.event_log.empty_success'));
        return $this->listRefresh();
    }

    /**
     * Preview page action
     * @return void
     */
    public function preview($id)
    {
        $this->addCss('/modules/system/assets/css/eventlogs/exception-beautifier.css', 'core');
        $this->addJs('/modules/system/assets/js/eventlogs/exception-beautifier.js', 'core');

        if (in_array(App::environment(), ['dev', 'local'])) {
            $this->addJs('/modules/system/assets/js/eventlogs/exception-beautifier.links.js', 'core');
        }

        return $this->asExtension('FormController')->preview($id);
    }
}
