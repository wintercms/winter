<?php
use System\Classes\PluginManager;
use System\Classes\UpdateManager;
use System\Classes\VersionManager;

use Winter\Storm\Database\Model as ActiveRecord;

class PluginManagerTest extends TestCase
{
    public $manager;
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
        self::callProtectedMethod($manager, 'loadDisabled');
        $manager->loadPlugins();
        self::callProtectedMethod($manager, 'loadDependencies');
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

    //
    // Tests
    //

    public function testLoadPlugins()
    {
        $result = $this->manager->loadPlugins();

        $this->assertCount(12, $result);
        $this->assertArrayHasKey('Winter.NoUpdates', $result);
        $this->assertArrayHasKey('Winter.Sample', $result);
        $this->assertArrayHasKey('Winter.Tester', $result);
        $this->assertArrayHasKey('Database.Tester', $result);
        $this->assertArrayHasKey('TestVendor.Test', $result);
        $this->assertArrayHasKey('DependencyTest.Found', $result);
        $this->assertArrayHasKey('DependencyTest.NotFound', $result);
        $this->assertArrayHasKey('DependencyTest.WrongCase', $result);
        $this->assertArrayHasKey('DependencyTest.Dependency', $result);

        $this->assertArrayNotHasKey('TestVendor.Goto', $result);

        $this->assertInstanceOf('Winter\NoUpdates\Plugin', $result['Winter.NoUpdates']);
        $this->assertInstanceOf('Winter\Sample\Plugin', $result['Winter.Sample']);
        $this->assertInstanceOf('Winter\Tester\Plugin', $result['Winter.Tester']);
        $this->assertInstanceOf('Database\Tester\Plugin', $result['Database.Tester']);
        $this->assertInstanceOf('TestVendor\Test\Plugin', $result['TestVendor.Test']);
        $this->assertInstanceOf('DependencyTest\Found\Plugin', $result['DependencyTest.Found']);
        $this->assertInstanceOf('DependencyTest\NotFound\Plugin', $result['DependencyTest.NotFound']);
        $this->assertInstanceOf('DependencyTest\WrongCase\Plugin', $result['DependencyTest.WrongCase']);
        $this->assertInstanceOf('DependencyTest\Dependency\Plugin', $result['DependencyTest.Dependency']);
    }

    public function testUnloadablePlugin()
    {
        $pluginNamespaces = $this->manager->getPluginNamespaces();
        $result = $this->manager->loadPlugin('\\testvendor\\goto', $pluginNamespaces['\\testvendor\\goto']);
        $this->assertNull($result);
    }

    public function testGetPluginPath()
    {
        $result = $this->manager->getPluginPath('Winter\Tester');
        $basePath = str_replace('\\', '/', base_path());
        $this->assertEquals($basePath . '/tests/fixtures/plugins/winter/tester', $result);
    }

    public function testGetPlugins()
    {
        $result = $this->manager->getPlugins();

        $this->assertCount(9, $result);
        $this->assertArrayHasKey('Winter.NoUpdates', $result);
        $this->assertArrayHasKey('Winter.Sample', $result);
        $this->assertArrayHasKey('Winter.Tester', $result);
        $this->assertArrayHasKey('Database.Tester', $result);
        $this->assertArrayHasKey('TestVendor.Test', $result);
        $this->assertArrayHasKey('DependencyTest.Found', $result);
        $this->assertArrayHasKey('DependencyTest.WrongCase', $result);
        $this->assertArrayHasKey('DependencyTest.Dependency', $result);

        $this->assertArrayNotHasKey('DependencyTest.NotFound', $result);
        $this->assertArrayNotHasKey('TestVendor.Goto', $result);

        $this->assertInstanceOf('Winter\NoUpdates\Plugin', $result['Winter.NoUpdates']);
        $this->assertInstanceOf('Winter\Sample\Plugin', $result['Winter.Sample']);
        $this->assertInstanceOf('Winter\Tester\Plugin', $result['Winter.Tester']);
        $this->assertInstanceOf('Database\Tester\Plugin', $result['Database.Tester']);
        $this->assertInstanceOf('TestVendor\Test\Plugin', $result['TestVendor.Test']);
        $this->assertInstanceOf('DependencyTest\Found\Plugin', $result['DependencyTest.Found']);
        $this->assertInstanceOf('DependencyTest\WrongCase\Plugin', $result['DependencyTest.WrongCase']);
        $this->assertInstanceOf('DependencyTest\Dependency\Plugin', $result['DependencyTest.Dependency']);
    }

    public function testFindByNamespace()
    {
        $result = $this->manager->findByNamespace('Winter\Tester');
        $this->assertInstanceOf('Winter\Tester\Plugin', $result);
    }

