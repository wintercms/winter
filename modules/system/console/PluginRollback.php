<?php namespace System\Console;

use InvalidArgumentException;
use Winter\Storm\Console\Command;
use System\Classes\UpdateManager;
use System\Classes\VersionManager;

/**
 * Console command to rollback a plugin.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginRollback extends Command
{
    use \Winter\Storm\Console\Traits\ConfirmsWithInput;
    use Traits\HasPluginArgument;

    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'plugin:rollback';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'plugin:rollback
        {plugin : The plugin to disable. <info>(eg: Winter.Blog)</info>}
        {version? : If this parameter is not specified the plugin will be completely rolled back; otherwise it will stop on the specified version. <info>(eg: 1.3.9)</info>}
        {--f|force : Force the operation to run and ignore production warnings and confirmation questions.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Rollback an existing plugin.';

    /**
     * Execute the console command.
     * @throws Exception if the UpdateManager is unable to rollback the requested plugin to the requested version
     * @throws InvalidArgumentException if the requested rollback version can't be found
     */
    public function handle(): int
    {
        $pluginName = $this->getPluginIdentifier();
        $stopOnVersion = ltrim(($this->argument('version') ?: null), 'v');

        if ($stopOnVersion) {
            if (!VersionManager::instance()->hasDatabaseVersion($pluginName, $stopOnVersion)) {
                throw new InvalidArgumentException('Plugin version not found');
            }
            $confirmQuestion = "This will revert $pluginName to version $stopOnVersion - changes to the database and potential data loss may occur.";
        } else {
            $confirmQuestion = "This will completely rollback $pluginName. This may result in potential data loss.";
        }

        if (!$this->confirmWithInput(
            $confirmQuestion,
            $pluginName
        )) {
            return 1;
        }

        $manager = UpdateManager::instance()->setNotesOutput($this->output);

        try {
            $manager->rollbackPlugin($pluginName, $stopOnVersion);
        } catch (\Exception $exception) {
            $lastVersion = VersionManager::instance()->getCurrentVersion($pluginName);
            $this->output->writeln(sprintf("<comment>An exception occurred during the rollback and the process has been stopped. %s was rolled back to version v%s.</comment>", $pluginName, $lastVersion));
            throw $exception;
        }

        return 0;
    }

    /**
     * Suggest values for the optional version argument
     */
    public function suggestVersionValues(string $value = null, array $allInput): array
    {
        // Get the currently selected plugin
        $pluginName = $this->getPluginIdentifier($allInput['arguments']['plugin']);

        // Get that plugin's versions from the database
        $history = VersionManager::instance()->getDatabaseHistory($pluginName);

        // Compile a list of available versions to rollback to
        $availableVersions = [];
        foreach ($history as $record) {
            $availableVersions[] = $record->version;
        }

        return $availableVersions;
    }
}
