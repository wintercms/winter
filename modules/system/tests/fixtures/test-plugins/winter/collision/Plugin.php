<?php namespace Winter\Collision;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Winter.Collision',
            'description' => 'Test plugin for version key collision',
            'author' => 'Winter CMS',
            'icon' => 'icon-leaf'
        ];
    }
}
