<?php

namespace System\Console\Plugin;

use System\Classes\Extensions\PluginManager;
use System\Console\Traits;
use Winter\Storm\Console\Command;
use Winter\Storm\Exception\ApplicationException;

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
    use \Winter\Storm\Console\Traits\ConfirmsWithInput;
    use Traits\HasPluginArgument;

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
        {--f|force : Force the operation to run and ignore production warnings and confirmation questions.}
        {--r|no-rollback : Skip the rollback of the plugin migrations.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Removes an existing plugin.';

    /**
     * Execute the console command.
     * @throws ApplicationException
     */
    public function handle(): int
    {
        $pluginName = $this->getPluginIdentifier();

        $confirmQuestion = sprintf('This will remove the files for the "%s" plugin.', $pluginName);

        if (!$this->option('no-rollback')) {
            $confirmQuestion = sprintf('This will remove the database tables and files for the "%s" plugin.', $pluginName);
        }

        if (!$this->confirmWithInput(
            $confirmQuestion,
            $pluginName
        )) {
            return 1;
        }

        PluginManager::instance()->uninstall($pluginName, $this->option('no-rollback'));

        return 0;
    }
}
