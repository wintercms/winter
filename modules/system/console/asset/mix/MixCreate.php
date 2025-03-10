<?php namespace System\Console\Asset\Mix;

use System\Console\Asset\AssetCreate;

class MixCreate extends AssetCreate
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:create';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:create
        {packageName : The package name to add configuration for}
        {--no-stubs : Disable stub file generation}
        {--s|silent : Enables silent mode, no output will be shown.}
        {--f|force : Force file overwrites}';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'mix:config',
    ];

    /**
     * The type of compilable to configure
     */
    protected string $assetType = 'mix';

    /**
     * The name of the config file
     */
    protected string $configFile = 'winter.mix.js';
}
