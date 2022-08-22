<?php

namespace Backend\Traits;

trait GetsOptionsFromModel
{
    /**
     * Get the available options for the attribute from the model
     */
    public function getOptionsFromModel(
        Model $model,
        string $attribute,
        string|array $options = [],
        array $data = []
    ): array
    {
        $result = [];

        /*
         * Advanced usage, supplied options are callable
         * [\Path\To\Class, methodName]
         */
        if (is_array($options) && is_callable($options)) {
            $fieldOptions = call_user_func($options, $this, $field);
        }








        $model = new $model;
        $items = $model->all();
        foreach ($items as $item) {
            $options[$item->id] = $item->name;
        }
        return $options;
    }

    /**
     * Internal helper for method existence checks.
     */
    protected function objectMethodExists(object $object, string $method): bool
    {
        if (method_exists($object, 'methodExists')) {
            return $object->methodExists($method);
        }

        return method_exists($object, $method);
    }
}