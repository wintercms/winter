<?php

namespace System\Console\Asset\Vite;

use System\Console\Asset\AssetConfig;

class ViteConfig extends AssetConfig
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'vite:config';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'vite:config
        {packageName : The package name to add configuration for}
        {--t|tailwind : Setup tailwind}
        {--u|vue : Setup vue}';

    /**
     * The type of compilable to configure
     */
    protected string $assetType = 'vite';

    /**
     * The name of the config file
     */
    protected string $configFile = 'vite.config.js';
}
