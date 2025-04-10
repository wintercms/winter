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
        {--no-stubs : Disable stub file generation}
        {--s|silent : Enables silent mode, no output will be shown.}
        {--f|force : Force file overwrites}';

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

    /**
     * Output a helpful message with the twig code to set up vite after config generation is complete
     */
    public function afterExecution(): void
    {
        if ($this->option('silent')) {
            return;
        }

        $packageName = $this->makePackageName($this->argument('packageName'));
        $this->output->writeln('');
        $this->info('Add the following to your twig to enable asset loading:');
        if ($this->option('react')) {
            $this->output->writeln(sprintf(
                '<fg=blue>{{ viteReactRefresh(\'%1$s\') }}</>',
                strtolower($this->argument('packageName'))
            ));
        }
        $this->output->writeln(sprintf(
            '<fg=blue>{{ vite([\'assets/src/css/%1$s.css\', \'assets/src/js/%1$s.js\'], \'%2$s\') }}</>',
            $packageName,
            strtolower($this->argument('packageName'))
        ));
    }
}
