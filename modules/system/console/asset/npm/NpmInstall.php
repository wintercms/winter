<?php

namespace System\console\asset\npm;

use System\Console\Asset\NpmCommand;

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
        {--npm= : Defines a custom path to the "npm" binary}';

    /**
     * @inheritDoc
     */
    public $replaces = [
        'mix:update'
    ];

    public function handle(): int
    {
        [$package, $packageJson] = $this->getPackage();

        $command = ($this->argument('npmArgs')) ?? [];

        if (!$command) {
            return 0;
        }

        array_unshift($command, 'npm', 'install');

        return $this->npmRun($command, $package['path'] ?? '');
    }
}
