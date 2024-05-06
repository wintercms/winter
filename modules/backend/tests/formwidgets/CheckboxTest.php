<?php

namespace Backend\Tests\FormWidgets;

use Backend\Widgets\Form;
use Winter\Storm\Database\Model;
use System\Tests\Bootstrap\PluginTestCase;

class CheckboxTest extends PluginTestCase
{
    public $form = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->form = new Form(null, [
            'model' => new Model,
            'arrayName' => 'array',
            'fields' => [
                'unchecked' => [
                    'type' => 'checkbox',
                    'label' => 'My Test Checkbox unchecked',
                ],
                'checkedForced' => [
                    'type' => 'checkbox',
                    'label' => 'My Test Checkbox checked',
                    'default' => true,
                ],
                'uncheckedForced' => [
                    'type' => 'checkbox',
                    'label' => 'My Test Checkbox unchecked',
                    'default' => false,
                ],
            ],
        ]);
        
        $this->form->render();
    }

    public function testConfigDefaultValue()
    {
        $unchecked = $this->form->getField('unchecked');
        $this->assertFalse($unchecked->isSelected());

        $checkedForced = $this->form->getField('checkedForced');
        $this->assertTrue($checkedForced->isSelected());

        $uncheckedForced = $this->form->getField('uncheckedForced');
        $this->assertFalse($uncheckedForced->isSelected());
    }
}
