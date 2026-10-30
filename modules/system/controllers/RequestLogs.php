<?php namespace System\Controllers;

use Lang;
use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use System\Models\RequestLog;

/**
 * Request Logs controller
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class RequestLogs extends Controller
{
    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
    ];

    /**
     * @var array methods that are blocked from being called as actions
     *
     * This screen implements the form behaviour for its preview action alone. The behaviour's
     * create and update actions would let a holder of system.access_logs rewrite an entry or
     * insert one of their own.
     */
    protected $guarded = ['create', 'update'];

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
        SettingsManager::setContext('Winter.System', 'request_logs');
    }

    public function index_onRefresh()
    {
        return $this->listRefresh();
    }

    public function index_onEmptyLog()
    {
        RequestLog::truncate();
        Flash::success(Lang::get('system::lang.request_log.empty_success'));
        return $this->listRefresh();
    }
}
