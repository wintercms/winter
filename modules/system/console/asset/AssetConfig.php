<?php

namespace System\Console\Asset;

use Cms\Classes\Theme;
use Symfony\Component\Console\Input\InputOption;
use System\Classes\CompilableAssets;
use System\Classes\NodePackages;
use System\Classes\PackageJson;
use System\Classes\PluginManager;
use Winter\Storm\Console\Command;
use Winter\Storm\Support\Facades\File;

abstract class AssetConfig extends Command
{
    protected const TYPE_THEME = 'theme';
    protected const TYPE_PLUGIN = 'plugin';

    /**
     * @var string The console command description.
     */
    protected $description = 'Create configuration.';

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

        foreach (NodePackages::instance()->getBundles() as $bundle) {
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

        $compilableAssets = CompilableAssets::instance();
        $compilableAssets->fireCallbacks();

        $packages = $compilableAssets->getPackages($this->assetType, true);

        if (
            isset($packages[$package])
            && !$this->confirm('Package `' . $package . '` has already been configured, are you sure you wish to continue?')
        ) {
            return 1;
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

        $packageJson->save();

        $this->warn('File generated: ' . str_after($packageJson->getPath(), base_path()));
        $this->info(ucfirst($this->assetType) . ' configuration complete.');

        return 0;
    }

    /**
     * Resolve the path and type of the package by name
     *
     * @return array|null[]
     */
    protected function getPackagePathType(string $package): array
    {
        if (str_starts_with($package, 'theme-')) {
            if ($theme = Theme::load(str_after($package, 'theme-'))) {
                return [$theme->getPath(), static::TYPE_THEME];
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
        $packageName = strtolower(str_replace('.', '-', $packageName));
        // Bind the nodePackages instance
        $nodePackages = NodePackages::instance();

        // Get the default config
        $config = $this->getFixture(
            $this->assetType . '/' . pathinfo($this->configFile, PATHINFO_BASENAME) . '.fixture'
        );

        // For each bundle offered by node packages
        foreach ($nodePackages->getBundles() as $bundle) {
            // If the bundle was not selected exit
            if (!$this->option($bundle)) {
                continue;
            }

            // Require all packages specified by the bundle
            foreach ($nodePackages->getBundlePackages($bundle, $this->assetType) as $dependency => $version) {
                $packageJson->addDependency($dependency, $version, dev: true);
            }

            // Fire any setup handlers required
            foreach ($nodePackages->getSetupHandlers($bundle) as $setupHandler) {
                \Closure::bind($setupHandler, $this)->call($this, $packagePath, $packageType);
            }

            // Loop through all the scaffold handlers to build configs / stubs
            foreach ($nodePackages->getScaffoldHandlers($bundle) as $scaffoldHandler) {
                // Generate the config
                $config = \Closure::bind($scaffoldHandler, $this)->call($this, $config, $this->assetType);
                // Generate stub files if required
                if ($this->option('stubs')) {
                    $css = \Closure::bind($scaffoldHandler, $this)->call($this, $css ?? '', 'css');
                    $js = \Closure::bind($scaffoldHandler, $this)->call($this, $js ?? '', 'js');
                }
            }
        }

        // Create stub files if required
        if ($this->option('stubs')) {
            foreach (['css', 'js'] as $asset) {
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
        if (File::exists($path) && !$this->confirm(sprintf('%s already exists, overwrite?', basename($path)))) {
            return 0;
        }

        $result = File::put($path, $content);

        $this->warn('File generated: ' . str_after($path, base_path()));

        return $result;
    }

    protected function getFixture(string $path): string
    {
        return File::get($this->fixturePath . '/' . $path);
    }
}
