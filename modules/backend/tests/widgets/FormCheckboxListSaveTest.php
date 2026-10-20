<?php

namespace Backend\Tests\Widgets
{
    use Backend\Classes\Controller;
    use Backend\Widgets\Form;
    use System\Tests\Bootstrap\PluginTestCase;
    use Winter\Storm\Database\Model;

    class FormCheckboxListSaveTestModel extends Model
    {
        public $jsonable = ['options'];

        protected $fillable = ['options'];
    }

    /**
     * A checkboxlist renders a hidden "0" input so that unchecking every box still
     * posts a value. That "0" must not be saved as the field's value: on a jsonable
     * attribute it was stored as the JSON scalar 0, which the model then returned as
     * the string "0" instead of an array.
     */
    class FormCheckboxListSaveTest extends PluginTestCase
    {
        protected function postForm(array $data): array
        {
            request()->setMethod('POST');
            request()->request->replace(['array' => $data]);

            $form = new Form(new Controller, [
                'model' => new FormCheckboxListSaveTestModel,
                'arrayName' => 'array',
                'fields' => [
                    'options' => [
                        'type' => 'checkboxlist',
                        'options' => ['a' => 'A', 'b' => 'B'],
                    ],
                ],
            ]);

            return $form->getSaveData();
        }

        public function testNothingCheckedIsSavedAsNull()
        {
            $data = $this->postForm(['options' => '0']);

            $this->assertArrayHasKey('options', $data);
            $this->assertNull($data['options']);
        }

        public function testCheckedOptionsAreKept()
        {
            $data = $this->postForm(['options' => ['a', 'b']]);

            $this->assertSame(['a', 'b'], $data['options']);
        }

        public function testNothingCheckedLeavesAJsonableAttributeNull()
        {
            $model = new FormCheckboxListSaveTestModel;
            $model->fill($this->postForm(['options' => '0']));

            $this->assertNull($model->getAttributes()['options']);
            $this->assertNull($model->options);
        }
    }
}
