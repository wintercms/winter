<?php namespace Backend\FormWidgets;

use Lang;
use Backend\Classes\FormWidgetBase;
use ApplicationException;

/**
 * Color picker
 * Renders a color picker field.
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class ColorPicker extends FormWidgetBase
{
    //
    // Configurable properties
    //

    /**
     * @var array Default available colors
     */
    public $availableColors = [
        '#1abc9c', '#16a085',
        '#6cc551', '#52a838',
        '#b1dbef', '#88c9e7',
        '#2da7c7', '#227f96',
        '#b281c5', '#7b4e8e',
        '#103141', '#081821', 
        '#F8E095', '#dcb22d', 
        '#de8754', '#d66829', 
        '#b33f32', '#ab2a1c',
        '#95a5a6', '#7f8c8d',
    ];

    /**
     * @var bool Allow empty value
     */
    public $allowEmpty = false;

    /**
     * @var bool Show opacity slider
     */
    public $showAlpha = false;

    /**
     * @var bool If true, the color picker is set to read-only mode
     */
    public $readOnly = false;

    /**
     * @var bool If true, the color picker is set to disabled mode
     */
    public $disabled = false;

    //
    // Object properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'colorpicker';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'availableColors',
            'allowEmpty',
            'showAlpha',
            'readOnly',
            'disabled',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('colorpicker');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $value = $this->getLoadValue();
        $this->vars['availableColors'] = $availableColors = $this->getAvailableColors();
        $this->vars['allowEmpty'] = $this->allowEmpty;
        $this->vars['showAlpha'] = $this->showAlpha;
        $this->vars['readOnly'] = $this->readOnly;
        $this->vars['disabled'] = $this->disabled;
        $this->vars['isCustomColor'] = !in_array($value, $availableColors);
    }

    /**
     * Gets the appropriate list of colors.
     *
     * @return array
     */
    protected function getAvailableColors()
    {
        $availableColors = $this->availableColors;
        if (is_array($availableColors)) {
            return $availableColors;
        }
        elseif (is_string($availableColors) && !empty($availableColors)) {
            if ($this->model->methodExists($availableColors)) {
                return $this->availableColors = $this->model->{$availableColors}(
                    $this->formField->fieldName,
                    $this->formField->value,
                    $this->formField->config
                );
            } else {
                throw new ApplicationException(Lang::get('backend::lang.field.colors_method_not_exists', [
                    'model'  => get_class($this->model),
                    'method' => $availableColors,
                    'field'  => $this->formField->fieldName
                ]));
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('vendor/spectrum/spectrum.css', 'core');
        $this->addJs('vendor/spectrum/spectrum.js', 'core');
        $this->addCss('css/colorpicker.css', 'core');
        $this->addJs('js/colorpicker.js', 'core');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return strlen($value) ? $value : null;
    }
}
