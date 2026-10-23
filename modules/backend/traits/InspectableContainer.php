<?php namespace Backend\Traits;

use Lang;
use Str;
use Request;
use ApplicationException;
use Backend\Classes\WidgetManager;

/**
 * Inspectable Container Trait
 * Extension for controllers that can host inspectable widgets (Components, etc.)
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */

trait InspectableContainer
{
    public function onInspectableGetOptions()
    {
        // Disable asset broadcasting
        $this->flushAssets();

        $property = trim(Request::input('inspectorProperty'));
        if (!$property) {
            throw new ApplicationException('The property name is not specified.');
        }

        $className = trim(Request::input('inspectorClassName'));
        if (!$className) {
            throw new ApplicationException('The inspectable class name is not specified.');
        }

        /*
         * The class name is resolved against the list of things this controller offers the inspector before it is used for anything else. Both of the steps below reach the class: class_uses_recursive() autoloads it, and the reflection after that constructs it.
         */
        $offered = array_map(fn ($class) => strtolower(Str::normalizeClassName($class)), $this->listInspectableClasses());
        if (!in_array(strtolower(Str::normalizeClassName($className)), $offered, true)) {
            throw new ApplicationException('The options cannot be loaded for the specified class.');
        }

        $traitFound = in_array('System\Traits\PropertyContainer', class_uses_recursive($className));
        if (!$traitFound) {
            throw new ApplicationException('The options cannot be loaded for the specified class.');
        }

        // Determine constructor requirements and pass the default value (or NULL) to all required arguments
        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();
        if (is_null($constructor) || !count($constructor->getParameters())) {
            $obj = new $className();
        } else {
            $args = $constructor->getParameters();
            $passed = [];
            foreach ($args as $arg) {
                if ($arg->isOptional()) {
                    $passed[] = $arg->getDefaultValue();
                    continue;
                }
                $passed[] = null;
            }
            $obj = $reflection->newInstanceArgs($passed);
        }

        // Nested properties have names like object.property.
        // Convert them to Object.Property.
        $propertyNameParts = explode('.', $property);
        $propertyMethodName = '';
        foreach ($propertyNameParts as $part) {
            $part = trim($part);

            if (!strlen($part)) {
                continue;
            }

            $propertyMethodName .= ucfirst($part);
        }

        $methodName = 'get'.$propertyMethodName.'Options';
        if (method_exists($obj, $methodName)) {
            $options = $obj->$methodName();
        }
        else {
            $options = $obj->getPropertyOptions($property);
        }

        /*
         * Convert to array to retain the sort order in JavaScript
         */
        $optionsArray = [];
        foreach ((array) $options as $value => $title) {
            $optionsArray[] = ['value' => $value, 'title' => Lang::get($title)];
        }

        return [
            'options' => $optionsArray
        ];
    }

    /**
     * Returns the class names this controller offers the inspector.
     *
     * Registered components and registered report widgets are the only classes anything in core renders a `data-inspector-class` for, and the two base classes they extend are the only ones in core using the `System\Traits\PropertyContainer` this handler requires. A controller that publishes an inspectable class of its own overrides this to add it.
     *
     * @return array
     */
    protected function listInspectableClasses(): array
    {
        $classes = array_keys(WidgetManager::instance()->listReportWidgets());

        // winter/wn-backend-module does not require the CMS module.
        if (class_exists(\Cms\Classes\ComponentManager::class)) {
            $classes = array_merge($classes, array_values(\Cms\Classes\ComponentManager::instance()->listComponents()));
        }

        return $classes;
    }
}
