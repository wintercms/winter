<?php namespace Backend\Traits;

use File;
use Lang;
use Request;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Arr;
use Winter\Storm\Support\Str;

/**
 * Inspectable Container Trait
 * Extension for controllers that can host inspectable widgets (Components, etc.)
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */

trait InspectableContainer
{
    use \System\Traits\ConfigMaker;

    /**
     * @var string The name of the inspector config file.
     */
    protected string $inspectorFile = 'inspector.yaml';

    /**
     * Gets the inspector configuration.
     *
     * This method will look for the configuration file called `inspector.yaml` first within the given class' config
     * directory, and failing that, the current controller's given config directory. If it cannot find the file in
     * either location, an exception will be thrown.
     *
     * You may overwrite the filename for the inspector config file by specifying the `$inspectorFile` property in the
     * controller.
     *
     * @throws ApplicationException if the inspector config file cannot be found.
     */
    public function onGetInspectorConfiguration(): array
    {
        $config = [];
        $inspectorClass = trim(Request::input('inspectorClassName'));
        $configPaths = [
            $this->guessConfigPath('/' . $this->inspectorFile),
        ];

        if ($inspectorClass) {
            // Try to find the inspector config within the given class first
            array_unshift($configPaths, $this->guessConfigPathFrom($inspectorClass, '/' . $this->inspectorFile));
        }

        foreach ($configPaths as $configPath) {
            if (File::isFile($configPath)) {
                $config = (array) $this->makeConfig($configPath);
                break;
            }
        }

        if (!count($config)) {
            throw new ApplicationException('Missing inspector configuration file.');
        }

        return $this->processLangKeys($config);
    }

    /**
     * Translates certain keys provided by the config.
     *
     * @param array $config
     * @return array
     */
    protected function processLangKeys(array $config): array
    {
        $translatableKeys = [
            'title',
            'description',
            'fields.*.label',
            'fields.*.comment',
            'fields.*.placeholder',
            'fields.*.emptyOption',
        ];

        $flattened = Arr::dot($config);

        foreach ($flattened as $key => $value) {
            foreach ($translatableKeys as $translatable) {
                if (Str::is($translatable, $key)) {
                    $flattened[$key] = e(Lang::get($value));
                }
            }
        }

        return Arr::undot($flattened);
    }

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
}
