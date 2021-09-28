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
 * @author Winter CMS
 */
class ColorPicker extends FormWidgetBase
{
    // All color formats supported
    const ALL_FORMATS = ['cmyk', 'hex', 'hsl', 'rgb'];

    //
    // Configurable properties
    //

    /**
     * @var array Default available colors
     */
    public $availableColors = [
        '#1abc9c', '#16a085',
        '#2ecc71', '#27ae60',
        '#3498db', '#2980b9',
        '#9b59b6', '#8e44ad',
        '#34495e', '#2b3e50',
        '#f1c40f', '#f39c12',
        '#e67e22', '#d35400',
        '#e74c3c', '#c0392b',
        '#ecf0f1', '#bdc3c7',
        '#95a5a6', '#7f8c8d',
    ];

    /**
     * @var bool Allow empty value
     */
    public $allowEmpty = false;

    /**
     * @var bool Allow a custom color
     */
    public $allowCustom = true;

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

    /**
     * @var string|array Color format(s) to allow for the resulting color value. Specify "all" as a string to allow all
     * formats.
     * Allowed values: 'cmyk', 'hex', 'hsl', 'rgb', 'all'
     */
    public $formats = 'hex';

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
            'formats',
            'allowEmpty',
            'allowCustom',
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
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['availableColors'] = $this->getAvailableColors();
        $this->vars['formats'] = $this->getFormats();
        $this->vars['allowEmpty'] = (bool) $this->allowEmpty;
        $this->vars['allowCustom'] = (bool) $this->allowCustom;
        $this->vars['showAlpha'] = (bool) $this->showAlpha;
        $this->vars['readOnly'] = (bool) $this->readOnly;
        $this->vars['disabled'] = (bool) $this->disabled;
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
     * Returns the allowed color formats.
     *
     * If no valid formats are specified, the "hex" format will be used.
     *
     * @return array
     */
    protected function getFormats()
    {
        if ($this->formats === 'all') {
            return static::ALL_FORMATS;
        }

        $availableFormats = [];
        $configFormats = (is_string($this->formats))
            ? [$this->formats]
            : $this->formats;

        foreach ($configFormats as $format) {
            if (in_array($format, static::ALL_FORMATS)) {
                $availableFormats[] = $format;
            }
        }

        return (count($availableFormats))
            ? $availableFormats
            : ['hex'];
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('vendor/pickr/pickr-nano.min.css', 'core');
        $this->addJs('vendor/pickr/pickr.min.js', 'core');
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
