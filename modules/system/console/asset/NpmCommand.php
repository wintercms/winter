<?php

namespace System\Console\Asset;

use Symfony\Component\Process\Process;
use System\Classes\Asset\PackageJson;
use System\Classes\Asset\PackageManager;
use System\Console\Asset\Exceptions\PackageNotRegisteredException;
use Winter\Storm\Console\Command;

abstract class NpmCommand extends Command
{
    protected function getPackage(): ?array
    {
        $compilableAssets = PackageManager::instance();
        $compilableAssets->fireCallbacks();

        $name = $this->argument('package');

        if (!$name) {
            return null;
        }

        if (!$compilableAssets->hasPackage($name, true)) {
            throw new PackageNotRegisteredException(sprintf('Package "%s" is not a registered package.', $name));
        }

        $package = $compilableAssets->getPackage($name, true)[0] ?? [];

        // Assume that packages with matching names have matching package.json files
        $packageJson = new PackageJson($package['package'] ?? null);

        return [$package, $packageJson];
    }

    protected function npmRun(array $command, string $path): int
    {
        $process = new Process(
            $command,
            base_path($path),
            ['NODE_ENV' => $this->getNodeEnv()],
            null,
            null
        );

        try {
            $process->setTty(true);
        } catch (\Throwable $e) {
            // This will fail on unsupported systems
        }

        return $process->run(function ($status, $stdout) {
            if (!$this->option('silent')) {
                $this->getOutput()->write($stdout);
            }
        });
    }

    protected function getNodeEnv(): string
    {
        if (!$this->hasOption('production')) {
            return 'development';
        }

        return $this->option('production', false) ? 'production' : 'development';
    }
}
