<?php namespace System\Console;

use File;
use Illuminate\Console\Command;
use System\Classes\MixAssets;

class MixList extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'mix:list';

    /**
     * @var string The console command description.
     */
    protected $description = 'List all registered Mix packages in this project.';

    public function handle(): int
    {
        $this->line('');

        $mixedAssets = MixAssets::instance();
        $mixedAssets->fireCallbacks();

        $packages = $mixedAssets->getPackages();

        if (count($packages) === 0) {
            $this->info('No packages have been registered.');
            return 0;
        }

        $this->info('Packages registered:');
        $this->line('');

        $errors = [];

        foreach ($packages as $name => $package) {
            $this->info($name);
            $this->line('  Path:           ' . $package['path']);
            $this->line('  Configuration:  ' . $package['mix']);

            if (!File::exists($package['mix'])) {
                $errors[] = "The mix file for $name doesn't exist, try running artisan mix:install";
            }
        }

        $this->line('');

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->error($error);
            }
        }

        return 0;
    }
}
