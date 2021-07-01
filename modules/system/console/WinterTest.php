<?php namespace System\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;
use System\Classes\PluginManager;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Console command to test a plugin or the Winter CMS core.
 *
 * If a plugin is provided, this command will search for a `phpunit.xml` file inside the plugin's directory and run its tests.
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
    protected $description = "Run tests for the Winter CMS core or an existing plugin.";

    protected $exec = ['./vendor/bin/phpunit'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        /**
         * Ignore validation errors as option proxying is used by this command
         * @see https://github.com/nunomaduro/collision/blob/stable/src/Adapters/Laravel/Commands/TestCommand.php
         */
        $this->ignoreValidationErrors();
    }

    /**
     * Execute the console command.
     * @return int
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

            $pluginPHPUnitXMLFile = $pluginDir . '/phpunit.xml';
            if (!file_exists($pluginPHPUnitXMLFile)) {
                throw new \InvalidArgumentException(sprintf('phpunit.xml file not found in "%s".', $pluginDir));
            }

            /*
             * Test plugin
             */
            $this->exec[] = "--configuration=$pluginPHPUnitXMLFile";
            $this->output->writeln(sprintf('<info>Running  plugin\'s tests: %s.</info>', $pluginName));
        }

        // Run the core Winter CMS tests
        else {
            $this->info('Running  Winter\'s core tests.');
        }

        // Add eventual PHPUnit native options
        $options = array_slice($_SERVER['argv'], $this->argument('plugin') ? 3 : 2);

        $this->exec = array_merge($this->exec, $options);

        $process = (new Process($this->exec))->setTimeout(null);

        try {
            return $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
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
            ['plugin', InputArgument::OPTIONAL, 'The name of the plugin. Eg: AuthorName.PluginName'],
        ];
    }
}
