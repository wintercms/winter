<?php namespace System\Console;

use Winter\Storm\Console\Command;
use System\Classes\UpdateManager;

/**
 * Console command to refresh a plugin.
 *
 * This destroys all database tables for a specific plugin, then builds them up again.
 * It is a great way for developers to debug and develop new plugins.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginRefresh extends Command
{
    use \Winter\Storm\Console\Traits\ConfirmsWithInput;
    use Traits\HasPluginArgument;

    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'plugin:refresh';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'plugin:refresh
        {plugin : The plugin to refresh. <info>(eg: Winter.Blog)</info>}
        {--f|force : Force the operation to run and ignore production warnings and confirmation questions.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Removes and re-adds an existing plugin.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pluginName = $this->getPluginIdentifier();

        if (!$this->confirmWithInput(
            "This will completely remove and reinstall $pluginName. This may result in potential data loss.",
            $pluginName
        )) {
            return 1;
        }

        // Set the UpdateManager output stream to the CLI
        $manager = UpdateManager::instance()->setNotesOutput($this->output);

        // Rollback the plugin
        $manager->rollbackPlugin($pluginName);

        // Reinstall the plugin
        $this->output->writeln('<info>Reinstalling plugin...</info>');
        $manager->updatePlugin($pluginName);

        return 0;
    }
}
