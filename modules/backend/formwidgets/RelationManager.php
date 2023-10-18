<?php

namespace Backend\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Lang;
use SystemException;

class RelationManager extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'relationmanager';

    /**
     * Disables the ability to add, update, delete or create relations.
     */
    protected bool $readOnly = false;

    /**
     * Path to controller action to open a record.
     */
    protected string $recordUrl = '';

    /**
     * Custom JavaScript code to execute when clicking on a record.
     */
    protected string $recordOnClick = '';

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

        if (!isset($this->readOnly)) {
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

        $options = [
            'readOnly' => $this->readOnly,
            'recordUrl' => $this->recordUrl,
        ];

        if ($this->recordOnClick) {
            $options['recordOnClick'] = $this->recordOnClick;
        }

        $relation = $this->relation ?: $this->formField->fieldName;

        return $this->controller->relationRender($relation, $options);
    }

    public function getSaveValue($value)
    {
        return FormField::NO_SAVE_DATA;
    }
}
