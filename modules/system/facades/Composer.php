<?php

namespace System\Facades;

use Winter\Storm\Support\Facade;

/**
 * @mixin \Winter\Packager\Composer
 * @see \Winter\Packager\Composer
 */
class Composer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'system.composer';
    }
}
