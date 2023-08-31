<?php namespace System\Console;

use Winter\Storm\Console\Command;
use System\Classes\UpdateManager;
use System\Classes\PluginManager;

/**
 * Console command to install a new plugin.
 *
 * This adds a new plugin by requesting it from the Winter marketplace.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginInstall extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'plugin:install';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'plugin:install
        {plugin : The plugin to install. <info>(eg: Winter.Blog)</info>}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Install a plugin from the Winter marketplace.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $pluginName = $this->argument('plugin');
        $manager = UpdateManager::instance()->setNotesOutput($this->output);

        $pluginDetails = $manager->requestPluginDetails($pluginName);

        $code = array_get($pluginDetails, 'code');
        $hash = array_get($pluginDetails, 'hash');

        $this->output->writeln(sprintf('<info>Downloading plugin: %s</info>', $code));
        $manager->downloadPlugin($code, $hash, true);

        $this->output->writeln(sprintf('<info>Unpacking plugin: %s</info>', $code));
        $manager->extractPlugin($code, $hash);

        /*
         * Make sure plugin is registered
         */
        $pluginManager = PluginManager::instance();
        $pluginManager->loadPlugins();
        $plugin = $pluginManager->findByIdentifier($code);
        $pluginManager->registerPlugin($plugin, $code);

        /*
         * Migrate plugin
         */
        $this->output->writeln(sprintf('<info>Migrating plugin...</info>', $code));
        $manager->updatePlugin($code);
    }
}
