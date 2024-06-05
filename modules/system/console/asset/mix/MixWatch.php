<?php

namespace System\Console\Asset\Mix;

use System\Classes\CompilableAssets;

class MixWatch extends MixCompile
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:watch';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:watch
        {package : Defines the package to watch for changes}
        {webpackArgs?* : Arguments to pass through to the Webpack CLI}
        {--f|production : Runs compilation in "production" mode}
        {--m|manifest= : Defines package.json to use for compile}
        {--s|silent : Silent mode}
        {--no-progress : Do not show mix progress}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Mix and compile assets on-the-fly as changes are made.';

    /**
     * Call the AssetCompile::watchHandle with the mix type
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->watchHandle('mix');
    }

    /**
     * Create the command array to create a Process object with
     */
    protected function createCommand(string $configPath): array
    {
        $command = parent::createCommand($configPath);

        // @TODO: Detect Homestead running on Windows to switch to watch-poll-options instead, see https://laravel-mix.com/docs/6.0/cli#polling
        $command[] = '--watch';

        return $command;
    }

    /**
     * Handle the cleanup of this command if a termination signal is received
     */
    public function handleCleanup(): void
    {
        $this->newLine();
        $this->info('Cleaning up: ' . $this->getPackagePath(base_path($this->watchingFilePath)));
        $this->afterExecution(base_path($this->watchingFilePath));
    }
}
