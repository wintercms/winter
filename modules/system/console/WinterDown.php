<?php namespace System\Console;

use App;
use Illuminate\Console\Command;
use System\Classes\UpdateManager;
use Symfony\Component\Console\Input\InputOption;

/**
 * Console command to tear down the database.
 *
 * This destroys all database tables that are registered for Winter and all plugins.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class WinterDown extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * The console command name.
     */
    protected $name = 'winter:down';

    /**
     * The console command description.
     */
    protected $description = 'Destroys all database tables for Winter and all plugins.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October
        $this->setAliases(['october:down']);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (App::isProduction() && !$this->option('force')) {
            $this->warn('YOUR APPLICATION IS IN PRODUCTION');
        }

        $this->warn('This will completely delete all database tables in use with your Winter installation.');

        $confirmed = false;
        $prompt = 'Please type "DELETE" to proceed';
        do {
            if (strtolower($this->ask($prompt)) === 'delete') {
                $confirmed = true;
            }
        } while ($confirmed === false);

        UpdateManager::instance()
            ->setNotesOutput($this->output)
            ->uninstall()
        ;
    }

    /**
     * Get the console command options.
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run and ignore production warning.'],
        ];
    }

    /**
     * Get the default confirmation callback.
     * @return \Closure
     */
    protected function getDefaultConfirmCallback()
    {
        return function () {
            return true;
        };
    }
}
