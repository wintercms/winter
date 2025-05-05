<?php

namespace System\Twig;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Illuminate\Database\Eloquent\Model as DbModel;
use Illuminate\Session\SessionManager;
use Twig\Markup;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Sandbox\SecurityNotAllowedMethodError;
use Twig\Sandbox\SecurityNotAllowedPropertyError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityPolicyInterface;
use Twig\Template;
use Winter\Storm\Halcyon\Datasource\DatasourceInterface;
use Winter\Storm\Halcyon\Model as HalcyonModel;

/**
 * SecurityPolicy globally blocks accessibility of certain methods and properties.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges, Luke Towers, Ben Thomson
 */
final class SecurityPolicy implements SecurityPolicyInterface
{
    /**
     * @var array<string, string[]> List of forbidden methods, grouped by applicable instance.
     */
    protected $blockedMethods = [
        '*' => [
            // Prevent accessing Twig itself
            'getTwig',

            // Prevent extensions of any objects
            'addDynamicMethod',
            'addDynamicProperty',
            'extendClassWith',
            'getClassExtension',
            'extendableSet',

            // Prevent binding to events
            'bindEvent',
            'bindEventOnce',
        ],

        // Prevent some controller methods
        Controller::class => [
            'runPage',
            'renderPage',
            'getLoader',
        ],

        // Prevent model data modification
        DbModel::class => [
            'fill',
            'setAttribute',
            'setRawAttributes',
            'save',
            'push',
            'update',
            'delete',
            'forceDelete',
            'getQuery',
        ],
        HalcyonModel::class => [
            'fill',
            'setAttribute',
            'setRawAttributes',
            'setSettingsAttribute',
            'setFileNameAttribute',
            'save',
            'push',
            'update',
            'delete',
            'forceDelete',
            'getQuery',
        ],
        DatasourceInterface::class => [
            'insert',
            'update',
            'delete',
            'forceDelete',
            'write',
            'usingSource',
            'pushToSource',
            'removeFromSource',
        ],
        Theme::class => [
            'setDirName',
            'registerHalcyonDatasource',
            'getDatasource'
        ],
    ];

    /**
     * @var array<string, string[]> List of allowed methods, grouped by applicable instance.
     */
    protected $allowedMethods = [
        SessionManager::class => [
            'put',
            'get',
            'has',
            'forget',
            'flush',
            'pull',
        ],
    ];

    /**
     * @var array<string, string[]> List of forbidden properties, grouped by applicable instance.
     */
    protected $blockedProperties = [
        Theme::class => [
            'datasource',
        ],
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $properties = [
            'blockedMethods',
            'allowedMethods',
            'blockedProperties',
        ];

        foreach ($properties as $property) {
            foreach ($this->{$property} as $type => $values) {
                $this->{$property}[$type] = array_map('strtolower', $values);
            }
        }
    }

    /**
     * Check the provided arguments against this security policy
     *
     * @param array $tags Array of tags to be checked against the policy ['tag', 'tag2', 'etc']
     * @param array $filters Array of filters to be checked against the policy ['filter', 'filter2', 'etc']
     * @param array $functions Array of funtions to be checked against the policy ['function', 'function2', 'etc']
     * @throws SecurityNotAllowedTagError if a given tag is not allowed
     * @throws SecurityNotAllowedFilterError if a given filter is not allowed
     * @throws SecurityNotAllowedFunctionError if a given function is not allowed
     */
    public function checkSecurity($tags, $filters, $functions): void
    {
    }

    /**
     * Checks if a given property is permitted to be accessed on a given object
     *
     * @param object $obj
     * @param string $property
     * @throws SecurityNotAllowedPropertyError
     */
    public function checkPropertyAllowed($obj, $property): void
    {
        // No need to check Twig internal objects
        if ($obj instanceof Template || $obj instanceof Markup) {
            return;
        }

        $property = strtolower($property);

        foreach ($this->blockedProperties as $type => $properties) {
            if ($obj instanceof $type && in_array($property, $properties)) {
                $class = get_class($obj);
                throw new SecurityNotAllowedPropertyError(sprintf('Getting "%s" property in a "%s" object is blocked.', $property, $class), $class, $property);
            }
        }
    }

    /**
     * Checks if a given method is allowed to be called on a given object
     *
     * @param object $obj
     * @param string $method
     * @throws SecurityNotAllowedMethodError
     */
    public function checkMethodAllowed($obj, $method): void
    {
        // No need to check Twig internal objects
        if ($obj instanceof Template || $obj instanceof Markup) {
            return;
        }

        $method = strtolower($method);

        if (
            in_array($method, $this->blockedMethods['*'])
        ) {
            $class = get_class($obj);
            throw new SecurityNotAllowedMethodError(sprintf('Calling "%s" method on a "%s" object is blocked.', $method, $class), $class, $method);
        }

        foreach ($this->allowedMethods as $type => $methods) {
            if ($obj instanceof $type && !in_array($method, $methods)) {
                $class = get_class($obj);
                throw new SecurityNotAllowedMethodError(sprintf('Calling "%s" method on a "%s" object is blocked.', $method, $class), $class, $method);
            }
        }

        foreach ($this->blockedMethods as $type => $methods) {
            if ($obj instanceof $type && in_array($method, $methods)) {
                $class = get_class($obj);
                throw new SecurityNotAllowedMethodError(sprintf('Calling "%s" method on a "%s" object is blocked.', $method, $class), $class, $method);
            }
        }
    }
}
