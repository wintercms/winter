<?php

namespace System\Console\Asset;

use Cms\Classes\Theme;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;
use System\Classes\CompilableAssets;
use System\Classes\PackageJson;
use System\Classes\PluginManager;
use Winter\Storm\Console\Command;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Str;

abstract class AssetInstall extends Command
{
    /**
     * The path to the "npm" executable.
     */
    protected string $npmPath = 'npm';

    /**
     * Terms used in messages.
     */
    protected array $terms = [
        'complete' => 'install',
        'completed' => 'installed',
    ];

    /**
     * The NPM command to run.
     */
    protected string $npmCommand = 'install';

    /**
     * Type of asset to be installed, @see CompilableAssets
     */
    protected string $assetType;

    /**
     * The asset config file
     */
    protected string $configFile;

    /**
     * The packages required for asset compilation
     */
    protected array $packages;

    /**
     * Execute the console command.
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

        [$requestedPackages, $registeredPackages] = $this->getRequestedAndRegisteredPackages();

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
        $packageJsonPath = base_path('package.json');

        // Get base package.json
        $packageJson = new PackageJson($packageJsonPath);
        // Ensure asset compiling packages are set in package.json, then save
        $this->validateRequirePackagesPresent($packageJson)
            ->save();
        // Process compilable asset packages, then save
        $this->processPackages($registeredPackages, $packageJson)
            ->save();

        // Ensure separation between package.json modification messages and rest of output
        $this->info('');

        if ($this->installPackageDeps() !== 0) {
            $this->error("Unable to {$this->terms['complete']} dependencies.");
            return 1;
        }

        $this->info("Dependencies successfully {$this->terms['completed']}!");

        return 0;
    }

    protected function getRequestedAndRegisteredPackages(): array
    {
        $compilableAssets = CompilableAssets::instance();
        $compilableAssets->fireCallbacks();

        $registeredPackages = $compilableAssets->getPackages(type: $this->assetType);
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
                    $compilableAssets->registerPackage(
                        $package,
                        base_path('modules/' . Str::after($package, 'module-') . '/' . $this->configFile),
                        $this->assetType
                    );
                    continue;
                }

                // Check if package could be a theme
                if (
                    $cmsEnabled
                    && Str::startsWith($package, 'theme-')
                    && Theme::exists(Str::after($package, 'theme-'))
                ) {
                    $theme = Theme::load(Str::after($package, 'theme-'));
                    $compilableAssets->registerPackage(
                        $package,
                        $theme->getPath() . '/' . $this->configFile,
                        $this->assetType
                    );
                    continue;
                }

                // Check if a package could be a plugin
                if (PluginManager::instance()->exists($package)) {
                    $compilableAssets->registerPackage(
                        $package,
                        PluginManager::instance()->getPluginPath($package) . '/' . $this->configFile,
                        $this->assetType
                    );
                    continue;
                }
            }

            // Get an updated list of packages including any newly added packages
            $registeredPackages = $compilableAssets->getPackages();

            // Filter the registered packages to only deal with the requested packages
            foreach (array_keys($registeredPackages) as $name) {
                if (!in_array($name, $requestedPackages)) {
                    unset($registeredPackages[$name]);
                }
            }
        }

        return [$requestedPackages, $registeredPackages];
    }

    protected function validateRequirePackagesPresent(PackageJson $packageJson): PackageJson
    {
        // Check to see if required packages are already present as a dependency
        foreach ($this->packages as $package => $version) {
            if (
                !$packageJson->hasDependency($package)
                && $this->confirm($package . ' was not found as a dependency in package.json, would you like to add it?', true)
            ) {
                $packageJson->addDependency($package, $version, dev: true);
            }
        }

        return $packageJson;
    }

    protected function processPackages(array $registeredPackages, PackageJson $packageJson): PackageJson
    {
        // Process each package
        foreach ($registeredPackages as $name => $package) {
            // Normalize package path across OS types
            $packagePath = Str::replace(DIRECTORY_SEPARATOR, '/', $package['path']);
            // Add the package path to the instance's package.json->workspaces->packages property if not present
            if (!$packageJson->hasWorkspace($packagePath) && !$packageJson->hasIgnoredPackage($packagePath)) {
                if (
                    $this->confirm(
                        sprintf(
                            "Detected %s (%s), should it be added to your package.json?",
                            $name,
                            $packagePath
                        ),
                        true
                    )
                ) {
                    $packageJson->addWorkspace($packagePath);
                    $this->info(sprintf(
                        'Adding %s (%s) to the workspaces.packages property in package.json',
                        $name,
                        $packagePath
                    ));
                } else {
                    $packageJson->addIgnoredPackage($packagePath);
                    $this->warn(
                        sprintf('Ignoring %s (%s)', $name, $packagePath)
                    );
                }
            }

            // Detect missing winter.mix.js files and install them
            if (!File::exists($package['config'])) {
                $this->info(sprintf(
                    'No config file found for %s, creating one at %s...',
                    $name,
                    $package['config']
                ));
                // @TODO: consider this when using vite
                File::put($package['config'], File::get(__DIR__ . '/fixtures/' . $this->configFile . '.fixture'));
            }
        }

        return $packageJson;
    }

    /**
     * Installs the dependencies for the given package.
     */
    protected function installPackageDeps(): int
    {
        $command = $this->argument('npmArgs') ?? [];
        array_unshift($command, 'npm', $this->npmCommand);

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
     * Gets the installed NPM version.
     */
    protected function getNpmVersion(): string
    {
        $process = new Process(['npm', '--version']);
        $process->run();
        return $process->getOutput();
    }
}
