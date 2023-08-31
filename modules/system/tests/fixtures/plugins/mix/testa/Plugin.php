<?php namespace Mix\TestA;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Mix Test A',
            'description' => 'Sample plugin used by unit tests.',
            'author' => 'Mix Test'
        ];
    }
}
