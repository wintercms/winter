<?php

namespace System\Console;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use System\Classes\PluginManager;

/**
 * Console command to check code style and formatting issues.
 *
 * If a plugin is provided, this command will search for a `phpcs.xml file inside the plugin's directory. If not found,
 * the user will be prompted to create one if they wish.
 */
class WinterSniff extends BaseScaffoldCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'winter:sniff';

    /**
     * @var string The console command name.
     */
    protected $name = 'winter:sniff';

    /**
     * The console command description.
     */
    protected $description = 'Validates source code to ensure a consistent coding style.';

    /**
     * @var string The console command signature as ignoreValidationErrors causes options not to be registered.
     */
    protected $signature = 'winter:sniff
        {paths?* : The path to the files and/or directories to check, if you wish to limit the scope of files checked.}
        {?--c|config= : Path to a custom PHPCS configuration XML file}
        {?--p|plugin= : Checks the coding style of a plugin}
        {?--e|no-warnings : Ignore warnings and only show errors}
        {?--s|summary : Display a summary of the results}
    ';

    protected $stubs = [
        'scaffold/phpcs/phpcs.xml.stub' => 'phpcs.xml',
    ];

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October
        $this->setAliases(['winter:phpcs']);
    }

    /**
     * Execute the console command.
     *
     * @throws \Winter\Storm\Exception\ApplicationException
     * @return int|void
     */
    public function handle()
    {
        $configFile = $this->getConfigFile();

        if (!File::exists($configFile)) {
            $this->error(sprintf('No configuration file found. Exiting.', $configFile));
            return 1;
        }

        $phpCs = $this->findPhpCsExecutable();

        if (is_null($phpCs)) {
            $this->error('Unable to find the `phpcs` executable. Please ensure that you have installed the developer
            dependencies through Composer.');
            return 1;
        }

        $phpCsArgs = $this->buildPhpCsArgs($phpCs, $configFile);

        ini_set('memory_limit', '512M');
        $process = new Process($phpCsArgs, base_path());

        // Set an unlimited timeout
        $process->setTimeout(0);

        // Attempt to set tty mode, catch and warn with the exception message if unsupported
        try {
            $process->setTty(true);
        } catch (\Throwable $e) {
            $this->warn($e->getMessage());
        }

        $exitCode = 1;

        try {
            $exitCode = $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }

            return 1;
        }

        if ($exitCode === 0) {
            return $this->components->info('No coding style issues found.');
        }
    }

    /**
     * Gets the config file path.
     *
     * If the config file is not found, the user will be prompted to create one if they wish.
     */
    protected function getConfigFile(): ?string
    {
        $path = $this->expectedConfigPath();

        if (!File::exists($path)) {
            $this->warn(sprintf('Configuration file %s not found.', $path));

            if ($this->confirm('Would you like to create a new configuration file?')) {
                $this->createConfigFile();
            }
        }

        return $path;
    }

    /**
     * Determines the expected path for the config file.
     *
     * For plugins, this will be a "phpcs.xml" file inside the plugin's directory. For the core, this will be a
     * "phpcs.xml" file in the root directory of the project.
     */
    protected function expectedConfigPath(): ?string
    {
        if ($this->option('config')) {
            return $this->option('config');
        }

        if ($this->option('plugin')) {
            $pluginPath = PluginManager::instance()->getPluginPath($this->getPluginIdentifier());

            if (is_null($pluginPath)) {
                $this->error(sprintf('Plugin %s not found.', $this->getPluginIdentifier()));
            }

            return $pluginPath . '/phpcs.xml';
        }

        return base_path('phpcs.xml');
    }

    /**
     * Creates a new configuration file for PHPCS.
     */
    protected function createConfigFile(): void
    {
        if ($this->option('plugin')) {
            $this->vars = [
                'plugin' => $this->getPluginIdentifier(),
                'plugin_code' => str_replace('.', '_', $this->getPluginIdentifier()),
            ];
            $this->makeStub('scaffold/phpcs/phpcs.xml.stub');
            return;
        }

        File::copy(
            __DIR__ . '/scaffold/phpcs/phpcs.core.xml.stub',
            $this->option('config') ?? base_path('phpcs.xml')
        );
    }

    protected function getDestinationForStub(string $stubName): string
    {
        if ($this->option('config')) {
            return $this->option('config');
        }

        return parent::getDestinationForStub($stubName);
    }

    public function getPluginIdentifier($identifier = null): string
    {
        $pluginManager = PluginManager::instance();
        $pluginName = $identifier ?? $this->option('plugin');
        $pluginName = $pluginManager->normalizeIdentifier($pluginName);

        if (!$pluginManager->hasPlugin($pluginName)) {
            $this->error(sprintf('Plugin %s not found.', $this->getPluginIdentifier()));
        }

        return $pluginName;
    }

    /**
     * Finds the `phpcs` executable in the system.
     *
     * This should be in the `vendor/bin` directory of the project, if the project has developer dependencies installed
     * through Composer.
     */
    protected function findPhpCsExecutable(): ?string
    {
        return (new ExecutableFinder())
            ->find(
                'phpcs',
                base_path('vendor/bin/phpcs'),
                [
                    base_path('vendor'),
                ]
            );
    }

    /**
     * Gets the base path where files and directories will be checked.
     *
     * If the `--plugin` option is provided, this will return the path to the plugin. Otherwise, it will return the base
     * path of the project.
     *
     * @return string
     */
    protected function getBasePath(): string
    {
        if ($this->option('plugin')) {
            $pluginPath = PluginManager::instance()->getPluginPath($this->getPluginIdentifier());

            if (is_null($pluginPath)) {
                $this->error(sprintf('Plugin %s not found.', $this->getPluginIdentifier()));
            }

            return $pluginPath;
        }

        return base_path();
    }

    /**
     * Builds the arguments to pass to the `phpcs` executable.
     *
     * @return string[]
     */
    protected function buildPhpCsArgs(string $phpCs, string $configFile): array
    {
        $args = [
            $phpCs,
            '-d',
            'memory_limit=512M',
            '--basepath=' . $this->getBasePath(),
            '--standard=' . $configFile,
        ];

        if ($this->option('no-warnings')) {
            $args[] = '--warning-severity=0';
        }

        if ($this->option('summary')) {
            $args[] = '--report=summary';
        }

        $args = array_merge(
            $args,
            $this->argument('paths') ?: (
            ($this->option('plugin'))
                ? [PluginManager::instance()->getPluginPath($this->getPluginIdentifier())]
                : []
            )
        );

        return $args;
    }
}
