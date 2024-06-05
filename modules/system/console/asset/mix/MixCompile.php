<?php

namespace System\Console\Asset\Mix;

use System\Console\Asset\AssetCompile;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Str;

class MixCompile extends AssetCompile
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
        {--s|silent : Silent mode}
        {--e|stop-on-error : Exit once an error is encountered}
        {--m|manifest= : Defines package.json to use for compile}
        {--p|package=* : Defines one or more packages to compile}
        {--no-progress : Do not show mix progress}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Mix and compile assets';

    protected string $configFile = 'mix.webpack.js';

    /**
     * @var array Local cache of the package.json file contents
     */
    protected array $packageJson;

    /**
     * Call the AssetCompile::compileHandle with the mix type
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->compileHandle('mix');
    }

    /**
     * Create the command array to create a Process object with
     */
    protected function createCommand(string $configPath): array
    {
        $basePath = base_path();
        $command = $this->argument('webpackArgs') ?? [];
        array_unshift(
            $command,
            $basePath . sprintf('%1$snode_modules%1$s.bin%1$swebpack', DIRECTORY_SEPARATOR),
            'build',
            $this->option('silent') ? '--stats=none' : '--progress',
            '--config=' . $this->getPackagePath($configPath)
        );

        return $command;
    }

    /**
     * Create the temporary mix.webpack.js config file to run webpack with
     */
    protected function beforeExecution(string $configPath): void
    {
        $basePath = base_path();
        $fixture = File::get(__DIR__ . '/../fixtures/mix.webpack.js.fixture');

        $config = Str::swap([
            '%base%' => addslashes($basePath),
            '%notificationInject%' => 'mix._api.disableNotifications();',
            '%mixConfigPath%' => addslashes($configPath),
            '%pluginsPath%' => addslashes(plugins_path()),
            '%appPath%' => addslashes(base_path()),
            '%silent%' => (int) $this->option('silent'),
            '%noProgress%' => (int) $this->option('no-progress')
        ], $fixture);

        File::put($this->getPackagePath($configPath), $config);
    }

    /**
     * Remove the temporary mix.webpack.js file
     */
    protected function afterExecution(string $configPath): void
    {
        $webpackJsPath = $this->getPackagePath($configPath);
        if (File::exists($webpackJsPath)) {
            File::delete($webpackJsPath);
        }
    }
}
