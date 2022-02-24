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
    use Traits\HasPluginArgument;

    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'plugin:refresh';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'plugin:refresh
        {plugin : The plugin to refresh. <info>(eg: Winter.Blog)</info>}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Removes and re-adds an existing plugin.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $pluginName = $this->getPluginIdentifier();

        // @TODO: Implement confirmation logic here.
        // @TODO: Replace Laravel confirmation logic with the type x to proceed instead of yes/no for very destructive actions

        // Set the UpdateManager output stream to the CLI
        $manager = UpdateManager::instance()->setNotesOutput($this->output);

        // Rollback the plugin
        $manager->rollbackPlugin($pluginName);

        // Reinstall the plugin
        $this->output->writeln('<info>Reinstalling plugin...</info>');
        $manager->updatePlugin($pluginName);
    }
}
