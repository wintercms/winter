<?php namespace System\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use System\Classes\MixAssets;

class MixInstall extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'mix:install';

    /**
     * @var string The console command description.
     */
    protected $description = 'Install Node.js dependencies required for mixed assets';

    /**
     * @var string The path to the "npm" executable.
     */
    protected $npmPath = 'npm';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('npm')) {
            $this->npmPath = $this->option('npm', 'npm');
        }

        if (!version_compare($this->getNpmVersion(), '6', '>')) {
            $this->error('"npm" version 7 or above must be installed to run this command.');
            return 1;
        }

        $mixedAssets = MixAssets::instance();
        $mixedAssets->fireCallbacks();

        $packages = $mixedAssets->getPackages();

        if (count($this->option('package')) && count($packages)) {
            foreach (array_keys($packages) as $name) {
                if (!in_array($name, $this->option('package'))) {
                    unset($packages[$name]);
                }
            }
        }

        if (!count($packages)) {
            if (count($this->option('package'))) {
                $this->error('No registered packages matched the requested packages for installation.');
                return 1;
            } else {
                $this->info('No packages registered for mixing.');
                return 0;
            }
        }

        // Process each package
        foreach ($packages as $name => $package) {
            $this->info(
                sprintf('Installing dependencies for package "%s"', $name)
            );
            if ($this->installPackageDeps($package) !== 0) {
                $this->error(
                    sprintf('Unable to install dependencies for package "%s"', $name)
                );
            }
        }

        return 0;
    }

    /**
     * Installs the dependencies for the given package.
     *
     * @param string $package
     * @return int
     */
    protected function installPackageDeps($package)
    {
        $process = new Process(['npm', 'i'], $package['path']);
        $process->run(function ($status, $stdout) {
            $this->getOutput()->write($stdout);
        });

        return $process->getExitCode();
    }

    /**
     * Gets the install NPM version.
     *
     * @return string
     */
    protected function getNpmVersion()
    {
        $process = new Process(['npm', '--version']);
        $process->run();
        return $process->getOutput();
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['npm', null, InputOption::VALUE_REQUIRED, 'Defines a custom path to the "npm" binary'],
            ['package', 'p', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Defines one or more packages to install'],
        ];
    }
}
