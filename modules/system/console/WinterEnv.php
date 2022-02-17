<?php namespace System\Console;

use App;
use Illuminate\Console\Command;
use Winter\Storm\Config\ConfigFile;
use Winter\Storm\Config\EnvFile;

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
     * The current config cursor.
     */
    protected $config;

    /**
     * The current database connection cursor.
     */
    protected $connection;

    /**
     * @var string env file name
     */
    protected $envFile = '.env';

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
     * @return int
     */
    public function handle(): int
    {
        if (file_exists($this->getEnvPath())) {
            $this->error('.env file already exists.');
            return 1;
        }

        $env = EnvFile::read($this->getEnvPath());
        $this->setEnvValues($env);
        $env->write();

        $this->updateConfig();


        $this->info('.env configuration file has been created.');

        return 0;
    }

    /**
     * Get the full path of the env file
     * @return string
     */
    protected function getEnvPath(): string
    {
        return base_path($this->envFile);
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
     * @param EnvFile $env
     * @return void
     */
    protected function setEnvValues(EnvFile $env): void
    {
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
            }
            $env->addNewLine();
        }
    }

    /**
     * @param $line
     */
    protected function writeDbEnvSettings($line)
    {
        if ($this->connection == config('database.default') || $this->connection == 'redis') {
            $this->writeToEnv($line);
        }
    }

    /**
     * @param $configKey
     * @return string
     */
    protected function envValue($configKey)
    {
        $value = config("$this->config.$configKey");

        if ($this->config == 'database') {
            $value = $this->databaseConfigValue($configKey);
        }

        return $value;
    }

    /**
     * @param $configKey
     * @return string
     */
    protected function databaseConfigValue($configKey)
    {
        if ($configKey == 'default') {
            return config('database.default');
        }

        if ($this->connection == 'redis') {
            return config("database.redis.default.$configKey");
        }

        return config("database.connections.$this->connection.$configKey");
    }

    /**
     * Normalizes a value to be inserted into the .env file
     *
     * @param $value
     * @return string
     */
    protected function normalizeForEnv($value)
    {
        if (is_string($value)) {
            if (preg_match('/["\'#]/', $value)) {
                return '"' . str_replace('"', '\\"', $value) . '"';
            } else {
                return $value;
            }
            $conf->write();
        }
    }

    /**
     * Return an array used to generate the arguments for an env function call with protection of specific keys
     * @param string $envConfig
     * @param string $path
     * @return array
     */
    protected function getEnvArgs(string $envConfig, string $path): array
    {
        return [$envConfig, in_array($envConfig, $this->protectedKeys) ? '' : config($path)];
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
                'MAIL_DRIVER' => 'driver',
                'MAIL_HOST' => 'host',
                'MAIL_PORT' => 'port',
                'MAIL_USERNAME' => 'username',
                'MAIL_PASSWORD' => 'password',
                'MAIL_ENCRYPTION' => 'encryption',
            ],
            'cms' => [
                'ROUTES_CACHE' => 'enableRoutesCache',
                'ASSET_CACHE' => 'enableAssetCache',
                'LINK_POLICY' => 'linkPolicy',
                'ENABLE_CSRF' => 'enableCsrfProtection',
                'DATABASE_TEMPLATES' => 'databaseTemplates'
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
}
