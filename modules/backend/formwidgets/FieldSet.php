<?php

namespace Backend\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Backend\Widgets\Form;

/**
 * FieldSet
 * Renders a fieldset from multiple form fields.
 *
 * @package winter\wn-backend-module
 * @author Marc Jauvin <marc.jauvin@gmail.com>
 */
class FieldSet extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'fieldset';

    /**
     * @var array Field configuration
     */
    public $fields;

    /**
     * @var bool Determines if this form field should display comments and labels.
     */
    public $showLabels = false;

    /**
     * @var Form form widget reference
     */
    protected $formWidget;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'fields',
        ]);

        if ($this->formField->disabled) {
            $this->previewMode = true;
        }

        $config = $this->makeConfig(['fields' => $this->fields]);
        $config->model = $this->model;
        $config->data = $this->getLoadValue();
        $config->alias = $this->alias . $this->defaultAlias;
        // set arrayName from parent form to save fields to the model
        $config->arrayName = $this->getParentForm()->arrayName;
        // the fields are nested visually only; they resolve against the parent
        // form's model and array name, exactly as if defined on the parent form
        $config->isNested = true;
        $config->sharesParentScope = true;

        $widget = $this->formWidget = $this->makeWidget(Form::class, $config);
        $widget->previewMode = $this->previewMode;
        $widget->bindToController();
    }

    protected function loadAssets()
    {
        $this->addCss('css/fieldset.css', 'core');
    }

    /**
     * Returns the save data for the nested fields, to be merged into the parent
     * form's data as if these fields were defined at that level. Reusing the nested
     * form's getSaveData() ensures number casting, widget getSaveValue() handling,
     * NO_SAVE_DATA exclusion and disabled/hidden skipping all behave identically to
     * a regular field.
     */
    public function getSaveData(): array
    {
        return $this->formWidget->getSaveData();
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('fieldset');
    }

    public function prepareVars()
    {
        $this->formWidget->previewMode = $this->previewMode;
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return FormField::NO_SAVE_DATA;
    }
}
