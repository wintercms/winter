<?php namespace System\Console\Asset\Vite;

use File;
use Symfony\Component\Process\Process;
use System\Classes\CompilableAssets;
use Winter\Storm\Console\Command;
use Winter\Storm\Support\Str;

class ViteCompile extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'vite:compile';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'vite:compile
        {webpackArgs?* : Arguments to pass through to the Webpack CLI}
        {--f|production : Runs compilation in "production" mode}
        {--s|silent : Silent mode}
        {--e|stop-on-error : Exit once an error is encountered}
        {--m|manifest= : Defines package.json to use for compile}
        {--p|package=* : Defines one or more packages to compile}
        {--no-progress : Do not show vite progress}';

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
     */
    public function handle(): int
    {
        // Exit early if node_modules isn't available yet
        if (!File::exists(base_path('node_modules'))) {
            $this->error('The Node dependencies are not available, try running vite:install first.');
            return 1;
        }

        $viteedAssets = CompilableAssets::instance();
        $viteedAssets->fireCallbacks();

        $registeredPackages = $viteedAssets->getPackages();
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
            $registeredPackages = $viteedAssets->getPackages();

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
                $this->info('No packages registered for viteing.');
                return 0;
            }
        }

        $exits = [];
        foreach ($registeredPackages as $name => $package) {
            $relativeMixJsPath = $package['vite'];
            if (!$this->canCompilePackage($relativeMixJsPath)) {
                $this->error(sprintf(
                    'Unable to compile "%s", %s was not found in the package.json\'s workspaces.packages property.'
                     . ' Try running vite:install first.',
                    $name,
                    $relativeMixJsPath
                ));
                continue;
            }

            if (!$this->option('silent')) {
                $this->info(sprintf('Mixing package "%s"', $name));
            }

            $exitCode = $this->vitePackage(base_path($relativeMixJsPath));

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

    /**
     * Get the package path for the provided winter.vite.js file
     */
    protected function getPackagePath(string $viteJsPath): string
    {
        return pathinfo($viteJsPath, PATHINFO_DIRNAME);
    }

    /**
     * Get the path to the vite.webpack.js file for the provided winter.vite.js file
     */
    protected function getWebpackJsPath(string $viteJsPath): string
    {
        return $this->getPackagePath($viteJsPath) . DIRECTORY_SEPARATOR . 'vite.webpack.js';
    }

    /**
     * Check if Mix is able to compile the provided winter.vite.js file
     */
    protected function canCompilePackage(string $viteJsPath): bool
    {
        if (!isset($this->packageJson)) {
            // Load the main package.json for the project
            $this->packageJson = $this->readNpmPackageManifest();
        }

        $workspacesPackages = $this->packageJson['workspaces']['packages'] ?? [];

        return in_array(
            Str::replace(DIRECTORY_SEPARATOR, '/', $this->getPackagePath($viteJsPath)),
            $workspacesPackages
        );
    }

    /**
     * Read the package.json file for the project, path configurable with the
     * `--manifest` option
     */
    protected function readNpmPackageManifest(): array
    {
        $packageJsonPath = base_path($this->option('manifest') ?? 'package.json');
        return File::exists($packageJsonPath)
            ? json_decode(File::get($packageJsonPath), true)
            : [];
    }

    /**
     * Run the vite command against the provided package
     */
    protected function vitePackage(string $viteJsPath): int
    {
        $this->createWebpackConfig($viteJsPath);
        $command = $this->createCommand($viteJsPath);

        $process = new Process(
            $command,
            $this->getPackagePath($viteJsPath),
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
            if (!$this->option('silent')) {
                $this->getOutput()->write($stdout);
            }
        });

        $this->removeWebpackConfig($viteJsPath);

        return $exitCode;
    }

    /**
     * Create the command array to create a Process object with
     */
    protected function createCommand(string $viteJsPath): array
    {
        $basePath = base_path();
        $command = $this->argument('webpackArgs') ?? [];
        array_unshift(
            $command,
            $basePath . sprintf('%1$snode_modules%1$s.bin%1$swebpack', DIRECTORY_SEPARATOR),
            'build',
            $this->option('silent') ? '--stats=none' : '--progress',
            '--config=' . $this->getWebpackJsPath($viteJsPath)
        );
        return $command;
    }

    /**
     * Create the temporary vite.webpack.js config file to run webpack with
     */
    protected function createWebpackConfig(string $viteJsPath): void
    {
        $basePath = base_path();
        $fixture = File::get(__DIR__ . '/fixtures/vite.webpack.js.fixture');

        $config = str_replace(
            ['%base%', '%notificationInject%', '%viteConfigPath%', '%pluginsPath%', '%appPath%', '%silent%', '%noProgress%'],
            [addslashes($basePath), 'vite._api.disableNotifications();', addslashes($viteJsPath), addslashes(plugins_path()), addslashes(base_path()), (int) $this->option('silent'), (int) $this->option('no-progress')],
            $fixture
        );

        File::put($this->getWebpackJsPath($viteJsPath), $config);
    }

    /**
     * Remove the temporary vite.webpack.js file
     */
    protected function removeWebpackConfig(string $viteJsPath): void
    {
        $webpackJsPath = $this->getWebpackJsPath($viteJsPath);
        if (File::exists($webpackJsPath)) {
            File::delete($webpackJsPath);
        }
    }
}
