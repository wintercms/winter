<?php namespace Backend\Traits;

use Exception;
use System\Classes\ErrorHandler;
use System\Models\EventLog;
use Winter\Storm\Exception\ApplicationException;

/**
 * Error Maker Trait
 * Adds exception based methods to a class, goes well with `System\Traits\ViewMaker`.
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
trait ErrorMaker
{
    /**
     * @var string Object used for storing a fatal error.
     */
    protected $fatalError;

    /**
     * @return boolean Whether a fatal error has been set or not.
     */
    public function hasFatalError()
    {
        return !is_null($this->fatalError);
    }

    /**
     * @return string The fatal error message
     */
    public function getFatalError()
    {
        return $this->fatalError;
    }

    /**
     * Sets standard page variables in the case of a controller error.
     */
    public function handleError($exception)
    {
        if (
            $exception instanceof Exception
            && !($exception instanceof ApplicationException)
        ) {
            EventLog::addException($exception);
        }

        $errorMessage = ErrorHandler::getDetailedMessage($exception);
        $this->fatalError = $errorMessage;
        $this->vars['fatalError'] = $errorMessage;
    }
}
