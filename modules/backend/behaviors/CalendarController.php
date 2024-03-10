<?php

namespace Backend\Behaviors;

use ApplicationException;
use Backend\Classes\ControllerBehavior;
use Backend\Widgets\Calendar as CalendarWidget;
use Backend\Widgets\Filter as FilterWidget;
use Backend\Widgets\Toolbar as ToolbarWidget;
use Lang;
use stdClass;
use Winter\Storm\Support\Str;
use Winter\Storm\Database\Model;

/**
 * Adds features for working with backend records through a Calendar interface.
 *
 * This behavior is implemented in the controller like so:
 *
 *     public $implement = [
 *         \Backend\Behaviors\CalendarController::class,
 *     ];
 *
 *     public $calendarConfig = 'config_calendar.yaml';
 *
 * The `$calendarConfig` property makes reference to the calendar configuration
 * values as either a YAML file, located in the controller view directory,
 * or directly as a PHP array.
 *
 * @package winter\wn-backend-module
 * @author Luke Towers
 */
class CalendarController extends ControllerBehavior
{
    protected ?ToolbarWidget $toolbarWidget = null;
    protected ?FilterWidget $filterWidget = null;
    protected ?CalendarWidget $calendarWidget = null;

    /**
     * The initialized model used by the behavior.
     */
    protected Model $model;

    /**
     * The primary calendar alias to use, default 'calendar'
     */
    protected string $primaryDefinition = 'calendar';

    /**
     * Configuration values that must exist when applying the primary config file.
     * - modelClass: Class name for the model
     * - searchList: list field definitions for the search widget
     */
    protected array $requiredConfig = ['modelClass', 'searchList'];

    /**
     * Behavior constructor
     */
    public function __construct(\Backend\Classes\Controller $controller)
    {
        parent::__construct($controller);

        // Build the configuration
        $this->config = $this->makeConfig($controller->calendarConfig, $this->requiredConfig);
        $this->config->modelClass = Str::normalizeClassName($this->config->modelClass);
    }

    /**
     * Calendar Controller action
     */
    public function calendar(): void
    {
        $this->controller->pageTitle = $this->controller->pageTitle ? : Lang::get($this->getConfig(
            'title',
            'luketowers.calendarwidget::lang.behaviors.calendar.title'
        ));
        $this->controller->bodyClass = 'slim-container';
        $this->makeCalendar();
    }

    /**
     * Creates the Calendar widget used by this behavior
     */
    public function makeCalendar(): CalendarWidget
    {
        $model = $this->controller->calendarCreateModelObject();

        $config = $this->config;
        $config->model = $model;
        $config->alias = $this->primaryDefinition;

        // Initialize the Calendar widget
        $widget = $this->makeWidget(CalendarWidget::class, $config);
        $widget->model = $model;
        $widget->bindToController();
        $this->calendarWidget = $widget;

        // Initialize the Toolbar & Filter widgets
        $this->initToolbar($config, $widget);
        $this->initFilter($config, $widget);

        return $widget;
    }

    /**
     * Prepare the Toolbar widget if necessary
     */
    protected function initToolbar(stdClass $config, CalendarWidget $widget): void
    {
        if (empty($config->toolbar)) {
            return;
        }

        // Prepare the config and intialize the Toolbar widget
        $toolbarConfig = $this->makeConfig($config->toolbar);
        $toolbarConfig->alias = $widget->alias . 'Toolbar';
        $toolbarWidget = $this->makeWidget(ToolbarWidget::class, $toolbarConfig);
        $toolbarWidget->bindToController();
        $toolbarWidget->cssClasses[] = 'list-header';

        /*
         * Link the Search widget to the Calendar widget
         */
        if ($searchWidget = $toolbarWidget->getSearchWidget()) {
            $searchWidget->bindEvent('search.submit', function () use ($widget, $searchWidget) {
                $widget->setSearchTerm($searchWidget->getActiveTerm());
                return $widget->onRefresh();
            });

            $widget->setSearchOptions([
                'mode' => $searchWidget->mode,
                'scope' => $searchWidget->scope,
            ]);

            // Find predefined search term
            $widget->setSearchTerm($searchWidget->getActiveTerm());
        }

        $this->toolbarWidget = $toolbarWidget;
    }

    /**
     * Prepare the Filter widget if necessary
     */
    protected function initFilter(stdClass $config, CalendarWidget $widget): void
    {
        if (empty($config->filter)) {
            return;
        }

        $widget->cssClasses[] = 'list-flush';

        // Prepare the config and intialize the Toolbar widget
        $filterConfig = $this->makeConfig($config->filter);
        $filterConfig->alias = $widget->alias . 'Filter';
        $filterWidget = $this->makeWidget(FilterWidget::class, $filterConfig);
        $filterWidget->bindToController();

        /*
         * Filter the Calendar when the scopes are changed
         */
        $filterWidget->bindEvent('filter.update', function () use ($widget, $filterWidget) {
            return $widget->onFilter();
        });

        // Apply predefined filter values
        $widget->addFilter([$filterWidget, 'applyAllScopesToQuery']);
        $this->filterWidget = $filterWidget;
        $widget->filterWidget = $this->filterWidget;

    }

    /**
     * Creates a new instance of a calendar model. This logic can be changed by overriding it in the controller.
     */
    public function calendarCreateModelObject(): Model
    {
        $class = $this->config->modelClass;
        return new $class;
    }

    /**
     * Render the calendar widget
     *
     * @throws ApplicationException if the calendar widget has not been initialized
     */
    public function calendarRender($options = []): string
    {
        if (empty($this->calendarWidget)) {
            throw new ApplicationException(Lang::get('backend::lang.calendar.behavior_not_ready'));
        }

        if (!empty($options['readOnly']) || !empty($options['disabled'])){
            $this->calendarWidget->previewMode = true;
        }

        if (isset($options['preview'])) {
            $this->calendarWidget->previewMode = $options['preview'];
        }

        return $this->calendarMakePartial('container', [
            'toolbar'  => $this->toolbarWidget,
            'filter'   => $this->filterWidget,
            'calendar' => $this->calendarWidget,
        ]);
    }

    /**
     * Render the requested partial, providing opportunity for the controller to take over
     */
    public function calendarMakePartial(string $partial, array $params = []): string
    {
        $contents = $this->controller->makePartial('calendar_' . $partial, $params, false);
        if (!$contents) {
            $contents = $this->makePartial($partial, $params);
        }
        return $contents;
    }
}
