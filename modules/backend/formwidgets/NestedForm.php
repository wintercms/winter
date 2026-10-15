<?php namespace Backend\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Backend\Widgets\Form;

/**
 * Nested Form
 * Renders a nested form bound to a jsonable field of a model.
 *
 * @package winter\wn-backend-module
 * @author Sascha Aeppli
 */
class NestedForm extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'nestedform';

    /**
     * @var array Form configuration
     */
    public $form;

    /**
     * @var bool defines if the nested form is styled like a panel (default true).
     */
    public $usePanelStyles = true;

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
            'usePanelStyles',
        ]);

        if ($this->formField->disabled) {
            $this->previewMode = true;
        }

        $config = $this->makeConfig($this->form);
        $config->model = $this->model;
        // Scope the nested form to its own value. When the field has no value yet,
        // pass an empty array rather than null: a null data source makes the inner
        // Form fall back to the parent model (Form::getModel()), which resolves
        // sub-fields against the model's attributes and crashes when a sub-field
        // name collides with a model attribute (e.g. a jsonable array). See #1522.
        $config->data = $this->getLoadValue() ?: [];
        $config->alias = $this->alias . $this->defaultAlias;
        $config->arrayName = $this->getFieldName();
        $config->isNested = true;

        if (object_get($this->getParentForm()->config, 'enableDefaults') === true) {
            $config->enableDefaults = true;
        }

        $widget = $this->makeWidget(Form::class, $config);
        $widget->previewMode = $this->previewMode;
        $widget->bindToController();

        $this->formWidget = $widget;
    }

    protected function loadAssets()
    {
        $this->addCss('css/nestedform.css', 'core');
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('nestedform');
    }

    public function prepareVars()
    {
        $this->formWidget->previewMode = $this->previewMode;
    }
}
