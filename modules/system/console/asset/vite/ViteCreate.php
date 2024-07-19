<?php

namespace System\Console\Asset\Vite;

use System\Console\Asset\AssetCreate;

class ViteCreate extends AssetCreate
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'vite:create';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'vite:create
        {packageName : The package name to add configuration for}
        {--no-stubs : Disable stub file generation}';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'vite:config',
    ];

    /**
     * The type of compilable to configure
     */
    protected string $assetType = 'vite';

    /**
     * The name of the config file
     */
    protected string $configFile = 'vite.config.mjs';
}
