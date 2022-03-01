<?php namespace System\Console;

use File;
use Config;
use Cms\Classes\Theme;
use Winter\Storm\Support\Str;
use Winter\Storm\Console\Command;
use Symfony\Component\Process\Process;
use System\Classes\MixAssets;
use System\Classes\PluginManager;
use Symfony\Component\Process\Exception\ProcessSignaledException;

class MixInstall extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:install';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:install
        {npmArgs?* : Arguments to pass through to the "npm" binary}
        {--npm= : Defines a custom path to the "npm" binary}
        {--p|package=* : Defines one or more packages to install}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Install Node.js dependencies required for mixed assets';

    /**
     * @var string The path to the "npm" executable.
     */
    protected $npmPath = 'npm';

    /**
     * @var string Default version of Laravel Mix to install
     */
    protected $defaultMixVersion = '^6.0.41';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('npm')) {
            $this->npmPath = $this->option('npm', 'npm');
        }

        if (!version_compare($this->getNpmVersion(), '7', '>')) {
            $this->error('"npm" version 7 or above must be installed to run this command.');
            return 1;
        }

        $mixedAssets = MixAssets::instance();
        $mixedAssets->fireCallbacks();

        $registeredPackages = $mixedAssets->getPackages();
        $requestedPackages = $this->option('package') ?: [];

        // Normalize the requestedPackages option
        if (count($requestedPackages)) {
            foreach ($requestedPackages as &$name) {
                $name = strtolower($name);
            }
            unset($name);
        }

        // Filter the registered packages to only include requested packages
        if (count($requestedPackages) && count($registeredPackages)) {
            $availablePackages = array_keys($registeredPackages);
            $cmsEnabled = in_array('Cms', Config::get('cms.loadModules'));

            // Autogenerate config files for packages that don't exist but can be autodiscovered
            foreach ($requestedPackages as $package) {
                // Check if the package is already registered
                if (in_array($package, $availablePackages)) {
                    continue;
                }

                // Check if package could be a module (but explicitly ignore core Winter modules)
                if (Str::startsWith($package, 'module-') && !in_array($package, ['system', 'backend', 'cms'])) {
                    $mixedAssets->registerPackage($package, base_path('modules/' . Str::after($package, 'module-') . '/winter.mix.js'));
                    continue;
                }

                // Check if package could be a theme
                if (
                    $cmsEnabled
                    && Str::startsWith($package, 'theme-')
                    && Theme::exists(Str::after($package, 'theme-'))
                ) {
                    $theme = Theme::load(Str::after($package, 'theme-'));
                    $mixedAssets->registerPackage($package, $theme->getPath() . '/winter.mix.js');
                    continue;
                }

                // Check if a package could be a plugin
                if (PluginManager::instance()->exists($package)) {
                    $mixedAssets->registerPackage($package, PluginManager::instance()->getPluginPath($package) . '/winter.mix.js');
                    continue;
                }
            }

            // Get an updated list of packages including any newly added packages
            $registeredPackages = $mixedAssets->getPackages();

            // Filter the registered packages to only deal with the requested packages
            foreach (array_keys($registeredPackages) as $name) {
                if (!in_array($name, $requestedPackages)) {
                    unset($registeredPackages[$name]);
                }
            }
        }

        if (!count($registeredPackages)) {
            if (count($requestedPackages)) {
                $this->error('No registered packages matched the requested packages for installation.');
                return 1;
            } else {
                $this->info('No packages registered for mixing.');
                return 0;
            }
        }

        // Load the main package.json for the project
        $canModifyPackageJson = null;
        $packageJsonPath = base_path('package.json');
        $packageJson = [];
        if (File::exists($packageJsonPath)) {
            $packageJson = json_decode(File::get($packageJsonPath), true);
        }
        $workspacesPackages = $packageJson['workspaces']['packages'] ?? [];

        // Check to see if Laravel Mix is already present as a dependency
        if (
            (
                !isset($packageJson['dependencies']['laravel-mix'])
                && !isset($packageJson['devDependencies']['laravel-mix'])
            )
            && $this->confirm('laravel-mix was not found as a dependency in package.json, would you like to add it?', true)
        ) {
            $canModifyPackageJson = true;
            $packageJson['devDependencies'] = array_merge($packageJson['devDependencies'] ?? [], ['laravel-mix' => $this->defaultMixVersion]);
        }

        // Process each package
        foreach ($registeredPackages as $name => $package) {
            // Detect missing winter.mix.js files and install them
            if (!File::exists($package['mix'])) {
                $this->info(
                    sprintf('No Mix file found for %s, creating one at %s...', $name, $package['mix'])
                );
                File::put($package['mix'], File::get(__DIR__ . '/fixtures/winter.mix.js.fixture'));
            }

            // Add the package path to the instance's package.json->workspaces->packages property if not present
            if (!in_array($package['path'], $workspacesPackages)) {
                if (!isset($canModifyPackageJson)) {
                    if ($this->confirm('package.json will be modified. Continue?', true)) {
                        $canModifyPackageJson = true;
                    } else {
                        $canModifyPackageJson = false;
                        break;
                    }
                }

                $this->info(
                    sprintf('Adding %s (%s) to the workspaces.packages property in package.json', $name, $package['path'])
                );
                $workspacesPackages = array_merge($workspacesPackages, [$package['path']]);
            }
        }

        // Modify the package.json file if required
        if ($canModifyPackageJson) {
            asort($workspacesPackages);
            $packageJson['workspaces']['packages'] = array_values($workspacesPackages);
            File::put($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            // Ensure separation between package.json modification messages and rest of output
            $this->info('');
        }

        if ($this->installPackageDeps() !== 0) {
            $this->error('Unable to install dependencies.');
        } else {
            $this->info('Dependencies successfully installed!');
        }

        return 0;
    }

    /**
     * Installs the dependencies for the given package.
     *
     * @return int
     */
    protected function installPackageDeps()
    {
        $command = $this->argument('npmArgs') ?? [];
        array_unshift($command, 'npm', 'i');

        $process = new Process($command, base_path(), null, null, null);

        // Attempt to set tty mode, catch and warn with the exception message if unsupported
        try {
            $process->setTty(true);
        } catch (\Throwable $e) {
            $this->warn($e->getMessage());
        }

        try {
            return $process->run(function ($status, $stdout) {
                $this->getOutput()->write($stdout);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }

            return 1;
        }

        $this->info('');

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
}
