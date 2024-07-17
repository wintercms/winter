<?php

namespace System\Tests\Classes;

use System\Classes\NodePackageVersions;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\Config;

class NodePackageVersionsTest extends TestCase
{
    /**
     * Test getting a version number for a package
     *
     * @return void
     */
    public function testGetPackageVersion(): void
    {
        $version = NodePackageVersions::get('winter-test-js-package');

        $this->assertIsString($version);
        $this->assertEquals('^1.0.3', $version);
    }

    /**
     * Test getting a missing package returns null
     *
     * @return void
     */
    public function testGetMissingPackageVersion(): void
    {
        $this->assertNull(NodePackageVersions::get('this-is-a-test'));
    }

    /**
     * Test setting a package version in config overwrites the version returned
     *
     * @return void
     */
    public function testConfigOverriding(): void
    {
        $version = NodePackageVersions::get('winter-test-js-package');

        $this->assertIsString($version);
        $this->assertEquals('^1.0.3', $version);

        Config::set('node.packages.winter-test-js-package', '999.0.1');

        $version = NodePackageVersions::get('winter-test-js-package');

        $this->assertIsString($version);
        $this->assertEquals('999.0.1', $version);
    }
}
