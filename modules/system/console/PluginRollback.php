<?php namespace System\Console;

use App;
use Illuminate\Console\Command;
use System\Classes\UpdateManager;
use System\Classes\PluginManager;
use System\Classes\VersionManager;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;

/**
 * Console command to rollback a plugin.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginRollback extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'plugin:rollback';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Rollback an existing plugin.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        /*
         * Lookup plugin
         */
        $pluginManager = PluginManager::instance();
        $pluginName = $this->argument('name');
        $pluginName = $pluginManager->normalizeIdentifier($pluginName);

        if (!$pluginManager->hasPlugin($pluginName)) {
            return $this->error(sprintf('Unable to find a registered plugin called "%s"', $pluginName));
        }

        if (App::isProduction() && !$this->option('force')) {
            $this->warn('YOUR APPLICATION IS IN PRODUCTION');
        }

        $stopOnVersion = ltrim(($this->argument('version') ?: null), 'v');

        if ($stopOnVersion) {
            if (!VersionManager::instance()->hasDatabaseVersion($pluginName, $stopOnVersion)) {
                throw new \InvalidArgumentException('Plugin version not found');
            }
            $confirmQuestion = "Please confirm that you wish to revert $pluginName to version $stopOnVersion. This may result in changes to your database and potential data loss.";
        } else {
            $confirmQuestion = "Please confirm that you wish to completely rollback $pluginName. This may result in potential data loss.";
        }

        if ($this->option('force') || $this->confirm($confirmQuestion)) {
            $manager = UpdateManager::instance()->setNotesOutput($this->output);

            try {
                $manager->rollbackPlugin($pluginName, $stopOnVersion);
            } catch (\Exception $exception) {
                $lastVersion = VersionManager::instance()->getCurrentVersion($pluginName);
                $this->output->writeln(sprintf("<comment>An exception occurred during the rollback and the process has been stopped. %s was rolled back to version v%s.</comment>", $pluginName, $lastVersion));
                throw $exception;
            }
        }
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the plugin to be rolled back. Eg: AuthorName.PluginName'],
            ['version', InputArgument::OPTIONAL, 'If this parameter is specified, the process will stop on the specified version, if not, it will completely rollback the plugin. Example: 1.3.9'],
        ];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force rollback', null],
        ];
    }

    /**
     * Provide autocompletion for this command's input
     */
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('name')) {
            $plugins = array_keys(PluginManager::instance()->getPlugins());
            $suggestions->suggestValues($plugins);
        }
    }
}
