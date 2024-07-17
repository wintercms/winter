<?php

namespace System\Tests\Classes;

use System\Classes\NodePackages;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\Config;

class NodePackagesTest extends TestCase
{
    protected NodePackages $nodePackages;

    public function setUp(): void
    {
        parent::setUp();
        $this->nodePackages = NodePackages::instance();
    }

    /**
     * Test getting all supported bundles
     */
    public function testGetBundles(): void
    {
        $bundles = $this->nodePackages->getBundles();

        $this->assertIsArray($bundles);
        $count = count($bundles);

        Config::set('node.bundlePackages.test', [
            'a' => 'v0.1.2'
        ]);

        $this->assertCount($count + 1, $this->nodePackages->getBundles());
    }

    /**
     * Test getting bundle packages works and allows for config overloading
     */
    public function testGetBundlePackages(): void
    {
        // Test that getting a default returns an array and validate one of the packages
        $packages = $this->nodePackages->getBundlePackages('tailwind', 'mix');
        $this->assertIsArray($packages);
        $this->assertArrayHasKey('tailwindcss', $packages);

        // Test that getting a package with compiler dependant packages does not return the package for invalid compiler
        $packages = $this->nodePackages->getBundlePackages('vue', 'mix');
        $this->assertIsArray($packages);
        $this->assertArrayNotHasKey('@vitejs/plugin-vue', $packages);

        // Test that getting a package with compiler dependant packages does return the package for a valid compiler
        $packages = $this->nodePackages->getBundlePackages('vue', 'vite');
        $this->assertIsArray($packages);
        $this->assertArrayHasKey('@vitejs/plugin-vue', $packages);

        // Validate that `testing` does not exist
        $packages = $this->nodePackages->getBundlePackages('testing', 'vite');
        $this->assertIsArray($packages);
        $this->assertEmpty($packages);

        // Add a `testing` bundle
        Config::set('node.bundlePackages.testing', [
            'a' => 'v0.1.2',
            'mix' => [
                'b' => 'v0.1.3',
            ]
        ]);

        // Validate the testing bundle works with compiler dependent packages
        $packages = $this->nodePackages->getBundlePackages('testing', 'vite');
        $this->assertIsArray($packages);
        $this->assertCount(1, $packages);

        $packages = $this->nodePackages->getBundlePackages('testing', 'mix');
        $this->assertIsArray($packages);
        $this->assertCount(2, $packages);
    }

    /**
     * Test getting compiler packages works and allows for config overloading
     */
    public function testGetCompilerPackages(): void
    {
        // Test a default compiler
        $packages = $this->nodePackages->getCompilerPackages('mix');
        $this->assertIsArray($packages);
        $this->assertArrayHasKey('laravel-mix', $packages);

        // Add a custom compiler
        Config::set('node.compilerPackages.testing', [
            'a' => 'v0.1.2',
            'b' => 'v0.1.3',
        ]);

        // Test the compiler packages are returned correctly
        $packages = $this->nodePackages->getCompilerPackages('testing');
        $this->assertIsArray($packages);
        $this->assertArrayHasKey('a', $packages);
        $this->assertArrayHasKey('b', $packages);
    }

    /**
     * Test the NodePackages setup handlers functionality
     */
    public function testSetupHandlers(): void
    {
        // Test that the default handler is accessible
        $handlers = $this->nodePackages->getSetupHandlers('tailwind');
        $this->assertIsArray($handlers);
        $this->assertArrayHasKey(0, $handlers);
        $this->assertIsCallable($handlers[0]);

        // Add a new handler
        $this->nodePackages->addSetupHandler('testing', fn () => true);
        $handlers = $this->nodePackages->getSetupHandlers('testing');
        $this->assertIsArray($handlers);

        // Validate that the array returns our handler
        $this->assertArrayHasKey(0, $handlers);
        $this->assertIsCallable($handlers[0]);
        $this->assertTrue($handlers[0]());

        // Add a second handler
        $this->nodePackages->addSetupHandler('testing', fn () => false);
        $handlers = $this->nodePackages->getSetupHandlers('testing');
        $this->assertIsArray($handlers);

        // Validate that the first handler does what we expect
        $this->assertArrayHasKey(0, $handlers);
        $this->assertIsCallable($handlers[0]);
        $this->assertTrue($handlers[0]());

        // Validate that the second handler does what we expect
        $this->assertArrayHasKey(1, $handlers);
        $this->assertIsCallable($handlers[1]);
        $this->assertFalse($handlers[1]());
    }

    /**
     * Test the NodePackages scaffold handlers functionality
     */
    public function testScaffoldHandlers(): void
    {
        // Test that the default handler is accessible
        $handlers = $this->nodePackages->getScaffoldHandlers('tailwind');
        $this->assertIsArray($handlers);
        $this->assertArrayHasKey(0, $handlers);
        $this->assertIsCallable($handlers[0]);

        // Add a new handler
        $this->nodePackages->addScaffoldHandler('testing', fn () => true);
        $handlers = $this->nodePackages->getScaffoldHandlers('testing');
        $this->assertIsArray($handlers);

        // Validate that the array returns our handler
        $this->assertArrayHasKey(0, $handlers);
        $this->assertIsCallable($handlers[0]);
        $this->assertTrue($handlers[0]());

        // Add a second handler
        $this->nodePackages->addScaffoldHandler('testing', fn () => false);
        $handlers = $this->nodePackages->getScaffoldHandlers('testing');
        $this->assertIsArray($handlers);

        // Validate that the first handler does what we expect
        $this->assertArrayHasKey(0, $handlers);
        $this->assertIsCallable($handlers[0]);
        $this->assertTrue($handlers[0]());

        // Validate that the second handler does what we expect
        $this->assertArrayHasKey(1, $handlers);
        $this->assertIsCallable($handlers[1]);
        $this->assertFalse($handlers[1]());
    }
}
