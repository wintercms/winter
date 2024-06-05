<?php

namespace System\Console\Asset\Vite;

use System\Classes\CompilableAssets;
use System\Console\Asset\AssetCompile;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Str;

class ViteCompile extends AssetCompile
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'vite:compile';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'vite:compile
        {viteArgs?* : Arguments to pass through to the Vite CLI}
        {--f|production : Runs compilation in "production" mode}
        {--s|silent : Silent mode}
        {--e|stop-on-error : Exit once an error is encountered}
        {--m|manifest= : Defines package.json to use for compile}
        {--p|package=* : Defines one or more packages to compile}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Vite and compile assets';

    protected string $configFile = 'vite.config.js';

    /**
     * @var array Local cache of the package.json file contents
     */
    protected array $packageJson;

    /**
     * Call the AssetCompile::compileHandle with the vite type
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->compileHandle('vite');
    }

    /**
     * Create the command array to create a Process object with
     */
    protected function createCommand(string $configPath): array
    {
        $basePath = base_path();
        $command = $this->argument('viteArgs') ?? [];
        array_unshift(
            $command,
            $basePath . sprintf('%1$snode_modules%1$s.bin%1$svite', DIRECTORY_SEPARATOR),
            'build',
            $this->option('silent') ? '--logLevel=silent' : '',
            '--base=' . Str::after($this->getPackagePath($configPath), base_path())
        );

        return $command;
    }
}
