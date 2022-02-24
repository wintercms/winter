<?php namespace System\Console;

use App;
use File;
use Winter\Storm\Console\Command;
use System\Classes\UpdateManager;
use System\Classes\PluginManager;

/**
 * Console command to remove a plugin.
 *
 * This completely deletes an existing plugin, including database tables, files
 * and directories.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginRemove extends Command
{
    use Traits\HasPluginArgument;
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * @var string Suggest all plugins
     */
    protected $hasPluginsFilter = 'all';

    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'plugin:remove';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'plugin:remove
        {plugin : The plugin to remove. <info>(eg: Winter.Blog)</info>}
        {--f|force : Force the operation to run and ignore production warning.}
        {--r|no-rollback : Skip the rollback of the plugin migrations.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Removes an existing plugin.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $pluginName = $this->getPluginIdentifier();
        $pluginManager = PluginManager::instance();

        if (App::isProduction() && !$this->option('force')) {
            $this->warn('YOUR APPLICATION IS IN PRODUCTION');
        }

        if (!$this->option('no-rollback')) {
            $this->warn(sprintf('This will remove the database tables and files for the "%s" plugin.', $pluginName));
        } else {
            $this->warn(sprintf('This will remove the files for the "%s" plugin.', $pluginName));
        }

        $confirmed = false;
        $prompt = sprintf('Please type "%s" to proceed', $pluginName);
        do {
            if (strtolower($this->ask($prompt)) === strtolower($pluginName)) {
                $confirmed = true;
            }
        } while ($confirmed === false);

        if (!$this->option('no-rollback')) {
            /*
            * Rollback plugin
            */
            $manager = UpdateManager::instance()->setNotesOutput($this->output);
            $manager->rollbackPlugin($pluginName);
        }

        /*
         * Delete from file system
         */
        if ($pluginPath = $pluginManager->getPluginPath($pluginName)) {
            File::deleteDirectory($pluginPath);
            $this->output->writeln(sprintf('<info>Deleted: %s</info>', $pluginName));
        }
    }

    /**
     * Get the default confirmation callback.
     * @return \Closure
     */
    protected function getDefaultConfirmCallback()
    {
        return function () {
            return true;
        };
    }
}
