<?php

namespace System\Console\Asset\Npm;

use System\Console\Asset\Npm\NpmCommand;

class NpmRun extends NpmCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'npm:run';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'npm:run
        {package : Defines the package where the script is located.}
        {script : The name of the script to run, as defined in the package.json "scripts" config.}
        {additionalArgs?* : Arguments to pass through to the script being run.}
        {--f|production : Runs the script in "production" mode.}
        {--s|silent : Silent mode.}
        {--disable-tty : Disable tty mode}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Runs a script in a given package.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'mix:run'
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        [$package, $packageJson] = $this->getPackage();

        $script = $this->argument('script');

        if (!$packageJson->hasScript($script)) {
            $this->error(
                sprintf('Script "%s" is not defined in package "%s".', $script, $this->argument('package'))
            );
            return 1;
        }

        if (!$this->option('silent')) {
            $this->info(sprintf('Running script "%s" in package "%s"', $script, $this->argument('package')));
        }

        $command = ($this->argument('additionalArgs')) ?? [];
        if (count($command)) {
            array_unshift($command, 'npm', 'run', $script, '--');
        } else {
            array_unshift($command, 'npm', 'run', $script);
        }

        return $this->npmRun($command, $package['path']);
    }
}
