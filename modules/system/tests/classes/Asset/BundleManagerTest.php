<?php

namespace System\Tests\Classes\Asset;

use System\Classes\Asset\BundleManager;
use System\Tests\Bootstrap\TestCase;

class BundleManagerTest extends TestCase
{
    protected BundleManager $bundleManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->bundleManager = BundleManager::instance();
    }

    /**
     * Test getting all supported bundles
     */
    public function testGetBundles(): void
    {
        $bundles = $this->bundleManager->getBundles();

        $this->assertIsArray($bundles);
        $count = count($bundles);

        $this->bundleManager->registerBundle('test', [
            'a' => 'v0.1.2',
        ]);

        $this->assertCount($count + 1, $this->bundleManager->getBundles());
    }

    /**
     * Test getting bundle packages works and allows for config overloading
     */
    public function testGetBundlePackages(): void
    {
        // Test that getting a default returns an array and validate one of the packages
        $packages = $this->bundleManager->getBundlePackages('tailwind', 'mix');
        $this->assertIsArray($packages);
        $this->assertArrayHasKey('tailwindcss', $packages);

        // Test that getting a package with compiler dependant packages does not return the package for invalid compiler
        $packages = $this->bundleManager->getBundlePackages('vue', 'mix');
        $this->assertIsArray($packages);
        $this->assertArrayNotHasKey('@vitejs/plugin-vue', $packages);

        // Test that getting a package with compiler dependant packages does return the package for a valid compiler
        $packages = $this->bundleManager->getBundlePackages('vue', 'vite');
        $this->assertIsArray($packages);
        $this->assertArrayHasKey('@vitejs/plugin-vue', $packages);

        // Validate that `testing` does not exist
        $packages = $this->bundleManager->getBundlePackages('testing', 'vite');
        $this->assertIsArray($packages);
        $this->assertEmpty($packages);

        $this->bundleManager->registerBundle('testing', [
            'a' => 'v0.1.2',
            'mix' => [
                'b' => 'v0.1.3',
            ]
        ]);

        // Validate the testing bundle works with compiler dependent packages
        $packages = $this->bundleManager->getBundlePackages('testing', 'vite');
        $this->assertIsArray($packages);
        $this->assertCount(1, $packages);

        $packages = $this->bundleManager->getBundlePackages('testing', 'mix');
        $this->assertIsArray($packages);
        $this->assertCount(2, $packages);
    }

    /**
     * Test the AssetBundles setup handlers functionality
     */
    public function testSetupHandlers(): void
    {
        // Test that the default handler is accessible
        $handler = $this->bundleManager->getSetupHandler('tailwind');
        $this->assertIsCallable($handler);

        // Add a new handler
        $this->bundleManager->registerSetupHandler('testing', fn () => true);
        $handler = $this->bundleManager->getSetupHandler('testing');
        $this->assertIsCallable($handler);

        // Validate that handler returned is ours
        $this->assertTrue($handler());

        // Override the handler
        $this->bundleManager->registerSetupHandler('testing', fn () => false);
        $handler = $this->bundleManager->getSetupHandler('testing');
        $this->assertIsCallable($handler);
        $this->assertFalse($handler());
    }

    /**
     * Test the AssetBundles scaffold handlers functionality
     */
    public function testScaffoldHandlers(): void
    {
        // Test that the default handler is accessible
        $handler = $this->bundleManager->getScaffoldHandler('tailwind');
        $this->assertIsCallable($handler);

        // Add a new handler
        $this->bundleManager->registerScaffoldHandler('testing', fn () => true);
        $handler = $this->bundleManager->getScaffoldHandler('testing');
        $this->assertIsCallable($handler);

        // Validate that handler returned is ours
        $this->assertTrue($handler());

        // Override the handler
        $this->bundleManager->registerScaffoldHandler('testing', fn () => false);
        $handler = $this->bundleManager->getScaffoldHandler('testing');
        $this->assertIsCallable($handler);
        $this->assertFalse($handler());
    }
}
