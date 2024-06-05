<?php

namespace System\Console\Asset;

use Cms\Classes\Theme;
use System\Classes\CompilableAssets;
use System\Classes\PackageJson;
use System\Classes\PluginManager;
use Winter\Storm\Console\Command;
use Winter\Storm\Support\Facades\File;

abstract class AssetConfig extends Command
{
    protected const TYPE_THEME = 'theme';
    protected const TYPE_PLUGIN = 'plugin';

    protected const DEFAULT_VERSION_TAILWIND = '^3.4.0';
    protected const DEFAULT_VERSION_VUE = '^3.4.0';

    /**
     * @var string The console command description.
     */
    protected $description = 'Create configuration.';

    private string $configPath;

    protected string $assetType;

    public function handle(): int
    {
        $package = $this->argument('packageName');

        $this->configPath = __DIR__ . '/fixtures/config';

        $compilableAssets = CompilableAssets::instance();
        $compilableAssets->fireCallbacks();

        $packages = $compilableAssets->getPackages(true);

        if (isset($packages[$package])) {
            $this->warn('Package `' . $package . '` has already been configured');
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

        switch ($this->assetType) {
            case 'mix':
                $this->installMix($packageJson, $package, $type, $path, [
                    'tailwind' => $this->option('tailwind'),
                    'vue' => $this->option('vue')
                ]);
                break;
            case 'vite':
                $this->installVite($packageJson, $package, $type, $path, [
                    'tailwind' => $this->option('tailwind'),
                    'vue' => $this->option('vue')
                ]);
                break;
        }

        $packageJson->save();

        return 0;
    }

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

    protected function installMix(
        PackageJson $packageJson,
        string $package,
        string $type,
        string $path,
        array $options
    ): void {
        if ($options['tailwind']) {
            $this->writeFile(
                $path . '/tailwind.config.js',
                File::get($this->configPath . '/' . $type . '.tailwind.config.js.fixture')
            );

            $packageJson->addDependency('tailwindcss', static::DEFAULT_VERSION_TAILWIND, dev: true);
        }

        if ($options['vue']) {
            $packageJson->addDependency('vue', static::DEFAULT_VERSION_VUE, dev: true);
        }

        $config = str_replace(
            '{{packageName}}',
            strtolower(str_replace('.', '-', $package)),
            File::get(
                sprintf(
                    '%s/mix/winter.mix%s%s.js.fixture',
                    $this->configPath,
                    $options['tailwind'] ? '.tailwind' : '',
                    $options['vue'] ? '.vue' : '',
                )
            )
        );

        $this->writeFile($path . '/winter.mix.js', $config);
    }

    protected function installVite(
        PackageJson $packageJson,
        string $package,
        string $type,
        string $path,
        array $options
    ): void {
        if ($options['tailwind']) {
            $this->writeFile(
                $path . '/tailwind.config.js',
                File::get($this->configPath . '/' . $type . '.tailwind.config.js.fixture')
            );

            $packageJson->addDependency('tailwindcss', static::DEFAULT_VERSION_TAILWIND, dev: true);
        }

        if ($options['vue']) {
            $packageJson->addDependency('vue', static::DEFAULT_VERSION_VUE, dev: true);
        }

        $config = str_replace(
            '{{packageName}}',
            strtolower(str_replace('.', '-', $package)),
            File::get(
                sprintf(
                    '%s/vite/vite.config%s%s.js.fixture',
                    $this->configPath,
                    $options['tailwind'] ? '.tailwind' : '',
                    $options['vue'] ? '.vue' : '',
                )
            )
        );

        $this->writeFile($path . '/vite.config.js', $config);
    }

    protected function writeFile(string $path, string $content): int
    {
        if (File::exists($path) && !$this->confirm(sprintf('%s already exists, overwrite?', basename($path)))) {
            return 0;
        }

        return File::put($path, $content);
    }
}
