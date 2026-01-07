<?php

namespace System\Tests\Console;

use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Foundation\Bootstrap\LoadConfiguration;

class WinterEnvTest extends TestCase
{
    /** @var bool If the config fixtures have been copied */
    public static $fixturesCopied = false;

    /** @var string Stores the original config path from the app container */
    public static $origConfigPath;

    /** @var string Stores the original environment path from the app container */
    public static $origEnvPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpConfigFixtures();
    }

    public function testCommand()
    {
        $this->artisan('winter:env')
            ->assertExitCode(0);

        // Check environment file
        $envFile = file_get_contents($this->app->environmentFilePath());

        $this->assertStringContainsString('APP_DEBUG=true', $envFile);
        $this->assertStringContainsString('APP_URL="https://env-test.localhost"', $envFile);
        $this->assertStringContainsString('DB_CONNECTION="mysql"', $envFile);
        $this->assertStringContainsString('DB_DATABASE="data#base"', $envFile);
        $this->assertStringContainsString('DB_USERNAME="teal\'c"', $envFile);
        $this->assertStringContainsString('DB_PASSWORD="test\\"quotes\'test"', $envFile);
        $this->assertStringContainsString('DB_PORT=3306', $envFile);

        // Check app.php config file
        $appConfigFile = file_get_contents(storage_path('temp/tests/config/app.php'));

        $this->assertStringContainsString('\'debug\' => env(\'APP_DEBUG\', true),', $appConfigFile);
        $this->assertStringContainsString('\'url\' => env(\'APP_URL\', \'https://env-test.localhost\'),', $appConfigFile);

        // Check database.php config file
        $appConfigFile = file_get_contents(storage_path('temp/tests/config/database.php'));

        $this->assertStringContainsString('\'default\' => env(\'DB_CONNECTION\', \'mysql\')', $appConfigFile);
        $this->assertStringContainsString('\'port\' => env(\'DB_PORT\', 3306),', $appConfigFile);
        // Both the following configurations had values in the original config, they should be stripped out once
        // the .env file is generated.
        $this->assertStringContainsString('\'username\' => env(\'DB_USERNAME\', \'\'),', $appConfigFile);
        $this->assertStringContainsString('\'password\' => env(\'DB_PASSWORD\', \'\'),', $appConfigFile);
    }

    protected function tearDown(): void
    {
        $this->tearDownConfigFixtures();

        parent::tearDown();
    }

    protected function setUpConfigFixtures()
    {
        // Mock config path and copy fixtures
        if (!is_dir(storage_path('temp/tests/config'))) {
            mkdir(storage_path('temp/tests/config'), 0777, true);
        }
        if (!is_dir(storage_path('temp/tests/env'))) {
            mkdir(storage_path('temp/tests/env'), 0777, true);
        }

        foreach (glob(base_path('modules/system/tests/fixtures/config/*.php')) as $file) {
            $path = pathinfo($file);
            copy($file, storage_path('temp/tests/config/' . $path['basename']));
        }

        static::$fixturesCopied = true;

        // Store original config path
        static::$origConfigPath = $this->app->make('path.config');
        static::$origEnvPath = $this->app->environmentPath();

        $this->app->instance('path.config', storage_path('temp/tests/config'));
        $this->app->useEnvironmentPath(storage_path('temp/tests/env'));

        // Re-load configuration
        $configBootstrap = new LoadConfiguration;
        $configBootstrap->bootstrap($this->app);
    }

    protected function tearDownConfigFixtures()
    {
        // Remove copied config fixtures
        if (static::$fixturesCopied) {
            foreach (glob(storage_path('temp/tests/config/*.php')) as $file) {
                unlink($file);
            }
            rmdir(storage_path('temp/tests/config'));
            unlink(storage_path('temp/tests/env/.env'));
            rmdir(storage_path('temp/tests/env'));
            rmdir(storage_path('temp/tests'));

            static::$fixturesCopied = false;
        }

        // Restore config path
        if (self::$origConfigPath) {
            $this->app->instance('path.config', static::$origConfigPath);
            static::$origConfigPath = null;
        }

        // Restore environment path
        if (self::$origEnvPath) {
            $this->app->useEnvironmentPath(static::$origEnvPath);
            static::$origEnvPath = null;
        }

        // Re-load configuration
        $configBootstrap = new LoadConfiguration;
        $configBootstrap->bootstrap($this->app);
    }
}
