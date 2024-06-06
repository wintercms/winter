<?php namespace System\Console\Asset\Mix;

use System\Console\Asset\AssetConfig;

class MixConfig extends AssetConfig
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:config';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:config
        {packageName : The package name to add configuration for}
        {--t|tailwind : Setup tailwind}
        {--u|vue : Setup vue}';

    /**
     * The type of compilable to configure
     */
    protected string $assetType = 'mix';

    /**
     * The name of the config file
     */
    protected string $configFile = 'winter.mix.js';
}
