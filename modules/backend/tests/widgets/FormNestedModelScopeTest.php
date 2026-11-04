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

    enum FormNestedModelScopeTestStatus: string
    {
        case Draft = 'draft';
        case Published = 'published';
    }

    class FormNestedModelScopeTestModel extends Model
    {
        public $table = 'form_nested_model_scope_test';

        protected $jsonable = ['items', 'meta'];

        protected $casts = [
            'status' => FormNestedModelScopeTestStatus::class,
        ];
    }

    /**
     * Covers which forms Form::setFormValues() is allowed to fill the model from.
     *
     * A repeater item form and a nested form bring a data scope of their own: their
     * fields are keys of that scope, not attributes of the model, even though they are
     * handed the parent form's model instance to render against. Filling the model from
     * there writes foreign values onto it whenever a field shares its name with an
     * attribute - silently for a plain attribute, fatally for a cast one.
     *
     * A fieldset is the exception: it groups fields visually and its fields do resolve
     * against the model, which is what $sharesModelScope flags.
     */
    class FormNestedModelScopeTest extends PluginTestCase
    {
        public function setUp(): void
        {
            parent::setUp();

            // The backend module's form widgets are not auto-registered under PluginTestCase.
            WidgetManager::instance()->registerFormWidget(FieldSet::class, 'fieldset');
            WidgetManager::instance()->registerFormWidget(NestedForm::class, 'nestedform');
            WidgetManager::instance()->registerFormWidget(Repeater::class, 'repeater');
        }

        public function testTopLevelFormFillsTheModel()
        {
            $form = $this->makeForm();
            $form->setFormValues(['status' => 'draft']);

            $this->assertSame('draft', $form->model->getAttributes()['status']);
        }

        public function testFieldSetFormFillsTheModel()
        {
            $form = $this->makeForm();
            $inner = $this->getInnerForm($form, 'group', FieldSet::class);

            $inner->setFormValues(['status' => 'draft']);

            // A fieldset's fields are the model's attributes, so its refresh must keep
            // reaching the model the parent form renders from.
            $this->assertSame('draft', $form->model->getAttributes()['status']);
        }

        public function testNestedFormDoesNotFillTheModel()
        {
            $form = $this->makeForm();
            $inner = $this->getInnerForm($form, 'meta', NestedForm::class);

            $inner->setFormValues(['status' => 'featured']);

            $this->assertSame('published', $form->model->getAttributes()['status']);
            // the value still reaches the field it belongs to
            $this->assertSame('featured', $inner->getField('status')->value);
        }

        public function testRepeaterItemFormDoesNotFillTheModel()
        {
            $form = $this->makeForm();
            $inner = $this->getRepeaterItemForm($form);

            $inner->setFormValues(['status' => 'featured']);

            // Without the scope guard this throws, "featured" not being a valid backing
            // value for the enum the model casts its own `status` attribute to.
            $this->assertSame('published', $form->model->getAttributes()['status']);
            $this->assertSame('featured', $inner->getField('status')->value);
        }

        protected function makeForm(): Form
        {
            $model = new FormNestedModelScopeTestModel;
            $model->status = FormNestedModelScopeTestStatus::Published;
            $model->meta = ['status' => 'highlighted'];
            $model->items = [['status' => 'highlighted']];

            return new Form(new Controller, [
                'model' => $model,
                'arrayName' => 'array',
                'fields' => [
                    'status' => ['type' => 'text'],
                    'group' => [
                        'type' => 'fieldset',
                        'fields' => [
                            'status' => ['type' => 'text'],
                        ],
                    ],
                    'meta' => [
                        'type' => 'nestedform',
                        'form' => [
                            'fields' => [
                                'status' => ['type' => 'text'],
                            ],
                        ],
                    ],
                    'items' => [
                        'type' => 'repeater',
                        'form' => [
                            'fields' => [
                                'status' => ['type' => 'text'],
                            ],
                        ],
                    ],
                ],
            ]);
        }

        protected function getRepeaterItemForm(Form $form): Form
        {
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
