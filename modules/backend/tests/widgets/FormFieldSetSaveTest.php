<?php

namespace Backend\Tests\Widgets
{
    use Backend\Classes\Controller;
    use Backend\Classes\FormField;
    use Backend\Classes\FormWidgetBase;
    use Backend\Classes\WidgetManager;
    use Backend\FormWidgets\FieldSet;
    use System\Tests\Bootstrap\PluginTestCase;
    use Winter\Storm\Database\Model;
    use Backend\Widgets\Form;

    class FormFieldSetSaveTestModel extends Model
    {
    }

    /**
     * A trivial form widget whose getSaveValue() observably transforms the posted value,
     * used to prove that a fieldset's nested `widget` fields have getSaveValue() applied.
     */
    class FieldSetSaveStubWidget extends FormWidgetBase
    {
        protected $defaultAlias = 'fieldsetsavestub';

        public function getSaveValue($value)
        {
            return is_string($value) ? strtoupper($value) : $value;
        }
    }

    /**
     * A nested form widget that opts out of saving (as e.g. FileUpload does, since its
     * relation manages persistence). Its NO_SAVE_DATA sentinel must never reach the model.
     */
    class FieldSetNoSaveStubWidget extends FormWidgetBase
    {
        protected $defaultAlias = 'fieldsetnosavestub';

        public function getSaveValue($value)
        {
            return FormField::NO_SAVE_DATA;
        }
    }

    /**
     * Covers Form::getSaveData()'s handling of the `fieldset` form widget: the fieldset
     * visually groups fields but they must be saved as if they were regular fields at the
     * parent level, while the fieldset container itself saves nothing.
     */
    class FormFieldSetSaveTest extends PluginTestCase
    {
        public function setUp(): void
        {
            parent::setUp();

            // The backend module's form widgets are not auto-registered under PluginTestCase,
            // so make the aliases used by the form field configs below resolvable.
            WidgetManager::instance()->registerFormWidget(FieldSet::class, 'fieldset');
            WidgetManager::instance()->registerFormWidget(FieldSetSaveStubWidget::class, 'fieldsetsavestub');
            WidgetManager::instance()->registerFormWidget(FieldSetNoSaveStubWidget::class, 'fieldsetnosavestub');
        }

        protected function makeForm(): Form
        {
            return new Form(new Controller, [
                'model' => new FormFieldSetSaveTestModel,
                'arrayName' => 'array',
                'fields' => [
                    'top_level' => [
                        'type' => 'text',
                    ],
                    'group' => [
                        'type' => 'fieldset',
                        'label' => 'Grouped Fields',
                        'fields' => [
                            'nested_text' => [
                                'type' => 'text',
                            ],
                            'nested_number' => [
                                'type' => 'number',
                            ],
                            'nested_widget' => [
                                'type' => 'fieldsetsavestub',
                            ],
                            'nested_nosave' => [
                                'type' => 'fieldsetnosavestub',
                            ],
                            'nested_disabled' => [
                                'type' => 'text',
                                'disabled' => true,
                            ],
                        ],
                    ],
                ],
            ]);
        }

        /**
         * getSaveData() reads its values with post(), which only returns real data when
         * the request is genuinely a POST -- so simulate the postback first.
         */
        protected function postForm(array $data): array
        {
            request()->setMethod('POST');
            request()->request->replace(['array' => $data]);

            return $this->makeForm()->getSaveData();
        }

        public function testNestedFieldSetFieldsAreCollectedToParentLevel()
        {
            $data = $this->postForm([
                'top_level' => 'top',
                'nested_text' => 'hello',
                'nested_number' => '42',
            ]);

            // Nested fields are hoisted to the parent save data...
            $this->assertArrayHasKey('nested_text', $data);
            $this->assertArrayHasKey('nested_number', $data);
            $this->assertEquals('hello', $data['nested_text']);

            // ...and the top level field is unaffected.
            $this->assertEquals('top', $data['top_level']);

            // The fieldset container itself must not be saved.
            $this->assertArrayNotHasKey('group', $data);
        }

        public function testNestedNumberFieldIsCastToFloat()
        {
            $data = $this->postForm([
                'nested_number' => '42',
            ]);

            $this->assertSame(42.0, $data['nested_number']);
        }

        public function testNestedNumberEmptyStringBecomesNull()
        {
            $data = $this->postForm([
                'nested_number' => '   ',
            ]);

            $this->assertArrayHasKey('nested_number', $data);
            $this->assertNull($data['nested_number']);
        }

        public function testNestedWidgetFieldHasSaveValueApplied()
        {
            $data = $this->postForm([
                'nested_widget' => 'abc',
            ]);

            // The nested widget's getSaveValue() must be applied (stub upper-cases it),
            // rather than the raw posted value being stored.
            $this->assertArrayHasKey('nested_widget', $data);
            $this->assertSame('ABC', $data['nested_widget']);
        }

        public function testNestedWidgetReturningNoSaveDataDoesNotLeakSentinel()
        {
            $data = $this->postForm([
                'nested_nosave' => 'ignore me',
            ]);

            // A nested widget that returns NO_SAVE_DATA must behave exactly as it would
            // at the top level of a form: the NO_SAVE_DATA sentinel (-1) must never be
            // written to the model.
            $this->assertNotSame(FormField::NO_SAVE_DATA, $data['nested_nosave'] ?? null);
        }

        public function testDisabledNestedFieldIsNotSaved()
        {
            $data = $this->postForm([
                'nested_text' => 'hello',
                'nested_disabled' => 'tampered',
            ]);

            // Disabled fields are omitted from the save data, just like top-level fields.
            $this->assertArrayHasKey('nested_text', $data);
            $this->assertArrayNotHasKey('nested_disabled', $data);
        }

        public function testMissingNestedValuesAreOmitted()
        {
            $data = $this->postForm([
                'top_level' => 'top',
            ]);

            $this->assertArrayNotHasKey('nested_text', $data);
            $this->assertArrayNotHasKey('nested_number', $data);
        }
    }
}