    public function testHasPlugin()
    {
        $result = $this->manager->hasPlugin('Winter\Tester');
        $this->assertTrue($result);

        $result = $this->manager->hasPlugin('DependencyTest.Found');
        $this->assertTrue($result);

        $result = $this->manager->hasPlugin('DependencyTest\WrongCase');
        $this->assertTrue($result);

        $result = $this->manager->hasPlugin('DependencyTest\NotFound');
        $this->assertTrue($result);

        $result = $this->manager->hasPlugin('Winter\XXXXX');
        $this->assertFalse($result);

        /**
         * Test case for https://github.com/octobercms/october/pull/4337
         */
        $result = $this->manager->hasPlugin('dependencyTest\Wrongcase');
        $this->assertTrue($result);

        $result = $this->manager->hasPlugin('dependencyTest.Wrongcase');
        $this->assertTrue($result);
    }

    public function testGetPluginNamespaces()
    {
        $result = $this->manager->getPluginNamespaces();

        $this->assertCount(13, $result);
        $this->assertArrayHasKey('\winter\noupdates', $result);
        $this->assertArrayHasKey('\winter\sample', $result);
        $this->assertArrayHasKey('\winter\tester', $result);
        $this->assertArrayHasKey('\database\tester', $result);
        $this->assertArrayHasKey('\testvendor\test', $result);
        $this->assertArrayHasKey('\testvendor\goto', $result);
        $this->assertArrayHasKey('\dependencytest\found', $result);
        $this->assertArrayHasKey('\dependencytest\notfound', $result);
        $this->assertArrayHasKey('\dependencytest\wrongcase', $result);
        $this->assertArrayHasKey('\dependencytest\dependency', $result);
    }

    public function testGetVendorAndPluginNames()
    {
        $vendors = $this->manager->getVendorAndPluginNames();

        $this->assertCount(4, $vendors);
        $this->assertArrayHasKey('winter', $vendors);
        $this->assertArrayHasKey('noupdates', $vendors['winter']);
        $this->assertArrayHasKey('sample', $vendors['winter']);
        $this->assertArrayHasKey('tester', $vendors['winter']);

        $this->assertArrayHasKey('database', $vendors);
        $this->assertArrayHasKey('tester', $vendors['database']);

        $this->assertArrayHasKey('testvendor', $vendors);
        $this->assertArrayHasKey('test', $vendors['testvendor']);
        $this->assertArrayHasKey('goto', $vendors['testvendor']);

        $this->assertArrayHasKey('dependencytest', $vendors);
        $this->assertArrayHasKey('found', $vendors['dependencytest']);
        $this->assertArrayHasKey('notfound', $vendors['dependencytest']);
        $this->assertArrayHasKey('wrongcase', $vendors['dependencytest']);
        $this->assertArrayHasKey('dependency', $vendors['dependencytest']);
    }

    public function testPluginDetails()
    {
        $testPlugin = $this->manager->findByNamespace('Winter\XXXXX');
        $this->assertNull($testPlugin);

        $testPlugin = $this->manager->findByNamespace('Winter\Tester');
        $this->assertNotNull($testPlugin);
        $pluginDetails = $testPlugin->pluginDetails();

        $this->assertEquals('Winter Test Plugin', $pluginDetails['name']);
        $this->assertEquals('Test plugin used by unit tests.', $pluginDetails['description']);
        $this->assertEquals('Alexey Bobkov, Samuel Georges', $pluginDetails['author']);
    }

    public function testUnregisterall()
    {
        $result = $this->manager->getPlugins();
        $this->assertCount(9, $result);

        $this->manager->unregisterAll();
        $this->assertEmpty($this->manager->getPlugins());
    }

    public function testGetDependencies()
    {
        $result = $this->manager->getDependencies('DependencyTest.Found');
        $this->assertCount(1, $result);
        $this->assertContains('DependencyTest.Dependency', $result);

        $result = $this->manager->getDependencies('DependencyTest.WrongCase');
        $this->assertCount(1, $result);
        $this->assertContains('Dependencytest.dependency', $result);

        $result = $this->manager->getDependencies('DependencyTest.NotFound');
        $this->assertCount(1, $result);
        $this->assertContains('DependencyTest.Missing', $result);
    }

    public function testIsDisabled()
    {
        $result = $this->manager->isDisabled('DependencyTest.Found');
        $this->assertFalse($result);

        $result = $this->manager->isDisabled('DependencyTest.WrongCase');
        $this->assertFalse($result);

        $result = $this->manager->isDisabled('DependencyTest.NotFound');
        $this->assertTrue($result);

        /**
         * Test case for https://github.com/octobercms/october/pull/4838
         */
        $result = $this->manager->isDisabled('dependencyTest\Wrongcase');
        $this->assertFalse($result);

        $result = $this->manager->isDisabled('dependencyTest.Wrongcase');
        $this->assertFalse($result);

        $result = $this->manager->isDisabled('dependencytest.notfound');
        $this->assertTrue($result);
    }

    public function testExists()
    {
        $result = $this->manager->exists('DependencyTest.Found');
        $this->assertTrue($result);

        $result = $this->manager->exists('DependencyTest.WrongCase');
        $this->assertTrue($result);

        $result = $this->manager->exists('DependencyTest.NotFound');
        $this->assertFalse($result);

        $result = $this->manager->exists('Unknown.Plugin');
        $this->assertFalse($result);
    }

