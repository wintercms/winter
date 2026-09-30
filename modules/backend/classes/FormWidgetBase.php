<?php namespace Backend\Classes;

use Winter\Storm\Html\Helper as HtmlHelper;

/**
 * Form Widget base class
 * Widgets used specifically for forms
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
abstract class FormWidgetBase extends WidgetBase
{

    //
    // Configurable properties
    //

    /**
     * @var \Winter\Storm\Database\Model Form model object.
     */
    public $model;

    /**
     * @var array Dataset containing field values, if none supplied model should be used.
     */
    public $data;

    /**
     * @var string Active session key, used for editing forms and deferred bindings.
     */
    public $sessionKey;

    /**
     * @var bool Render this form with uneditable preview data.
     */
    public $previewMode = false;

    /**
     * @var bool Whether preview mode was set by the form or relation that built this widget, as
     * opposed to being set by the widget itself while rendering. This is what decides whether a
     * write handler is refused; see abortIfPreviewMode().
     */
    protected $previewModeFromConfig = false;

    /**
     * @var bool Determines if this form field should display comments and labels.
     */
    public $showLabels = true;

    //
    // Object properties
    //

    /**
     * @var FormField Object containing general form field information.
     */
    protected $formField;

    /**
     * @var Backend\Widgets\Form The parent form that contains this field
     */
    protected $parentForm = null;

    /**
     * @var string Form field name.
     */
    protected $fieldName;

    /**
     * @var string Model attribute to get/set value from.
     */
    protected $valueFrom;

    /**
     * Constructor
     * @param $controller Controller Active controller object.
     * @param $formField FormField Object containing general form field information.
     * @param $configuration array Configuration the relates to this widget.
     */
    public function __construct($controller, $formField, $configuration = [])
    {
        $this->formField = $formField;
        $this->fieldName = $formField->fieldName;
        $this->valueFrom = $formField->valueFrom;

        $this->config = $this->makeConfig($configuration);

        $this->fillFromConfig([
            'model',
            'data',
            'sessionKey',
            'previewMode',
            'showLabels',
            'parentForm',
        ]);

        // Recorded before any widget adjusts $previewMode for its own rendering, so that the
        // write guard below follows what built this widget rather than how it draws itself
        $this->previewModeFromConfig = (bool) $this->previewMode;

        parent::__construct($controller, $configuration);
    }

    /**
     * Abort the request with an access-denied code if the form that built this widget is not
     * editable.
     *
     * The parent form propagates preview mode to every field widget it builds
     * (Backend\Widgets\Form::makeFormFieldWidget()), and it is set for a form rendered in a
     * preview context and for a read only relation's forms. Widgets already honour it while
     * rendering, but an AJAX handler is dispatched straight to the widget by alias, so it does
     * not pass through whatever authorized the page.
     *
     * A field-level `disabled` is deliberately not covered. Several widgets set $previewMode from
     * it to render themselves as not editable, but `disabled` is a rendering concern - the client
     * decides whether it comes back - so it has never been a server side control and is not made
     * into one here. Only the configuration of the form or relation counts, which is what
     * $previewModeFromConfig holds.
     *
     * Any widget handler that writes must call this first. Handlers that only read - refreshing
     * a preview, loading a library, rendering a picker - should not, since a preview form is
     * still allowed to be looked at.
     */
    protected function abortIfPreviewMode(): void
    {
        if ($this->previewModeFromConfig) {
            abort(403);
        }
    }

    /**
     * Retrieve the parent form for this formwidget
     *
     * @return Backend\Widgets\Form|null
     */
    public function getParentForm()
    {
        return $this->parentForm;
    }

    /**
     * Returns the HTML element field name for this widget, used for capturing
     * user input, passed back to the getSaveValue method when saving.
     * @return string HTML element name
     */
    public function getFieldName()
    {
        return $this->formField->getName();
    }

    /**
     * Returns a unique ID for this widget. Useful in creating HTML markup.
     */
    public function getId($suffix = null)
    {
        $id = parent::getId($suffix);
        $id .= '-' . $this->fieldName;
        return HtmlHelper::nameToId($id);
    }

    /**
     * Process the postback value for this widget. If the value is omitted from
     * postback data, it will be NULL, otherwise it will be an empty string.
     * @param mixed $value The existing value for this widget.
     * @return string The new value for this widget.
     */
    public function getSaveValue($value)
    {
        return $value;
    }

    /**
     * Returns the value for this form field,
     * supports nesting via HTML array.
     * @return string
     */
    public function getLoadValue()
    {
        if ($this->formField->value !== null) {
            return $this->formField->value;
        }

        $defaultValue = !$this->model->exists
            ? $this->formField->getDefaultFromData($this->data ?: $this->model)
            : null;

        return $this->formField->getValueFromData($this->data ?: $this->model, $defaultValue);
    }
}
