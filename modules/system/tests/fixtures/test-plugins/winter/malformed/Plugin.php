<?php namespace Winter\Malformed;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Winter.Malformed',
            'description' => 'Test plugin for malformed version keys',
            'author' => 'Winter CMS',
            'icon' => 'icon-leaf'
        ];
    }
}
