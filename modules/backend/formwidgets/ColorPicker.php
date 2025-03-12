<?php namespace Backend\FormWidgets;

use Lang;
use Backend\Classes\FormWidgetBase;
use ApplicationException;
use Backend\Models\BrandSetting;

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
    public $availableColors = null;

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

    /**
     * @var array|string[] Patterns to validate colour string on save
     */
    protected array $validationPatterns = [
        'cmyk' => '/^cmyk\((\d{1,2}\.?\d{0,2}%,? ?){4}\)$/',
        'hex' => '/^#[\w\d]{6,8}$/',
        'hsl' => '/^hsla\((\d{1,3}\.?\d{0,2}%?, ?){3}\d\.?\d{0,2}?\)$/',
        'rgb' => '/^rgba\((\d{1,3}\.?\d{0,2}, ?){3}\d\.?\d{0,2}?\)$/',
    ];

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
        } elseif (is_string($availableColors) && !empty($availableColors)) {
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
        } else {
            return $this->availableColors = array_map(function ($color) {
                return $color['color'];
            }, BrandSetting::get('default_colors', [
                [
                    'color' => '#1abc9c',
                ],
                [
                    'color' => '#16a085',
                ],
                [
                    'color' => '#6cc551',
                ],
                [
                    'color' => '#52a838',
                ],
                [
                    'color' => '#b1dbef',
                ],
                [
                    'color' => '#88c9e7',
                ],
                [
                    'color' => '#2da7c7',
                ],
                [
                    'color' => '#227f96',
                ],
                [
                    'color' => '#b281c5',
                ],
                [
                    'color' => '#7b4e8e',
                ],
                [
                    'color' => '#103141',
                ],
                [
                    'color' => '#081821',
                ],
                [
                    'color' => '#f8e095',
                ],
                [
                    'color' => '#dcb22d',
                ],
                [
                    'color' => '#de8754',
                ],
                [
                    'color' => '#d66829',
                ],
                [
                    'color' => '#b33f32',
                ],
                [
                    'color' => '#ab2a1c',
                ],
                [
                    'color' => '#95a5a6',
                ],
                [
                    'color' => '#7f8c8d',
                ],
            ]));
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
        $this->addJs('js/dist/colorpicker.js', 'core');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        if (!strlen($value)) {
            return null;
        }

        switch (is_array($this->formats) ? 'all' : $this->formats) {
            case 'cmyk':
            case 'hex':
            case 'hsl':
            case 'rgb':
                if (!preg_match($this->validationPatterns[$this->formats], $value)) {
                    throw new ApplicationException(Lang::get('backend::lang.field.colors_invalid_input'));
                }
                break;
            case 'all':
                $valid = false;
                foreach ($this->validationPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $valid = true;
                        break;
                    }
                }
                if (!$valid) {
                    throw new ApplicationException(Lang::get('backend::lang.field.colors_invalid_input'));
                }
                break;
        }

        return $value;
    }
}
