<?php namespace Backend\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;

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

    public function init(): void
    {
        $this->fillFromConfig([
            'readOnly',
            '$recordUrl',
            '$recordOnClick',
        ]);

        if (!isset($this->readOnly)) {
            $this->readOnly = $this->config->previewMode;
        }
    }

    public function render()
    {
        $options = [
            'readOnly' => $this->readOnly,
            'recordUrl' => $this->recordUrl,
        ];
        if (isset($this->recordOnClick)) {
            $options['recordOnClick'] = $this->recordOnClicks;
        }

        return $this->controller->relationRender($this->formField->fieldName, $options);
    }

    public function getSaveValue($value)
    {
        return FormField::NO_SAVE_DATA;
    }
}
