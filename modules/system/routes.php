<?php

/**
 * Register System routes before all user routes.
 */

/**
 * @event system.beforeRoute
 * Fires before system routes get added
 *
 * Example usage:
 *
 *     Event::listen('system.beforeRoute', function () {
 *         // your code here
 *     });
 *
 */
Event::fire('system.beforeRoute');

/*
 * Combine JavaScript and StyleSheet assets
 */
Route::any('combine/{file}', 'System\Classes\SystemController@combine');

/*
 * Resize image assets
 */
Route::get('resizer/{identifier}/{encodedUrl}', 'System\Classes\SystemController@resizer');

/**
 * @event system.route
 * Fires after system routes get added
 *
 * Example usage:
 *
 *     Event::listen('system.route', function () {
 *         // your code here
 *     });
 *
 */
Event::fire('system.route');
