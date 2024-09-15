<?php

namespace System\Console\Asset;

use Cms\Classes\Theme;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;
use System\Classes\Asset\PackageJson;
use System\Classes\Asset\PackageManager;
use System\Classes\PluginManager;
use System\Console\Asset\Exceptions\PackageIgnoredException;
use System\console\asset\exceptions\PackageNotConfiguredException;
use System\Console\Asset\Exceptions\PackageNotFoundException;
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
     * Type of asset to be installed, @see PackageManager
     */
    protected string $assetType;

    /**
     * The asset config file
     */
    protected string $configFile;

    /**
     * The required packages for this compiler
     */
    protected array $requiredPackages = [];

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
        $compilableAssets = PackageManager::instance();
        $compilableAssets->fireCallbacks();

        $registeredPackages = $compilableAssets->getPackages($this->assetType);
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

                switch (PackageManager::instance()->getPackageTypeFromName($package)) {
                    case PackageManager::TYPE_MODULE:
                        $compilableAssets->registerPackage(
                            $package,
                            base_path('modules/' . Str::after($package, 'module-') . '/' . $this->configFile),
                            $this->assetType
                        );
                        break;
                    case PackageManager::TYPE_THEME:
                        $theme = Theme::load(Str::after($package, 'theme-'));
                        $compilableAssets->registerPackage(
                            $package,
                            $theme->getPath() . '/' . $this->configFile,
                            $this->assetType
                        );
                        break;
                    case PackageManager::TYPE_PLUGIN:
                        $compilableAssets->registerPackage(
                            $package,
                            PluginManager::instance()->getPluginPath($package) . '/' . $this->configFile,
                            $this->assetType
                        );
                        break;
                    case null:
                        throw new PackageNotFoundException(sprintf(
                            'The package `%s` does not exist.',
                            $package
                        ));
                }
            }

            // Get an updated list of packages including any newly added packages
            $registeredPackages = $compilableAssets->getPackages($this->assetType);

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
        foreach ($this->requiredPackages as $package => $version) {
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
        // Check if the user requested a specific package for install
        $requestedPackage = strtolower($this->argument('assetPackage'));

        if ($requestedPackage) {
            // We did not find the package, exit
            if (!isset($registeredPackages[$requestedPackage])) {
                if ($detected = PackageManager::instance()->getPackage($requestedPackage)) {
                    switch (count($detected)) {
                        case 1:
                            if ($detected[0]['type'] !== $this->assetType) {
                                throw new PackageNotConfiguredException(sprintf(
                                    'The requested package `%s` is only configured for %s. Run `php artisan %s:create %1$s`',
                                    $this->argument('assetPackage'),
                                    $detected[0]['type'],
                                    $this->assetType
                                ));
                            }

                            if ($detected[0]['ignored']) {
                                throw new PackageIgnoredException(sprintf(
                                    'The requested package `%s` is ignored, remove it from package.json to continue',
                                    $this->argument('assetPackage'),
                                ));
                            }
                            break;
                        case 2:
                        default:
                            if (($detected[0]['ignored'] ?? false) || ($detected[1]['ignored'] ?? false)) {
                                throw new PackageIgnoredException(sprintf(
                                    'The requested package `%s` is ignored, remove it from package.json to continue',
                                    $this->argument('assetPackage'),
                                ));
                            }
                            break;
                    }
                }

                throw new PackageNotFoundException(sprintf(
                    'The requested package `%s` could not be found.',
                    $this->argument('assetPackage'),
                ));
            }

            $this->processPackage($packageJson, $requestedPackage, $registeredPackages[$requestedPackage], true);

            return $packageJson;
        }

        // Process each found package
        foreach ($registeredPackages as $name => $package) {
            $this->processPackage($packageJson, $name, $package);
        }

        return $packageJson;
    }

    protected function processPackage(PackageJson $packageJson, string $name, array $package, bool $force = false): bool
    {
        // Normalize package path across OS types
        $packagePath = Str::replace(DIRECTORY_SEPARATOR, '/', $package['path']);
        // Add the package path to the instance's package.json->workspaces->packages property if not present
        if (!$packageJson->hasWorkspace($packagePath) && !$packageJson->hasIgnoredPackage($packagePath)) {
            if (
                $force
                || $this->confirm(
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

        // Detect missing config files and install them
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
    protected function installPackageDeps(): int
    {
        $command = [];
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
