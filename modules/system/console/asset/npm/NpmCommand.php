<?php

namespace System\Console\Asset\Npm;

use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;
use System\Classes\Asset\PackageJson;
use System\Classes\Asset\PackageManager;
use Winter\Storm\Console\Command;
use Winter\Storm\Exception\SystemException;

abstract class NpmCommand extends Command
{
    /**
     * Gets a package config and its PackageJson file based on the `package` argument.
     * @throws SystemException
     * @throws \JsonException
     */
    protected function getPackage(): ?array
    {
        $compilableAssets = PackageManager::instance();
        $compilableAssets->fireCallbacks();

        $name = $this->argument('package');

        if (!$name) {
            return null;
        }

        if (!$compilableAssets->hasPackage($name, true)) {
            throw new SystemException(sprintf('Package "%s" is not a registered package.', $name));
        }

        $package = $compilableAssets->getPackage($name, true)[0] ?? [];

        // Assume that packages with matching names have matching package.json files
        $packageJson = new PackageJson($package['package'] ?? null);

        return [$package, $packageJson];
    }

    /**
     * Starts a npm process with the command and cwd provided
     */
    protected function npmRun(array $command, string $path): int
    {
        $process = new Process(
            $command,
            base_path($path),
            ['NODE_ENV' => $this->getNodeEnv()],
            null,
            null
        );

        if (!$this->option('disable-tty') && !$this->option('silent')) {
            try {
                $process->setTty(true);
            } catch (ProcessSignaledException $e) {
                if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                    throw $e;
                }

                return 1;
            } catch (\Throwable $e) {
                // This will fail on unsupported systems
            }
        }

        return $process->run(function ($status, $stdout) {
            if (!$this->option('silent')) {
                $this->getOutput()->write($stdout);
            }
        });
    }

    /**
     * Get the env env to provide to node
     */
    protected function getNodeEnv(): string
    {
        if (!$this->hasOption('production')) {
            return 'development';
        }

        return $this->option('production', false) ? 'production' : 'development';
    }
}
