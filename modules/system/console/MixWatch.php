<?php namespace System\Console;

use File;
use System\Classes\MixAssets;

class MixWatch extends MixCompile
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:watch';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:watch
        {package : Defines the package to watch for changes}
        {webpackArgs?* : Arguments to pass through to the Webpack CLI}
        {--f|production : Runs compilation in "production" mode}
        {--m|manifest= : Defines package.json to use for compile}
        {--s|silent : Silent mode}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Mix and compile assets on-the-fly as changes are made.';

    /**
     * @var string The path currently being watched
     */
    protected $mixJsPath;

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
                sprintf('Unable to watch "%s", %s was not found in the package.json\'s workspaces.packages property. Try running mix:install first.', $name, $relativeMixJsPath)
            );
            return 1;
        }

        $this->info(
            sprintf('Watching package "%s" for changes', $name)
        );
        $this->mixJsPath = $relativeMixJsPath;

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
            ['%base%', '%notificationInject%', '%mixConfigPath%', '%pluginsPath%', '%appPath%', '%silent%'],
            [addslashes($basePath), 'mix._api.disableNotifications();', addslashes($mixJsPath), addslashes(plugins_path()), addslashes(base_path()), (int) $this->option('silent')],
            $fixture
        );

        File::put($this->getWebpackJsPath($mixJsPath), $config);
    }

    /**
     * Handle the cleanup of this command if a termination signal is received
     */
    public function handleCleanup(): void
    {
        $this->newLine();
        $this->info('Cleaning up: ' . $this->getWebpackJsPath(base_path($this->mixJsPath)));
        $this->removeWebpackConfig(base_path($this->mixJsPath));
    }
}
