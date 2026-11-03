<?php namespace Backend\Classes;

/**
 * Report Widget base class
 * Report widgets are used inside the ReportContainer.
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class ReportWidgetBase extends WidgetBase
{
    /**
     * Returns this widget's configured title, translated.
     *
     * A key naming a translation group resolves to an array, which cannot be rendered, so fall
     * back to the configured value rather than letting one stored title break the dashboard.
     */
    public function getTitle(): string
    {
        $title = $this->property('title');

        if (!is_string($title)) {
            return '';
        }

        $translated = trans($title);

        return is_string($translated) ? $translated : $title;
    }

    use \System\Traits\PropertyContainer;

    public function __construct($controller, $properties = [])
    {
        $this->properties = $this->validateProperties($properties);

        /*
         * Ensure the provided alias (if present) takes effect as the widget configuration is
         * not passed to the WidgetBase constructor which would normally take care of that
         */
        if (!isset($this->alias)) {
            $this->alias = $properties['alias'] ?? $this->defaultAlias;
        }

        parent::__construct($controller);
    }
}
