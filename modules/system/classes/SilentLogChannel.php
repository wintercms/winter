<?php

namespace System\Classes;

use Illuminate\Events\NullDispatcher;
use Illuminate\Log\Logger;

/**
 * Log channel tap that stops the channel firing MessageLogged, so its messages never reach the
 * backend event log. Used for the "null" deprecations channel.
 */
class SilentLogChannel
{
    public function __invoke(Logger $logger): void
    {
        if ($dispatcher = $logger->getEventDispatcher()) {
            $logger->setEventDispatcher(new NullDispatcher($dispatcher));
        }
    }
}
