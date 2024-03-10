<?php

namespace Backend\Widgets\Calendar\Classes;

use ApplicationException;
use DateTime;
use DateTimeZone;

/**
 * Event Data Class, used by FullCalendar.js
 *
 * JSON (toArray) Format:
 * {
 *      "title": "Repeating Event",
 *      "start": "2019-01-09T16:00:00-05:00"
 * }
 */
class EventData
{
    // Tests whether the given ISO8601 string has a time-of-day or not
    const ALL_DAY_REGEX = '/^\d{4}-\d\d-\d\d$/'; // matches strings like "2013-12-29"

    /**
     * Title of the event
     */
    public string $title;

    /**
     * Indicates if the event runs all day
     */
    public bool $allDay;

    /**
     * The event start
     */
    public DateTime $start;

    /**
     * The event end
     */
    public ?DateTime $end = null;

    /**
     * Tooltip content
     */
    public ?string $tooltip = null;

    /**
     * Other additional properties
     */
    public array $properties = [];

    /**
     * Constructs an EventData object from the provided configuration
     *
     * @param array $config ['title' => '', 'allDay' => '', 'start' => '', 'end' => '']
     * @param DateTimeZone|null $timeZone The timezone to force
     */
    public function __construct(array $config, ?DateTimeZone $timeZone = null)
    {
        $required = ['title', 'start'];
        foreach ($required as $property) {
            if (empty($config[$property])) {
                throw new ApplicationException("$property is required when instancing an EventData object");
            }
        }

        $this->title = $config['title'];

        // Guess the allDay property
        if (isset($config['allDay'])) {
            $this->allDay = (bool) $config['allDay'];
        } else {
            $this->allDay = preg_match(self::ALL_DAY_REGEX, $config['start']) && (!isset($config['end']) || preg_match(self::ALL_DAY_REGEX, $config['end']));
        }

        // If dates are allDay, we want to parse them in UTC to avoid DST issues.
        if ($this->allDay) {
            $timeZone = null;
        }

        // Parse dates
        $this->start = $this->parseDateTime($config['start'], $timeZone);
        $this->end = isset($config['end']) ? $this->parseDateTime($config['end'], $timeZone) : null;

        // Store other properties
        foreach ($config as $name => $value) {
            if (!in_array($name, ['title', 'allDay', 'start', 'end'])) {
                $this->properties[$name] = $value;
            }
        }
    }

    /**
     * Convert the EventData object into a plain array to be used to pass to FullCalendar.js as JSON
     */
    public function toArray(): array
    {
        // Get the defined additional properties
        $properties = $this->properties;

        // Get the title
        $properties['title'] = $this->title;

        // Figure out the date format. This essentially encodes allDay into the date string.
        if ($this->allDay) {
            $format = 'Y-m-d'; // output like "2013-12-29"
        } else {
            $format = 'c'; // full ISO8601 output, like "2013-12-29T09:00:00+08:00"
        }

        // Add the dates
        $properties['start'] = $this->start->format($format);
        if (isset($this->end)) {
            $properties['end'] = $this->end->format($format);
        }

        return $properties;
    }

    /**
     * Parses a string into a DateTime object, optionally forced into the given timeZone.
     */
    protected function parseDateTime(string $dateTime, ?DateTimeZone $timeZone = null): DateTime
    {
        $date = new DateTime(
            $dateTime,
            $timeZone ? $timeZone : new DateTimeZone('UTC')
            // Used only when the string is ambiguous.
            // Ignored if $dateTime has a timeZone offset in it.
        );
        if ($timeZone) {
            // If our timeZone was ignored above, force it.
            $date->setTimezone($timeZone);
        }
        return $date;
    }
}
