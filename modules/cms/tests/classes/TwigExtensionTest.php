<?php

namespace Cms\Tests\Classes;

use Cms\Twig\Extension;
use Cms\Classes\Controller;

use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Exception\SystemException;

class TwigExtensionTest extends TestCase
{
    public function testPartialFunction()
    {
        $extension = new Extension;
        $controller = Controller::getController() ?: new Controller;
        $extension->setController($controller);

        $this->assertFalse($extension->partialFunction('invalid-partial-file', [], false));

        $this->expectException(SystemException::class);
        $this->expectExceptionMessageMatches('/is\snot\sfound/');
        $this->assertFalse($extension->partialFunction('invalid-partial-file', [], true));
    }

    public function testContentFunction()
    {
        $extension = new Extension;
        $controller = Controller::getController() ?: new Controller;
        $extension->setController($controller);

        $this->assertFalse($extension->contentFunction('invalid-content-file', [], false));

        $this->expectException(SystemException::class);
        $this->expectExceptionMessageMatches('/is\snot\sfound/');
        $this->assertFalse($extension->contentFunction('invalid-content-file', [], true));
    }
}
