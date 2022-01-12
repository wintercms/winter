<?php namespace System\Console;

use File;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use System\Classes\MixAssets;

class MixList extends MixCompile
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

        foreach ($packages as $name => $package) {
            $this->info($name);
            $this->line('  Path:           ' . $package['path']);
            $this->line('  Configuration:  ' . $package['mix']);
        }

        $this->line('');
        return 0;
    }
}
