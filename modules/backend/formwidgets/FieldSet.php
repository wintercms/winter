<?php
namespace Backend\FormWidgets;

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
     * @var array Form configuration
     */
    public $form;

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
            'form',
        ]);

        if ($this->formField->disabled) {
            $this->previewMode = true;
        }

        $config = $this->makeConfig($this->form);
        $config->model = $this->model;
        $config->data = $this->getLoadValue();
        $config->alias = $this->alias . $this->defaultAlias;
        // set arrayName from parent form to save fields to the model
        $config->arrayName = $this->getParentForm()->arrayName;
        $config->isNested = true;

        $this->formWidget = $widget = $this->makeWidget(Form::class, $config);
        $widget->previewMode = $this->previewMode;
        $widget->bindToController();
    }

    protected function loadAssets()
    {
        $this->addCss('css/fieldset.css', 'core');
    }

    public function getFormFields()
    {
        return $this->formWidget->getFields();
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
}
