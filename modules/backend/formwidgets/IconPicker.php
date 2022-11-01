<?php namespace Backend\FormWidgets;

use Url;
use Config;
use Backend\Classes\FormWidgetBase;
use Winter\Storm\Exception\ApplicationException;

/**
 * Icon picker
 * Renders an icon picker field.
 *
 * @package winter\wn-backend-module
 * @author Robert Alexa, Jack Wilkinson
 */
class IconPicker extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'iconpicker';

    /**
     * @inheritDoc
     */
    public function render()
    {
        try {
            $this->prepareVars();
        } catch (ApplicationException $ex) {
            $this->vars['error'] = $ex->getMessage();
        }

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
        $this->assetPath = Url::asset('modules/backend/formwidgets/iconpicker/assets/dist');
        $this->addJs('app.js', 'core');
    }

    public function onLoadIconLibrary()
    {
        return json_encode(Config::get('backend::icons'));
    }
}
