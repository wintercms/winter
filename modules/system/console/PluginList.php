<?php namespace System\Console;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use System\Classes\PluginManager;
use System\Models\PluginVersion;
use Winter\Storm\Console\Command;

/**
 * Console command to list existing plugins.
 *
 * @package winter\wn-system-module
 * @author Lucas Zamora
 */
class PluginList extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'plugin:list';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'plugin:list';

    /**
     * @var string The console command description.
     */
    protected $description = 'List existing plugins.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $plugins = PluginManager::instance()->getAllPlugins();
        $pluginVersions = PluginVersion::all()->keyBy('code');

        if (count($plugins) <= 0) {
            $this->info('No plugin found');
            return;
        }

        $rows = [];
        foreach ($plugins as $plugin) {
            $rows[] = [
                $plugin->getPluginIdentifier(),
                $plugin->package,
                $plugin->getPluginVersion(),
                (!$pluginVersions[$plugin->getPluginIdentifier()]->is_frozen) ? '<info>Yes</info>': '<fg=red>No</>',
                (!$pluginVersions[$plugin->getPluginIdentifier()]->is_disabled) ? '<info>Yes</info>': '<fg=red>No</>',
            ];
        }

        $this->table(['Plugin name', 'Composer Package', 'Version', 'Updates enabled', 'Plugin enabled'], $rows);
    }
}
