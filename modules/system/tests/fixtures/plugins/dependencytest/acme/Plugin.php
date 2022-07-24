<?php namespace DependencyTest\Acme;

use Backend;
use Backend\Models\UserRole;
use System\Classes\PluginBase;

/**
 * Acme Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = [
        'DependencyTest.Dependency',
    ];

    /**
     * Returns information about this plugin.
     */
    public function pluginDetails(): array
    {
        return [
            'name' => 'ACME',
            'description' => 'This is a test plugin that will be used to check dependencies are loaded first.',
            'author' => 'Eric Pfeiffer',
            'icon' => 'icon-leaf'
        ];
    }
}
