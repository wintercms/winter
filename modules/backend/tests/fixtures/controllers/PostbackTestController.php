<?php

namespace Backend\Tests\Fixtures\Controllers;

use Backend\Classes\Controller;
use Backend\Tests\Fixtures\Widgets\PostbackTestWidget;

class PostbackTestController extends Controller
{
    /**
     * @var bool Render the bare view so the fixture needs no layout scaffolding.
     */
    public $suppressLayout = true;

    /**
     * @var int Number of times the index action ran.
     */
    public $actionRuns = 0;

    /**
     * @var int Number of times the controller handler ran.
     */
    public $handlerRuns = 0;

    public function index()
    {
        $this->actionRuns++;

        (new PostbackTestWidget($this))->bindToController();
    }

    public function onNoop()
    {
        $this->handlerRuns++;
    }

    public function onSelfHandled()
    {
        $this->actionRuns++;
    }
}
