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
            $this->npmPath = $this->option('npm');
        }

        $mixedAssets = MixAssets::instance();
        $mixedAssets->fireCallbacks();

        if ($mixedAssets->getPackageCount() === 0) {
            $this->info('No packages registered for mixing.');
            return 0;
        }

        if (!version_compare($this->getNpmVersion(), '6', '>')) {
            $this->error('"npm" version 7 or above must be installed to run this command.');
            return 1;
        }

        // Process each package
        foreach ($mixedAssets->getPackages() as $name => $package) {
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
        ];
    }
}
