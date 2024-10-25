<?php

namespace System\Console\Asset\Npm;

use System\Console\Asset\Npm\NpmCommand;
use Winter\Storm\Exception\SystemException;

class NpmInstall extends NpmCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'npm:install';

    /**
     * @inheritDoc
     */
    protected $description = 'Install Node.js dependencies for a package';

    /**
     * @inheritDoc
     */
    protected $signature = 'npm:install
        {package? : The package name to add configuration for}
        {npmArgs?* : Arguments to pass through to the "npm" binary}
        {--npm= : Defines a custom path to the "npm" binary}
        {--d|dev : Install packages in devDependencies}
        {--s|silent : Silent mode.}
        {--disable-tty : Disable tty mode}';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $command = ($this->argument('npmArgs')) ?? [];

        try {
            [$package, $packageJson] = $this->getPackage();
        } catch (SystemException $e) {
            if (!str_contains($e->getMessage(), 'is not a registered package.')) {
                throw $e;
            }
            array_unshift($command, $this->argument('package'));
        }

        array_unshift($command, 'npm', 'install');

        if ($this->option('dev')) {
            $command[] = '--save-dev';
        }

        return $this->npmRun($command, $package['path'] ?? '');
    }
}
