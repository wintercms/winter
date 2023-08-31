<?php

Event::listen('system.route', function () {
    /**
     * Register CMS routes before all user routes.
     */

    /**
     * @event cms.beforeRoute
     * Fires before cms routes get added
     *
     * Example usage:
     *
     *     Event::listen('cms.beforeRoute', function () {
     *         // your code here
     *     });
     *
     */
    $result = Event::fire('cms.beforeRoute', [], true);
    if ($result === false) {
        return;
    }

    /*
     * The CMS module handles all URLs that have not already been handled by the other modules & plugins.
     */
    Route::any('{slug?}', 'Cms\Classes\CmsController@run')->where('slug', '(.*)?')->middleware('web');

    /**
     * @event cms.route
     * Fires after cms routes get added
     *
     * Example usage:
     *
     *     Event::listen('cms.route', function () {
     *         // your code here
     *     });
     *
     */
    Event::fire('cms.route');
}, PHP_INT_MIN);
