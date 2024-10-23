<?php

namespace System\Console\Asset\Npm;

use System\Console\Asset\Exceptions\PackageNotRegisteredException;
use System\Console\Asset\Npm\NpmCommand;

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
        {package? : The package name to add configuration for.}
        {npmArgs?* : Arguments to pass through to the "npm" binary.}
        {--npm= : Defines a custom path to the "npm" binary.}
        {--a|save : Tell npm to update package.json.}
        {--s|silent : Silent mode.}
        {--disable-tty : Disable tty mode}';

    /**
     * @inheritDoc
     */
    public $replaces = [
        'mix:update'
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $command = ($this->argument('npmArgs')) ?? [];

        try {
            [$package, $packageJson] = $this->getPackage();
        } catch (PackageNotRegisteredException $e) {
            array_unshift($command, $this->argument('package'));
        }

        $args = ['npm', 'update'];

        if ($this->option('save')) {
            $args[] = '--save';
        }

        if (count($command)) {
            $args[] = '--';
        }

        array_unshift($command, ...$args);

        return $this->npmRun($command, $package['path'] ?? '');
    }
}
