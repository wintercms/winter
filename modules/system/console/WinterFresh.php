<?php namespace System\Console;

use File;
use Artisan;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Console command to remove boilerplate.
 *
 * This removes the demo theme and plugin. A great way to start a fresh project!
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class WinterFresh extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * The console command name.
     */
    protected $name = 'winter:fresh';

    /**
     * The console command description.
     */
    protected $description = 'Removes the demo theme and plugin.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October
        $this->setAliases(['october:fresh']);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirmToProceed('Are you sure?')) {
            return;
        }

        $themeRemoved = false;
        $pluginRemoved = false;

        $demoThemePath = themes_path().'/demo';
        if (File::exists($demoThemePath)) {
            File::deleteDirectory($demoThemePath);
            $themeRemoved = true;
        }

        $demoPluginPath = plugins_path().'/winter/demo';
        if (File::exists($demoPluginPath)) {
            Artisan::call('plugin:remove', ['plugin' => 'Winter.Demo', '--force' => true]);
            $pluginRemoved = true;
        }

        if ($themeRemoved && $pluginRemoved) {
            $this->info('Demo theme and plugin have been removed! Enjoy a fresh start.');
        } elseif ($themeRemoved) {
            $this->info('Demo theme has been removed! Enjoy a fresh start.');
        } elseif ($pluginRemoved) {
            $this->info('Demo plugin has been removed! Enjoy a fresh start.');
        } else {
            $this->info('Demo theme and plugin have already been removed.');
        }
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run.'],
        ];
    }
}
