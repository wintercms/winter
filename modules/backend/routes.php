<?php

Event::listen('system.route', function () {

    /**
     * Register Backend routes before all user routes.
     */

    /**
     * @event backend.beforeRoute
     * Fires before backend routes get added
     *
     * Example usage:
     *
     *     Event::listen('backend.beforeRoute', function () {
     *         // your code here
     *     });
     *
     */
    Event::fire('backend.beforeRoute');

    /*
     * Route everything to `Backend\Classes\BackendController`, which itself acts as a router for the Backend.
     */
    Route::group([
        'middleware' => ['web'],
        'prefix' => Config::get('cms.backendUri', 'backend')
    ], function () {
        Route::any('{slug?}', 'Backend\Classes\BackendController@run')->where('slug', '(.*)?');
    });

    /**
     * @event backend.route
     * Fires after backend routes have been added
     *
     * Example usage:
     *
     *     Event::listen('backend.route', function () {
     *         // your code here
     *     });
     *
     */
    Event::fire('backend.route');
});
