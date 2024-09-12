<?php

namespace System\Console\Asset\Npm;

use System\Console\Asset\NpmCommand;

class NpmUpdate extends NpmCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'npm:update';

    /**
     * @inheritDoc
     */
    protected $description = 'Update Node.js dependencies required for mixed assets';

    /**
     * @inheritDoc
     */
    protected $signature = 'npm:update
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
        if (count($command)) {
            array_unshift($command, 'npm', 'update', '--');
        } else {
            array_unshift($command, 'npm', 'update');
        }

        return $this->npmRun($command, $package['path'] ?? '');
    }
}
