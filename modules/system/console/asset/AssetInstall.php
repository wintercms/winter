<?php

namespace System\Console\Asset;

use Cms\Classes\Theme;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;
use System\Classes\Asset\PackageJson;
use System\Classes\Asset\PackageManager;
use System\Classes\PluginManager;
use Winter\Storm\Console\Command;
use Winter\Storm\Exception\SystemException;
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
     * Path to package json, if null use base_path.
     */
    protected ?string $packageJsonPath = null;

    /**
     * Type of asset to be installed, @see PackageManager
     */
    protected string $assetType;

    /**
     * The asset config file
     */
    protected string $configFile;

    /**
     * The required dependencies for this compiler
     */
    protected array $requiredDependencies = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($npmPath = $this->option('npm')) {
            if (!File::exists($npmPath) || !is_executable($npmPath)) {
                $this->error('The supplied --npm path does not exist or is not executable.');
                return 1;
            }
            $this->npmPath = $npmPath;
        }

        if (!version_compare($this->getNpmVersion(), '7', '>')) {
            $this->error('"npm" version 7 or above must be installed to run this command.');
            return 1;
        }

        // If a custom path is passed, then validate it
        if ($packageJsonPath = $this->option('package-json')) {
            // If this is not an absolute path, then make it relative
            if (!str_starts_with($packageJsonPath, '/')) {
                $packageJsonPath = base_path($packageJsonPath);
            }

            if (!File::exists($packageJsonPath)) {
                $this->error('The supplied --package-json path does not exist.');
                return 1;
            }

            $this->packageJsonPath = $packageJsonPath;
        }

        // Get any packages the user has requested
        $requestedPackages = $this->argument('assetPackage') ?: [];

        $registeredPackages = $this->getRegisteredPackages($requestedPackages);

        if (!$registeredPackages) {
            if ($requestedPackages) {
                $this->error('No registered packages matched the requested packages for installation.');
                return 1;
            }

            $this->info('No packages registered for mixing.');
            return 0;
        }

        // Get base package.json
        $packageJson = new PackageJson($this->packageJsonPath ?? base_path('package.json'));

        // Ensure asset compiling packages are set in package.json, then save
        $this->validateRequireDependenciesPresent($packageJson)
            ->save();

        // Process compilable asset packages, then save
        $this->processPackages($registeredPackages, $packageJson)
            ->save();

        if (!$this->option('no-install')) {
            // Ensure separation between package.json modification messages and rest of output
            $this->info('');

            if ($this->runNpmInstall() !== 0) {
                $this->error("Unable to {$this->terms['complete']} dependencies.");
                return 1;
            }

            $this->info("Dependencies successfully {$this->terms['completed']}!");
        }

        return 0;
    }

    /**
     * Returns all packages registered by the system filtered by requestedPackages if defined
     * @throws SystemException
     */
    protected function getRegisteredPackages(array $requestedPackages = []): array
    {
        $packageManager = $this->getPackageManager();

        $registeredPackages = $packageManager->getPackages($this->assetType, true);

        // Normalize the requestedPackages option
        $requestedPackages = array_map(fn ($name) => strtolower($name), $requestedPackages);

        // Filter the registered packages to only include requested packages
        if (count($requestedPackages) && count($registeredPackages)) {
            $cmsEnabled = in_array('Cms', Config::get('cms.loadModules'));

            // Autogenerate config files for packages that don't exist but can be autodiscovered
            foreach ($requestedPackages as $package) {
                // Check if the package is already registered
                if (isset($registeredPackages[$package])) {
                    continue;
                }

                switch ($packageManager->getPackageTypeFromName($package)) {
                    case PackageManager::TYPE_MODULE:
                        $packageManager->registerPackage(
                            $package,
                            base_path('modules/' . Str::after($package, 'module-') . '/' . $this->configFile),
                            $this->assetType
                        );
                        break;
                    case PackageManager::TYPE_THEME:
                        if (!$cmsEnabled) {
                            break;
                        }
                        $theme = Theme::load(Str::after($package, 'theme-'));
                        $packageManager->registerPackage(
                            $package,
                            $theme->getPath() . '/' . $this->configFile,
                            $this->assetType
                        );
                        break;
                    case PackageManager::TYPE_PLUGIN:
                        $packageManager->registerPackage(
                            $package,
                            PluginManager::instance()->getPluginPath($package) . '/' . $this->configFile,
                            $this->assetType
                        );
                        break;
                    case null:
                        throw new SystemException(sprintf(
                            'PackageNotFoundException: The package `%s` does not exist.',
                            $package
                        ));
                }
            }

            // Get an updated list of packages including any newly added packages
            $registeredPackages = $packageManager->getPackages($this->assetType, true);

            // Filter the registered packages to only deal with the requested packages
            foreach (array_keys($registeredPackages) as $name) {
                if (!in_array($name, $requestedPackages)) {
                    unset($registeredPackages[$name]);
                }
            }
        }

        return $registeredPackages;
    }

    /**
     * Checks if the package.json of a package has the dependencies required for this command and asks the user if
     * they want to install them if not present.
     */
    protected function validateRequireDependenciesPresent(PackageJson $packageJson): PackageJson
    {
        // Check to see if required packages are already present as a dependency
        foreach ($this->requiredDependencies as $dependency => $version) {
            if (
                !$packageJson->hasDependency($dependency)
                && $this->confirm($dependency . ' was not found as a dependency in package.json, would you like to add it?', true)
            ) {
                $packageJson->addDependency($dependency, $version, dev: true);
            }
        }

        return $packageJson;
    }

    /**
     * Validates if the packages passed can be installed and if possible, installs them.
     * @throws SystemException
     * @throws PackageNotFoundException
     */
    protected function processPackages(array $registeredPackages, PackageJson $packageJson): PackageJson
    {
        // Check if the user requested a specific package for install
        if ($requestedPackages = array_map(fn ($name) => strtolower($name), $this->argument('assetPackage'))) {
            $packageManager = $this->getPackageManager();
            foreach ($requestedPackages as $requestedPackage) {
                // We did not find the package, exit
                if (!isset($registeredPackages[$requestedPackage])) {
                    if ($detected = $packageManager->getPackage($requestedPackage, true)) {
                        switch (count($detected)) {
                            case 1:
                                if ($detected[0]['type'] !== $this->assetType) {
                                    throw new SystemException(sprintf(
                                        'PackageNotConfiguredException: The requested package `%s` is only configured for %s. Run `php artisan %s:create %1$s`',
                                        $requestedPackage,
                                        $detected[0]['type'],
                                        $this->assetType
                                    ));
                                }

                                if ($detected[0]['ignored']) {
                                    throw new SystemException(sprintf(
                                        'PackageIgnoredException: The requested package `%s` is ignored, remove it from package.json to continue',
                                        $requestedPackage,
                                    ));
                                }
                                break;
                            case 2:
                            default:
                                if (($detected[0]['ignored'] ?? false) || ($detected[1]['ignored'] ?? false)) {
                                    throw new SystemException(sprintf(
                                        'PackageIgnoredException: The requested package `%s` is ignored, remove it from package.json to continue',
                                        $requestedPackage,
                                    ));
                                }
                                break;
                        }
                    }

                    throw new SystemException(sprintf(
                        'PackageNotFoundException: The requested package `%s` could not be found.',
                        $requestedPackage,
                    ));
                }

                $this->processPackage($packageJson, $requestedPackage, $registeredPackages[$requestedPackage], true);
            }

            return $packageJson;
        }

        // Process each found package
        foreach ($registeredPackages as $name => $package) {
            $this->processPackage($packageJson, $name, $package);
        }

        return $packageJson;
    }

    /**
     * Adds a package to the project workspace or mark it as ignored based on user input
     */
    protected function processPackage(PackageJson $packageJson, string $name, array $package, bool $force = false): bool
    {
        // Normalize package path across OS types
        $packagePath = Str::replace(DIRECTORY_SEPARATOR, '/', $package['path']);

        // Nicely report if the package is already in the workspace
        if ($packageJson->hasWorkspace($packagePath)) {
            $this->warn(sprintf(
                'Package %s (%s) is already included in workspaces.packages.',
                $name,
                $packagePath
            ));

            return true;
        }

        if ($packageJson->hasIgnoredPackage($packagePath)) {
            $this->warn(sprintf(
                'The requested package %s (%s) is ignored, remove it from package.json to continue.',
                $name,
                $packagePath
            ));

            return true;
        }

        // Add the package path to the instance's package.json->workspaces->packages property if not present
        if (!$packageJson->hasWorkspace($packagePath) && !$packageJson->hasIgnoredPackage($packagePath)) {
            if (
                $force
                || $this->confirm(
                    sprintf(
                        "Detected %s (%s), would you like to add it to package.json to include it in your project workspace? Answer no to ignore it.",
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

        // Detect missing config files and provide feedback
        if (!File::exists($package['config'])) {
            $this->info(sprintf(
                'No config file found for %s, you should run %s:config',
                $name,
                $this->assetType
            ));

            return false;
        }

        return true;
    }

    /**
     * Installs the dependencies for the given package.
     */
    protected function runNpmInstall(): int
    {
        $process = new Process(
            command: [$this->npmPath, 'install'],
            cwd: $this->packageJsonPath ? dirname($this->packageJsonPath) : base_path(),
            timeout: null
        );

        if (!$this->option('disable-tty')) {
            try {
                $process->setTty(true);
            } catch (\Throwable $e) {
                // This will fail on unsupported systems
            }
        }

        try {
            return $process->run(function ($status, $stdout) {
                if (!$this->option('silent')) {
                    $this->getOutput()->write($stdout);
                }
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }

            return 1;
        }
    }

    /**
     * Returns the root package.json as a PackageManager object
     */
    protected function getPackageManager(): PackageManager
    {
        // Flush the instance
        $packageManager = PackageManager::instance()->fireCallbacks();
        // Ensure the instance follows any custom package.json
        if ($this->packageJsonPath) {
            $packageManager->setPackageJsonPath($this->packageJsonPath);
        }

        return $packageManager;
    }

    /**
     * Gets the installed NPM version.
     */
    protected function getNpmVersion(): string
    {
        $process = new Process([$this->npmPath, '--version']);
        $process->run();
        return $process->getOutput();
    }
}
