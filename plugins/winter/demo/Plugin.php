<?php

/**
 * The plugin.php file (called the plugin initialization script) defines the plugin information class.
 */

namespace Winter\Demo;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'Winter Demo',
            'description' => 'Provides features used by the provided demonstration theme.',
            'author'      => 'Alexey Bobkov, Samuel Georges',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            '\Winter\Demo\Components\Todo' => 'demoTodo'
        ];
    }
}
