<?php namespace System\Console;

use Winter\Storm\Console\Command;
use System\Classes\UpdateManager;
use System\Classes\PluginManager;
use Throwable;
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
            File::deleteDirectory($tempPath);
            File::copy(base_path('plugin-test.zip'), $packageZip);

            // Check if the file exists
            if (!File::exists($packageZip)) {
                $this->output->writeln(sprintf('<error>File not found: %s</error>', $pluginName));
                return 1;
            }

            // Attempt to extract the plugin
            $manager->extractArchive($packageZip, $tempPath);

            // Look for plugins to install
            $plugins = $manager->findPluginsInPath($tempPath);

            if (empty($plugins)) {
                $this->output->writeln(sprintf('<error>No plugins found in: %s</error>', $pluginName));
                return 1;
            }

            if (!$this->confirm(sprintf('Detected the following plugins in %s: %s. Would you like to install them all?', $pluginName, Str::join(array_keys($plugins))))) {
                return 0;
            };

            $pluginManager = PluginManager::instance();

            foreach ($plugins as $code => $pluginClassFile) {
                // @TODO: Check if plugin is already installed
                if ($pluginManager->findByIdentifier($code)) {
                    if (!$this->confirm(sprintf(
                        'Plugin %s is already installed. Would you like to replace the currently installed version with the one found in %s?',
                        $code,
                        $pluginName
                    ))) {
                        $this->output->writeln(sprintf('<info>Skipping plugin: %s</info>', $code));
                        unset($plugins[$code]);
                        continue;
                    }
                }

                $this->output->writeln(sprintf('<info>Installing plugin: %s</info>', $code));
                $pluginDir = plugins_path(strtolower(str_replace('.', DIRECTORY_SEPARATOR, $code)));

                try {
                    // Copy the files
                    File::copyDirectory(pathinfo($pluginClassFile, PATHINFO_DIRNAME), $pluginDir);

                    // Load the plugin
                    $pluginManager->loadPlugins();
                    $plugin = $pluginManager->findByIdentifier($code);
                    $pluginManager->registerPlugin($plugin, $code);

                    $pluginManager->clearFlagCache();
                    $pluginManager->loadPluginFlags();

                    $pluginsToMigrate[] = $code;

                    // Run any pending migrations for the plugin
                    // @TODO: May have to add some logic here to just run all pending migrations after completing installing all of the plugins
                    // to account for dependencies. Something else to consider is how we abort a plugin that fails to migrate.
                    $manager->updatePlugin($code);

                    $this->output->writeln('');
                } catch (Throwable $e) {
                    $this->output->writeln(sprintf('<error>Error installing plugin: %s</error>', $code));
                    $this->output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
                    File::deleteDirectory($pluginDir);

                    return 1;
                }
            }

            $this->output->success(sprintf('Successfully installed %d plugin(s) from %s', count($plugins), $pluginName));

            return 0;
        }

        $pluginDetails = $manager->requestPluginDetails($pluginName);

        $code = array_get($pluginDetails, 'code');
        $hash = array_get($pluginDetails, 'hash');

        $this->output->writeln(sprintf('<info>Downloading plugin: %s</info>', $code));
        $manager->downloadPlugin($code, $hash, true);

        $this->output->writeln(sprintf('<info>Unpacking plugin: %s</info>', $code));
        $manager->extractPlugin($code, $hash);

        // Make sure plugin is registered
        $pluginManager = PluginManager::instance();
        $pluginManager->loadPlugins();
        $plugin = $pluginManager->findByIdentifier($code);
        $pluginManager->registerPlugin($plugin, $code);

        // Migrate plugin
        $this->output->writeln(sprintf('<info>Migrating plugin...</info>', $code));
        $manager->updatePlugin($code);

        return 0;
    }
}
