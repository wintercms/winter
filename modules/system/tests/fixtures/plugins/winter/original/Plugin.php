<?php namespace Winter\Original;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Winter Sample Plugin',
            'description' => 'Sample plugin used by unit tests.',
            'author' => 'Alexey Bobkov, Samuel Georges'
        ];
    }
}
