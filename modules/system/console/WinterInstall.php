<?php namespace System\Console;

use Backend\Database\Seeds\SeedSetupAdmin;
use Config;
use Db;
use Exception;
use File;
use Illuminate\Encryption\Encrypter;
use PDO;
use Str;
use Symfony\Component\Console\Input\InputOption;
use System\Classes\UpdateManager;
use Winter\Storm\Config\ConfigWriter;
use Winter\Storm\Console\Command;

/**
 * Console command to install Winter.
 *
 * This sets up Winter for the first time. It will prompt the user for several
 * configuration items, including application URL and database config, and then
 * perform a database migration.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class WinterInstall extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * The console command name.
     */
    protected $name = 'winter:install';

    /**
     * The console command description.
     */
    protected $description = 'Set up Winter for the first time.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'october:install',
    ];

    /**
     * @var Winter\Storm\Config\ConfigWriter
     */
    protected $configWriter;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->configWriter = new ConfigWriter;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayIntro();

        if (
            $this->laravel->hasDatabase() &&
            !$this->confirm('Application appears to be installed already. Continue anyway?', false)
        ) {
            return;
        }

        $this->setupDatabaseConfig();
        $this->setupAdminUser();
        $this->setupCommonValues();

        $chosenToInstall = [];

        if ($this->confirm('Would you like to change the backend options? (URL, default locale, default timezone)')) {
            $this->setupBackendValues();
        }

        if ($this->confirm('Configure advanced options?', false)) {
            $this->setupEncryptionKey();
            $this->setupAdvancedValues();
            $chosenToInstall = $this->askToInstallPlugins();
        }
        else {
            $this->setupEncryptionKey(true);
        }

        $this->setupMigrateDatabase();

        foreach ($chosenToInstall as $pluginCode) {
            $this->output->writeln('<info>Installing plugin ' . $pluginCode . '</info>');
            $this->callSilent('plugin:install', [
                'plugin' => $pluginCode
            ]);
            $this->output->writeln('<info>' . $pluginCode . ' installed successfully.</info>');
        }

        $this->displayOutro();
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run.'],
        ];
    }

    //
    // Misc
    //

    protected function setupCommonValues()
    {
        $url = $this->ask('Application URL', Config::get('app.url'));
        $this->writeToConfig('app', ['url' => $url]);
    }

    protected function setupBackendValues()
    {
        // cms.backendUri
        $backendUri = $this->ask('Backend URL', Config::get('cms.backendUri'));
        $this->writeToConfig('cms', ['backendUri' => $backendUri]);

        // app.locale
        $defaultLocale = Config::get('app.locale');
        try {
            $availableLocales = (new \Backend\Models\Preference)->getLocaleOptions();
            $localesByName = [];
            $i = $defaultLocaleIndex = 0;
            foreach ($availableLocales as $locale => $name) {
                $localesByName[$name[0]] = $locale;
                if ($locale === $defaultLocale) {
                    $defaultLocaleIndex = $i;
                }
                $i++;
            }

            $localeName = $this->choice('Default Backend Locale', array_keys($localesByName), $defaultLocaleIndex);

            $locale = $localesByName[$localeName];
        } catch (\Exception $e) {
            // Installation failed halfway through, recover gracefully
            $locale = $this->ask('Default Backend Locale', $defaultLocale);
        }
        $this->writeToConfig('app', ['locale' => $locale]);

        // cms.backendTimezone
        $defaultTimezone = Config::get('cms.backendTimezone');
        try {
            $availableTimezones = (new \Backend\Models\Preference)->getTimezoneOptions();
            $longestTimezone = max(array_map('strlen', $availableTimezones));
            $padTo = $longestTimezone - 13 + 1; // (UTC +10:00)
            $timezonesByName = [];

            foreach ($availableTimezones as $timezone => $name) {
                $nameParts = explode(') ', $name);
                $nameParts[0] .= ')';

                $name = str_pad($nameParts[1], $padTo) . $nameParts[0];

                $timezonesByName[$name] = $timezone;
            }

            $timezonesByContinent = [];
            $defaultTimezoneIndex = 0;
            $defaultTimezoneGroupIndex = 0;
            foreach ($timezonesByName as $name => $zone) {
                $zone = explode('/', $zone)[0];
                $timezonesByContinent[$zone][$name] = $zone;
                if ($zone === $defaultTimezone) {
                    $defaultTimezoneGroupIndex = count(array_keys($timezonesByContinent)) - 1;
                    $defaultTimezoneIndex = count(array_keys($timezonesByContinent[$zone])) - 1;
                }
            }

            $timezoneGroup = $this->choice('Timezone Continent', array_keys($timezonesByContinent), $defaultTimezoneGroupIndex);

            $timezoneName = $this->choice('Default Backend Timezone', array_keys($timezonesByContinent[$timezoneGroup]), $defaultTimezoneIndex);

            $timezone = $timezonesByName[$timezoneName];
        } catch (\Exception $e) {
            // Installation failed halfway through, recover gracefully
            $timezone = $this->ask('Default Backend Timezone', $defaultTimezone);
        }
        $this->writeToConfig('cms', ['backendTimezone' => $timezone]);
    }

    protected function setupAdvancedValues()
    {
        $defaultMask = $this->ask('File Permission Mask', Config::get('cms.defaultMask.file') ?: '777');
        $this->writeToConfig('cms', ['defaultMask.file' => $defaultMask]);

        $defaultMask = $this->ask('Folder Permission Mask', Config::get('cms.defaultMask.folder') ?: '777');
        $this->writeToConfig('cms', ['defaultMask.folder' => $defaultMask]);

        $debug = (bool) $this->confirm('Enable Debug Mode?', true);
        $this->writeToConfig('app', ['debug' => $debug]);
    }

    protected function askToInstallPlugins()
    {
        $chosenToInstall = [];
        if ($this->confirm('Install the Winter.Builder plugin?', false)) {
            $chosenToInstall[] = 'Winter.Builder';
        }
        return $chosenToInstall;
    }

    //
    // Encryption key
    //

    protected function setupEncryptionKey($force = false)
    {
        $validKey = false;
        $cipher = Config::get('app.cipher');
        $keyLength = $this->getKeyLength($cipher);
        $randomKey = $this->getRandomKey($cipher);

        if ($force) {
            $key = $randomKey;
        }
        else {
            $this->line(sprintf('Enter a new value of %s characters, or press ENTER to use the generated key', $keyLength));

            while (!$validKey) {
                $key = $this->ask('Application key', $randomKey);
                $validKey = Encrypter::supported($key, $cipher);
                if (!$validKey) {
                    $this->error(sprintf('[ERROR] Invalid key length for "%s" cipher. Supplied key must be %s characters in length.', $cipher, $keyLength));
                }
            }
        }

        $this->writeToConfig('app', ['key' => $key]);

        $this->info(sprintf('Application key [%s] set successfully.', $key));
    }

    /**
     * Generate a random key for the application.
     *
     * @param  string  $cipher
     * @return string
     */
    protected function getRandomKey($cipher)
    {
        return Str::random($this->getKeyLength($cipher));
    }

    /**
     * Returns the supported length of a key for a cipher.
     *
     * @param  string  $cipher
     * @return int
     */
    protected function getKeyLength($cipher)
    {
        return $cipher === 'AES-128-CBC' ? 16 : 32;
    }

    //
    // Database config
    //

    protected function setupDatabaseConfig()
    {
        $type = $this->choice('Database type', ['MySQL', 'Postgres', 'SQLite', 'SQL Server'], 'SQLite');

        $typeMap = [
            'SQLite' => 'sqlite',
            'MySQL' => 'mysql',
            'Postgres' => 'pgsql',
            'SQL Server' => 'sqlsrv',
        ];

        $driver = array_get($typeMap, $type, 'sqlite');

        $method = 'setupDatabase'.Str::studly($driver);

        $newConfig = $this->$method();

        $this->writeToConfig('database', ['default' => $driver]);

        foreach ($newConfig as $config => $value) {
            $this->writeToConfig('database', ['connections.'.$driver.'.'.$config => $value]);
        }
    }

    protected function setupDatabaseMysql()
    {
        $result = [];
        $result['host'] = $this->ask('MySQL Host', Config::get('database.connections.mysql.host'));
        $result['port'] = $this->output->ask('MySQL Port', Config::get('database.connections.mysql.port') ?: false) ?: '';
        $result['database'] = $this->ask('Database Name', Config::get('database.connections.mysql.database'));
        $result['username'] = $this->ask('MySQL Login', Config::get('database.connections.mysql.username'));
        $result['password'] = $this->ask('MySQL Password', Config::get('database.connections.mysql.password') ?: false) ?: '';
        return $result;
    }

    protected function setupDatabasePgsql()
    {
        $result = [];
        $result['host'] = $this->ask('Postgres Host', Config::get('database.connections.pgsql.host'));
        $result['port'] = $this->ask('Postgres Port', Config::get('database.connections.pgsql.port') ?: false) ?: '';
        $result['database'] = $this->ask('Database Name', Config::get('database.connections.pgsql.database'));
        $result['username'] = $this->ask('Postgres Login', Config::get('database.connections.pgsql.username'));
        $result['password'] = $this->ask('Postgres Password', Config::get('database.connections.pgsql.password') ?: false) ?: '';
        return $result;
    }

    protected function setupDatabaseSqlite()
    {
        $filename = $this->ask('Database path', Config::get('database.connections.sqlite.database'));

        try {
            if (!file_exists($filename)) {
                $directory = dirname($filename);
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }

                new PDO('sqlite:'.$filename);
            }
        }
        catch (Exception $ex) {
            $this->error($ex->getMessage());
            $this->setupDatabaseSqlite();
        }

        return ['database' => Str::after($filename, base_path() . DIRECTORY_SEPARATOR)];
    }

    protected function setupDatabaseSqlsrv()
    {
        $result = [];
        $result['host'] = $this->ask('SQL Host', Config::get('database.connections.sqlsrv.host'));
        $result['port'] = $this->ask('SQL Port', Config::get('database.connections.sqlsrv.port') ?: false) ?: '';
        $result['database'] = $this->ask('Database Name', Config::get('database.connections.sqlsrv.database'));
        $result['username'] = $this->ask('SQL Login', Config::get('database.connections.sqlsrv.username'));
        $result['password'] = $this->ask('SQL Password', Config::get('database.connections.sqlsrv.password') ?: false) ?: '';
        return $result;
    }

    //
    // Migration
    //

    protected function setupAdminUser()
    {
        $this->line('Enter a new value, or press ENTER for the default');

        SeedSetupAdmin::$firstName = $this->ask('First Name', SeedSetupAdmin::$firstName);
        SeedSetupAdmin::$lastName = $this->ask('Last Name', SeedSetupAdmin::$lastName);
        SeedSetupAdmin::$email = $this->ask('Email Address', SeedSetupAdmin::$email);
        SeedSetupAdmin::$login = $this->ask('Admin Login', SeedSetupAdmin::$login);
        SeedSetupAdmin::$password = $this->ask('Admin Password', Str::random(22));

        if (!$this->confirm('Is the information correct?', true)) {
            $this->setupAdminUser();
        }
    }

    protected function setupMigrateDatabase()
    {
        $this->line('Migrating application and plugins...');

        try {
            Db::purge();

            UpdateManager::instance()
                ->setNotesOutput($this->output)
                ->update()
            ;
        }
        catch (Exception $ex) {
            $this->error($ex->getMessage());
            $this->setupDatabaseConfig();
            $this->setupMigrateDatabase();
        }
    }

    //
    // Helpers
    //

    protected function displayIntro()
    {
        $message = [
            ".========================================================================.",
            "                                                                          ",
            " db   d8b   db d888888b d8b   db d888888b d88888b d8888b.       \033[1;34m...\033[0m ",
            " 88   I8I   88   `88'   888o  88 `~~88~~' 88'     88  `8D  \033[1;34m... ..... ...\033[0m ",
            " 88   I8I   88    88    88V8o 88    88    88ooooo 88oobY'    \033[1;34m.. ... ..\033[0m ",
            " Y8   I8I   88    88    88 V8o88    88    88~~~~~ 88`8b      \033[1;34m.. ... ..\033[0m ",
            " `8b d8'8b d8'   .88.   88  V888    88    88.     88 `88.  \033[1;34m... ..... ...\033[0m ",
            "  `8b8' `8d8'  Y888888P VP   V8P    YP    Y88888P 88   YD       \033[1;34m...\033[0m ",
            "                                                                         ",
            "`============================= INSTALLATION =============================",
            "",
        ];

        $this->line($message);
    }

    protected function displayOutro()
    {
        $message = [
            // Sourced from https://www.asciiart.eu/holiday-and-events/christmas/snowmen
            ".===========================================================.",
            " *    *           *.   *   .                      *     .    ",
            "         .   .               __   *    .     * .     *       ",
            "      *         *   . .    _|__|_        *    __   .       * ",
            "  /\       /\               ('')    *       _|__|_     .     ",
            " /  \   * /  \  *      .  <( . )> *  .       ('')   *   *    ",
            " /  \     /  \   .       _(__.__)_  _   ,--<(  . )>  .    .  ",
            "/    \   /    \      *   |       |  )),`   (   .  )     *    ",
            " `||` ..  `||`   . *... ==========='`   ... '--`-` ... * jb .",
            "`================== INSTALLATION COMPLETE =================='",
            "",
        ];

        $this->line($message);
    }

    protected function writeToConfig($file, $values)
    {
        $configFile = $this->getConfigFile($file);

        foreach ($values as $key => $value) {
            Config::set($file.'.'.$key, $value);
        }

        $this->configWriter->toFile($configFile, $values);
    }

    /**
     * Get a config file and contents.
     *
     * @return array
     */
    protected function getConfigFile($name = 'app')
    {
        $env = $this->option('env') ? $this->option('env').'/' : '';

        $name .= '.php';

        $contents = File::get($path = $this->laravel['path.config']."/{$env}{$name}");

        return $path;
    }
}
