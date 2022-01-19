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

        $this->info(
            sprintf('Watching package "%s" for changes', $name)
        );
        if ($this->mixPackage($package) !== 0) {
            $this->error(
                sprintf('Unable to compile package "%s"', $name)
            );
            return 1;
        }

        return 0;
    }

    protected function createCommand($package)
    {
        $command = parent::createCommand($package);
        $command[] = '--watch';

        return $command;
    }

    protected function createWebpackConfig($path, $mixPath)
    {
        $fixture = File::get(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'mix.webpack.js.fixture');

        $config = str_replace(
            ['%base%', '%notificationInject%', '%mixConfigPath%'],
            [$path, 'mix._api.disableNotifications();', $mixPath],
            $fixture
        );

        File::put($path . DIRECTORY_SEPARATOR . 'mix.webpack.js', $config);
    }
}
