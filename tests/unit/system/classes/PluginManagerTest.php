<?php
use System\Classes\PluginManager;

class PluginManagerTest extends TestCase
{
    public $manager;

    public function setUp() : void
    {
        parent::setUp();

        $manager = PluginManager::instance();
        self::callProtectedMethod($manager, 'loadDisabled');
        $manager->loadPlugins();
        self::callProtectedMethod($manager, 'loadDependencies');

        $this->manager = $manager;
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
}
