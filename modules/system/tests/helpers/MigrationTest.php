<?php

namespace System\Tests\Helpers;

use System\Helpers\Migration;
use System\Tests\Bootstrap\TestCase;

class MigrationTest extends TestCase
{
    public function testSwitchFieldType()
    {
        $result = Migration::mapFieldType('switchTest', [
            'type' => 'switch',
        ]);
        $this->assertEquals($result['type'], 'boolean');
    }

    public function testCheckboxFieldType()
    {
        $result = Migration::mapFieldType('checkboxTest', [
            'type' => 'checkbox',
        ]);
        $this->assertEquals($result['type'], 'boolean');
    }

    public function testNumberFieldType()
    {
        $result = Migration::mapFieldType('numberTest', [
            'type' => 'number',
            'min' => 0,
            'step' => 1,
        ]);
        $this->assertEquals($result['type'], 'unsignedInteger');

        $result = Migration::mapFieldType( 'integerTest', [
            'type' => 'number',
            'step' => 2,
        ]);
        $this->assertEquals($result['type'], 'integer');

        $result = Migration::mapFieldType('numberTest', [
            'type' => 'number',
            'step' => 0.5,
        ]);
        $this->assertEquals($result['type'], 'double');
    }

    public function testRangeFieldType()
    {
        $result = Migration::mapFieldType('rangeTest', [
            'type' => 'range',
        ]);
        $this->assertEquals($result['type'], 'unsignedInteger');
    }

    public function testDatepickerType()
    {
        $result = Migration::mapFieldType('datepickerTest', [
            'type' => 'datepicker',
        ]);
        $this->assertEquals($result['type'], 'datetime');
    }

    public function testTextareaType()
    {
        $result = Migration::mapFieldType('textareaTest', [
            'type' => 'textarea',
        ]);
        $this->assertEquals($result['type'], 'mediumText');
    }

    public function testMarkdownType()
    {
        $result = Migration::mapFieldType('markdownTest', [
            'type' => 'markdown',
        ]);
        $this->assertEquals($result['type'], 'mediumText');
    }

    public function testTextType()
    {
        $result = Migration::mapFieldType('textTest', [
            'type' => 'text',
        ]);
        $this->assertEquals($result['type'], 'string');
    }

    public function testPasswordType()
    {
        $result = Migration::mapFieldType('passwordTest', [
            'type' => 'password',
        ]);
        $this->assertEquals($result['type'], 'string');
    }

    public function testDropdownType()
    {
        $result = Migration::mapFieldType('dropdownTest', [
            'type' => 'dropdown',
        ]);
        $this->assertEquals($result['type'], 'string');
    }

    public function testRadioListType()
    {
        $result = Migration::mapFieldType('radiolistTest', [
            'type' => 'radiolist',
        ]);
        $this->assertEquals($result['type'], 'string');
    }

    public function testBalloonSelectorType()
    {
        $result = Migration::mapFieldType('balloonselectorTest', [
            'type' => 'balloonselector',
        ]);
        $this->assertEquals($result['type'], 'string');
    }

    public function testEmailType()
    {
        $result = Migration::mapFieldType('emailTest', [
            'type' => 'email',
        ]);
        $this->assertEquals($result['type'], 'string');
    }
}
