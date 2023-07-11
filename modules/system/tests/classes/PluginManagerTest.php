<?php

namespace System\Tests\Classes;

use System\Tests\Bootstrap\PluginManagerTestCase;
use System\Classes\PluginManager;
use System\Classes\PluginBase;

class PluginManagerTest extends PluginManagerTestCase
{
    const INSTALLED_PLUGIN_COUNT = 17;
    const ENABLED_PLUGIN_COUNT = 14;
    const PLUGIN_NAMESPACE_COUNT = 18;
    const PLUGIN_VENDOR_COUNT = 5;

    //
    // Tests
    //

    public function testLoadPlugins()
    {
        $result = $this->manager->loadPlugins();

        $this->assertCount(static::INSTALLED_PLUGIN_COUNT, $result);
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
        $this->assertEquals($basePath . '/modules/system/tests/fixtures/plugins/winter/tester', $result);
    }

    public function testGetPlugins()
    {
        $result = $this->manager->getPlugins();

        $this->assertCount(static::ENABLED_PLUGIN_COUNT, $result);
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

        $this->assertCount(static::PLUGIN_NAMESPACE_COUNT, $result);
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

        $this->assertCount(static::PLUGIN_VENDOR_COUNT, $vendors);
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
        $this->assertCount(static::ENABLED_PLUGIN_COUNT, $result);

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

    public function testHasPluginReplacement()
    {
        // check a replaced plugin
        $this->assertTrue($this->manager->hasPlugin('Winter.Original'));
        $this->assertTrue($this->manager->isDisabled('Winter.Original'));
        // check a replacement plugin
        $this->assertTrue($this->manager->hasPlugin('Winter.Replacement'));
        $this->assertFalse($this->manager->isDisabled('Winter.Replacement'));
        // check a plugin where the replacement is invalid
        $this->assertTrue($this->manager->hasPlugin('Winter.InvalidReplacement'));
        $this->assertTrue($this->manager->isDisabled('Winter.InvalidReplacement'));
        // check a plugin replacing a plugin not found on disk
        $this->assertTrue($this->manager->hasPlugin('Winter.ReplaceNotInstalled'));
        $this->assertFalse($this->manager->isDisabled('Winter.ReplaceNotInstalled'));
        // ensure searching for the alias of a replacement (plugin not installed)
        $this->assertTrue($this->manager->hasPlugin('Winter.NotInstalled'));

        $this->assertInstanceOf(\Winter\Replacement\Plugin::class, $this->manager->findByIdentifier('Winter.Original'));
        $this->assertInstanceOf(\Winter\Replacement\Plugin::class, $this->manager->findByIdentifier('Winter.Replacement'));

        // check getting a plugin via it's not installed original plugin identifier
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('Winter.NotInstalled'));
        $this->assertNull($this->manager->findByIdentifier('Winter.NotInstalled', true));

        // force getting the original plugin
        $this->assertInstanceOf(\Winter\Original\Plugin::class, $this->manager->findByIdentifier('Winter.Original', true));
    }

    public function testHasPluginReplacementMixedCase()
    {
        // test checking casing of installed plugin (resolved via getNormalizedIdentifier())
        $this->assertTrue($this->manager->hasPlugin('Winter.ReplaceNotInstalled'));
        $this->assertTrue($this->manager->hasPlugin('Winter.replaceNotInstalled'));
        $this->assertTrue($this->manager->hasPlugin('Winter.replacenotInstalled'));
        $this->assertTrue($this->manager->hasPlugin('winter.replacenotInstalled'));
        $this->assertTrue($this->manager->hasPlugin('winter.replacenotinstalled'));

        // test checking casing of installed replaced plugin (resolved via getNormalizedIdentifier() & replacementMap)
        $this->assertTrue($this->manager->hasPlugin('Winter.Original'));
        $this->assertTrue($this->manager->hasPlugin('Winter.original'));
        $this->assertTrue($this->manager->hasPlugin('winter.original'));

        // test checking casing of uninstalled plugin (resolved via strtolower() on replacement keys)
        $this->assertTrue($this->manager->hasPlugin('Winter.NotInstalled'));
        $this->assertTrue($this->manager->hasPlugin('Winter.notInstalled'));
        $this->assertTrue($this->manager->hasPlugin('winter.notInstalled'));
        $this->assertTrue($this->manager->hasPlugin('Winter.notinstalled'));
    }

    public function testExistsReplacementMixedCase()
    {
        // test checking casing of installed plugin (resolved via getNormalizedIdentifier())
        $this->assertTrue($this->manager->exists('Winter.ReplaceNotInstalled'));
        $this->assertTrue($this->manager->exists('Winter.replaceNotInstalled'));
        $this->assertTrue($this->manager->exists('Winter.replacenotInstalled'));
        $this->assertTrue($this->manager->exists('winter.replacenotInstalled'));
        $this->assertTrue($this->manager->exists('winter.replacenotinstalled'));

        // test checking casing of installed replaced plugin (resolved via getNormalizedIdentifier() & replacementMap)
        $this->assertFalse($this->manager->exists('Winter.Original'));
        $this->assertFalse($this->manager->exists('Winter.original'));
        $this->assertFalse($this->manager->exists('winter.original'));

        // test checking casing of uninstalled plugin (resolved via strtolower() on replacement keys)
        $this->assertTrue($this->manager->exists('Winter.NotInstalled'));
        $this->assertTrue($this->manager->exists('Winter.notInstalled'));
        $this->assertTrue($this->manager->exists('winter.notInstalled'));
        $this->assertTrue($this->manager->exists('Winter.notinstalled'));
    }

