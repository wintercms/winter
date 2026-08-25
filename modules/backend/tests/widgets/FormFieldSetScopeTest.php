<?php

namespace Backend\Tests\Widgets
{
    use Backend\Classes\Controller;
    use Backend\Classes\WidgetManager;
    use Backend\FormWidgets\FieldSet;
    use Backend\FormWidgets\NestedForm;
    use Backend\Widgets\Form;
    use System\Tests\Bootstrap\PluginTestCase;
    use Winter\Storm\Database\Model;
    use Winter\Storm\Database\Traits\Validation;

    class FormFieldSetScopeTestModel extends Model
    {
        use Validation;

        public $table = 'form_fieldset_scope_test';

        public $rules = [
            // Resolved as-is by a scope sharing form (fieldset)...
            'nested_text' => 'required',
            // ...and prefixed with the nested form's own scope by a regular nested form.
            'meta.title' => 'required',
        ];
    }

    /**
     * Covers the data scope of the `fieldset` form widget's inner form.
     *
     * A fieldset nests fields visually only: its inner form reuses the parent form's
     * model and arrayName, and its save data is merged back up as if the fields had
     * been declared on the parent form. It is therefore flagged as sharing the parent's
     * scope, while remaining flagged as nested so that code extending form widgets can
     * still tell it apart from the form it is nested in.
     */
    class FormFieldSetScopeTest extends PluginTestCase
    {
        public function setUp(): void
        {
            parent::setUp();

            // The backend module's form widgets are not auto-registered under PluginTestCase.
            WidgetManager::instance()->registerFormWidget(FieldSet::class, 'fieldset');
            WidgetManager::instance()->registerFormWidget(NestedForm::class, 'nestedform');
        }

        protected function makeForm(): Form
        {
            return new Form(new Controller, [
                'model' => new FormFieldSetScopeTestModel,
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
                ],
            ]);
        }

        public function testFieldSetInnerFormIsStillFlaggedAsNested()
        {
            // Extending code uses isNested to avoid injecting fields on the wrong Form
            // widget instance; a fieldset shares its parent's model and controller, so
            // the flag must stay set or those guards would no longer hold.
            $this->assertTrue($this->getInnerForm('group', FieldSet::class)->isNested);
        }

        public function testFieldSetInnerFormSharesTheParentScope()
        {
            $this->assertTrue($this->getInnerForm('group', FieldSet::class)->sharesParentScope);
        }

        public function testFormsDoNotShareTheParentScopeByDefault()
        {
            $this->assertFalse($this->makeForm()->sharesParentScope);
            $this->assertFalse($this->getInnerForm('meta', NestedForm::class)->sharesParentScope);
        }

        public function testFieldSetFieldsResolveRequiredAgainstTheModelAttribute()
        {
            $inner = $this->getInnerForm('group', FieldSet::class);
            $inner->getFields();

            // The rule is defined as "nested_text", not ".nested_text": a scope sharing
            // form must not prefix the attribute name with its (inherited) arrayName.
            $this->assertTrue($inner->getField('nested_text')->required);
            $this->assertFalse($inner->getField('nested_other')->required);
        }

        public function testNestedFormFieldsStillResolveRequiredAgainstTheNestedAttribute()
        {
            $inner = $this->getInnerForm('meta', NestedForm::class);
            $inner->getFields();

            // A regular nested form owns its data scope, so the attribute name is still
            // prefixed with it: the rule is defined as "meta.title".
            $this->assertTrue($inner->getField('title')->required);
            $this->assertFalse($inner->getField('subtitle')->required);
        }

        protected function getInnerForm(string $field, string $widgetClass): Form
        {
            $form = $this->makeForm();

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
