<?php

namespace System\Console\Asset\Vite;

use Winter\Storm\Support\Facades\File;

class ViteWatch extends ViteCompile
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'vite:watch';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'vite:watch
        {package : The package to watch for changes (ex. author.plugin, theme-code, etc)}
        {viteArgs?* : Arguments to pass through to the Webpack CLI}
        {--f|production : Runs compilation in "production" mode}
        {--m|manifest= : Defines package.json to use for compile}
        {--s|silent : Enables silent mode, no output will be shown.}
        {--d|disable-tty : Disable tty mode}
        {--no-progress : Do not show mix progress}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Vite and compile assets on-the-fly as changes are made.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'vite:dev'
    ];

    /**
     * Call the AssetCompile::watchHandle with the vite type
     */
    public function handle(): int
    {
        return $this->watchHandle('vite');
    }

    /**
     * Create the command array to create a Process object with
     */
    protected function createCommand(string $configPath): array
    {
        $command = parent::createCommand($configPath);
        $key = array_search('build', $command);
        unset($command[$key]);

        $command[] = '--host';

        return array_values($command);
    }

    /**
     * Handle the cleanup of this command if a termination signal is received
     */
    public function handleCleanup(): void
    {
        $this->newLine();
        $this->info('Running compile to ensure files exist after exit');

        $this->call('vite:compile', [
            '--package' => $this->argument('package'),
        ]);
    }
}
