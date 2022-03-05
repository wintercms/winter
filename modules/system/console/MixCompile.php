<?php namespace System\Console;

use File;
use Winter\Storm\Console\Command;
use Symfony\Component\Process\Process;
use System\Classes\MixAssets;

class MixCompile extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:compile';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:compile
        {webpackArgs?* : Arguments to pass through to the Webpack CLI}
        {--f|production : Runs compilation in "production" mode}
        {--p|package=* : Defines one or more packages to compile}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Mix and compile assets';

    /**
     * @var array Local cache of the package.json file contents
     */
    protected $packageJson;

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        // Exit early if node_modules isn't available yet
        if (!File::exists(base_path('node_modules'))) {
            $this->error('The Node dependencies are not available, try running mix:install first.');
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
                $this->error('No registered packages matched the requested packages for compilation.');
                return 1;
            } else {
                $this->info('No packages registered for mixing.');
                return 0;
            }
        }

        foreach ($registeredPackages as $name => $package) {
            $relativeMixJsPath = $package['mix'];
            if (!$this->canCompilePackage($relativeMixJsPath)) {
                $this->error(
                    sprintf('Unable to compile "%s", %s was not found in the package.json\'s workspaces.packages property. Try running mix:install first.', $name, $packagePath)
                );
                continue;
            }

            $this->info(
                sprintf('Mixing package "%s"', $name)
            );
            if ($this->mixPackage(base_path($relativeMixJsPath)) !== 0) {
                $this->error(
                    sprintf('Unable to compile package "%s"', $name)
                );
            }
        }

        return 0;
    }

    /**
     * Get the package path for the provided winter.mix.js file
     */
    protected function getPackagePath(string $mixJsPath): string
    {
        return pathinfo($mixJsPath, PATHINFO_DIRNAME);
    }

    /**
     * Get the path to the mix.webpack.js file for the provided winter.mix.js file
     */
    protected function getWebpackJsPath(string $mixJsPath): string
    {
        return $this->getPackagePath($mixJsPath) . '/mix.webpack.js';
    }

    /**
     * Check if Mix is able to compile the provided winter.mix.js file
     */
    protected function canCompilePackage(string $mixJsPath): bool
    {
        if (!isset($this->packageJson)) {
            // Load the main package.json for the project
            $canModifyPackageJson = null;
            $packageJsonPath = base_path('package.json');
            $packageJson = [];
            if (File::exists($packageJsonPath)) {
                $packageJson = json_decode(File::get($packageJsonPath), true);
            }
            $this->packageJson = $packageJson;
        }

        $workspacesPackages = $this->packageJson['workspaces']['packages'] ?? [];

        return in_array($this->getPackagePath($mixJsPath), $workspacesPackages);
    }

    /**
     * Run the mix command against the provided package
     */
    protected function mixPackage(string $mixJsPath): int
    {
        $this->createWebpackConfig($mixJsPath);
        $command = $this->createCommand($mixJsPath);

        $process = new Process(
            $command,
            $this->getPackagePath($mixJsPath),
            ['NODE_ENV' => $this->option('production', false) ? 'production' : 'development'],
            null,
            null
        );

        try {
            $process->setTty(true);
        } catch (\Throwable $e) {
            // This will fail on unsupported systems
        }

        $exitCode = $process->run(function ($status, $stdout) {
            if ($this->option('verbose')) {
                $this->getOutput()->write($stdout);
            }
        });

        $this->removeWebpackConfig($mixJsPath);

        return $exitCode;
    }

    /**
     * Create the command array to create a Process object with
     */
    protected function createCommand(string $mixJsPath): array
    {
        $basePath = base_path();
        $command = $this->argument('webpackArgs') ?? [];
        array_unshift(
            $command,
            $basePath . '/node_modules/webpack/bin/webpack.js',
            '--progress',
            '--config=' . $this->getWebpackJsPath($mixJsPath)
        );
        return $command;
    }

    /**
     * Create the temporary mix.webpack.js config file to run webpack with
     */
    protected function createWebpackConfig(string $mixJsPath): void
    {
        $basePath = base_path();
        $fixture = File::get(__DIR__ . '/fixtures/mix.webpack.js.fixture');

        $config = str_replace(
            ['%base%', '%notificationInject%', '%mixConfigPath%', '%pluginsPath%', '%appPath%'],
            [$basePath, '', $mixJsPath, plugins_path(), base_path()],
            $fixture
        );

        File::put($this->getWebpackJsPath($mixJsPath), $config);
    }

    /**
     * Remove the temporary mix.webpack.js file
     */
    protected function removeWebpackConfig(string $mixJsPath): void
    {
        $webpackJsPath = $this->getWebpackJsPath($mixJsPath);
        if (File::exists($webpackJsPath)) {
            File::delete($webpackJsPath);
        }
    }
}
