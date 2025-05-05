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
        {assetPackage?* : The asset package name to install.}
        {--no-install : Tells Winter not to run npm install after config update.}
        {--npm= : Defines a custom path to the "npm" binary.}
        {--d|disable-tty : Disable tty mode.}
        {--s|silent : Enables silent mode, no output will be shown.}
        {--p|package-json= : Defines a custom path to "package.json" file. Must be above the workspace path.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Install Node.js dependencies required for mixed assets';

    /**
     * The asset compiler being used
     */
    protected string $assetType = 'mix';

    /**
     * The asset config file
     */
    protected string $configFile = 'winter.mix.js';

    /**
     * The required packages for this compiler
     */
    protected array $requiredDependencies = [
        'laravel-mix' => '^6.0.41',
    ];
}
