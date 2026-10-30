<?php

return [
    'throttle' => [

        /*
        |--------------------------------------------------------------------------
        | Enable throttling of Backend authentication attempts
        |--------------------------------------------------------------------------
        |
        | If set to true, users will be given a limited number of attempts to sign
        | in to the Backend before being blocked for a specified number of minutes.
        |
        */

        'enabled' => true,

        /*
        |--------------------------------------------------------------------------
        | Failed Authentication Attempt Limit
        |--------------------------------------------------------------------------
        |
        | Number of failed attempts allowed while trying to authenticate a user.
        |
        */

        'attemptLimit' => 5,

        /*
        |--------------------------------------------------------------------------
        | Suspension Time
        |--------------------------------------------------------------------------
        |
        | The number of minutes to suspend further attempts on authentication once
        | the attempt limit is reached.
        |
        */

        'suspensionTime' => 15,
    ],

    'passwords' => [
        'backend' => [

            /*
            |--------------------------------------------------------------------------
            | Password Reset Code Lifetime
            |--------------------------------------------------------------------------
            |
            | The number of minutes a Backend password reset code stays valid for after
            | it has been issued. Once that window has passed the code is refused and a
            | new password reset has to be requested.
            |
            */

            'expire' => 60,

            /*
            |--------------------------------------------------------------------------
            | Password Reset Re-issue Window
            |--------------------------------------------------------------------------
            |
            | The number of seconds that must pass before the public password restore
            | form will issue another code for the same account. Inside the window the
            | request is answered normally but nothing is issued and nothing is mailed,
            | so a code already in the account holder's inbox stays usable. Set this to
            | zero to allow unlimited re-issues.
            |
            */

            'throttle' => 60,
        ],
    ],
];
