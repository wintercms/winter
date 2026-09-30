<?php

namespace Backend\Tests\Widgets
{
    use Backend\Classes\Controller;
    use Backend\Classes\WidgetManager;
    use Backend\FormWidgets\FieldSet;
    use Backend\FormWidgets\NestedForm;
    use Backend\FormWidgets\Repeater;
    use Backend\Widgets\Form;
    use System\Tests\Bootstrap\PluginTestCase;
    use Winter\Storm\Database\Model;
    use Winter\Storm\Database\Traits\Validation;

    class FormFieldSetScopeTestModel extends Model
    {
        use Validation;

        public $table = 'form_fieldset_scope_test';

        protected $jsonable = ['items'];

        public $rules = [
            // Resolved as-is by a form that brings no data scope of its own (fieldset)...
            'nested_text' => 'required',
            // ...and prefixed with its own scope by a form that does.
            'meta.title' => 'required',
            'items.*.title' => 'required',
        ];
    }

    /**
     * Covers the data scope of the `fieldset` form widget's inner form.
     *
     * A fieldset groups fields visually only: its inner form reuses the parent form's
     * model and arrayName, and its save data is merged back up as if the fields had been
     * declared on the parent form. It is therefore flagged as sharing the model's scope,
     * while remaining flagged as nested so that code extending form widgets can still
     * tell it apart from the form it is nested in.
     *
     * The scope it inherits is only the model's when the parent form's own is: nested
     * inside a repeater item, a fieldset's fields still belong to the repeater's data.
     */
    class FormFieldSetScopeTest extends PluginTestCase
    {
        public function setUp(): void
        {
            parent::setUp();

            // The backend module's form widgets are not auto-registered under PluginTestCase.
            WidgetManager::instance()->registerFormWidget(FieldSet::class, 'fieldset');
            WidgetManager::instance()->registerFormWidget(NestedForm::class, 'nestedform');
            WidgetManager::instance()->registerFormWidget(Repeater::class, 'repeater');
        }

        protected function makeForm(): Form
        {
            $model = new FormFieldSetScopeTestModel;
            $model->items = [['title' => 'first']];

            return new Form(new Controller, [
                'model' => $model,
                'arrayName' => 'array',
                'fields' => [
                    'group' => [
                        'type' => 'fieldset',
                        'label' => 'Grouped Fields',
                        'fields' => [
                            'nested_text' => ['type' => 'text'],
                            'nested_other' => ['type' => 'text'],
                        ],
                    ],
                    'meta' => [
                        'type' => 'nestedform',
                        'form' => [
                            'fields' => [
                                'title' => ['type' => 'text'],
                                'subtitle' => ['type' => 'text'],
                            ],
                        ],
                    ],
                    'items' => [
                        'type' => 'repeater',
                        'form' => [
                            'fields' => [
                                'group' => [
                                    'type' => 'fieldset',
                                    'fields' => [
                                        'title' => ['type' => 'text'],
                                        'subtitle' => ['type' => 'text'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        }

        public function testFieldSetInnerFormIsStillFlaggedAsNested()
        {
            // Extending code uses isNested to avoid injecting fields on the wrong Form
            // widget instance; a fieldset shares its parent's model and controller, so
            // the flag must stay set or those guards would no longer hold.
            $this->assertTrue($this->getFieldSetForm()->isNested);
        }

        public function testFieldSetInnerFormSharesTheModelScope()
        {
            $this->assertTrue($this->getFieldSetForm()->sharesModelScope);
        }

        public function testFormsDoNotShareTheModelScopeByDefault()
        {
            $this->assertFalse($this->makeForm()->sharesModelScope);
            $this->assertFalse($this->getInnerForm($this->makeForm(), 'meta', NestedForm::class)->sharesModelScope);
        }

        public function testFieldSetFieldsResolveRequiredAgainstTheModelAttribute()
        {
            $inner = $this->getFieldSetForm();
            $inner->getFields();

            // The rule is defined as "nested_text", not ".nested_text": a fieldset brings
            // no scope of its own, so there is nothing to prefix the attribute name with.
            $this->assertTrue($inner->getField('nested_text')->required);
            $this->assertFalse($inner->getField('nested_other')->required);
        }

        public function testNestedFormFieldsStillResolveRequiredAgainstTheNestedAttribute()
        {
            $inner = $this->getInnerForm($this->makeForm(), 'meta', NestedForm::class);
            $inner->getFields();

            // A nested form owns its data scope, so the attribute name is still prefixed
            // with it: the rule is defined as "meta.title".
            $this->assertTrue($inner->getField('title')->required);
            $this->assertFalse($inner->getField('subtitle')->required);
        }

        public function testFieldSetInsideARepeaterKeepsTheRepeaterItemScope()
        {
            $inner = $this->getInnerForm($this->getRepeaterItemForm(), 'group', FieldSet::class);
            $inner->getFields();

            // The fieldset inherits the repeater item's array name, so its fields belong
            // to the repeater's data, not to the model's attributes. The rule is defined
            // as "items.*.title" and the inherited scope must still be applied.
            $this->assertFalse($inner->sharesModelScope);
            $this->assertTrue($inner->getField('title')->required);
            $this->assertFalse($inner->getField('subtitle')->required);
        }

        protected function getFieldSetForm(): Form
        {
            return $this->getInnerForm($this->makeForm(), 'group', FieldSet::class);
        }

        protected function getRepeaterItemForm(): Form
        {
            $form = $this->makeForm();
            $form->bindToController();

            $repeater = $form->getFormWidget('items');
            $this->assertInstanceOf(Repeater::class, $repeater);

            $itemForms = \Closure::bind(fn () => $this->formWidgets, $repeater, Repeater::class)();
            $this->assertArrayHasKey(0, $itemForms);

            return $itemForms[0];
        }

        protected function getInnerForm(Form $form, string $field, string $widgetClass): Form
        {
            // Defining the parent's fields builds and caches its nested form widgets.
            $form->bindToController();

            $widget = $form->getFormWidget($field);
            $this->assertInstanceOf($widgetClass, $widget);

            $inner = \Closure::bind(fn () => $this->formWidget, $widget, $widgetClass)();
            $this->assertInstanceOf(Form::class, $inner);

            return $inner;
        }
    }
}
