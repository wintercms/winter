<?php

use System\Classes\PackageJson;

class PackageJsonTest extends TestCase
{
    public function tearDown(): void
    {
        if (file_exists($this->fixturePath('package-json/created.package.json'))) {
            @unlink($this->fixturePath('package-json/created.package.json'));
        }

        parent::tearDown();
    }

    public function testExists(): void
    {
        $package = new PackageJson($this->fixturePath('package-json/complex.package.json'));
        $this->assertTrue($package->exists());

        $package = new PackageJson($this->fixturePath('package-json/missing.package.json'));
        $this->assertFalse($package->exists());
    }

    public function testCreate(): void
    {
        $package = new PackageJson($this->fixturePath('package-json/created.package.json'));
        $package->create(['private' => true], false);

        $this->assertEquals([
            'private' => true,
        ], $package->get());

        $this->assertFileEquals(
            $this->fixturePath('package-json/assertions/testCreate.package.json'),
            $this->fixturePath('package-json/created.package.json')
        );
    }

    public function testCreateDefaults(): void
    {
        $package = new PackageJson($this->fixturePath('package-json/created.package.json'));
        $package->create();

        $this->assertEquals([
            'private' => true,
            'dependencies' => [],
            'devDependencies' => [],
            'engines' => [
                'node' => '>= 14',
                'npm' => '>= 7',
            ],
        ], $package->get());

        $this->assertFileEquals(
            $this->fixturePath('package-json/assertions/testCreateDefaults.package.json'),
            $this->fixturePath('package-json/created.package.json')
        );
    }
}
