<?php namespace System\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use System\Classes\PluginManager;
use Winter\Storm\Exception\ApplicationException;
use Config;

/**
 * Console command to run tests for plugins and modules.
 *
 * If a plugin is provided, this command will search for a `phpunit.xml` file inside the plugin's directory and run its tests.
 *
 * @package winter\wn-system-module
 */
class WinterTest extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'winter:test';

    /**
     * @var string The console command name.
     */
    protected $name = 'winter:test';

    /**
     * @var string The console command signature as ignoreValidationErrors causes options not to be registered.
     */
    protected $signature = 'winter:test
        {phpunitArgs?* : Arguments to pass through to PHPUnit}
        {?--c|configuration= : A specific phpunit xml file}
        {?--p|plugin=* : List of plugins to test}
        {?--m|module=* : List of modules to test}
    ';

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
        $arguments = $this->argument('phpunitArgs');

        if (($config = $this->option('configuration')) && file_exists($config)) {
            return $this->execPhpUnit($config, $arguments);
        }

        $configs = $this->getPhpUnitConfigs();
        $exitCode = null;

        // loop over arguments and run specified tests
        foreach (['module', 'plugin'] as $type) {
            if ($this->option($type)) {
                foreach ($this->option($type) as $target) {
                    $target = strtolower($target);
                    if (!isset($configs[$type . 's'][$target])) {
                        throw new ApplicationException(sprintf(
                            'Unable to find %s %s\'s phpunit.xml file',
                            $type,
                            $target
                        ));
                    }
                    $this->info(sprintf('Running tests for %s: %s', $type, $target));
                    $exit = $this->execPhpUnit($configs[$type . 's'][$target], $arguments);
                    // keep non 0 exit codes for return
                    $exitCode = !$exitCode ? $exit : $exitCode;
                }
            }
        }

        // if we ran a specific test above we should have an exit code
        if (!is_null($exitCode)) {
            return $exitCode;
        }

        // default to running all defined configs found
        foreach (['modules', 'plugins'] as $type) {
            foreach ($configs[$type] as $name => $config) {
                $this->info(
                    $type === 'plugins'
                        ? 'Running tests for plugin: ' . PluginManager::instance()->normalizeIdentifier($name)
                        : 'Running tests for module: ' . $name
                );
                $exit = $this->execPhpUnit($config, $arguments);
                // keep non 0 exit codes for return
                $exitCode = !$exitCode ? $exit : $exitCode;
            }
        }

        return $exitCode ?? 0;
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

        // Resolve the configuration path based on the current working directory
        $config = realpath($config);

        $process = new Process(
            array_merge([$this->phpUnitExec, '--configuration=' . $config], $args),
            base_path(),
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
            'modules' => [],
            'plugins' => []
        ];

        foreach (Config::get('cms.loadModules', ['System', 'Cms', 'Backend']) as $module) {
            $module = strtolower($module);
            if ($path = $this->getPhpUnitXmlFile(base_path('modules/' . $module))) {
                $configs['modules'][$module] = $path;
            }
        }

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
        $configFilePath = $path . DIRECTORY_SEPARATOR . 'phpunit.xml';

        if (file_exists($configFilePath)) {
            return $configFilePath;
        }

        // Fallback to phpunit.xml.dist file path if it exists
        $distFilePath = $path . DIRECTORY_SEPARATOR . 'phpunit.xml.dist';
        if (file_exists($distFilePath)) {
            return $distFilePath;
        }

        return null;
    }
}
