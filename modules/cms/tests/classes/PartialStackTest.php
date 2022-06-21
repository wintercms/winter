<?php

namespace Cms\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use Cms\Classes\PartialStack;

class PartialStackTest extends TestCase
{

    public function testStackPartials()
    {
        $stack = new PartialStack;

        /*
         * Stack em up
         */
        $stack->stackPartial();
        $stack->addComponent('override1', 'Winter\Tester\Components\MainMenu');
        $stack->addComponent('override2', 'Winter\Tester\Components\ContentBlock');

        $stack->stackPartial();
        $stack->addComponent('override3', 'Winter\Tester\Components\Post');
        $stack->addComponent('post', 'Winter\Tester\Components\Post');

        $stack->stackPartial();
        $stack->addComponent('mainMenu', 'Winter\Tester\Components\MainMenu');

        /*
         * Knock em down
         */
        $this->assertEquals('Winter\Tester\Components\MainMenu', $stack->getComponent('mainMenu'));
        $this->assertEquals('Winter\Tester\Components\MainMenu', $stack->getComponent('override1'));

        $stack->unstackPartial();

        $this->assertNull($stack->getComponent('mainMenu'));
        $this->assertEquals('Winter\Tester\Components\ContentBlock', $stack->getComponent('override2'));
        $this->assertEquals('Winter\Tester\Components\Post', $stack->getComponent('override3'));

        $stack->unstackPartial();

        $this->assertNull($stack->getComponent('mainMenu'));
        $this->assertNull($stack->getComponent('post'));
        $this->assertEquals('Winter\Tester\Components\MainMenu', $stack->getComponent('override1'));

        $stack->unstackPartial();

        $this->assertNull($stack->getComponent('post'));
        $this->assertNull($stack->getComponent('mainMenu'));
        $this->assertNull($stack->getComponent('override1'));
        $this->assertNull($stack->getComponent('override2'));
        $this->assertNull($stack->getComponent('override3'));
    }

    public function testEmptyStack()
    {
        $stack = new PartialStack;
        $this->assertNull($stack->getComponent('xxx'));
    }
}
