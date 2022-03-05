<?php namespace System\Console;

use Winter\Storm\Console\Command;
use System\Models\PluginVersion;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

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

        // Create a new Table instance.
        $table = new Table($this->output);

        // Set the table headers.
        $table->setHeaders([
            'Plugin name', 'Version', 'Updates enabled', 'Plugin enabled'
        ]);

        // Create a new TableSeparator instance.
        $separator = new TableSeparator;

        $pluginTable = [];

        $row = 0;
        foreach ($allPlugins as $plugin) {
            $row++;

            $pluginTable[] = [$plugin->code, $plugin->version, (!$plugin->is_frozen) ? 'Yes': 'No', (!$plugin->is_disabled) ? 'Yes': 'No'];

            if ($row < $pluginsCount) {
                $pluginTable[] = $separator;
            }
        }

        // Set the contents of the table.
        $table->setRows($pluginTable);

        // Render the table to the output.
        $table->render();
    }
}
