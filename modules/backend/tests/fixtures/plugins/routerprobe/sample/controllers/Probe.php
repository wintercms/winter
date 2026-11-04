<?php

namespace RouterProbe\Sample\Controllers;

use Backend\Classes\Controller;

/**
 * Stands in for a plugin controller that is not autoloadable under the casing the URL uses.
 *
 * `/backend/routerprobe/sample/probe` asks for `\routerprobe\sample\Controllers\probe`, which no
 * autoloader resolves on a case-sensitive filesystem. Only `BackendController::findController()`'s
 * case-insensitive lookup finds `Probe.php`, so routing to this class proves that lookup is intact.
 */
class Probe extends Controller
{
    public function index()
    {
    }
}
