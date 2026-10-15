<?php

namespace Backend\Tests\FormWidgets
{
    use Backend\Classes\Controller;
    use Backend\Classes\WidgetManager;
    use Backend\FormWidgets\NestedForm;
    use Backend\Widgets\Form;
    use System\Tests\Bootstrap\PluginTestCase;
    use Winter\Storm\Database\Model;

    class NestedFormScopingTestModel extends Model
    {
        public $table = 'nested_form_scoping_test';

        protected $jsonable = ['content'];

        protected $fillable = ['content'];
    }

    /**
     * Covers NestedForm data scoping.
     *
     * An empty nested form must scope its inner form to its own (empty) value rather
     * than falling back to the parent model. When it fell back to the model, a
     * sub-field whose name collided with a model attribute resolved the model's value
     * — and rendering an array attribute as, say, a textarea threw
     * "Array to string conversion".
     *
     * @see https://github.com/wintercms/winter/issues/1522
     */
    class NestedFormScopingTest extends PluginTestCase
    {
        public function setUp(): void
        {
            parent::setUp();

            // The nestedform alias is not auto-registered under PluginTestCase.
            WidgetManager::instance()->registerFormWidget(NestedForm::class, 'nestedform');
        }

        protected function makeForm(Model $model): Form
        {
            return new Form(new Controller, [
                'model' => $model,
                'arrayName' => 'array',
                'fields' => [
                    'meta' => [
                        'type' => 'nestedform',
                        'form' => [
                            'fields' => [
                                // "content" deliberately collides with the model attribute.
                                'content' => ['type' => 'textarea'],
                                'title' => ['type' => 'text'],
                            ],
                        ],
                    ],
                ],
            ]);
        }

        public function testEmptyNestedFormDoesNotResolveSubFieldsFromParentModel()
        {
            $model = new NestedFormScopingTestModel;
            // A jsonable array attribute on the model root, sharing a name with a nested
            // sub-field, while the nested form itself has no value.
            $model->content = [['data' => ['deep' => 'value']]];

            $inner = $this->getInnerForm($this->makeForm($model));
            $inner->getFields();

            $contentField = $inner->getField('content');
            $this->assertNotNull($contentField);

            // The sub-field must be scoped to the empty nested value, NOT the model's
            // root array.
            $this->assertNotSame($model->content, $contentField->value);
            $this->assertEmpty($contentField->value);
        }

        public function testPopulatedNestedFormStillResolvesItsOwnValues()
        {
            $model = new NestedFormScopingTestModel;
            $model->content = [['data' => ['deep' => 'value']]]; // model root (should be ignored)
            $model->meta = ['content' => 'nested content', 'title' => 'nested title'];

            $inner = $this->getInnerForm($this->makeForm($model));
            $inner->getFields();

            // The nested value wins over the colliding model attribute.
            $this->assertSame('nested content', $inner->getField('content')->value);
            $this->assertSame('nested title', $inner->getField('title')->value);
        }

        protected function getInnerForm(Form $form): Form
        {
            // Defining the parent's fields builds and caches its nested form widget.
            $form->bindToController();

            $nested = $form->getFormWidget('meta');
            $this->assertInstanceOf(NestedForm::class, $nested);

            $inner = \Closure::bind(fn () => $this->formWidget, $nested, NestedForm::class)();
            $this->assertInstanceOf(Form::class, $inner);

            return $inner;
        }
    }
}
