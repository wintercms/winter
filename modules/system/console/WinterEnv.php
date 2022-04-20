<?php namespace System\Console;

use App;
use Winter\Storm\Parse\EnvFile;
use Winter\Storm\Console\Command;
use Winter\Storm\Parse\PHP\ArrayFile;

/**
 * Console command to convert configuration to use .env files.
 *
 * This creates an .env file with some default configuration values, it also converts
 * the existing PHP-based configuration files to use the `env` function for values.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class WinterEnv extends Command
{
    /**
     * The console command name.
     */
    protected $name = 'winter:env';

    /**
     * The console command description.
     */
    protected $description = 'Creates .env file with default configuration values.';

    /**
     * @var array The env keys that need to have their original values removed from the config files
     */
    protected $protectedKeys = [
        'APP_KEY',
        'DB_USERNAME',
        'DB_PASSWORD',
        'MAIL_USERNAME',
        'MAIL_PASSWORD',
        'REDIS_PASSWORD',
    ];

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October
        $this->setAliases(['october:env']);
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (
            file_exists($this->laravel->environmentFilePath())
            && !$this->confirmToProceed()
        ) {
            return 1;
        }

        $this->updateEnvFile();
        $this->updateConfigFiles();

        $this->info('.env configuration file has been created.');

        return 0;
    }

    /**
     * Confirm before proceeding with the action.
     *
     * This method only asks for confirmation in production.
     *
     * @param  string  $warning
     * @param  \Closure|bool|null  $callback
     * @return bool
     */
    public function confirmToProceed($warning = 'Application In Production!', $callback = null)
    {
        if ($this->hasOption('force') && $this->option('force')) {
            return true;
        }

        $this->alert('The .env file already exists. Proceeding may overwrite some values!');

        $confirmed = $this->confirm('Do you really wish to run this command?');

        if (!$confirmed) {
            $this->comment('Command Canceled!');

            return false;
        }

        return true;
    }

    /**
     * Get the full path of a config file
     * @param string $config
     * @return string
     */
    protected function getConfigPath(string $config): string
    {
        return rtrim(App::make('path.config'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $config . '.php';
    }

    /**
     * Set env keys to their config values within the EnvFile object
     */
    protected function updateEnvFile(): void
    {
        $env = EnvFile::open($this->laravel->environmentFilePath());

        foreach ($this->config() as $config => $items) {
            foreach ($items as $envKey => $configKey) {
                $env->set($envKey, config($config . '.' . $configKey));
                if ($config === 'database' && $envKey === 'DB_CONNECTION') {
                    $default = config('database.default');
                    $dbConfig = $this->dbConfig()[$default] ?? [];

                    foreach ($dbConfig as $dbEnvKey => $dbConfigKey) {
                        $env->set($dbEnvKey, config(join('.', [$config, 'connections', $default, $dbConfigKey])));
                    }
                }

                if ($config === 'mail' && $envKey === 'MAIL_MAILER') {
                    $default = config('mail.default');
                    $mailConfig = $this->mailConfig()[$default] ?? [];

                    foreach ($mailConfig as $mailEnvKey => $mailConfigKey) {
                        $env->set($mailEnvKey, config(join('.', [$config, 'mailers', $default, $mailConfigKey])));
                    }
                }
            }
            $env->addEmptyLine();
        }

        $env->write();
    }

    /**
     * Update config files with env function calls
     */
    protected function updateConfigFiles(): void
    {
        foreach ($this->config() as $config => $items) {
            $arrayFile = ArrayFile::open($this->getConfigPath($config));
            foreach ($items as $envKey => $configKey) {
                $arrayFile->set(
                    $configKey,
                    $arrayFile->function('env', $this->getKeyValuePair($envKey, $config . '.' . $configKey))
                );
                if ($config === 'database' && $envKey === 'DB_CONNECTION') {
                    foreach ($this->dbConfig() as $connection => $keys) {
                        foreach ($keys as $dbEnvKey => $dbConfigKey) {
                            $path = sprintf('connections.%s.%s', $connection, $dbConfigKey);
                            $arrayFile->set(
                                $path,
                                $arrayFile->function('env', $this->getKeyValuePair($dbEnvKey, $config . '.' . $path))
                            );
                        }
                    }
                }
                if ($config === 'mail' && $envKey === 'MAIL_MAILER') {
                    foreach ($this->mailConfig() as $mailer => $keys) {
                        foreach ($keys as $mailEnvKey => $mailConfigKey) {
                            $path = sprintf('mailers.%s.%s', $mailer, $mailConfigKey);
                            $arrayFile->set(
                                $path,
                                $arrayFile->function('env', $this->getKeyValuePair($mailEnvKey, $config . '.' . $path))
                            );
                        }
                    }
                }
            }
            $arrayFile->write();
        }
    }

    /**
     * Returns an array containing the key as the first element and the value
     * as the second if the key is not a protected key; otherwise the value
     * will be an empty string
     */
    protected function getKeyValuePair(string $envKey, string $configKey): array
    {
        $return = [$envKey, in_array($envKey, $this->protectedKeys) ? '' : config($configKey)];
        return $return;
    }

    /**
     * Returns a map of env keys to php config keys for db configs
     * @return array
     */
    protected function config(): array
    {
        return [
            'app' => [
                'APP_DEBUG' => 'debug',
                'APP_URL' => 'url',
                'APP_KEY' => 'key',
            ],
            'database' => [
                'DB_CONNECTION' => 'default',
            ],
            'cache' => [
                'CACHE_DRIVER' => 'default',
            ],
            'session' => [
                'SESSION_DRIVER' => 'driver',
            ],
            'queue' => [
                'QUEUE_CONNECTION' => 'default',
            ],
            'mail' => [
                'MAIL_MAILER' => 'default',
            ],
            'cms' => [
                'ROUTES_CACHE' => 'enableRoutesCache',
                'ASSET_CACHE' => 'enableAssetCache',
                'LINK_POLICY' => 'linkPolicy',
                'ENABLE_CSRF' => 'enableCsrfProtection',
                'DATABASE_TEMPLATES' => 'databaseTemplates',
            ],
        ];
    }

    /**
     * Returns a map of env keys to php config keys for db configs
     * @return array
     */
    protected function dbConfig(): array
    {
        return [
            'sqlite' => [
                'DB_DATABASE' => 'database',
            ],
            'mysql' => [
                'DB_HOST' => 'host',
                'DB_PORT' => 'port',
                'DB_DATABASE' => 'database',
                'DB_USERNAME' => 'username',
                'DB_PASSWORD' => 'password',
            ],
            'pgsql' => [
                'DB_HOST' => 'host',
                'DB_PORT' => 'port',
                'DB_DATABASE' => 'database',
                'DB_USERNAME' => 'username',
                'DB_PASSWORD' => 'password',
            ],
            'redis' => [
                'REDIS_HOST' => 'host',
                'REDIS_PASSWORD' => 'password',
                'REDIS_PORT' => 'port',
            ],
        ];
    }

    /**
     * Returns a map of env keys to php config keys for mail configs
     * @return array
     */
    protected function mailConfig(): array
    {
        return [
            'smtp' => [
                'MAIL_ENCRYPTION' => 'encryption',
                'MAIL_HOST' => 'host',
                'MAIL_PASSWORD' => 'password',
                'MAIL_PORT' => 'port',
                'MAIL_USERNAME' => 'username',
            ],
            'sendmail' => [
                'MAIL_SENDMAIL_PATH' => 'path',
            ],
            'log' => [
                'MAIL_LOG_CHANNEL' => 'channel',
            ],
        ];
    }
}
