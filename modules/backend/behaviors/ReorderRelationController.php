<?php namespace Backend\Behaviors;

use ApplicationException;
use Backend;
use Backend\Classes\ControllerBehavior;
use Lang;
use Winter\Storm\Database\Model;
use Winter\Storm\Database\Traits\HasSortableRelations;

/**
 * Used for reordering and sorting related records.
 *
 * This behavior is implemented in the controller like so:
 *
 *     public $implement = [
 *         'Backend.Behaviors.ReorderRelationController',
 *     ];
 *     // Optional:
 *     public $reorderRelationConfig = 'config_reorder_relation.yaml';
 *
 * The `$reorderRelationConfig` property makes reference to the configuration
 * values as either a YAML file, located in the controller view directory,
 * or directly as a PHP array.
 *
 * @package winter\wn-backend-module
 */
class ReorderRelationController extends ControllerBehavior
{
    /**
     * @var Model The related sort model
     */
    public $model;

    /**
     * @var HasSortableRelations|Model The parent/form model
     */
    public $parentModel;

    /**
     * @var string The relation that is being sorted
     */
    public $relation;

    /**
     * Behavior constructor
     * @param Backend\Classes\Controller $controller
     */
    public function __construct($controller)
    {
        parent::__construct($controller);

        // The configuration is optional for this behavior.
        $this->config = [];
        if ($controller->reorderRelationConfig) {
            $this->config = $this->makeConfig($controller->reorderRelationConfig, []);
        }
    }

    //
    // AJAX
    //

    /**
     * Updates the relation order.
     * @throws \Exception
     */
    public function onReorderRelation()
    {
        $this->reorderRelationGetModel();
        $this->validateModel();

        if (
            (!$ids = post('record_ids')) ||
            (!$orders = post('sort_orders'))
        ) {
            return;
        }

        /** @var HasSortableRelations $instance */
        $instance = $this->parentModel->newQuery()->find($this->postValue('_reorder_parent_id'));
        $instance->setRelationOrder($this->relation, $ids, $orders);
    }

    /**
     * Returns the modal contents.
     * @return string
     */
    public function onRelationButtonReorder()
    {
        $this->addJs('../../reordercontroller/assets/js/winter.reorder.js', 'core');

        $this->reorderRelationGetModel();
        $this->validateModel();
        $this->prepareVars();

        $params = [
            'reorderRelation' => $this->relation,
            'reorderModel' => get_class($this->parentModel),
            'reorderParentId' => $this->postValue('_reorder_parent_id'),
            'reorderSortColumn' => $this->parentModel->getRelationSortOrderColumn($this->relation),
        ];

        return $this->reorderRelationMakePartial(
            'relation_modal',
            $params + [
                'container' => $this->reorderRelationMakePartial('container', $params),
                'requestParams' => $this->reorderRelationMakePartial('request_params', $params),
            ]
        );
    }

    /**
     * Update the relation widget once the model gets closed.
     * @return array
     */
    public function onRelationModalClose()
    {
        $this->reorderRelationGetModel();

        $this->controller->initRelation(
            $this->parentModel->newQuery()->findOrFail($this->postValue('_reorder_parent_id')),
            $this->relation
        );

        return [
            '#' . $this->controller->relationGetId('view') => $this->controller->relationRenderView($this->relation),
        ];
    }

    //
    // Reordering
    //

    /**
     * Sets all required model properties.
     */
    public function reorderRelationGetModel()
    {
        $this->parentModel = $this->reorderRelationGetParentModel();
        $this->relation = post('_reorder_relation_name');

        $relationModelClass = array_get($this->parentModel->getRelationDefinition($this->relation), 0);
        if (!$relationModelClass) {
            throw new ApplicationException(
                sprintf('Could not determine model class for relation "%s"', $this->relation)
            );
        }

        return $this->model = new $relationModelClass;
    }

    /**
     * Returns all the records from the supplied model.
     * @return Collection
     */
    protected function getRecords()
    {
        $query = $this->parentModel->newQuery();

        $this->controller->reorderExtendRelationQuery($query);

        return $query
            ->with([$this->relation => function ($q) {
                $q->orderBy($this->parentModel->getRelationSortOrderColumn($this->relation), 'ASC');
            }])
            ->findOrFail($this->postValue('_reorder_parent_id'))
            ->{$this->relation};
    }

    /**
     * Extend the relation query used for finding reorder records. Extra conditions
     * can be applied to the query, for example, $query->withTrashed();
     * @param Winter\Storm\Database\Builder $query
     * @return void
     */
    public function reorderExtendRelationQuery($query)
    {
    }

    //
    // Helpers
    //

    /**
     * Prepares common partial variables.
     */
    protected function prepareVars()
    {
        $this->vars['reorderRecords'] = $this->getRecords();
        $this->vars['reorderModel'] = $this->model;
    }

    /**
     * Return a model instance based on the _reorder_model post value.
     * @return Model
     */
    public function reorderRelationGetParentModel()
    {
        $model = $this->postValue('_reorder_model');

        if (!class_exists($model)) {
            throw new ApplicationException(
                sprintf('Model class "%s" does not exist', $model)
            );
        }

        return new $model;
    }

    public function getRelationRecordSortOrder($record, $sortColumn)
    {
        return $record->pivot ? $record->pivot->{$sortColumn} : $record->{$sortColumn};
    }

    /**
     * Validate the supplied form model.
     * @return void
     */
    protected function validateModel()
    {
        $modelTraits = class_uses($this->parentModel);

        if (!isset($modelTraits[\Winter\Storm\Database\Traits\HasSortableRelations::class])) {
            throw new ApplicationException(
                sprintf('The "%s" model must implement the HasSortableRelations trait.', get_class($this->parentModel))
            );
        }
    }

    /**
     * Returns the name attribute for a given $record. The attribute
     * defined in the behaviour config is used here.
     *
     * @param Model $record
     * @param       $relation
     *
     * @return string
     */
    public function reorderRelationGetRecordName(Model $record, $relation)
    {
        $attribute = array_get((array)$this->config, "$relation.nameFrom");
        if ($attribute) {
            return (string)$record->$attribute;
        }

        // Take a guess if no "nameFrom" config is set.
        return (string)($record->name ?: $record->title);
    }

    /**
     * Controller accessor for making partials within this behavior.
     * @param string $partial
     * @param array $params
     * @return string Partial contents
     */
    public function reorderRelationMakePartial($partial, $params = [])
    {
        $contents = $this->controller->makePartial(
            'reorder_' . $partial,
            $params + $this->vars,
            false
        );

        if (!$contents) {
            $contents = $this->makePartial($partial, $params);
        }

        return $contents;
    }

    /**
     * Fetch a post value for the current relation.
     *
     * @param string $key
     *
     * @return mixed
     */
    private function postValue(string $key)
    {
        $relation = post('_reorder_relation_name');

        return post($key. '.' . $relation);
    }
}
