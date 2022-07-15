<?php

namespace System\Tests;

use System\Tests\Bootstrap\PluginTestCase;

class AliasesTest extends PluginTestCase
{
    public function testInputFacadeAlias()
    {
        $this->assertTrue(class_exists('Illuminate\Support\Facades\Input'));
        $this->assertInstanceOf(
            \Winter\Storm\Support\Facades\Input::class,
            new \Illuminate\Support\Facades\Input()
        );
    }

    public function testHtmlDumperAlias()
    {
        $this->assertTrue(class_exists('Illuminate\Support\Debug\HtmlDumper'));
        $this->assertInstanceOf(
            \Symfony\Component\VarDumper\Dumper\HtmlDumper::class,
            new \Illuminate\Support\Debug\HtmlDumper()
        );
    }
}
