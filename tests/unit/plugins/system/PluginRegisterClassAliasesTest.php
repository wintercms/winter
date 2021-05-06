<?php

class PluginRegisterClassAliasesTest extends PluginTestCase
{
    public function setUp() : void
    {
        parent::setUp();

        include_once base_path() . '/tests/fixtures/plugins/winter/tester/Plugin.php';

        $this->runPluginRefreshCommand('Winter.Tester');
    }

    public function testValidationUsingClosure()
    {
        $this->assertFalse(class_exists('My\NonExistent\Class'));
        $this->assertTrue(class_exists('Winter\Tester\Plugin'));
        $this->assertTrue(class_exists('My\Aliased\Class'));
    }
}
