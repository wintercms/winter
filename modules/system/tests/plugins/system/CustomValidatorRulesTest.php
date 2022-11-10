<?php

namespace System\Tests\Plugins\System;

use System\Tests\Bootstrap\PluginTestCase;
use Validator;

class CustomValidatorRulesTest extends PluginTestCase
{
    public function setUp() : void
    {
        parent::setUp();

        include_once base_path() . '/modules/system/tests/fixtures/plugins/winter/tester/rules/BeLikeBobRule.php';
        include_once base_path() . '/modules/system/tests/fixtures/plugins/winter/tester/Plugin.php';

        $this->runPluginRefreshCommand('Winter.Tester');
    }

    public function testValidationUsingClosure()
    {
        $validData = [
            'company' => 'ACME'
        ];

        $invalidData = [
            'company' => 'Acme'
        ];

        $rules = [
            'company' => 'uppercase'
        ];

        $validationWithValidData = Validator::make($validData, $rules);
        $validationWithInvalidData = Validator::make($invalidData, $rules);

        $this->assertFalse($validationWithValidData->fails());
        $this->assertTrue($validationWithInvalidData->fails());
    }

    public function testValidationUsingRuleObject()
    {
        $validData = [
            'name' => 'bob'
        ];

        $invalidData = [
            'name' => 'karen'
        ];

        $rules = [
            'name' => 'be_like_bob'
        ];

        $validationWithValidData = Validator::make($validData, $rules);
        $validationWithInvalidData = Validator::make($invalidData, $rules);

        $this->assertFalse($validationWithValidData->fails());
        $this->assertTrue($validationWithInvalidData->fails());
        $this->assertEquals($validationWithInvalidData->errors()->first(), 'You must be like bob');
    }
}
