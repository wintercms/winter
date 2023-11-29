<?php namespace Backend\FormWidgets;

use Backend\Classes\FormWidgetBase;
use File;
use Url;
use Yaml;

/**
 * Icon picker
 * Renders an icon picker field.
 *
 * @package winter\wn-backend-module
 * @author Robert Alexa, Jack Wilkinson
 */
class IconPicker extends FormWidgetBase
{
    public const DEFAULT_LIBRARIES = '~/modules/backend/formwidgets/iconpicker/meta/libraries.yaml';

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'iconpicker';

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('iconpicker');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['field'] = $this;
    }

    /**
     * @inheritDoc
     */
    public function loadAssets(): void
    {
        $this->addJs('js/dist/iconpicker.js', 'core');
    }

    public function onLoadIconLibrary()
    {
        $libraries = $this->config->libraries ?? static::DEFAULT_LIBRARIES;

        if (is_string($libraries)) {
            $libraries = Yaml::parseFile(File::symbolizePath($libraries));
        }

        return json_encode($libraries);
    }
}
