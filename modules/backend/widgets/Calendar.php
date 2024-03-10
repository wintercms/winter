<?php

namespace Backend\Widgets;

use ApplicationException;
use Backend;
use Backend\Classes\ListColumn;
use Backend\Classes\WidgetBase;
use Backend\Widgets\Calendar\Classes\EventData;
use Carbon\Carbon;
use Config;
use DateTimeZone;
use Db;
use DbDongle;
use Lang;
use Response;
use Winter\Storm\Database\Model;
use Winter\Storm\Html\Helper as HtmlHelper;
use Winter\Storm\Router\Helper as RouterHelper;

/**
 * Calendar Widget
 */
class Calendar extends WidgetBase
{
    const MONTH_START_END_CACHE_KEY = 'calendar-month-time-start-end';

    //
    // Configurable
    //

    /**
     * Calendar model object
     */
    public Model $model;

    /**
     * @var mixed The list configuration for additional table columns to search by (uses columns.yaml format)
     */
    public $searchList;

    /**
     * Link for each calendar event. Replace :id with the record id.
     */
    public string $recordUrl = '';

    /**
     * Click event for each calendar event. Replace :id with the record id.
     */
    public string $recordOnClick = '';

    /**
     * Triggered when the user clicks on a date or a time
     */
    public string $onClickDate = '';

    /**
     * The model property to use as the title displayed on the calendar
     */
    public string $recordTitle = 'name';

    /**
     * The model property to use as the start time for the record
     */
    public string $recordStart = 'start_at';

    /**
     * The model property to use as the end time for the record
     */
    public string $recordEnd = 'end_at';

    /**
     * The model property to use to show the background color of this record, '' = the default background color in the calendar.less
     */
    public ?string $recordColor = null;

    /**
     * The model property to use as the content of the tooltip for the record
     */
    public string|array $recordTooltip = '';

    /**
     * Display modes to allow ['month', 'week', 'day', 'list']
     */
    public array $availableDisplayModes = [];

    /**
     * Calendar of CSS classes to apply to the Calendar container element
     */
    public array $cssClasses = [];

    //
    // INTERNAL
    //

    /**
     * Collection of functions to apply to each list query.
     */
    protected array $filterCallbacks = [];

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'calendar';

    /**
     * Render this form with uneditable preview data.
     */
    public bool $previewMode = true;

    /**
     * Instantiated search columns ['name' => ListColumn]
     */
    protected array $searchColumns = [];

    public $searchTerm;
    public $searchMode;
    public $searchScope;

    public $filterWidget;

