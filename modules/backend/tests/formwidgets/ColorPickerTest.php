<?php

namespace Backend\Tests\FormWidgets;

use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\FormWidgets\ColorPicker;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Exception\ApplicationException;

class ColorPickerTest extends PluginTestCase
{
    public function testDefaultSaveValue(): void
    {
        $widget = $this->makeWidget();

        // Default only expects hex
        $this->assertEquals('#3498DB', $widget->getSaveValue('#3498DB'));

        // Getting a non-hex value should throw an exception
        $this->expectException(ApplicationException::class);
        $widget->getSaveValue('rgba(51.9, 152, 219, 1)');

        // Test a bunch of hex values
        $this->assertEquals('#3498DB', $widget->getSaveValue('#3498DB'));
        $this->assertEquals('#2980B9', $widget->getSaveValue('#2980B9'));
        $this->assertEquals('#9B59B6', $widget->getSaveValue('#9B59B6'));
    }

    public function testRgbSaveValue(): void
    {
        $widget = $this->makeWidget([
            'formats' => 'rgb'
        ]);

        // Config specifies only rgb
        $this->assertEquals('rgba(51.9, 152, 219, 1)', $widget->getSaveValue('rgba(51.9, 152, 219, 1)'));

        // Getting a non-rgb value should throw an exception
        $this->expectException(ApplicationException::class);
        $widget->getSaveValue('#3498DB');

        // Test a bunch of rgb values
        $this->assertEquals('rgba(1, 1, 1, 1)', $widget->getSaveValue('rgba(1, 1, 1, 1)'));
        $this->assertEquals('rgba(155, 89, 182, 0.5)', $widget->getSaveValue('rgba(155, 89, 182, 0.5)'));
        $this->assertEquals('rgba(1, 89, 182, 0.55)', $widget->getSaveValue('rgba(1, 89, 182, 0.55)'));
    }

    public function testCmykSaveValue(): void
    {
        $widget = $this->makeWidget([
            'formats' => 'cmyk'
        ]);

        // Config specifies only cmyk
        $this->assertEquals('cmyk(76.3%, 30.6%, 0%, 14.1%)', $widget->getSaveValue('cmyk(76.3%, 30.6%, 0%, 14.1%)'));

        // Getting a non-cmyk value should throw an exception
        $this->expectException(ApplicationException::class);
        $widget->getSaveValue('#3498DB');

        // Test a bunch of cmyk values
        $this->assertEquals('cmyk(14.8%, 51.1%, 0%, 28.6%)', $widget->getSaveValue('cmyk(14.8%, 51.1%, 0%, 28.6%)'));
        $this->assertEquals('cmyk(17%, 60.75%, 0%, 32.22%)', $widget->getSaveValue('cmyk(17%, 60.75%, 0%, 32.22%)'));
        $this->assertEquals('cmyk(17.9%, 60.75%, 0%, 32.2%)', $widget->getSaveValue('cmyk(17.9%, 60.75%, 0%, 32.2%)'));
    }

    public function testHslaSaveValue(): void
    {
        $widget = $this->makeWidget([
            'formats' => 'hsl'
        ]);

        // Config specifies only hsl
        $this->assertEquals('hsla(204.1, 69.9%, 53.1%, 1)', $widget->getSaveValue('hsla(204.1, 69.9%, 53.1%, 1)'));

        // Getting a non-hsl value should throw an exception
        $this->expectException(ApplicationException::class);
        $widget->getSaveValue('#3498DB');

        // Test a bunch of hsl values
        $this->assertEquals('hsla(282.3, 43.6%, 47.2%, 1)', $widget->getSaveValue('hsla(282.3, 43.6%, 47.2%, 1)'));
        $this->assertEquals('hsla(282.3, 43.6%, 47.2%, 0.1)', $widget->getSaveValue('hsla(282.3, 43.6%, 47.2%, 0.1)'));
        $this->assertEquals('hsla(282, 43.6%, 47.2%, 0.1)', $widget->getSaveValue('hsla(282, 43.6%, 47.2%, 0.1)'));
        $this->assertEquals('hsla(282, 43.56%, 47.2%, 0.1)', $widget->getSaveValue('hsla(282, 43.56%, 47.2%, 0.1)'));
        $this->assertEquals('hsla(282.22, 43%, 47.2%, 0.1)', $widget->getSaveValue('hsla(282.22, 43%, 47.2%, 0.1)'));
    }

    public function testAllSaveValue(): void
    {
        $widget = $this->makeWidget([
            'formats' => 'all'
        ]);

        // Config allows for any valid format
        $this->assertEquals('#3498DB', $widget->getSaveValue('#3498DB'));
        $this->assertEquals('rgba(51.9, 152, 219, 1)', $widget->getSaveValue('rgba(51.9, 152, 219, 1)'));
        $this->assertEquals('cmyk(76.3%, 30.6%, 0%, 14.1%)', $widget->getSaveValue('cmyk(76.3%, 30.6%, 0%, 14.1%)'));
        $this->assertEquals('hsla(204.1, 69.9%, 53.1%, 1)', $widget->getSaveValue('hsla(204.1, 69.9%, 53.1%, 1)'));

        // Getting a invalid value should throw an exception
        $this->expectException(ApplicationException::class);
        $widget->getSaveValue('#Winter Is Awesome');

        $this->expectException(ApplicationException::class);
        $widget->getSaveValue('rgba(51.9, 152, 219, 1) -- test');

        $this->expectException(ApplicationException::class);
        $widget->getSaveValue('Test(51.9, 152, 219, 1)');
    }

    public function testAllowCustomSaveValue(): void
    {
        $widget = $this->makeWidget([
            'formats' => 'custom'
        ]);

        // Config allows for any format
        $this->assertEquals('rgba(51.9, 152, 219, 1)', $widget->getSaveValue('rgba(51.9, 152, 219, 1)'));
        $this->assertEquals('#Winter Is Awesome', $widget->getSaveValue('#Winter Is Awesome'));
        $this->assertEquals('Test(51.9, 152, 219, 1)', $widget->getSaveValue('Test(51.9, 152, 219, 1)'));
    }

    protected function makeWidget(array $config = []): ColorPicker
    {
        return new ColorPicker(new Controller(), new FormField('test', 'Test'), $config);
    }
}
