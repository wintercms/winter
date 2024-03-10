<?php namespace System\Console;

use System\Classes\Packager\Composer;
use System\Classes\VersionManager;
use Winter\Storm\Console\Command;
use System\Classes\UpdateManager;
use System\Classes\PluginManager;
use Winter\Storm\Exception\SystemException;

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
    public function handle(): void
    {
        $pluginName = $this->argument('plugin');
        $manager = null;

        if (strpos($pluginName, '/')) {
            $code = $this->composerInstall($pluginName);
            if (!$code) {
                return;
            }
        } elseif (strpos($pluginName, '.')) {
            $manager = UpdateManager::instance()->setNotesOutput($this->output);
            $code = $this->winterInstall($pluginName, $manager);
        }

        if (!$manager) {
            $manager = UpdateManager::instance()->setNotesOutput($this->output);
        }

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

    public function winterInstall(string $pluginName, UpdateManager $manager): string
    {
        $pluginDetails = $manager->requestPluginDetails($pluginName);

        $code = array_get($pluginDetails, 'code');
        $hash = array_get($pluginDetails, 'hash');

        $this->output->writeln(sprintf('<info>Downloading plugin: %s</info>', $code));
        $manager->downloadPlugin($code, $hash, true);

        $this->output->writeln(sprintf('<info>Unpacking plugin: %s</info>', $code));
        $manager->extractPlugin($code, $hash);

        return $code;
    }

    public function composerInstall(string $pluginName): ?string
    {
        try {
            $result = Composer::search($pluginName, 'winter-plugin')->getResults();

            if (count($result) > 1) {
                throw new SystemException('More than 1 plugin returned via composer search');
            }

            if (count($result) === 0) {
                throw new SystemException('Plugin could not be found');
            }

            Composer::require($pluginName);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return null;
        }

        PluginManager::forgetInstance();
        UpdateManager::forgetInstance();
        VersionManager::forgetInstance();

        return PluginManager::instance()->findByComposerPackage($pluginName)->getPluginIdentifier();
    }
}
