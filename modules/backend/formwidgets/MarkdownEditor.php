<?php

namespace Backend\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Backend\Facades\BackendAuth;
use Winter\Storm\Support\Facades\Html;
use Winter\Storm\Support\Facades\Markdown;

/**
 * Code Editor
 * Renders a code editor field.
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MarkdownEditor extends FormWidgetBase
{
    //
    // Configurable properties
    //

    /**
     * @var string Display mode: split, tab.
     */
    public $mode = 'tab';

    /**
     * @var bool Render preview with safe markdown.
     */
    public $safe = false;

    /**
     * @var bool If true, the editor is set to read-only mode
     */
    public $readOnly = false;

    /**
     * @var bool If true, the editor is set to read-only mode
     */
    public $disabled = false;

    //
    // Object properties
    //

    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'markdown';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->fillFromConfig([
            'mode',
            'safe',
            'readOnly',
            'disabled',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('markdowneditor');
    }

    /**
     * Prepares the widget data
     */
    public function prepareVars()
    {
        $this->vars['mode'] = $this->mode;
        $this->vars['stretch'] = $this->formField->stretch;
        $this->vars['size'] = $this->formField->size;
        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['readOnly'] = $this->readOnly;
        $this->vars['disabled'] = $this->disabled;
        $this->vars['useMediaManager'] = BackendAuth::getUser()->hasAccess('media.manage_media');
    }

    /**
     * {@inheritDoc}
     */
    protected function loadAssets()
    {
        $this->addCss('css/markdowneditor.css', 'core');
        $this->addJs('js/markdowneditor.js', 'core');
        $this->addJs('/modules/backend/assets/vendor/ace-codeeditor/build-min.js', 'core');
    }

    /**
     * Check to see if the generated HTML should be cleaned to remove any potential XSS
     *
     * @return boolean
     */
    protected function shouldCleanHtml()
    {
        $user = BackendAuth::getUser();
        return !$user || !$user->hasAccess('backend.allow_unsafe_markdown');
    }

    /**
     * {@inheritDoc}
     */
    public function getSaveValue($value)
    {
        if ($this->shouldCleanHtml()) {
            $value = Html::clean($value);
        }

        return $value;
    }

    /**
     * AJAX handler to render the markdown as HTML
     *
     * @return array ['preview' => $generatedHTML]
     */
    public function onRefresh()
    {
        $value = post($this->getFieldName());
        $previewHtml = $this->safe
            ? Markdown::parseSafe($value)
            : Markdown::parse($value);

        if ($this->shouldCleanHtml()) {
            $previewHtml = Html::clean($previewHtml);
        }

        return [
            'preview' => $previewHtml
        ];
    }
}
