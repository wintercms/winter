<?php namespace System\Models;

use App;
use Exception;
use Model;
use Str;

/**
 * Model for logging system errors and debug trace messages
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class EventLog extends Model
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'system_event_logs';

    /**
     * @var array List of attribute names which are json encoded and decoded from the database.
     */
    protected $jsonable = ['details'];

    /**
     * Returns true if this logger should be used.
     */
    public static function useLogging(): bool
    {
        return (
            !defined('WINTER_NO_EVENT_LOGGING') &&
            class_exists('Model') &&
            Model::getConnectionResolver() &&
            App::hasDatabase() &&
            LogSetting::get('log_events')
        );
    }

    /**
     * Creates a log record
     */
    public static function add(string $message, string $level = 'info', ?array $details = null): self
    {
        $record = new static;
        $record->message = $message;
        $record->level = $level;

        if ($details !== null) {
            $record->details = (array) $details;
        }

        try {
            $record->save();
        }
        catch (Exception $ex) {
        }

        return $record;
    }

    /**
     * Beautify level value.
     */
    public function getLevelAttribute(string $level): string
    {
        return ucfirst($level);
    }

    /**
     * Creates a shorter version of the message attribute,
     * extracts the exception message or limits by 100 characters.
     */
    public function getSummaryAttribute(): string
    {
        if (preg_match("/with message '(.+)' in/", $this->message, $match)) {
            return $match[1];
        }

        // Get first line of message
        preg_match('/^([^\n\r]+)/m', $this->message, $matches);

        return Str::limit($matches[1] ?? '', 500);
    }
}
