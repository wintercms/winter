<?php namespace System\Console;

use Winter\Storm\Console\Command;
use System\Classes\UpdateManager;

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
    use \Winter\Storm\Console\Traits\ConfirmsWithInput;

    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'winter:down';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'winter:down
        {--f|force : Force the operation to run and ignore production warnings and confirmation questionss.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Destroys all database tables for Winter and all plugins.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October & Laravel
        $this->setAliases(['october:down', 'migrate:reset']);
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->confirmWithInput(
            "This will completely delete all database tables in use with your Winter installation.",
            "DELETE"
        )) {
            return 1;
        }

        UpdateManager::instance()
            ->setNotesOutput($this->output)
            ->uninstall();

        return 0;
    }
}
