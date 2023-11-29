<?php

namespace System\Tests\Bootstrap;

use System\Classes\UpdateManager;
use System\Classes\VersionManager;
use System\Classes\PluginManager;
use ReflectionClass;

use Winter\Storm\Database\Model as ActiveRecord;

class PluginManagerTestCase extends TestCase
{
    public ?PluginManager $manager = null;
    protected $output;

    /**
     * Creates the application.
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        /*
         * Store database in memory by default unless specified otherwise
         */
        if (!file_exists(base_path('config/testing/database.php'))) {
            $app['config']->set('database.connections.testing', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
            ]);
            $app['config']->set('database.default', 'testing');
        }

        return $app;
    }

    /**
     * Perform test case set up.
     * @return void
     */
    public function setUp() : void
    {
        /*
         * Force reload of Winter singletons
         */
        PluginManager::forgetInstance();
        UpdateManager::forgetInstance();

        // Forces plugin migrations to be run again on every test
        VersionManager::forgetInstance();

        $this->output = new \Symfony\Component\Console\Output\BufferedOutput();

        parent::setUp();

        /*
         * Ensure system is up to date
         */
        $this->runWinterUpCommand();

        $manager = PluginManager::instance();
        self::callProtectedMethod($manager, 'init');
        $this->manager = $manager;
    }

    /**
     * Flush event listeners and collect garbage.
     * @return void
     */
    public function tearDown() : void
    {
        $this->flushModelEventListeners();
        parent::tearDown();
        unset($this->app);
    }

    /**
     * Migrate database using winter:up command.
     * @return void
     */
    protected function runWinterUpCommand()
    {
        UpdateManager::instance()
            ->setNotesOutput($this->output)
            ->update();
    }

    /**
     * The models in Winter use a static property to store their events, these
     * will need to be targeted and reset ready for a new test cycle.
     * Pivot models are an exception since they are internally managed.
     * @return void
     */
    protected function flushModelEventListeners()
    {
        foreach (get_declared_classes() as $class) {
            if ($class === 'Winter\Storm\Database\Pivot' || strtolower($class) === 'october\rain\database\pivot') {
                continue;
            }

            $reflectClass = new ReflectionClass($class);
            if (
                !$reflectClass->isInstantiable() ||
                !$reflectClass->isSubclassOf('Winter\Storm\Database\Model') ||
                $reflectClass->isSubclassOf('Winter\Storm\Database\Pivot')
            ) {
                continue;
            }

            $class::flushEventListeners();
        }

        ActiveRecord::flushEventListeners();
    }
}