<?php namespace System\Console;

use Winter\Storm\Console\Command;
use System\Classes\UpdateManager;
use System\Classes\PluginManager;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Str;

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
     *
     *
     * Tests:
     *  plugin.zip - doesn't exist
     *  plugin.zip - does exist
     */
    public function handle(): int
    {
        $pluginName = $this->argument('plugin');
        $manager = UpdateManager::instance()->setNotesOutput($this->output);

        if (Str::endsWith($pluginName, '.zip')) {
            $packageZip = base_path($pluginName);
            $tempPath = temp_path('packages/' . md5($packageZip));

            // @TODO: Testing only
            File::copy(base_path('plugin-test.zip'), $packageZip);

            // Check if the file exists
            if (!File::exists($packageZip)) {
                $this->output->writeln(sprintf('<error>File not found: %s</error>', $pluginName));
                return 1;
            }

            // Attempt to extract the plugin
            $manager->extractArchive($packageZip, $tempPath);

            // Look for the Plugin.php file
            $pluginFile = $tempPath . '/Plugin.php';
            if (!File::exists($pluginFile)) {
                $this->output->writeln(sprintf('<error>File not found: %s</error>', $pluginFile));
                return 1;
            }


            return 1;
        }

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

        return 0;
    }
}
