<?php namespace System\Models;

use Exception;
use Illuminate\Support\Facades\App;
use Throwable;
use ReflectionClass;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Str;

/**
 * Model for logging system errors and debug trace messages
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class EventLog extends Model
{
    protected const EXCEPTION_LOG_VERSION = 2;
    protected const EXCEPTION_SNIPPET_LINES = 12;

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
            static::hasDatabaseTable() &&
            LogSetting::get('log_events')
        );
    }

    /**
     * Creates a log record
     */
    public static function add(string $message, string $level = 'info', ?array $details = null): static
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
     * Creates an exception log record
     */
    public static function addException(Throwable $throwable, string $level = 'error'): static
    {
        $record = new static;
        $record->message = $throwable->getMessage();
        $record->level = $level;
        $record->details = $record->getDetails($throwable);

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

    /**
     * Constructs the details array for logging
     */
    public function getDetails(Throwable $throwable): array
    {
        return [
            'logVersion' => static::EXCEPTION_LOG_VERSION,
            'exception' => $this->exceptionToArray($throwable),
            'environment' => $this->getEnviromentInfo(),
        ];
    }

    /**
     * Convert a throwable into an array of data for logging
     */
    protected function exceptionToArray(Throwable $throwable): array
    {
        return [
            'type' => $throwable::class,
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'snippet' => $this->getSnippet($throwable->getFile(), $throwable->getLine()),
            'trace' => $this->exceptionTraceToArray($throwable->getTrace()),
            'stringTrace' => $throwable->getTraceAsString(),
            'code' => $throwable->getCode(),
            'previous' => $throwable->getPrevious()
                ? $this->exceptionToArray($throwable->getPrevious())
                : null,
        ];
    }

    /**
     * Generate an array trace with extra data not provided by the default trace
     *
     * @throws \ReflectionException
     */
    protected function exceptionTraceToArray(array $trace): array
    {
        foreach ($trace as $index => $frame) {
            if (!isset($frame['file']) && isset($frame['class'])) {
                $ref = new ReflectionClass($frame['class']);
                $frame['file'] = $ref->getFileName();

                if (!isset($frame['line']) && isset($frame['function']) && !str_contains($frame['function'], '{')) {
                    foreach (file($frame['file']) as $line => $text) {
                        if (preg_match(sprintf('/function\s.*%s/', $frame['function']), $text)) {
                            $frame['line'] = $line + 1;
                            break;
                        }
                    }
                }
            }

            $trace[$index] = [
                'file' => $frame['file'] ?? null,
                'line' => $frame['line'] ?? null,
                'function' => $frame['function'] ?? null,
                'class' => $frame['class'] ?? null,
                'type' => $frame['type'] ?? null,
                'snippet' => !empty($frame['file']) && !empty($frame['line'])
                    ? $this->getSnippet($frame['file'], $frame['line'])
                    : '',
                'in_app' => ($frame['file'] ?? null) ? $this->isInAppError($frame['file']) : false,
                'arguments' => array_map(function ($arg) {
                    if (is_numeric($arg)) {
                        return $arg;
                    }
                    if (is_string($arg)) {
                        return "'$arg'";
                    }
                    if (is_null($arg)) {
                        return 'null';
                    }
                    if (is_bool($arg)) {
                        return $arg ? 'true' : 'false';
                    }
                    if (is_array($arg)) {
                        return 'Array';
                    }
                    if (is_object($arg)) {
                        return get_class($arg);
                    }
                    if (is_resource($arg)) {
                        return 'Resource';
                    }
                }, $frame['args'] ?? []),
            ];
        }

        return $trace;
    }

    /**
     * Get the code snippet referenced in a trace
     */
    protected function getSnippet(string $file, int $line): array
    {
        if (str_contains($file, ': eval()\'d code')) {
            return [];
        }

        $lines = file($file);

        if (count($lines) < static::EXCEPTION_SNIPPET_LINES) {
            return $lines;
        }

        return array_slice(
            $lines,
            $line - (static::EXCEPTION_SNIPPET_LINES / 2),
            static::EXCEPTION_SNIPPET_LINES,
            true
        );
    }

    /**
     * Get environment details to record with the exception
     */
    protected function getEnviromentInfo(): array
    {
        if (app()->runningInConsole()) {
            return [
                'context' => 'CLI',
                'testing' => app()->runningUnitTests(),
                'env' => app()->environment(),
            ];
        }

        return [
            'context' => 'Web',
            'backend' => method_exists(app(), 'runningInBackend') ? app()->runningInBackend() : false,
            'testing' => app()->runningUnitTests(),
            'url' => app('url')->current(),
            'method' => app('request')->method(),
            'env' => app()->environment(),
            'ip' => app('request')->ip(),
            'userAgent' => app('request')->header('User-Agent'),
        ];
    }

    /**
     * Helper to work out if a file should be considered "In App" or not
     */
    protected function isInAppError(string $file): bool
    {
        if (basename($file) === 'index.php' || basename($file) === 'artisan') {
            return false;
        }

        return !Str::startsWith($file, base_path('vendor')) && !Str::startsWith($file, base_path('modules'));
    }
}
