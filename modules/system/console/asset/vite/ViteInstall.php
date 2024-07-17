<?php

namespace System\Console\Asset\Vite;

use System\Console\Asset\AssetInstall;

class ViteInstall extends AssetInstall
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'vite:install';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'vite:install
        {npmArgs?* : Arguments to pass through to the "npm" binary}
        {--npm= : Defines a custom path to the "npm" binary}
        {--p|package=* : Defines one or more packages to install}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Install Node.js dependencies required for vite assets';

    /**
     * The type of compilable to configure
     */
    protected string $assetType = 'vite';

    /**
     * The asset config file
     */
    protected string $configFile = 'vite.config.mjs';
}
