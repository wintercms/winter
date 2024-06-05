<?php

namespace System\Console;

use Symfony\Component\Process\Process;
use System\Classes\CompilableAssets;
use Winter\Storm\Console\Command;
use Winter\Storm\Support\Facades\File;

class NpmRun extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'npm:run';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:run
        {package : Defines the package where the script is located.}
        {script : The name of the script to run, as defined in the package.json "scripts" config.}
        {additionalArgs?* : Arguments to pass through to the script being run.}
        {--f|production : Runs the script in "production" mode.}
        {--s|silent : Silent mode.}';

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
        $compilableAssets = CompilableAssets::instance();
        $compilableAssets->fireCallbacks();

        $packages = $compilableAssets->getPackages();
        $name = $this->argument('package');
        $script = $this->argument('script');

        if (!in_array($name, array_keys($packages))) {
            $this->error(
                sprintf('Package "%s" is not a registered package.', $name)
            );
            return 1;
        }

        $package = $packages[$name];
        $packageJson = $this->readPackageJson($package);

        if (!isset($packageJson['scripts'][$script])) {
            $this->error(
                sprintf('Script "%s" is not defined in package "%s".', $script, $name)
            );
            return 1;
        }

        $this->info(sprintf('Running script "%s" in package "%s"', $script, $name));

        $command = ($this->argument('additionalArgs')) ?? [];
        if (count($command)) {
            array_unshift($command, 'npm', 'run', $script, '--');
        } else {
            array_unshift($command, 'npm', 'run', $script);
        }


        $process = new Process(
            $command,
            base_path($package['path']),
            ['NODE_ENV' => $this->option('production', false) ? 'production' : 'development'],
            null,
            null
        );

        try {
            $process->setTty(true);
        } catch (\Throwable $e) {
            // This will fail on unsupported systems
        }

        return $process->run(function ($status, $stdout) {
            if (!$this->option('silent')) {
                $this->getOutput()->write($stdout);
            }
        });
    }

    /**
     * Reads the package.json file for the given package.
     */
    protected function readPackageJson(array $package): array
    {
        $packageJsonPath = base_path($package['package']);
        return File::exists($packageJsonPath)
            ? json_decode(File::get($packageJsonPath), true)
            : [];
    }
}
