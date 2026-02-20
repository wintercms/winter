<?php namespace Backend\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Backend\Models\Preference as BackendPreference;

/**
 * Code Editor
 * Renders a code editor field.
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class CodeEditor extends FormWidgetBase
{
    //
    // Configurable properties
    //

    /**
     * @var string Code language to display (php, twig)
     */
    public $language = 'php';

    /**
     * @var boolean Determines whether the gutter is visible.
     */
    public $showGutter = true;

    /**
     * @var boolean Indicates whether the the word wrapping is enabled.
     */
    public $wordWrap = true;

    /**
     * @var bool Enables code folding.
     */
    public $codeFolding = true;

    /**
     * @var boolean Automatically close tags and special characters,
     * like quotation marks, parenthesis, or brackets.
     */
    public $autoClosing = true;

    /**
     * @var boolean Indicates whether the the editor uses spaces for indentation.
     */
    public $useSoftTabs = true;

    /**
     * @var boolean Sets the size of the indentation.
     */
    public $tabSize = 4;

    /**
     * @var integer Sets the font size.
     */
    public $fontSize = 12;

    /**
     * @var integer Sets the editor margin size.
     */
    public $margin = 0;

    /**
     * @var float Number of screen heights to allow scrolling past the end of the document
     */
    public $scrollPastEnd = 0;

    /**
     * @var string Editor theme to use.
     */
    public $theme = 'twilight';

    /**
     * @var bool Show invisible characters.
     */
    public $showInvisibles = false;

    /**
     * @var bool Highlight the active line.
     */
    public $highlightActiveLine = true;

    /**
     * @var boolean If true, the editor is set to read-only mode
     */
    public $readOnly = false;

    /**
     * @var boolean If true, the editor show Indent Guides
     */
    public $displayIndentGuides = true;

    /**
     * @var boolean If true, the editor show Print Margin
     */
    public $showPrintMargin = false;

    /**
     * @var boolean Show minimap (code preview) on the right of the editor
     */
    public $showMinimap = true;

    /**
     * @var boolean Colorize brackets
     */
    public $bracketColors = false;

    /**
     * @var boolean Show inline color previews and color picker
     */
    public $showColors = true;

    //
    // Object properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'codeeditor';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->applyEditorPreferences();

        if ($this->formField->disabled) {
            $this->readOnly = true;
        }

        $this->fillFromConfig([
            'language',
            'showGutter',
            'wordWrap',
            'codeFolding',
            'autoClosing',
            'useSoftTabs',
            'tabSize',
            'fontSize',
            'margin',
            'scrollPastEnd',
            'theme',
            'showInvisibles',
            'highlightActiveLine',
            'readOnly',
            'displayIndentGuides',
            'showPrintMargin',
            'showMinimap',
            'bracketColors',
            'showColors',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('codeeditor');
    }

    /**
     * Prepares the widget data
     */
    public function prepareVars()
    {
        $this->vars['fontSize'] = $this->fontSize;
        $this->vars['wordWrap'] = $this->wordWrap;
        $this->vars['codeFolding'] = $this->codeFolding;
        $this->vars['autoClosing'] = $this->autoClosing;
        $this->vars['tabSize'] = $this->tabSize;
        $this->vars['theme'] = $this->theme;
        $this->vars['showInvisibles'] = $this->showInvisibles;
        $this->vars['highlightActiveLine'] = $this->highlightActiveLine;
        $this->vars['useSoftTabs'] = $this->useSoftTabs;
        $this->vars['showGutter'] = $this->showGutter;
        $this->vars['language'] = $this->language;
        $this->vars['margin'] = $this->margin;
        $this->vars['scrollPastEnd'] = $this->scrollPastEnd;
        $this->vars['stretch'] = $this->formField->stretch;
        $this->vars['size'] = $this->formField->size;
        $this->vars['readOnly'] = $this->readOnly;
        $this->vars['displayIndentGuides'] = $this->displayIndentGuides;
        $this->vars['showPrintMargin'] = $this->showPrintMargin;
        $this->vars['showMinimap'] = $this->showMinimap;
        $this->vars['bracketColors'] = $this->bracketColors;
        $this->vars['showColors'] = $this->showColors;

        // Double encode when escaping
        $this->vars['value'] = htmlentities($this->getLoadValue(), ENT_QUOTES, 'UTF-8', true);
        $this->vars['name'] = $this->getFieldName();
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('css/codeeditor.css', 'core');
        $this->addJs('js/build/codeeditor.bundle.js', 'core');
    }

    /**
     * Looks at the user preferences and overrides any set values.
     * @return void
     */
    protected function applyEditorPreferences()
    {
        // Load the editor system settings
        $preferences = BackendPreference::instance();

        $this->fontSize = $preferences->editor_font_size;
        $this->wordWrap = $preferences->editor_word_wrap;
        $this->codeFolding = $preferences->editor_enable_folding ?? ($preferences->editor_code_folding !== 'manual');
        $this->autoClosing = $preferences->editor_auto_closing;
        $this->tabSize = $preferences->editor_tab_size;
        $this->theme = $preferences->editor_theme;
        $this->showInvisibles = $preferences->editor_show_invisibles;
        $this->highlightActiveLine = $preferences->editor_highlight_active_line;
        $this->useSoftTabs = !$preferences->editor_use_hard_tabs;
        $this->showGutter = $preferences->editor_show_gutter;
        $this->displayIndentGuides = $preferences->editor_display_indent_guides;
        $this->showPrintMargin = $preferences->editor_show_print_margin;
        $this->showMinimap = $preferences->editor_show_minimap;
        $this->bracketColors = $preferences->editor_bracket_colors;
        $this->showColors = $preferences->editor_show_colors;
    }
}
