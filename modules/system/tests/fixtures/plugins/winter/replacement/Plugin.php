<?php namespace Winter\Replacement;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Winter Sample Plugin',
            'description' => 'Sample plugin used by unit tests.',
            'author' => 'Alexey Bobkov, Samuel Georges',
            'replaces' => [
                'Winter.Original' => '<=1.0.3'
            ]
        ];
    }
}
