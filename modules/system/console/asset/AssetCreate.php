<?php

namespace System\Console\Asset;

use Closure;
use Cms\Classes\Theme;
use Symfony\Component\Console\Input\InputOption;
use System\Classes\Asset\BundleManager;
use System\Classes\Asset\PackageJson;
use System\Classes\Asset\PackageManager;
use System\Classes\PluginManager;
use Winter\Storm\Console\Command;
use Winter\Storm\Support\Facades\File;

abstract class AssetCreate extends Command
{
    protected const TYPE_MODULE = 'module';
    protected const TYPE_PLUGIN = 'plugin';
    protected const TYPE_THEME = 'theme';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates the compiler configuration files for the provided package and optionally installs the necessary dependencies for any selected asset bundles.';

    /**
     * Local cache of fixture path
     */
    private string $fixturePath;

    /**
     * The type of compilable to configure
     */
    protected string $assetType;

    /**
     * The name of the config file
     */
    protected string $configFile;

    /**
     * Dynamically generate options for all available bundles
     */
    public function __construct()
    {
        parent::__construct();

        foreach (BundleManager::instance()->getBundles() as $bundle) {
            $this->addOption($bundle, null, InputOption::VALUE_NONE, 'Create ' . $bundle . ' configuration');
        }
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $package = $this->argument('packageName');

        $this->fixturePath = __DIR__ . '/fixtures/config';

        $compilableAssets = PackageManager::instance();
        $compilableAssets->fireCallbacks();

        $packages = $compilableAssets->getPackages($this->assetType, true);

        if (
            // We have the package already
            isset($packages[$package])
            // If the user has requested `force` & `no-interaction` then we do not ask for confirmation and continue
            && !($this->option('force') && $this->option('no-interaction'))
            // If the user has forced but with interaction, then we do not ask for confirmation, else we do
            && !($this->option('force') || $this->confirm('Package `' . $package . '` has already been configured, are you sure you wish to continue?'))
        ) {
            return 0;
        }

        [$path, $type] = $this->getPackagePathType($package);

        if (is_null($path) || is_null($type)) {
            $this->error('Package `' . $package . '` could not be resolved');
            return 1;
        }

        $packageJson = new PackageJson($path . '/package.json');
        if (!$packageJson->getName()) {
            $packageJson->setName(strtolower(str_replace('.', '-', $package)));
        }

        $this->installConfigs($packageJson, $package, $type, $path);

        $verb = File::exists($packageJson->getPath()) ? 'updated' : 'generated';
        $packageJson->save();

        if (!$this->option('silent')) {
            $this->warn("File $verb: " . str_after($packageJson->getPath(), base_path()));
            $this->info(ucfirst($this->assetType) . ' configuration complete.');
        }

        $this->afterExecution();

        return 0;
    }

    /**
     * Resolve the path and type of the package by name
     */
    protected function getPackagePathType(string $package): array
    {
        if (str_starts_with($package, 'theme-')) {
            if (($theme = Theme::load(str_after($package, 'theme-'))) && File::exists($theme->getPath())) {
                return [$theme->getPath(), static::TYPE_THEME];
            }

            return [null, null];
        }

        if (str_starts_with($package, 'module-')) {
            if (
                ($modulePath = base_path('modules') . '/' . str_after($package, 'module-'))
                && File::exists($modulePath)
            ) {
                return [$modulePath, static::TYPE_MODULE];
            }

            return [null, null];
        }

        if ($plugin = PluginManager::instance()->findByIdentifier($package)) {
            return [$plugin->getPluginPath(), static::TYPE_PLUGIN];
        }

        return [null, null];
    }

    /**
     * Write out config files based on assetType and the requested options
     */
    protected function installConfigs(
        PackageJson $packageJson,
        string $packageName,
        string $packageType,
        string $packagePath
    ): void {
        // Normalize package name
        $packageName = $this->makePackageName($packageName);
        // Bind the bundleManager instance
        $bundleManager = BundleManager::instance();

        // Get the default config
        $config = $this->getFixture(
            $this->assetType . '/' . pathinfo($this->configFile, PATHINFO_BASENAME) . '.fixture'
        );

        // For each bundle offered by node packages
        foreach ($bundleManager->getBundles() as $bundle) {
            // If the bundle was not selected exit
            if (!$this->option($bundle)) {
                continue;
            }

            // Require all packages specified by the bundle
            foreach ($bundleManager->getBundlePackages($bundle, $this->assetType) as $dependency => $version) {
                $packageJson->addDependency($dependency, $version, dev: true);
            }

            // Fire any setup handlers required
            $setupHandler = $bundleManager->getSetupHandler($bundle);
            if ($setupHandler) {
                Closure::bind($setupHandler, $this)->call($this, $packagePath, $packageType);
            }

            // Loop through all the scaffold handlers to build configs / stubs
            $scaffoldHandler = $bundleManager->getScaffoldHandler($bundle);
            if ($scaffoldHandler) {
                // Generate the config
                $config = Closure::bind($scaffoldHandler, $this)->call($this, $config, $this->assetType);
                // Generate stub files if required
                if (!$this->option('no-stubs')) {
                    $css = Closure::bind($scaffoldHandler, $this)->call($this, $css ?? '', 'css');
                    $js = Closure::bind($scaffoldHandler, $this)->call($this, $js ?? '', 'js');
                }
            }
        }

        // Create stub files if required
        if (!$this->option('no-stubs')) {
            foreach (['css', 'js'] as $asset) {
                // Create asset dist dir so laravel-vite-plugin doesn't complain
                if (!File::exists($packagePath . '/assets/dist')) {
                    File::makeDirectory($packagePath . '/assets/dist/', recursive: true);
                }
                // Create asset src dirs for stubs
                if (!File::exists($packagePath . '/assets/src/' . $asset)) {
                    File::makeDirectory($packagePath . '/assets/src/' . $asset, recursive: true);
                }
                $this->writeFile(
                    sprintf('%1$s/assets/src/%2$s/%3$s.%2$s', $packagePath, $asset, $packageName),
                    $$asset ?? null ? $$asset : $this->getFixture(sprintf('%1$s/default.%1$s.fixture', $asset))
                );
            }
        }

        // Write out the config file
        $this->writeFile($packagePath . '/' . $this->configFile, str_replace(
            '{{packageName}}',
            $packageName,
            $config
        ));
    }

    /**
     * Write a file but ask for conformation before overwriting
     */
    protected function writeFile(string $path, string $content): int
    {
        if (
            // If forced, ignore file existing and overwrite
            (!$this->option('force') && File::exists($path))
            && (
                // If no interaction requested, then skip the confirm check and return
                $this->option('no-interaction')
                // else ask the user if they want overwriting
                || !$this->confirm(sprintf('%s already exists, overwrite?', basename($path)))
            )
        ) {
            return 0;
        }

        File::ensureDirectoryExists(pathinfo($path, PATHINFO_DIRNAME));
        $result = File::put($path, $content);

        if (!$this->option('silent')) {
            $this->warn('File generated: ' . str_after($path, base_path()));
        }

        return $result;
    }

    /**
     * Helper method for loading fixtures from the default library
     */
    protected function getFixture(string $path): string
    {
        return File::get($this->fixturePath . '/' . $path);
    }

    /**
     * Converts the user supplied package name into a consistent internal format
     */
    protected function makePackageName(string $package): string
    {
        return strtolower(str_replace('.', '-', $package));
    }

    /**
     * Ran after configuration is complete, use for tearing down / reporting
     */
    protected function afterExecution(): void
    {
        // do nothing
    }
}
