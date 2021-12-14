<?php namespace System\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use System\Classes\PluginManager;
use Winter\Storm\Exception\ApplicationException;

/**
 * Console command to run tests for plugins or the Winter CMS core.
 *
 * If a plugin is provided, this command will search for a `phpunit.xml` file inside the plugin's directory and run its tests.
 *
 * @package winter\wn-system-module
 */
class WinterTest extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'winter:test';

    /**
     * @var string The console command signature as ignoreValidationErrors causes options not to be registered.
     */
    protected $signature = 'winter:test {?--p|plugin=} {?--c|configuration=} {?--o|core}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Run tests for the Winter CMS core or an existing plugin.';

    /**
     * @var ?string Path to phpunit binary
     */
    protected $phpUnitExec = null;

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
     *
     * @throws ApplicationException
     * @return int|void
     */
    public function handle()
    {
        $arguments = $this->getAdditionalArguments();

        if (($config = $this->option('configuration')) && file_exists($config)) {
            return $this->execPhpUnit($config, $arguments);
        }

        $configs = $this->getPhpUnitConfigs();

        if ($this->option('core')) {
            if (!$configs['core']) {
                throw new ApplicationException("Unable to find the core's phpunit.xml file. Try downloading it from GitHub.");
            }
            $this->info('Running tests for: Winter CMS core');

            return $this->execPhpUnit($configs['core'], $arguments);
        }

        if ($plugin = $this->option('plugin')) {
            if (!isset($configs['plugins'][strtolower($plugin)])) {
                throw new ApplicationException(sprintf("Unable to find %s\'s phpunit.xml file", $plugin));
            }
            $this->info('Running tests for plugin: ' . PluginManager::instance()->normalizeIdentifier($plugin));

            return $this->execPhpUnit($configs['plugins'][strtolower($plugin)], $arguments);
        }

        $exitCode = 0;

        foreach (['core', 'plugins'] as $type) {
            if (is_array($configs[$type])) {
                foreach ($configs[$type] as $plugin => $config) {
                    $this->info('Running tests for plugin: ' . PluginManager::instance()->normalizeIdentifier($plugin));
                    $exit = $this->execPhpUnit($config, $arguments);
                    $exitCode = $exitCode === 0 ? $exit : $exitCode;
                }
                continue;
            }

            $this->info('Running tests for Winter CMS: ' . $type);
            $exit = $this->execPhpUnit($configs[$type], $arguments);
            $exitCode = $exitCode === 0 ? $exit : $exitCode;
        }

        return $exitCode;
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['plugin', 'p', InputOption::VALUE_OPTIONAL, 'The name of the plugin. Ex: AuthorName.PluginName', null],
            ['configuration', 'c', InputOption::VALUE_OPTIONAL, 'The path to a PHPUnit XML config file', null],
            ['core', 'o', InputOption::VALUE_NONE, 'Run the Winter CMS core tests'],
        ];
    }

    /**
     * Execute a phpunit test
     *
     * @param string $config Path to configuration file
     * @param array $args Array of params for PHPUnit
     * @return int Exit code from process
     */
    protected function execPhpUnit(string $config, array $args): int
    {
        // Find and bind the phpunit executable
        if (!$this->phpUnitExec) {
            $this->phpUnitExec = (new ExecutableFinder())
                ->find('phpunit', base_path('vendor/bin/phpunit'), [base_path('vendor')]);
        }

        $process = new Process(
            array_merge([$this->phpUnitExec, '--configuration=' . $config], $args),
            dirname($config),
            null,
            null
        );

        // Set an unlimited timeout
        $process->setTimeout(0);

        // Attempt to set tty mode, catch and warn with the exception message if unsupported
        try {
            $process->setTty(true);
        } catch (\Throwable $e) {
            $this->warn($e->getMessage());
        }

        try {
            return $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }

            return 1;
        }
    }

    /**
     * Find all PHPUnit config files (core, lib, plugins)
     */
    protected function getPhpUnitConfigs(): array
    {
        $configs = [
            'core' => $this->getPhpUnitXmlFile(base_path()),
            'plugins' => []
        ];

        foreach (PluginManager::instance()->getPlugins() as $plugin) {
            if ($path = $this->getPhpUnitXmlFile($plugin->getPluginPath())) {
                $configs['plugins'][strtolower($plugin->getPluginIdentifier())] = $path;
            }
        }

        return $configs;
    }

    /**
     * Search for the config file to use.
     * Priority order is: phpunit.xml, phpunit.xml.dist
     */
    protected function getPhpUnitXmlFile(string $path): ?string
    {
        // If a phpunit.xml file exists, returns its path
        $distFilePath = $path . DIRECTORY_SEPARATOR . 'phpunit.xml';
        if (file_exists($distFilePath)) {
            return $distFilePath;
        }

        // Fallback to phpunit.xml.dist file path if it exists
        $configFilePath = $path . DIRECTORY_SEPARATOR . 'phpunit.xml.dist';
        if (file_exists($configFilePath)) {
            return $configFilePath;
        }

        return null;
    }

    /**
     * Strips out commands arguments and options in order to return arguments/options for PHPUnit.
     */
    protected function getAdditionalArguments(): array
    {
        $arguments = $_SERVER['argv'];

        // First two are always "artisan" and "winter:test"
        $arguments = array_slice($arguments, 2);

        // If nothing to do then just return
        if (!count($arguments)) {
            return $arguments;
        }

        // Get the arguments provided by this command
        foreach ($this->getOptions() as $argument) {
            // For position 0 & 1, pass their names with appropriate dashes
            for ($i = 0; $i < 2; $i++) {
                $arguments = $this->removeArgument($arguments, str_repeat('-', 2 - $i) . $argument[$i]);
            }
        }

        return $arguments;
    }

    /**
     * Removes flags from argument list and their value if present
     */
    protected function removeArgument(array $arguments, string $remove): array
    {
        // find args that have trailing chars
        $key = array_values(preg_grep("/^({$remove}|{$remove}=).*/i", $arguments));
        $remove = (isset($key[0])) ? $key[0] : $remove;

        // find the position of arguments to remove
        if (($position = array_search($remove, $arguments)) === false) {
            return $arguments;
        }

        // remove argument
        unset($arguments[$position]);

        // if the next item in the array is not a flag, consider it a value of the removed argument
        if (isset($arguments[$position + 1]) && substr($arguments[$position + 1], 0, 1) !== '-') {
            unset($arguments[$position + 1]);
        }

        return array_values($arguments);
    }
}
