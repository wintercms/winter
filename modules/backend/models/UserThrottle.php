<?php

namespace Backend\Models;

use Winter\Storm\Auth\Models\Throttle as ThrottleBase;
use Winter\Storm\Support\Facades\Config;

/**
 * Administrator throttling model
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class UserThrottle extends ThrottleBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'backend_user_throttle';

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => User::class,
    ];

    /**
     * @inheritDoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::$attemptLimit = Config::get('auth.throttle.attemptLimit', 5);
        static::$suspensionTime = Config::get('auth.throttle.suspensionTime', 15);
    }
}
