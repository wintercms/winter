<?php

namespace System\Console\Plugin;

use System\Classes\Extensions\PluginManager;
use System\Console\Traits;
use Winter\Storm\Console\Command;

/**
 * Console command to disable a plugin.
 *
 * @package winter\wn-system-module
 * @author Lucas Zamora
 */
class PluginDisable extends Command
{
    use Traits\HasPluginArgument;

    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'plugin:disable';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'plugin:disable
        {plugin : The plugin to disable. <info>(eg: Winter.Blog)</info>}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Disable an existing plugin.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $pluginName = $this->getPluginIdentifier();

        // Disable this plugin
        PluginManager::instance()->disable($pluginName);

        $this->output->info($pluginName . ': disabled.');
    }
}
