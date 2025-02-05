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
        // Reset the bundle manager
        BundleManager::forgetInstance();
        // Bind the instance for convenience
        $this->bundleManager = BundleManager::instance();
    }

    /**
     * Test the registerBundles & registerBundle functions correctly allow the user to append bundles
     */
    public function testRegisterBundles(): void
    {
        $defaultBundles = $this->bundleManager->getBundles();
        $this->bundleManager->registerBundles([
            'winter1js' => [
                'winter1js' => 'v1.0.0'
            ]
        ]);

        $this->bundleManager->registerBundle('winter2js', [
            'winter2js' => 'v1.0.0'
        ]);

        $bundles = $this->bundleManager->getBundles();
        $this->assertCount(count($defaultBundles) + 2, $bundles);
        $this->assertContains('winter1js', $bundles);
        $this->assertContains('winter2js', $bundles);
    }

    /**
     * This test ensures that defining new bundles does not affect the default bundles
     */
    public function testRegisterBundlesDoesNotBreakDefaults(): void
    {
        // Get the default bundles so we can validate them existing later
        $defaultBundles = $this->bundleManager->getBundles();
        // Flush the instance as if we have just booted
        BundleManager::forgetInstance();
        $this->bundleManager = BundleManager::instance();
        // Register a new bundle
        $this->bundleManager->registerBundles([
            'winterjs' => [
                'winterjs' => 'v1.0.0'
            ]
        ]);
        // Grab the current bundles
        $bundles = $this->bundleManager->getBundles();
        // Validate that all default bundles have been registered when adding a new bundle
        foreach ($defaultBundles as $defaultBundle) {
            $this->assertContains($defaultBundle, $bundles);
        }
    }

    /**
     * Test the forcing a package to a new version
     */
    public function testRegisterBundlesOverrideDefault(): void
    {
        $tailwindPackages = $this->bundleManager->getBundlePackages('tailwind', 'mix');

        $this->bundleManager->registerBundle('tailwind', [
            'tailwindcss' => 'dev-999.999.999',
            '@tailwindcss/forms' => 'dev-999.999.999',
            '@tailwindcss/typography' => 'dev-999.999.999',
        ]);

        $updatedTailwindPackages = $this->bundleManager->getBundlePackages('tailwind', 'mix');

        foreach ($tailwindPackages as $tailwindPackage => $version) {
            $this->assertArrayHasKey($tailwindPackage, $updatedTailwindPackages);
            $this->assertNotEquals($updatedTailwindPackages[$tailwindPackage], $version);
        }
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

    /**
     * Validate that when registered a callable is added to the `callbacks` array within the BundleManager
     */
    public function testRegisterCallback(): void
    {
        $this->expectExceptionMessage('callback registered');
        $this->bundleManager->registerCallback(function (BundleManager $manager) {
            throw new \RuntimeException('callback registered');
        });
    }

    /**
     * Test that calling registerBundles within registerCallback functions correctly
     */
    public function testLoadRegisteredBundles(): void
    {
        $this->bundleManager->registerCallback(function (BundleManager $manager) {
            $manager->registerBundles([
                'winter-test-js' => [
                    'test-package' => 'v0.1.2',
                    'vite' => [
                        'vite-package' => 'v0.1.3',
                    ]
                ]
            ]);
        });

        $this->assertContains('winter-test-js', $this->bundleManager->getBundles());

        $bundle = $this->bundleManager->getBundlePackages('winter-test-js', 'mix');
        $this->assertArrayHasKey('test-package', $bundle);
        $this->assertArrayNotHasKey('vite-package', $bundle);

        $bundle = $this->bundleManager->getBundlePackages('winter-test-js', 'vite');
        $this->assertArrayHasKey('test-package', $bundle);
        $this->assertArrayHasKey('vite-package', $bundle);
    }
}
