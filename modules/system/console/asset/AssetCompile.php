<?php

namespace System\Console\Asset;

use Symfony\Component\Process\Process;
use System\Classes\Asset\PackageManager;
use System\Classes\Asset\PackageJson;
use Winter\Storm\Console\Command;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Str;

abstract class AssetCompile extends Command
{
    /**
     * @var string The console command description.
     */
    protected $description = 'Mix and compile assets';

    /**
     * PackageJson object holding the contents of the active package.json
     */
    protected PackageJson $packageJson;

    /**
     * Name of config file i.e. mix.webpack.js, vite.config.js
     */
    protected string $configFile;

    /**
     * File path being watched, used for cleanup by mix:watch
     */
    protected string $watchingFilePath;

    public function compileHandle(string $type): int
    {
        // Exit early if node_modules isn't available yet
        if (!File::exists(base_path('node_modules'))) {
            $this->error(sprintf(
                'The Node dependencies are not available, try running %s:install first.',
                $type
            ));
            return 1;
        }

        $compilableAssets = PackageManager::instance();
        $compilableAssets->fireCallbacks();

        $registeredPackages = $compilableAssets->getPackages($type);
        $requestedPackages = $this->option('package') ?: [];

        // Calling commands in unit tests can cause the option casting to not work correctly,
        // ensure that the option value is always an array
        if (is_string($requestedPackages)) {
            $requestedPackages = [$requestedPackages];
        }

        // Normalize the requestedPackages option
        if (count($requestedPackages)) {
            foreach ($requestedPackages as &$name) {
                $name = strtolower($name);
            }
            unset($name);
        }

        // Filter the registered packages to only include requested packages
        if (count($requestedPackages) && count($registeredPackages)) {
            // Get an updated list of packages including any newly added packages
            $registeredPackages = $compilableAssets->getPackages($type);

            // Filter the registered packages to only deal with the requested packages
            foreach (array_keys($registeredPackages) as $name) {
                if (!in_array($name, $requestedPackages)) {
                    unset($registeredPackages[$name]);
                }
            }
        }

        if (!count($registeredPackages)) {
            if (count($requestedPackages)) {
                $this->error('No registered packages matched the requested packages for compilation.');
                return 1;
            } else {
                $this->info('No packages registered for mixing.');
                return 0;
            }
        }

        $exits = [];
        foreach ($registeredPackages as $name => $package) {
            $relativeMixJsPath = $package['config'];
            if (!$this->isPackageWithinWorkspace($relativeMixJsPath)) {
                $this->error(sprintf(
                    'Unable to compile "%s", %s was not found in the package.json\'s workspaces.packages property.'
                    . ' Try running %s:install first.',
                    $name,
                    $relativeMixJsPath,
                    $type
                ));
                continue;
            }

            if (!$this->option('silent')) {
                $this->info(sprintf('Compiling package "%s"', $name));
            }

            $exitCode = $this->executeProcess(base_path($relativeMixJsPath));

            if ($exitCode > 0) {
                $this->error(sprintf('Unable to compile package "%s"', $name));
            }

            if ($this->option('stop-on-error') && $exitCode > 0) {
                return $exitCode;
            }

            $exits[] = $exitCode;
        }

        return (int) !empty(array_filter($exits));
    }

    public function watchHandle(string $type): int
    {
        $compilableAssets = PackageManager::instance();
        $compilableAssets->fireCallbacks();

        $packages = $compilableAssets->getPackages($type);
        $name = $this->argument('package');
        $nameLower = strtolower($name);

        if (!in_array($nameLower, array_keys($packages))) {
            $this->error(
                sprintf('Package "%s" is not a registered package.', $name)
            );
            return 1;
        }

        $package = $packages[$nameLower];

        $relativeConfigPath = $package['config'];
        if (!$this->isPackageWithinWorkspace($relativeConfigPath)) {
            $this->error(sprintf(
                'Unable to watch "%s", %s was not found in the package.json\'s workspaces.packages property. Try running %s:install first.',
                $name,
                $relativeConfigPath,
                $type
            ));
            return 1;
        }

        if (!$this->option('silent')) {
            $this->info(sprintf('Watching package "%s" for changes', $name));
        }

        $this->watchingFilePath = $relativeConfigPath;

        if ($this->executeProcess(base_path($relativeConfigPath)) !== 0) {
            $this->error(sprintf('Unable to compile package "%s"', $name));
            return 1;
        }

        return 0;
    }

    /**
     * Get the package path for the provided winter.mix.js file
     */
    protected function getPackagePath(string $path): string
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Get the path to the mix.webpack.js file for the provided winter.mix.js file
     */
    protected function getJsConfigPath(string $path): string
    {
        return $this->getPackagePath($path) . DIRECTORY_SEPARATOR . $this->configFile;
    }

    /**
     * Check if Mix is able to compile the provided winter.mix.js file
     */
    protected function isPackageWithinWorkspace(string $mixJsPath): bool
    {
        if (!isset($this->packageJson)) {
            // Load the main package.json for the project
            $this->packageJson = $this->getNpmPackageManifest();
        }

        return $this->packageJson->hasWorkspace(
            Str::replace(DIRECTORY_SEPARATOR, '/', $this->getPackagePath($mixJsPath))
        );
    }

    /**
     * Read the package.json file for the project, path configurable with the
     * `--manifest` option
     */
    protected function getNpmPackageManifest(): PackageJson
    {
        return new PackageJson(base_path($this->option('manifest') ?? 'package.json'));
    }

    /**
     * Run the mix command against the provided package
     */
    protected function executeProcess(string $configPath): int
    {
        $this->beforeExecution($configPath);
        $command = $this->createCommand($configPath);
        $commandEnv = $this->createCommandEnv($configPath);

        $process = new Process(
            $command,
            $this->getPackagePath($configPath),
            [
                'NODE_ENV' => $this->option('production', false) ? 'production' : 'development',
                ...$commandEnv
            ],
            null,
            null
        );

        if (!$this->option('disable-tty')) {
            try {
                $process->setTty(true);
            } catch (\Throwable $e) {
                // This will fail on unsupported systems
            }
        }

        $exitCode = $process->run(function ($status, $stdout) {
            if (!$this->option('silent')) {
                $this->getOutput()->write($stdout);
            }
        });

        $this->afterExecution($configPath);

        return $exitCode;
    }

    /**
     * Ran before dispatching the compile process, use for setting up
     */
    protected function beforeExecution(string $configPath): void
    {
        // do nothing
    }

    /**
     * Ran after dispatching the compile process, use for tearing down
     */
    protected function afterExecution(string $configPath): void
    {
        // do nothing
    }

    /**
     * Create the command array to create a Process object with
     */
    abstract protected function createCommand(string $configPath): array;

    /**
     * Return values to append to the command env
     */
    protected function createCommandEnv(string $configPath): array
    {
        return [];
    }
}
