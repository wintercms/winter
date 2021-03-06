<?php namespace Backend\Facades;

use Winter\Storm\Support\Facade;

class BackendAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * Resolves to:
     * - Backend\Classes\AuthManager
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'backend.auth';
    }
}
