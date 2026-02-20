<?php

namespace System\Console\Asset\Vite;

use System\Console\Asset\AssetCompile;
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
        {--s|silent : Enables silent mode, no output will be shown.}
        {--d|disable-tty : Disable tty mode}
        {--e|stop-on-error : Exit once an error is encountered}
        {--m|manifest= : Defines package.json to use for compile}
        {--p|package=* : Defines one or more packages to compile}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Vite and compile assets';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'vite:build'
    ];

    /**
     * Name of config file i.e. mix.webpack.js, vite.config.js
     */
    protected string $configFile = 'vite.config.mjs';

    /**
     * Call the AssetCompile::compileHandle with the vite type
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
        );

        return $command;
    }

    /**
     * Return values to append to the command env
     */
    protected function createCommandEnv(string $configPath): array
    {
        return [
            'VITE_BASE' => Str::after($this->getPackagePath($configPath), base_path()),
        ];
    }
}
