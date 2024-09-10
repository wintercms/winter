<?php

namespace Backend\Widgets;

use Backend\Classes\WidgetBase;
use Winter\Storm\Database\Builder;
use Winter\Storm\Database\Model;
use Winter\Storm\Halcyon\Model as HalcyonModel;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\Event;

/**
 * Workbench list widget.
 *
 * Lists items in a workbench section.
 *
 * @author Ben Thomson <git@alfreido.com>
 */
class WorkbenchList extends WidgetBase
{
    protected string $model = '';

    protected ?string $scope = null;

    protected string $title = '';

    protected string $mode = 'list';

    protected bool $multiselect = false;

    protected Toolbar $toolbar;

    public function init()
    {
        $this->fillFromConfig([
            'model',
            'scope',
            'title',
            'mode',
            'multiselect',
        ]);

        if ($this->getConfig('toolbar')) {
            $this->toolbar = new Toolbar($this->controller);
        }
    }

    /**
     * Renders the widget.
     *
     * @return string|false
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('widget');
    }

    protected function prepareVars(): void
    {
        $this->vars['items'] = $this->loadItems();
    }

    protected function loadItems(): array
    {
        $query = $this->createModelQuery();

        // Allow extension of the query to retrieve items
        Event::dispatch('backend.workbench.list.extendQuery', [$query]);
        $this->fireEvent('workbench.list.extendQuery', [$query]);

        $items = $query->get()->collect();

        // Allow extension of the items collection
        Event::dispatch('backend.workbench.list.extendItems', [$items]);
        $this->fireEvent('workbench.list.extendItems', [$items]);

        return $items->map(function (Model|HalcyonModel $item) {
            $details = [];
            $details['id'] = $item->getKey();
            $details['title'] = $this->getItemData('title', $item);
            $details['description'] = $this->getItemData('description', $item);
            $details['icon'] = $this->getItemData('icon', $item);
            return $details;
        })->toArray();
    }

    protected function getItemData(string $field, Model|HalcyonModel $model): ?string
    {
        $dataField = $this->getConfig('item[' . $field . ']', $field);

        if (str_contains($dataField, '.')) {
            [$relation, $field] = explode('.', $dataField);

            if (!$model->hasRelation($relation) || !$model->{$relation}->hasAttribute($field)) {
                return null;
            }

            return (string) $model->{$relation}->{$field};
        }

        if (!$model->hasAttribute($dataField)) {
            return null;
        }

        return (string) $model->{$dataField};
    }

    protected function createModelQuery(): Builder
    {
        if (!class_exists($this->model)) {
            throw new ApplicationException(sprintf('Model class %s not found.', $this->model));
        }

        // Create query
        if (!is_null($this->scope)) {
            /** @var Builder */
            $query = $this->model::{$this->scope}();
        } else {
            /** @var Builder */
            $query = $this->model::query();
        }

        // Determine if we need to retrieve any relation info
        if ($this->getConfig('item[title]') && str_contains($this->getConfig('item[title]'), '.')) {
            $query->with(explode('.', $this->getConfig('item[title]'))[0]);
        }
        if ($this->getConfig('item[description]') && str_contains($this->getConfig('item[description]'), '.')) {
            $query->with(explode('.', $this->getConfig('item[description]'))[0]);
        }

        if ($this->getConfig('item[sort]', false)) {
            $query->orderBy($this->getConfig('item[sort][column]'), $this->getConfig('item[sort][direction]', 'asc'));
        }

        return $query;
    }
}
