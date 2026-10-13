<?php

namespace Backend\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Illuminate\Support\Facades\Lang;
use Winter\Storm\Exception\SystemException;

class RelationManager extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'relationmanager';

    /**
     * Disables the ability to add, update, delete or create relations.
     */
    protected ?bool $readOnly = null;

    /**
     * Path to controller action to open a record.
     */
    protected ?string $recordUrl = null;

    /**
     * Custom JavaScript code to execute when clicking on a record.
     */
    protected ?string $recordOnClick = null;

    /**
     * Relation name if different from the field name.
     */
    protected string $relation = '';

    public function init(): void
    {
        $this->fillFromConfig([
            'readOnly',
            'recordUrl',
            'recordOnClick',
            'relation',
        ]);

        if (!isset($this->readOnly) && $this->config->previewMode) {
            $this->readOnly = $this->config->previewMode;
        }
    }

    public function render()
    {
        if (!$this->controller->isClassExtendedWith(\Backend\Behaviors\RelationController::class)) {
            $error = Lang::get('backend::lang.relation.missing_behavior', [
                'field' => $this->formField->fieldName,
                'controller' => get_class($this->controller),
            ]);
            throw new SystemException($error);
        }

        $options = [];

        if (!is_null($this->readOnly)) {
            $options['readOnly'] = $this->readOnly;
        }

        if (!is_null($this->recordUrl)) {
            $options['recordUrl'] = $this->recordUrl;
        }

        if (!is_null($this->recordOnClick)) {
            $options['recordOnClick'] = $this->recordOnClick;
        }

        $relation = $this->relation ?: $this->formField->fieldName;

        // Explicitly binds the controller's relation context to THIS
        // widget's own model/field pair before rendering. Needed for a
        // nested relation manager (one rendered inside another relation
        // manager's own manage form): without this, relationRender()'s
        // own internal validateField() check would lazily call
        // initRelation() using $this->model - RelationController's own,
        // currently-active model, which for a nested field is still
        // whatever the OUTER field left it as, not this widget's own
        // bound model. Harmless and idempotent for a root-level field
        // (the widget's model and the controller's current model are
        // already the same one), so this applies at any depth
        // uniformly, with no special-casing needed here.
        $this->controller->initRelation($this->model, $relation);

        return $this->controller->relationRender($relation, $options);
    }

    public function getSaveValue($value)
    {
        return FormField::NO_SAVE_DATA;
    }
}
