<?php namespace Backend\Facades;

use Winter\Storm\Support\Facade;

class BackendMenu extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * Resolves to:
     * - Backend\Classes\NavigationManager
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'backend.menu';
    }
}
