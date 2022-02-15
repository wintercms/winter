<?php namespace System\Console;

use File;
use System\Classes\MixAssets;

class MixWatch extends MixCompile
{
    /**
     * @var string The console command name.
     */
    protected $name = 'mix:watch';

    /**
     * @var string The console command description.
     */
    protected $description = 'Mix and compile assets on-the-fly as changes are made.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mix:watch
        {package : Defines the package to watch for changes}
        {webpackArgs?* : Arguments to pass through to the Webpack CLI}
        {--f|production : Runs compilation in "production" mode}';

    public function handle(): int
    {
        $mixedAssets = MixAssets::instance();
        $mixedAssets->fireCallbacks();

        $packages = $mixedAssets->getPackages();
        $name = $this->argument('package');

        if (!in_array($name, array_keys($packages))) {
            $this->error(
                sprintf('Package "%s" is not a registered package.', $name)
            );
            return 1;
        }

        $package = $packages[$name];

        $relativeMixJsPath = $package['mix'];
        if (!$this->canCompilePackage($relativeMixJsPath)) {
            $this->error(
                sprintf('Unable to watch "%s", %s was not found in the package.json\'s workspaces.packages property. Try running mix:install first.', $name, $packagePath)
            );
            return 1;
        }

        $this->info(
            sprintf('Watching package "%s" for changes', $name)
        );
        if ($this->mixPackage(base_path($relativeMixJsPath)) !== 0) {
            $this->error(
                sprintf('Unable to compile package "%s"', $name)
            );
            return 1;
        }

        return 0;
    }

    /**
     * Create the command array to create a Process object with
     */
    protected function createCommand(string $mixJsPath): array
    {
        $command = parent::createCommand($mixJsPath);

        // @TODO: Detect Homestead running on Windows to switch to watch-poll-options instead, see https://laravel-mix.com/docs/6.0/cli#polling
        $command[] = '--watch';

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
            [$basePath, 'mix._api.disableNotifications();', $mixJsPath, plugins_path(), base_path()],
            $fixture
        );

        File::put($this->getWebpackJsPath($mixJsPath), $config);
    }
}
