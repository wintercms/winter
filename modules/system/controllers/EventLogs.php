<?php namespace System\Controllers;

use App;
use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use System\Classes\SettingsManager;
use System\Models\EventLog;
use Winter\Storm\Support\Facades\Flash;

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

    /**
     * Show a popup to ask user his choices about clearing the log
     */
    public function index_onClearLog()
    {
        $config = $this->makeConfig([
            'fields' => [
                'message' => [
                    'type' => 'text',
                    'label' => 'system::lang.event_log.delete_filtered_search',
                    'comment' => 'system::lang.event_log.delete_filtered_search_comment',
                    'span' => 'full',
                ],
                'date_start' => [
                    'type' => 'datepicker',
                    'label' => 'system::lang.event_log.delete_filtered_date_start',
                    'span' => 'left',
                    'mode' => 'datetime',
                ],
                'date_end' => [
                    'type' => 'datepicker',
                    'label' => 'system::lang.event_log.delete_filtered_date_end',
                    'span' => 'right',
                    'mode' => 'datetime',
                ],
            ],
        ]);
        $config->model = new EventLog;

        $formWidget = $this->makeWidget('Backend\Widgets\Form', $config);
        $formWidget->bindToController();

        return $this->makePartial('delete_filtered_form', [
            'formWidget' => $formWidget,
        ]);
    }

    /**
     * Return informations about the log to be cleared
     */
    public function index_onClearLogInfos()
    {
        $message = post('message');
        $dateStart = post('date_start');
        $dateEnd = post('date_end');

        if (!$message && !$dateStart && !$dateEnd) {
            return [
                '#deleteFilteredList' => '',
                '#deleteFilteredResults' => '',
            ];
        }

        $events = EventLog
            ::when($message, fn ($q) => $q->where('message', 'like', '%' . $message . '%'))
            ->when($dateStart, fn ($q) => $q->where('created_at', '>=', $dateStart))
            ->when($dateEnd, fn ($q) => $q->where('created_at', '<=', $dateEnd))
            ->orderBy('created_at', 'desc')
            ->get();

        $config = $this->makeConfig('~/modules/system/models/eventlog/columns.yaml');
        $config->model = new EventLog;

        $controlList = $this->makeWidget('Backend\Widgets\Lists', $config);
        $controlList->bindEvent('list.extendRecords', function ($records) use ($events, $message) {
            if ($events->count() > 0) {
                return $events;
            }
            return collect([]);
        });

        $controlList->bindToController();

        return [
            'total' => $events->count(),
            '#deleteFilteredList' => $controlList->render(),
            '#deleteFilteredResults' => Lang::get('system::lang.event_log.delete_filtered_results', [
                'what' => strtolower(Str::plural(Lang::get('system::lang.event_log.preview_title'), $events->count())),
                'count' => $events->count(),
            ])
        ];
    }

    /**
     * Delete the filtered events log
     */
    public function index_onClearLogDelete()
    {
        $message = post('message');
        $dateStart = post('date_start');
        $dateEnd = post('date_end');
        $eventLog = new EventLog;

        $eventLog = EventLog
            ::when($message, fn ($q) => $q->where('message', 'like', '%' . $message . '%'))
            ->when($dateStart, fn ($q) => $q->where('created_at', '>=', $dateStart))
            ->when($dateEnd, fn ($q) => $q->where('created_at', '<=', $dateEnd));

        $events = $eventLog->get();
        $eventLog->delete();

        Flash::success(Lang::get('system::lang.event_log.delete_filtered_success', [
            'what' => strtolower(Str::plural(Lang::get('system::lang.event_log.preview_title'), $events->count())),
            'count' => $events->count(),
        ]));
        return $this->listRefresh();
    }

    public function index_onEmptyLog()
    {
        EventLog::truncate();
        Flash::success(Lang::get('system::lang.event_log.empty_success'));
        return $this->listRefresh();
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $recordId) {
                if (!$record = EventLog::find($recordId)) {
                    continue;
                }
                $record->delete();
            }

            Flash::success(Lang::get('backend::lang.list.delete_selected_success'));
        }
        else {
            Flash::error(Lang::get('backend::lang.list.delete_selected_empty'));
        }

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
