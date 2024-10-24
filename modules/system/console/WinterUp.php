<?php namespace System\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use System\Classes\UpdateManager;

/**
 * Console command to migrate the database.
 *
 * This builds up all database tables that are registered for Winter and all plugins.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class WinterUp extends Command implements Isolatable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winter:up
                            {--seed : Included for compatibility with Laravel default signature, no effect at this time}';

    /**
     * The console command description.
     */
    protected $description = 'Builds database tables for Winter and all plugins.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October
        $this->setAliases(['october:up', 'migrate']);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->output->writeln('<info>Migrating application and plugins...</info>');

        UpdateManager::instance()
            ->setNotesOutput($this->output)
            ->update();
    }
}
