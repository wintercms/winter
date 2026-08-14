<?php

namespace Backend\Tests\Fixtures\Widgets;

use Backend\Classes\WidgetBase;

class PostbackTestWidget extends WidgetBase
{
    /**
     * @var string
     */
    protected $defaultAlias = 'postbackwidget';

    /**
     * @var int Number of times the widget handler ran.
     */
    public $handlerRuns = 0;

    public function render()
    {
        return '';
    }

    public function onWidgetNoop()
    {
        $this->handlerRuns++;
    }
}
