<?php namespace System\Console\Asset\Mix;

use System\Console\Asset\AssetInstall;

class MixInstall extends AssetInstall
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:install';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:install
        {npmArgs?* : Arguments to pass through to the "npm" binary}
        {--npm= : Defines a custom path to the "npm" binary}
        {--p|package=* : Defines one or more packages to install}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Install Node.js dependencies required for mixed assets';

    protected string $assetType = 'mix';

    /**
     * @var string The asset config file
     */
    protected string $configFile = 'winter.mix.js';

    /**
     * @var array The packages required for asset compilation
     */
    protected array $packages = [
        'laravel-mix' => '^6.0.41'
    ];
}
