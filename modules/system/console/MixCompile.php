<?php namespace System\Console;

use File;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use System\Classes\MixAssets;

class MixCompile extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'mix:compile';

    /**
     * @var string The console command description.
     */
    protected $description = 'Mix and compile assets';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mix:compile
        {webpackArgs?* : Arguments to pass through to the Webpack CLI}
        {--f|production : Runs compilation in "production" mode}
        {--p|package=* : Defines one or more packages to compile}';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $mixedAssets = MixAssets::instance();
        $mixedAssets->fireCallbacks();

        $packages = $mixedAssets->getPackages();

        if (count($this->option('package')) && count($packages)) {
            foreach (array_keys($packages) as $name) {
                if (!in_array($name, $this->option('package'))) {
                    unset($packages[$name]);
                }
            }
        }

        if (!count($packages)) {
            if (count($this->option('package'))) {
                $this->error('No registered packages matched the requested packages for compilation.');
                return 1;
            } else {
                $this->info('No packages registered for mixing.');
                return 0;
            }
        }

        foreach ($packages as $name => $package) {
            $this->info(
                sprintf('Mixing package "%s"', $name)
            );
            if ($this->mixPackage($package) !== 0) {
                $this->error(
                    sprintf('Unable to compile package "%s"', $name)
                );
            }
        }

        return 0;
    }

    protected function mixPackage($package)
    {
        $this->createWebpackConfig($package['path'], $package['mix']);
        $command = $this->createCommand($package);

        $process = new Process(
            $command,
            $package['path'],
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

        $this->removeWebpackConfig($package['path']);

        return $exitCode;
    }

    protected function createCommand($package)
    {
        $command = $this->argument('webpackArgs') ?? [];
        array_unshift(
            $command,
            $package['path'] . implode(DIRECTORY_SEPARATOR, ['', 'node_modules', 'webpack', 'bin', 'webpack.js']),
            '--progress',
            '--config=' . $package['path'] . DIRECTORY_SEPARATOR . 'mix.webpack.js'
        );
        return $command;
    }

    protected function createWebpackConfig($path, $mixPath)
    {
        $fixture = File::get(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'mix.webpack.js.fixture');

        $config = str_replace(
            ['%base%', '%notificationInject%', '%mixConfigPath%'],
            [$path, '', $mixPath],
            $fixture
        );

        File::put($path . DIRECTORY_SEPARATOR . 'mix.webpack.js', $config);
    }

    protected function removeWebpackConfig($path)
    {
        if (File::exists($path . DIRECTORY_SEPARATOR . 'mix.webpack.js')) {
            File::delete($path . DIRECTORY_SEPARATOR . 'mix.webpack.js');
        }
    }
}
