<?php namespace Backend\Controllers;

use Lang;
use Backend;
use Request;
use Response;
use Redirect;
use BackendMenu;
use Backend\Classes\Controller;
use Backend\Widgets\ReportContainer;

/**
 * Dashboard controller
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 *
 */
class Index extends Controller
{
    use \Backend\Traits\InspectableContainer;

    /**
     * @var array Permissions required to view this page.
     * @see checkPermissionRedirect()
     */
    public $requiredPermissions = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContextOwner('Winter.Backend');

        $this->addCss('/modules/backend/assets/css/dashboard/dashboard.css', 'core');

        /*
         * Custom redirect for unauthorized request.
         *
         * Bound to page.beforeDisplay rather than checked inside index() because AJAX
         * handlers are dispatched before the page action runs, so a check living in the
         * action alone would leave index_onInitReportContainer() reachable.
         */
        $this->bindEvent('page.beforeDisplay', function () {
            return $this->checkPermissionRedirect();
        });
    }

    public function index()
    {
        $this->initReportContainer();

        $this->pageTitle = 'backend::lang.dashboard.menu_label';

        BackendMenu::setContextMainMenu('dashboard');
    }

    public function index_onInitReportContainer()
    {
        $this->initReportContainer();

        return ['#dashReportContainer' => $this->widget->reportContainer->render()];
    }

    /**
     * Prepare the report widget used by the dashboard
     * @param Model $model
     * @return void
     */
    protected function initReportContainer()
    {
        new ReportContainer($this, 'config_dashboard.yaml');
    }

    /**
     * Custom permissions check that will redirect to the next
     * available menu item, if permission to this page is denied.
     */
    protected function checkPermissionRedirect()
    {
        if ($this->user->hasAccess('backend.access_dashboard')) {
            return;
        }

        if (Request::ajax()) {
            return Response::make(Lang::get('backend::lang.page.access_denied.label'), 403);
        }

        if ($first = array_first(BackendMenu::listMainMenuItems())) {
            return Redirect::intended($first->url);
        }

        return Backend::redirect('backend/myaccount');
    }
}
