<?php namespace System\Console;

use Illuminate\Console\Command;
use System\Classes\UpdateManager;
use System\Classes\PluginManager;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Console command to test a plugin.
 *
 * This searches for a phpunit.xml file into the plugin's directory and run its tests
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginTest extends Command
{

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'plugin:test';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Run tests of an existing plugin.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        /*
         * Lookup plugin
         */
        $pluginName = $this->argument('name');
        $pluginName = PluginManager::instance()->normalizeIdentifier($pluginName);
        if (!PluginManager::instance()->exists($pluginName)) {
            throw new \InvalidArgumentException(sprintf('Plugin "%s" not found.', $pluginName));
        }

        $pluginDir = PluginManager::instance()->getPluginPath($pluginName);

        if (!file_exists($pluginDir . '/phpunit.xml')) {
            throw new \InvalidArgumentException(sprintf('phpunit.xml file not found in "%s".', $pluginDir));
        }

        /*
         * Test plugin
         */
        $exec = 'cd ' . $pluginDir . ' && ';
        $exec .= '../../../vendor/bin/phpunit';
        $this->output->writeln(sprintf('<info>Running  plugin\'s tests: %s.</info>', $pluginName));
        echo shell_exec($exec);
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the plugin. Eg: AuthorName.PluginName'],
        ];
    }
}
