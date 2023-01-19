<?php

namespace Backend\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * Visual Editor
 *
 * Renders a visual content editor form field.
 *
 * @author Ben Thomson <git@alfreido.com>
 */
class VisualEditor extends FormWidgetBase
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        if ($this->formField->disabled) {
            $this->readOnly = true;
        }

        $this->fillFromConfig([
            'fullPage',
            'readOnly',
            'toolbarButtons',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('visualeditor');
    }

    public function prepareVars()
    {
        $this->vars['field'] = $this->formField;
        $this->vars['stretch'] = $this->formField->stretch;
        $this->vars['size'] = $this->formField->size;

        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $this->getLoadValue();
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('css/visualeditor.css', 'core');
        $this->addJs('js/dist/visualeditor.js', 'core');
    }
}
