<?php

namespace Backend\Tests\FormWidgets
{
    use Backend\Classes\Controller;
    use Backend\Classes\FormField;
    use Backend\FormWidgets\RecordFinder;
    use System\Tests\Bootstrap\PluginTestCase;
    use Winter\Storm\Database\Model;

    class RecordFinderTestModel extends Model
    {
        public $table = 'record_finder_test';

        protected $jsonable = ['data'];

        public $belongsTo = [
            'parent' => RecordFinderTestModel::class,
        ];
    }

    class RecordFinderTest extends PluginTestCase
    {
        public function testNameAndDescriptionFromPlainAttributes()
        {
            $widget = $this->makeWidget([
                'nameFrom' => 'name',
                'descriptionFrom' => 'email',
            ]);

            $this->assertSame('Jane Doe', $widget->getNameValue());
            $this->assertSame('jane@example.com', $widget->getDescriptionValue());
        }

        public function testNameAndDescriptionFromDotNotation()
        {
            $widget = $this->makeWidget([
                'nameFrom' => 'data.name',
                'descriptionFrom' => 'data.address.city',
            ]);

            $this->assertSame('Nested Jane', $widget->getNameValue());
            $this->assertSame('Ghent', $widget->getDescriptionValue());
        }

        public function testNameAndDescriptionFromArrayNotation()
        {
            $widget = $this->makeWidget([
                'nameFrom' => 'data[name]',
                'descriptionFrom' => 'data[address][city]',
            ]);

            $this->assertSame('Nested Jane', $widget->getNameValue());
            $this->assertSame('Ghent', $widget->getDescriptionValue());
        }

        public function testNestedValueFromRelation()
        {
            $widget = $this->makeWidget([
                'nameFrom' => 'parent.name',
                'descriptionFrom' => 'parent[email]',
            ]);

            $this->assertSame('John Doe', $widget->getNameValue());
            $this->assertSame('john@example.com', $widget->getDescriptionValue());
        }

        public function testMissingNestedValueReturnsNull()
        {
            $widget = $this->makeWidget([
                'nameFrom' => 'data.missing',
                'descriptionFrom' => 'data[address][missing]',
            ]);

            $this->assertNull($widget->getNameValue());
            $this->assertNull($widget->getDescriptionValue());
        }

        public function testResolvingValuesDoesNotModifyConfig()
        {
            $widget = $this->makeWidget([
                'nameFrom' => 'data[name]',
                'descriptionFrom' => 'data[address][city]',
            ]);

            $widget->getNameValue();
            $widget->getDescriptionValue();

            $this->assertSame('data[name]', $widget->nameFrom);
            $this->assertSame('data[address][city]', $widget->descriptionFrom);
        }

        protected function makeWidget(array $config = []): RecordFinder
        {
            $widget = new RecordFinder(new Controller(), new FormField('test', 'Test'), $config + [
                'useRelation' => false,
                'modelClass' => RecordFinderTestModel::class,
            ]);

            $parent = new RecordFinderTestModel;
            $parent->name = 'John Doe';
            $parent->email = 'john@example.com';

            $record = new RecordFinderTestModel;
            $record->name = 'Jane Doe';
            $record->email = 'jane@example.com';
            $record->data = [
                'name' => 'Nested Jane',
                'address' => ['city' => 'Ghent'],
            ];
            $record->setRelation('parent', $parent);

            $widget->relationModel = $record;

            return $widget;
        }
    }
}
