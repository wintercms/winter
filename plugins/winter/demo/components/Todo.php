<?php namespace Winter\Demo\Components;

use Flash;
use ApplicationException;
use Cms\Classes\ComponentBase;

class Todo extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Todo List',
            'description' => 'Implements a simple to-do list.'
        ];
    }

    public function defineProperties()
    {
        return [
            'max' => [
                'description'       => 'The most amount of todo items allowed',
                'title'             => 'Max items',
                'default'           => 10,
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'The Max Items value is required and should be integer.'
            ]
        ];
    }

    public function onAddItem()
    {
        $items = post('items', []);

        if (count($items) >= $this->property('max')) {
            throw new ApplicationException(sprintf('Sorry only %s items are allowed.', $this->property('max')));
        }

        $newItem = post('newItem');
        if (empty($newItem)) {
            Flash::error('You must specify an item to add to the To Do List.');
            return;
        }

        $items[] = $newItem;

        $this->page['items'] = $items;
    }

    public function onRemoveItem()
    {
        $items = post('items', []);
        $removeKey = post('key');

        $this->page['items'] = $items;

        if (is_null($removeKey)) {
            Flash::error('You did not specify an item to remove.');
            return;
        }

        if (!array_key_exists($removeKey, $items)) {
            $this->page['items'] = $items;
            Flash::error('The specified item does not exist.');
            return;
        }

        array_splice($items, $removeKey, 1);

        $this->page['items'] = $items;
    }
}
