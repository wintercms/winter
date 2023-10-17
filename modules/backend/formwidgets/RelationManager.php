<?php

namespace Backend\FormWidgets;

use ApplicationException;
use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Lang;

class RelationManager extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'relationmanager';

    /**
     * @var bool Disables the ability to add, update, delete or create relations.
     */
    protected $readOnly;

    /**
     * @var string path to controller action to open a record.
     */
    protected $recordUrl;

    /**
     * @var string custom JavaScript code to execute when clicking on a record.
     */
    protected $recordOnClick;

    /**
     * @var string relation name if different from the field name.
     */
    protected $relation;

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
            throw new ApplicationException($error);
        }

        $options = [
            'readOnly' => $this->readOnly,
            'recordUrl' => $this->recordUrl,
        ];
        if (isset($this->recordOnClick)) {
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
