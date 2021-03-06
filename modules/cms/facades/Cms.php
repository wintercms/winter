<?php namespace Cms\Facades;

use Winter\Storm\Support\Facade;

class Cms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @see \Cms\Helpers\Cms
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cms.helper';
    }
}
