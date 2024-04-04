<?php namespace System\Console;

use File;
use Winter\Storm\Console\Command;
use System\Classes\MixAssets;

class MixList extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:list';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:list
        {--json : Output as JSON}';

    /**
     * @var string The console command description.
     */
    protected $description = 'List all registered Mix packages in this project.';

    public function handle(): int
    {
        $mixedAssets = MixAssets::instance();
        $mixedAssets->fireCallbacks();

        $packages = $mixedAssets->getPackages(true);

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
                'configuration' => $package['mix'],
            ];

            if (!File::exists($package['mix'])) {
                $errors[] = "The mix file for $name doesn't exist, try running artisan mix:install";
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
