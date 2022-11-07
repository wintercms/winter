<?php namespace Mix\TestC;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Mix Test C',
            'description' => 'Sample plugin used by unit tests.',
            'author' => 'Mix Test'
        ];
    }
}
