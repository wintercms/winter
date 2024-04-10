<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache marketplace packages
    |--------------------------------------------------------------------------
    |
    | By default, marketplace metadata is cached locally to reduce the number
    | of calls to the API. This can be disabled by setting this value to false,
    | however, this may result in a slower experience when managing themes and
    | plugins, or browsing the marketplace.
    |
    */

    'cache' => true,

    /*
    |--------------------------------------------------------------------------
    | Marketplace package cache store
    |--------------------------------------------------------------------------
    |
    | If caching is enabled, the following value sets the store that should be
    | used to store the cache. If this is `null`, the default store will be
    | used.
    |
    */

    'store' => null,

];
