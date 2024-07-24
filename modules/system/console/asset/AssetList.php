<?php

namespace System\Console\Asset;

use System\Classes\Asset\PackageManager;
use Winter\Storm\Console\Command;
use Winter\Storm\Support\Facades\File;

abstract class AssetList extends Command
{
    protected string $assetType;

    public function handle(): int
    {
        $compilableAssets = PackageManager::instance();
        $compilableAssets->fireCallbacks();

        $packages = $compilableAssets->getPackages($this->assetType, true);

        if (count($packages) === 0) {
            $this->info('No packages have been registered.');
            return 0;
        }

        $errors = [];

        $rows = [];
        foreach ($packages as $name => $package) {
            $rows[] = [
                'name' => $name,
                'active' => !$package['ignored'],
                'path' => $package['path'],
                'configuration' => $package['config'],
            ];

            if (!File::exists($package['config'])) {
                $errors[] = "The config file for $name doesn't exist, try running artisan $this->assetType:install";
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->line('');
            $this->info('Packages registered:');
            $this->line('');
            $this->table(['Name', 'Active', 'Path', 'Configuration'], array_map(function ($row) {
                $row['active'] = ($row['active']) ? '<info>Yes</info>' : '<fg=red>No</>';
                return $row;
            }, $rows));
            $this->line('');
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->error($error);
            }
        }

        return 0;
    }
}
