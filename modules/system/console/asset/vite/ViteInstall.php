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
        {assetPackage?* : The asset package name to install.}
        {--no-install : Tells Winter not to run npm install after config update.}
        {--npm= : Defines a custom path to the "npm" binary.}
        {--d|disable-tty : Disable tty mode.}
        {--s|silent : Enables silent mode, no output will be shown.}
        {--p|package-json= : Defines a custom path to "package.json" file. Must be above the workspace path.}';

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

    /**
     * The required packages for this compiler
     */
    protected array $requiredDependencies = [
        'vite' => '^6.0.0',
        'laravel-vite-plugin' => '^1.1.0',
    ];
}
