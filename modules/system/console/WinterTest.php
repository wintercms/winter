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

    protected $signature = 'winter:test {plugin?} {--configuration=}';

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
     * @return int|void
     */
    public function handle()
    {
        // If the plugin argument is set, search for it and run its tests
        $pluginArgument = $this->argument('plugin');
        if (!is_null($pluginArgument)) {
            $pluginManager = PluginManager::instance();
            $pluginName = $pluginManager->normalizeIdentifier($pluginArgument);

            if (!$pluginManager->exists($pluginName)) {
                $this->error(sprintf('Plugin "%s" not found.', $pluginName));

                return;
            }

            $testsPath = $pluginManager->getPluginPath($pluginName);
        }

        // Run the core Winter CMS tests
        else {
            $testsPath = base_path();
        }

        $phpunitXMLFile = $this->lookForPHPUnitXMLFile($testsPath);

        if (!$phpunitXMLFile) {
            $this->error(sprintf('Configuration file not found in "%s".', $testsPath));

            return;
        }

        $this->exec[] = "--configuration=$phpunitXMLFile";

        // Add eventual PHPUnit native options
        $phpunitArguments = $this->getPHPUnitArguments();

        $this->exec = array_merge($this->exec, $phpunitArguments);

        $process = (new Process($this->exec))->setTimeout(null);

        try {
            $runningTestsMessage =
                isset($pluginName) ? sprintf('Running  plugin\'s tests: %s.', $pluginName)
                                   : 'Running  Winter\'s core tests.';

            $this->info($runningTestsMessage);
            $this->info(sprintf('Configuration file used: %s.', $phpunitXMLFile));

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

    /**
     * Search for the config file to use
     * and gives an opportunity to change it if not found,
     * priority order is: --configuration option, phpunit.xml then phpunit.xml.dist
     * @param string|bool $directory
     */
    protected function lookForPHPUnitXMLFile(string $directory)
    {
        $configurationOption = $this->option('configuration');

        // If the user explicitly provided a custom configuration file
        // Make sure it exists or abort as its clear we don't want to run default phpunit files
        if ($configurationOption) {
            $optionFilePath = $directory . DIRECTORY_SEPARATOR . $configurationOption;

            if (file_exists($optionFilePath)) {
                return $optionFilePath;
            }

            return false;
        }

        // If a phpunit.xml file exists, returns its path
        $distFilePath = $directory . DIRECTORY_SEPARATOR . 'phpunit.xml';
        if (file_exists($distFilePath)) {
            return $distFilePath;
        }

        // Fallback to phpunit.xml.dist file path if it exists
        $configFilePath = $directory . DIRECTORY_SEPARATOR . 'phpunit.xml.dist';
        if (file_exists($configFilePath)) {
            return $configFilePath;
        }

        return false;
    }

    /**
     * Strips out this commands arguments and options in order to return arguments/options for PHPUnit.
     *
     * @return array
     */
    protected function getPHPUnitArguments()
    {
        $arguments = $_SERVER['argv'];

        // First two are always "artisan" and "winter:test"
        $arguments = array_slice($arguments, 2);

        // Strip plugin argument
        if ($this->argument('plugin')) {
            array_shift($arguments);
        }

        // Strip "--configuration"
        if ($this->option('configuration')) {
            array_shift($arguments);
        }

        return $arguments;
    }
}
