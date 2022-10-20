<?php namespace Mix\TestB;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Mix Test B',
            'description' => 'Sample plugin used by unit tests.',
            'author' => 'Mix Test'
        ];
    }
}
