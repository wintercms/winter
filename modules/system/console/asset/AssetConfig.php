<?php

namespace System\Console\Asset;

use Cms\Classes\Theme;
use Symfony\Component\Console\Input\InputOption;
use System\Classes\CompilableAssets;
use System\Classes\NodePackages;
use System\Classes\NodePackageVersions;
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
     * @var array|array[] List of meta packages
     */
    protected array $packages = [
        'generic' => [
            'tailwind' => [
                'tailwindcss',
                '@tailwindcss/forms',
                '@tailwindcss/typography',
            ],
            'vue' => [
                'vue',
            ]
        ],
        'vite' => [
            'vue' => [
                '@vitejs/plugin-vue'
            ]
        ]
    ];

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

        $this->installConfigs($packageJson, $package, $type, $path, [
            'tailwind' => $this->option('tailwind'),
            'vue' => $this->option('vue'),
            'stubs' => $this->option('stubs')
        ]);

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
        string $package,
        string $type,
        string $path,
        array $options
    ): void {
        // Bind the nodePackages instance
        $nodePackages = NodePackages::instance();

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

            //
            foreach ($nodePackages->getHandlers($bundle) as $bootstrapper) {
                \Closure::bind($bootstrapper, $this)->call($this, $path, $type);
            }
        }

        $packageName = strtolower(str_replace('.', '-', $package));

        // TODO: migrate this to some sort of stream manager with callables modifying content
        if ($options['stubs']) {
            // Setup css stubs
            if (!File::exists($path . '/assets/src/css')) {
                File::makeDirectory($path . '/assets/src/css', recursive: true);
            }

            $this->writeFile(
                $path . '/assets/src/css/' . $packageName . '.css',
                File::get($this->fixturePath . '/css/' . ($options['tailwind'] ? 'tailwind' : 'default') . '.css.fixture')
            );

            // Setup js stubs
            if (!File::exists($path . '/assets/src/js')) {
                File::makeDirectory($path . '/assets/src/js', recursive: true);
            }

            $this->writeFile(
                $path . '/assets/src/js/' . $packageName . '.js',
                File::get($this->fixturePath . '/js/' . ($options['vue'] ? 'vue' : 'default') . '.js.fixture')
            );
        }

        $config = str_replace(
            '{{packageName}}',
            $packageName,
            File::get(
                sprintf(
                    '%s/%s/%s%s%s.js.fixture',
                    $this->fixturePath,
                    $this->assetType,
                    pathinfo($this->configFile, PATHINFO_FILENAME),
                    $options['tailwind'] ? '.tailwind' : '',
                    $options['vue'] ? '.vue' : '',
                )
            )
        );

        $this->writeFile($path . '/' . $this->configFile, $config);
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