    public function testFindByIdentifierReplacementMixedCase()
    {
        // test resolving plugin with mixed casing
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('Winter.ReplaceNotInstalled'));
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('Winter.replaceNotInstalled'));
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('Winter.replacenotInstalled'));
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('winter.replacenotInstalled'));
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('winter.replacenotinstalled'));

        // test resolving replacement plugin with mixed casing
        $this->assertInstanceOf(\Winter\Replacement\Plugin::class, $this->manager->findByIdentifier('Winter.Original'));
        $this->assertInstanceOf(\Winter\Replacement\Plugin::class, $this->manager->findByIdentifier('Winter.original'));
        $this->assertInstanceOf(\Winter\Replacement\Plugin::class, $this->manager->findByIdentifier('winter.original'));

        // test resolving original plugin with mixed casing when ignoring replacements
        $this->assertInstanceOf(\Winter\Original\Plugin::class, $this->manager->findByIdentifier('Winter.Original', true));
        $this->assertInstanceOf(\Winter\Original\Plugin::class, $this->manager->findByIdentifier('Winter.original', true));
        $this->assertInstanceOf(\Winter\Original\Plugin::class, $this->manager->findByIdentifier('winter.original', true));

        // test resolving replacement plugin of uninstalled plugin with mixed casing
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('Winter.NotInstalled'));
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('Winter.notInstalled'));
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('winter.notInstalled'));
        $this->assertInstanceOf(\Winter\ReplaceNotInstalled\Plugin::class, $this->manager->findByIdentifier('Winter.notinstalled'));
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

    public function testPluginNormalization()
    {
        // test lower to upper
        $this->assertEquals('Database.Tester', $this->manager->normalizeIdentifier('database.tester'));
        $this->assertEquals('Database.Tester', $this->manager->getNormalizedIdentifier('database.tester'));

        // test exact match
        $this->assertEquals('DependencyTest.Found', $this->manager->normalizeIdentifier('DependencyTest.Found'));
        $this->assertEquals('DependencyTest.Found', $this->manager->getNormalizedIdentifier('DependencyTest.Found'));

        // test mixed case
        $this->assertEquals('DependencyTest.Found', $this->manager->normalizeIdentifier('Dependencytest.Found'));
        $this->assertEquals('DependencyTest.Found', $this->manager->getNormalizedIdentifier('Dependencytest.Found'));

        // test typeo
        $this->assertEquals('dpendencytest.Found', $this->manager->normalizeIdentifier('dpendencytest.Found'));
        $this->assertEquals('dpendencytest.Found', $this->manager->getNormalizedIdentifier('dpendencytest.Found'));
        $this->assertEquals('Winter.NoUpdate', $this->manager->normalizeIdentifier('Winter.NoUpdate'));
        $this->assertEquals('Winter.NoUpdate', $this->manager->getNormalizedIdentifier('Winter.NoUpdate'));

        // test multiple mixed case installed plugin
        $this->assertEquals('Winter.NoUpdates', $this->manager->normalizeIdentifier('Winter.NoUpdates'));
        $this->assertEquals('Winter.NoUpdates', $this->manager->normalizeIdentifier('winter.noUpdates'));
        $this->assertEquals('Winter.NoUpdates', $this->manager->normalizeIdentifier('winter.noupdates'));
        $this->assertEquals('Winter.NoUpdates', $this->manager->getNormalizedIdentifier('Winter.NoUpdates'));
        $this->assertEquals('Winter.NoUpdates', $this->manager->getNormalizedIdentifier('winter.noUpdates'));
        $this->assertEquals('Winter.NoUpdates', $this->manager->getNormalizedIdentifier('winter.noupdates'));

        // test multiple mixed case not installed plugin
        $this->assertEquals('Winter.MissingPlugin', $this->manager->normalizeIdentifier('Winter.MissingPlugin'));
        $this->assertEquals('Winter.Missingplugin', $this->manager->normalizeIdentifier('Winter.Missingplugin'));
        $this->assertEquals('Winter.missingplugin', $this->manager->normalizeIdentifier('Winter.missingplugin'));
        $this->assertEquals('winter.missingplugin', $this->manager->normalizeIdentifier('winter.missingplugin'));
        $this->assertEquals('Winter.MissingPlugin', $this->manager->getNormalizedIdentifier('Winter.MissingPlugin'));
        $this->assertEquals('Winter.Missingplugin', $this->manager->getNormalizedIdentifier('Winter.Missingplugin'));
        $this->assertEquals('Winter.missingplugin', $this->manager->getNormalizedIdentifier('Winter.missingplugin'));
        $this->assertEquals('winter.missingplugin', $this->manager->getNormalizedIdentifier('winter.missingplugin'));

        // test passing plugin object
        $plugin = $this->manager->findByIdentifier('Winter.NoUpdates');
        $this->assertInstanceOf(PluginBase::class, $plugin);

        $this->assertEquals('Winter.NoUpdates', $this->manager->getNormalizedIdentifier($plugin));
    }

    public function testSortPluginDependencies()
    {
        $result = $this->manager->getPlugins();

        $this->assertGreaterThan(
            array_search('DependencyTest.Dependency', array_keys($result)),
            array_search('DependencyTest.Found', array_keys($result))
        );

        // check to make sure dependency comes first and didn't stay in alphanumeric order.
        $this->assertGreaterThan(
            array_search('DependencyTest.Dependency', array_keys($result)),
            array_search('DependencyTest.Acme', array_keys($result))
        );
    }
}
