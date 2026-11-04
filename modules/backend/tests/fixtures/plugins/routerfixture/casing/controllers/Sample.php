<?php

namespace RouterFixture\Casing\Controllers;

use Backend\Classes\Controller;

/**
 * Stands in for a plugin controller that is not autoloadable under the casing the URL uses.
 *
 * `/backend/routerfixture/casing/sample` asks for `\routerfixture\casing\Controllers\sample`, which
 * no autoloader resolves on a case-sensitive filesystem. Only `BackendController::findController()`'s
 * case-insensitive lookup finds `Sample.php`, so routing to this class proves that lookup is intact.
 */
class Sample extends Controller
{
    public function index()
    {
    }
}
