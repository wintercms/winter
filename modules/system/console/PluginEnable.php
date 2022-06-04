<?php namespace System\Console;

use Winter\Storm\Console\Command;
use System\Classes\PluginManager;
use System\Models\PluginVersion;

/**
 * Console command to enable a plugin.
 *
 * @package winter\wn-system-module
 * @author Lucas Zamora
 */
class PluginEnable extends Command
{
    use Traits\HasPluginArgument;

    /**
     * @var string Only suggest plugins that are disabled
     */
    protected $hasPluginsFilter = 'disabled';

    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'plugin:enable';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'plugin:enable
        {plugin : The plugin to disable. <info>(eg: Winter.Blog)</info>}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Enable an existing plugin.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $pluginName = $this->getPluginIdentifier();
        $pluginManager = PluginManager::instance();

        // Enable this plugin
        $pluginManager->enablePlugin($pluginName);

        $this->output->writeln(sprintf('<info>%s:</info> enabled.', $pluginName));
    }
}