    public $searchableColumns = null;
    public $visibleColumns = null;
    public $calendarVisibleColumns = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'columns',
            'recordUrl',
            'recordOnClick',
            'onClickDate',
            'recordTitle',
            'recordStart',
            'recordEnd',
            'recordColor',
            'recordTooltip',
            'previewMode',
            'searchList',
            'availableDisplayModes',
        ]);

        // Initialize the search columns
        $list = $this->makeConfig($this->searchList);
        $columns = [];
        if (!empty($list->columns)) {
            foreach ($list->columns as $name => $config) {
                $columns[$name] = $this->makeListColumn($name, $config);
            }
        }
        $this->searchColumns = $columns;

        $this->calendarVisibleColumns = [$this->recordTitle, $this->recordStart, $this->recordEnd];
    }

    /**
     * Returns the record URL address for a calendar event.
     */
    public function getRecordUrl(Model $record): ?string
    {
        if (!empty($this->recordOnClick)) {
            // return 'javascript:;';
            return $this->recordOnClick;
        }

        if (!isset($this->recordUrl)) {
            return null;
        }

        $url = RouterHelper::replaceParameters($record, $this->recordUrl);
        return Backend::url($url);
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss(['packages/core/main.min.css', 'packages/list/main.min.css', 'packages/daygrid/main.min.css', 'packages/timegrid/main.min.css'], '4.1.0');
        $this->addCss(['less/calendar.less'], 'Winter.Core');

        //Tooltip
        $this->addJs('packages/vendor/popper.min.js', '4.1.0');
        $this->addJs('packages/vendor/tooltip.min.js', '4.1.0');

        //Calendar
        $this->addJs('packages/core/main.min.js', '4.1.0');
        $this->addJs('packages/list/main.min.js', '4.1.0');
        $this->addJs('packages/daygrid/main.min.js', '4.1.0');
        $this->addJs('packages/timegrid/main.min.js', '4.1.0');
        $this->addJs('packages/interaction/main.min.js', '4.1.0');

        // @see https://fullcalendar.io/docs/v4/timeZone
        $this->addJs('packages/moment-timezone/main.min.js', '4.1.0');

        $this->addJs('js/calendar.cache.js', 'Winter.Core');
        $this->addJs('js/calendar.js', 'Winter.Core');
    }

    /**
     * @inheritDoc
     */
    public function prepareVars()
    {
        $this->vars['availableDisplayModes'] = $this->getDisplayModes();
        $this->vars['cssClasses'] = implode(' ', $this->cssClasses);
    }

    /**
     * Get the fullcalendar.js display modes to be used
     */
    protected function getDisplayModes(): array
    {
        // Convert our display modes to FullCalendar display modes
        if (!is_array($this->availableDisplayModes)) {
            $this->availableDisplayModes = [$this->availableDisplayModes];
        }

        $fullCalendarModes = [
            'month' => 'dayGridMonth',
            'week'  => 'timeGridWeek',
            'day'   => 'timeGridDay',
            'list'  => 'listMonth'
        ];

        $selectedModes = [];
        foreach ($this->availableDisplayModes as $mode) {
            if (!empty($fullCalendarModes[$mode])) {
                $selectedModes[] = $fullCalendarModes[$mode];
            }
        }
        return implode(',', $selectedModes);
    }

    /**
     * Render the widget
     */
    public function render(): string
    {
        $this->prepareVars();
        return $this->makePartial('calendar');
    }

    /**
     * Copy from Lists.php
     * Checks if a column refers to a pivot model specifically.
     * @param  ListColumn  $column List column object
     */
    protected function isColumnPivot($column): bool
    {
        if (!isset($column->relation) || $column->relation != 'pivot') {
            return false;
        }

        return true;
    }

    protected function isColumnInCalendar($column)
    {
        return in_array($column->columnName, $this->calendarVisibleColumns);
    }

    protected function isColumnRelated($column, $multi = false)
    {
        if (!isset($column->relation) || $this->isColumnPivot($column)) {
            return false;
        }

        if (!$this->model->hasRelation($column->relation)) {
            throw new ApplicationException(Lang::get(
                'backend::lang.model.missing_relation',
                ['class' => get_class($this->model), 'relation' => $column->relation]
            ));
        }

        if (!$multi) {
            return true;
        }

        $relationType = $this->model->getRelationType($column->relation);

        return in_array($relationType, [
            'hasMany',
            'belongsToMany',
            'morphToMany',
            'morphedByMany',
            'morphMany',
            'attachMany',
            'hasManyThrough'
        ]);
    }

    /**
     * Returns a collection of columns which can be searched.
     * @return array
     */
    protected function getSearchableColumns()
    {
        if ($this->searchableColumns != null) return $this->searchableColumns;
        $searchable = [];

        foreach ($this->searchColumns as $column) {
            if (empty($column->searchable)) {
                continue;
            }

            $searchable[] = $column;
        }
        $this->searchableColumns = $searchable;
        return $searchable;
    }

    protected function getVisibleRelationColumns()
    {
        if ($this->visibleColumns != null) return $this->visibleColumns;

        $defaultColumnNames = $this->calendarVisibleColumns;
        $searchableColumns = $this->getSearchableColumns();
        $searchableColumnNames = [];
        foreach($searchableColumns  as $column) $searchableColumnNames[] = $column->columnName;

        $visibleColumns = array_unique(array_merge($defaultColumnNames, $searchableColumnNames));

        $this->visibleColumns = [];
        foreach ($this->searchColumns as $name => $column){
            if(in_array($name , $visibleColumns )){
                $this->visibleColumns[$name] = $column;
            }
        }
        return $this->visibleColumns;
    }

    /**
     * Replaces the @ symbol with a table name in a model
     * @param  string $sql
     * @param  string $table
     * @return string
     */
    protected function parseTableName($sql, $table)
    {
        return str_replace('@', $table.'.', $sql);
    }

    /**
     * Applies the search constraint to a query.
     */
    protected function applySearchToQuery($query, $columns, $boolean = 'and')
    {
        $term = $this->searchTerm;

        if ($scopeMethod = $this->searchScope) {
            $searchMethod = $boolean == 'and' ? 'where' : 'orWhere';
            $query->$searchMethod(function ($q) use ($term, $columns, $scopeMethod) {
                $q->$scopeMethod($term, $columns);
            });
        }
        else {
            $searchMethod = $boolean == 'and' ? 'searchWhere' : 'orSearchWhere';
            $query->$searchMethod($term, $columns, $this->searchMode);
        }
    }

    /**
     * Creates a ListColumn object from its name and configuration.
     *
     * @param string $name
     * @param array $config
     * @return ListColumn
     */
    protected function makeListColumn($name, $config)
    {
        if (is_string($config)) {
            $label = $config;
        } elseif (isset($config['label'])) {
            $label = $config['label'];
        } else {
            $label = studly_case($name);
        }

        /*
         * Auto configure pivot relation
         */
        if (starts_with($name, 'pivot[') && strpos($name, ']') !== false) {
            $_name = HtmlHelper::nameToArray($name);
            $relationName = array_shift($_name);
            $valueFrom = array_shift($_name);

            if (count($_name) > 0) {
                $valueFrom  .= '['.implode('][', $_name).']';
            }

            $config['relation'] = $relationName;
            $config['valueFrom'] = $valueFrom;
            $config['searchable'] = false;
        }
        /*
         * Auto configure standard relation
         */
        elseif (strpos($name, '[') !== false && strpos($name, ']') !== false) {
            $config['valueFrom'] = $name;
            $config['sortable'] = false;
            $config['searchable'] = false;
        }

        $columnType = $config['type'] ?? null;

        $column = new ListColumn($name, $label);
        $column->displayAs($columnType, $config);

        return $column;
    }

    /**
     * Applies any filters to the model.
     * @param integer $startTime unixTimestamp, the current calendar month startTime, eg: 1546149600
     * @param integer $endTime unixTimestamp, the current calendar month endTime, eg: 1549778400
     */
    public function prepareQuery($startTime = 0, $endTime = 0)
    {
        $query = $this->model->newQuery();
        $primaryTable = $this->model->getTable();
        $selects = [$primaryTable.'.*'];
        $joins = [];
        $withs = [];

        /**
         * @event backend.calendar.extendQueryBefore
         * Provides an opportunity to modify the `$query` object before the Calendar widget applies its scopes to it.
         *
         * Example usage:
         *
         *     Event::listen('backend.calendar.extendQueryBefore', function($calendarWidget, $query) {
         *         $query->whereNull('deleted_at');
         *     });
         *
         * Or
         *
         *     $calendarWidget->bindEvent('calendar.extendQueryBefore', function ($query) {
         *         $query->whereNull('deleted_at');
         *     });
         *
         */
        $this->fireSystemEvent('backend.calendar.extendQueryBefore', [$query]);

        /*
         * Prepare searchable column names
         */
        $primarySearchable = [];
        $relationSearchable = [];

        $columnsToSearch = [];
        if (!empty($this->searchTerm) && ($searchableColumns = $this->getSearchableColumns())) {
            foreach ($searchableColumns as $column) {
                /*
                 * Related
                 */
                if ($this->isColumnRelated($column)) {
                    $table = $this->model->makeRelation($column->relation)->getTable();
                    $columnName = isset($column->sqlSelect)
                        ? DbDongle::raw($this->parseTableName($column->sqlSelect, $table))
                        : $table . '.' . $column->valueFrom;

                    $relationSearchable[$column->relation][] = $columnName;
                }
                /*
                 * Primary
                 */
                else {
                    $columnName = isset($column->sqlSelect)
                        ? DbDongle::raw($this->parseTableName($column->sqlSelect, $primaryTable))
                        : DbDongle::cast(Db::getTablePrefix() . $primaryTable . '.' . $column->columnName, 'TEXT');

                    $primarySearchable[] = $columnName;
                }
            }
        }
        $visibleColumns = $this->getVisibleRelationColumns();
        foreach ($visibleColumns as $column) {

            // If useRelationCount is enabled, eager load the count of the relation into $relation_count
            if ($column->relation && @$column->config['useRelationCount']) {
                $query->withCount($column->relation);
            }
            if (!$this->isColumnRelated($column) || (!isset($column->sqlSelect) && !isset($column->valueFrom))) {
                continue;
            }
            if (isset($column->valueFrom)) {
                $withs[] = $column->relation;
            }
            $joins[] = $column->relation;
        }

        /*
         * Add eager loads to the query
         */
        if ($withs) {
            $query->with(array_unique($withs));
        }
        /*
         * Apply search term and start_time end_time
         */
        $query->where(function ($innerQuery) use ($primarySearchable, $relationSearchable, $joins, $startTime, $endTime) {

            /*
             * Search primary columns
             */
            if (count($primarySearchable) > 0) {
                $this->applySearchToQuery($innerQuery, $primarySearchable, 'or');
            }

            /*
             * Search relation columns
             */
            if ($joins) {
                foreach (array_unique($joins) as $join) {
                    /*
                     * Apply a supplied search term for relation columns and
                     * constrain the query only if there is something to search for
                     */
                    $columnsToSearch = array_get($relationSearchable, $join, []);

                    if (count($columnsToSearch) > 0) {
                        $innerQuery->orWhereHas($join, function ($_query) use ($columnsToSearch) {
                            $this->applySearchToQuery($_query, $columnsToSearch);
                        });
                    }
                }
            }

        });

        /*
         * Custom select queries
         */
        foreach ($visibleColumns as $column) {
            if (!isset($column->sqlSelect) || !$this->isColumnInCalendar($column)) {
                continue;
            }

            $alias = $query->getQuery()->getGrammar()->wrap($column->columnName);

            /*
             * Relation column
             */
            if (isset($column->relation)) {

                // @todo Find a way...
                $relationType = $this->model->getRelationType($column->relation);
                if ($relationType == 'morphTo') {
                    throw new ApplicationException('The relationship morphTo is not supported for Calendar columns.');
                }

                $table =  $this->model->makeRelation($column->relation)->getTable();
                $sqlSelect = $this->parseTableName($column->sqlSelect, $table);

                /*
                 * Manipulate a count query for the sub query
                 */
                $relationObj = $this->model->{$column->relation}();
                $countQuery = $relationObj->getRelationExistenceQuery($relationObj->getRelated()->newQueryWithoutScopes(), $query);

                $joinSql = $this->isColumnRelated($column, true)
                    ? DbDongle::raw("group_concat(" . $sqlSelect . " separator ', ')")
                    : DbDongle::raw($sqlSelect);

                $joinSql = $countQuery->select($joinSql)->toSql();

                $selects[] = Db::raw("(".$joinSql.") as ".$alias);
            }
            /*
             * Primary column
             */
            else {
                $sqlSelect = $this->parseTableName($column->sqlSelect, $primaryTable);
                $selects[] = DbDongle::raw($sqlSelect . ' as '. $alias);
            }
        }

        /*
         * Apply filters
         */
        foreach ($this->filterCallbacks as $callback) {
            $callback($query);
        }
        /*
         * Add custom selects
         */
        $query->addSelect($selects);

        /**
         * @event backend.calendar.extendQuery
         * Provides an opportunity to modify and / or return the `$query` object after the Calendar widget has applied its scopes to it and before it's used to get the records.
         *
         * Example usage:
         *
         *     Event::listen('backend.calendar.extendQuery', function($calendarWidget, $query) {
         *         $newQuery = MyModel::newQuery();
         *         return $newQuery;
         *     });
         *
         * Or
         *
         *     $calendarWidget->bindEvent('calendar.extendQuery', function ($query) {
         *         $query->whereNull('deleted_at');
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('backend.calendar.extendQuery', [$query])) {
            return $event;
        }
        return $query;
    }

    /**
     *
     * Create a MD5 string based on current query SQL
     * to set the cacheKey in calendar cache
     *
     * @see MemoryCache->hash()
     *
     * @param QueryBuilder $query
     * @return string md5
     */
    protected function getCacheKey($query){
        $bindings = array_map(function ($binding) {
            return (string)$binding;
        }, $query->getBindings());

        $name = $query->getConnection()->getName();
        $md5 = md5($name . $query->toSql() . serialize($bindings));
        return $md5;
    }

    /**
     *
     *
     * @param integer $startTime unixTimestamp, the current calendar month startTime, eg: 1546149600
     * @param integer $endTime unixTimestamp, the current calendar month endTime, eg: 1549778400
     * @return array ['events'=> [ {url, title, start, end}], 'cacheKey'=> 'MD5 String']
     */
    public function getRecords($startTime = 0 , $endTime = 0)
    {
        $query = $this->prepareQuery($startTime, $endTime);
        $cacheKey = $this->getCacheKey($query);

        /**
         * The $startTime and $endTime are from calendar month, should be ignore
         */
        if ($startTime > 0 ||  $endTime > 0){
            $query->where(function ($innerQuery) use ($startTime, $endTime) {
                if ($startTime > 0) $innerQuery->whereRaw($this->recordEnd .' >= ?', [Carbon::createFromTimestamp($startTime)]);
                if ($endTime > 0) $innerQuery->whereRaw($this->recordStart . ' < ?', [Carbon::createFromTimestamp($endTime)]);
            });
        }

        $records = $query->get();

        /**
         * @event 'backend.calendar.extendRecords'
         * Provides an opportunity to modify and / or return the `$records` Collection object before the widget uses it.
         *
         * Example usage:
         *
         *     Event::listen('backend.calendar.extendRecords', function($calendarWidget, &$records , $startTime, $endTime) {
         *         $records=$data;
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('backend.calendar.extendRecords', [&$records, $startTime, $endTime])) {
            $records = $event;
        }


        $events = [];

        $timeZone = new DateTimeZone(Config::get('app.timezone','UTC'));

        foreach ($records as $record) {
            if (empty($this->recordTooltip)) {
                $tooltip = null;
            } else {
                if (is_array($this->recordTooltip)) {
                    $tooltip = '';
                    foreach ($this->recordTooltip as $item){
                        $keyName = $this->{$item};
                        $tooltip .= $record->{$keyName} . ' ';
                    }
                } else {
                    $tooltip = $record->{$this->recordTooltip};
                }
            }

            $eventData = new EventData([
                'id'     => $record->getKey(),
                'url'    => $this->getRecordUrl($record),
                'title'  => $record->{$this->recordTitle},
                'start'  => $record->{$this->recordStart},
                'end'    => $record->{$this->recordEnd},
                'allDay' => (bool) $record->allDay,
                'color'  => empty($this->recordColor) ? '' : $record->{$this->recordColor},
                'tooltip' => $tooltip
            ], $timeZone);
            $events[] = $eventData->toArray();
        }

        /**
         * @event 'backend.calendar.extendEvents'
         * Provides an opportunity to modify and / or return the `$event` Collection object before the widget uses it.
         *
         * Example usage:
         *
         *     Event::listen('backend.calendar.extendEvents', function($calendarWidget, &$events) {
         *         $records=$data;
         *     });
         *
         *
         */
        if ($event = $this->fireSystemEvent('backend.calendar.extendEvents', [&$events])) {
            $events = $event;
        }

        return [
            'events' => $events,
            'cacheKey' => $cacheKey,
            'startTime' => $startTime,
            'endTime' => $endTime,
        ];
    }


    public function onFetchEvents()
    {
        return Response::json($this->getRecords());
    }

    /**
     * It has been called from the first refresh page
     * and next or previous button click event
     *
     * @return json
     */
    public function onRefreshEvents()
    {
        $startTime = post('startTime');
        $endTime = post('endTime');
        $timeZone = post('timeZone');

        $data = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'timeZone' => $timeZone
        ];

        if ($this->isFilteredByDateRange()){
            $startTime = 0;
            $endTime = 0;
        }

        return Response::json($this->getRecords($startTime, $endTime));
    }

    // search

    /**
     * Applies a search term to the list results, searching will disable tree
     * view if a value is supplied.
     * @param string $term
     */
    public function setSearchTerm($term)
    {
        $this->searchTerm = $term;
    }

    /**
     * Applies a search options to the list search.
     * @param array $options
     */
    public function setSearchOptions($options = [])
    {
        extract(array_merge([
            'mode' => null,
            'scope' => null
        ], $options));

        $this->searchMode = $mode;
        $this->searchScope = $scope;
    }

    /**
     *
     * If filter has DateRange type and some values with that
     * will ignore the current month startTime and endTime
     *
     * @return boolean
     */
    protected function isFilteredByDateRange()
    {
        if ($this->filterWidget === null) return false;

        $filterScopes = $this->filterWidget->getScopes();

        if (!empty($filterScopes)){
            foreach ($filterScopes as $scope) {
                if ($scope->type === 'daterange' && !empty($scope->value)) {
                    return true;
                }
            }
        }

        /**
         * Need to double check the session
         *
         * scopes is config array
         */
        $scopes = $this->filterWidget->scopes;

        if (empty($scopes)) return false;

        foreach($scopes as $scopeName => $scopeConfig)
        {
            if ($scopeConfig['type'] !== 'daterange') continue;
            $cacheKey = 'scope-' . $scopeName;
            $value = $this->filterWidget->getSession($cacheKey, null);
            if (!empty($value)) return true;
        }

        return false;
    }


    /**
     * Get the startTime and endTime from the october.calendar.js Calendar.prototype.beforeFilterRequestSend
     *
     * @return array $data [startTime=>1546149600, endTime=>1549778400, timeZone=>America/Regina ]
     */
    protected function getMonthStartEndTime()
    {
        $calendar_time = post('calendar_time');
        return $calendar_time;
    }


     /**
     * Event handler for refreshing the calendar.
     * The search widget will call onRefresh
     * @see CalendarController->initToolbar
     */
    public function onRefresh()
    {
        $startTime = 0;
        $endTime = 0;
        if (!$this->isFilteredByDateRange()){
            $dateData = $this->getMonthStartEndTime();
            if(!empty($dateData)){
                $startTime = $dateData['startTime'];
                $endTime = $dateData['endTime'];
            }
        }

        $records = $this->getRecords($startTime, $endTime);
        $records['id'] = 'calendar';
        $records['method'] = 'onRefresh';

        return $records;
    }

    /**
     * Event handler for changing the filter
     */
    public function onFilter()
    {
        // $this->currentPageNumber = 1;
        return $this->onRefresh();
    }

    //
    // Filtering
    //

    public function addFilter(callable $filter)
    {
        $this->filterCallbacks[] = $filter;
    }
}

