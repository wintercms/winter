<?php namespace System\Console;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
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
        $allPlugins  = PluginVersion::all();
        $pluginsCount = count($allPlugins);

        if ($pluginsCount <= 0) {
            $this->info('No plugin found');
            return;
        }

        $rows = [];
        foreach ($allPlugins as $plugin) {
            $rows[] = [
                $plugin->code,
                $plugin->version,
                (!$plugin->is_frozen) ? '<info>Yes</info>': '<fg=red>No</>',
                (!$plugin->is_disabled) ? '<info>Yes</info>': '<fg=red>No</>',
            ];
        }

        $this->table(['Plugin name', 'Version', 'Updates enabled', 'Plugin enabled'], $rows);
    }
}
