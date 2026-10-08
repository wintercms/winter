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
     * A key naming a translation group resolves to an array rather than a string, and a title
     * is stored configuration that any widget publisher can set, so the configured value is
     * rendered as written whenever translating it does not produce a string. Otherwise one
     * stored title would make every dashboard carrying the widget unrenderable, including the
     * toolbar that would let it be reset.
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
