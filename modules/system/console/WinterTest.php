<?php namespace System\Console;

use Illuminate\Console\Command;
use System\Classes\PluginManager;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Console command to test a plugin or winter's core.
 *
 * This searches for a phpunit.xml file into the plugin's directory and run its tests
 *
 * @package winter\wn-system-module
 */
class WinterTest extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'winter:test';

    protected $signature = 'winter:test {plugin?}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Run tests of Winter or an existing plugin.';

    /**
     * @var array Selected plugins, and the path to their browser tests.
     */
    protected $plugins = [];

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        // If the plugin argument is set, search for it and run its tests
        if ($pluginArgument = $this->argument('plugin')) {
            $pluginManager = PluginManager::instance();
            $pluginName = $pluginManager->normalizeIdentifier($pluginArgument);

            if (!$pluginManager->exists($pluginName)) {
                throw new \InvalidArgumentException(sprintf('Plugin "%s" not found.', $pluginName));
            }

            $pluginDir = $pluginManager->getPluginPath($pluginName);

            if (!file_exists($pluginDir . '/phpunit.xml')) {
                throw new \InvalidArgumentException(sprintf('phpunit.xml file not found in "%s".', $pluginDir));
            }

            /*
             * Test plugin
             */
            $exec = 'cd ' . $pluginDir . ' && ';
            $exec .= '../../../vendor/bin/phpunit';
            $this->output->writeln(sprintf('<info>Running  plugin\'s tests: %s.</info>', $pluginName));
        }

        // Else run winter's tests
        else {
            $exec = './vendor/bin/phpunit';
            $this->output->writeln('<info>Running  Winter\'s tests.</info>');
        }

        echo shell_exec($exec);
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::OPTIONAL, 'The name of the plugin. Eg: AuthorName.PluginName'],
        ];
    }
}