    public function testReplacement()
    {
        $this->assertFalse($this->manager->isDisabled('Winter.Replacement'));
        $this->assertTrue($this->manager->isDisabled('Winter.Original'));
        $this->assertTrue($this->manager->isDisabled('Winter.InvalidReplacement'));

        $this->assertEquals('Winter.Replacement', $this->manager->findByIdentifier('Winter.Original')->getPluginIdentifier());
    }

    public function testGetReplacements()
    {
        $replacementPluginReplaces = $this->manager->findByIdentifier('Winter.Replacement')->getReplaces();

        $this->assertIsArray($replacementPluginReplaces);
        $this->assertCount(1, $replacementPluginReplaces);
        $this->assertEquals('Winter.Original', $replacementPluginReplaces[0]);

        $invalidPluginReplaces = $this->manager->findByIdentifier('Winter.InvalidReplacement')->getReplaces();

        $this->assertIsArray($invalidPluginReplaces);
        $this->assertCount(1, $invalidPluginReplaces);
        $this->assertEquals('Winter.Tester', $invalidPluginReplaces[0]);
    }

    public function testReplaceVersion()
    {
        $invalidReplacementPlugin = $this->manager->findByIdentifier('Winter.InvalidReplacement');

        $this->assertTrue($invalidReplacementPlugin->canReplacePlugin('Winter.Tester', '9'));
        $this->assertTrue($invalidReplacementPlugin->canReplacePlugin('Winter.Tester', '9.0'));
        $this->assertTrue($invalidReplacementPlugin->canReplacePlugin('Winter.Tester', '11.0.0'));
        $this->assertFalse($invalidReplacementPlugin->canReplacePlugin('Winter.Tester', '8.0'));

        $replacementPlugin = $this->manager->findByIdentifier('Winter.Replacement');

        $this->assertTrue($replacementPlugin->canReplacePlugin('Winter.Original', '1.0.2'));
        $this->assertTrue($replacementPlugin->canReplacePlugin('Winter.Original', '1.0'));
        $this->assertFalse($replacementPlugin->canReplacePlugin('Winter.Original', '2.0.1'));
    }

    public function testActiveReplacementMap()
    {
        $map = $this->manager->getActiveReplacementMap();
        $this->assertArrayHasKey('Winter.Original', $map);
        $this->assertEquals('Winter.Replacement', $map['Winter.Original']);

        $this->assertEquals('Winter.Replacement', $this->manager->getActiveReplacementMap('Winter.Original'));
        $this->assertNull($this->manager->getActiveReplacementMap('Winter.InvalidReplacement'));
    }

    public function testFlagDisableStatus()
    {
        $plugin = $this->manager->findByIdentifier('DependencyTest.Dependency');
        $flags = $this->manager->getPluginFlags($plugin);
        $this->assertEmpty($flags);

        $plugin = $this->manager->findByIdentifier('DependencyTest.NotFound');
        $flags = $this->manager->getPluginFlags($plugin);
        $this->assertCount(1, $flags);
        $this->assertArrayHasKey(PluginManager::DISABLED_MISSING_DEPENDENCIES, $flags);

        $plugin = $this->manager->findByIdentifier('Winter.InvalidReplacement');
        $flags = $this->manager->getPluginFlags($plugin);
        $this->assertCount(1, $flags);
        $this->assertArrayHasKey(PluginManager::DISABLED_REPLACEMENT_FAILED, $flags);

        $plugin = $this->manager->findByIdentifier('Winter.Original', true);
        $flags = $this->manager->getPluginFlags($plugin);
        $this->assertCount(1, $flags);
        $this->assertArrayHasKey(PluginManager::DISABLED_REPLACED, $flags);
    }

    public function testFlagDisabling()
    {
        $plugin = $this->manager->findByIdentifier('Winter.Tester', true);

        $flags = $this->manager->getPluginFlags($plugin);
        $this->assertEmpty($flags);

        $this->manager->disablePlugin($plugin);

        $flags = $this->manager->getPluginFlags($plugin);
        $this->assertCount(1, $flags);
        $this->assertArrayHasKey(PluginManager::DISABLED_BY_USER, $flags);

        $this->manager->enablePlugin($plugin);

        $flags = $this->manager->getPluginFlags($plugin);
        $this->assertEmpty($flags);

        $this->manager->disablePlugin($plugin, PluginManager::DISABLED_BY_CONFIG);

        $flags = $this->manager->getPluginFlags($plugin);
        $this->assertCount(1, $flags);
        $this->assertArrayHasKey(PluginManager::DISABLED_BY_CONFIG, $flags);

        $this->manager->enablePlugin($plugin, PluginManager::DISABLED_BY_CONFIG);

        $flags = $this->manager->getPluginFlags($plugin);
        $this->assertEmpty($flags);
    }
}
