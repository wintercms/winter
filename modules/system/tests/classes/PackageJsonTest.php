<?php

namespace System\tests\classes;

use System\Classes\PackageJson;
use System\Tests\Bootstrap\TestCase;

class PackageJsonTest extends TestCase
{
    /**
     * Test loading a package.json file from path
     *
     * @return void
     */
    public function testLoadFile(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        $contents = $packageJson->getContents();

        $this->assertArrayHasKey('workspaces', $contents);
        $this->assertArrayHasKey('packages', $contents['workspaces']);
        $this->assertIsArray($contents['workspaces']['packages']);
    }

    /**
     * Test creating an instance with non-existing file
     *
     * @return void
     */
    public function testNewFile(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test-new.json');
        $contents = $packageJson->getContents();
        $this->assertIsArray($contents);
        $this->assertCount(0, $contents);
    }

    /**
     * Test creating an instance without a path
     *
     * @return void
     */
    public function testMemoryInstance(): void
    {
        $packageJson = new PackageJson();
        $contents = $packageJson->getContents();
        $this->assertIsArray($contents);
        $this->assertCount(0, $contents);
    }

    /**
     * Test setting and getting the name property
     *
     * @return void
     */
    public function testNameMethods(): void
    {
        $packageJson = new PackageJson();
        $contents = $packageJson->getContents();
        $this->assertIsArray($contents);
        $this->assertCount(0, $contents);

        $packageJson->setName('example-name');

        $this->assertEquals('example-name', $packageJson->getName());

        $contents = $packageJson->getContents();
        $this->assertIsArray($contents);
        $this->assertCount(1, $contents);
        $this->assertArrayHasKey('name', $contents);
        $this->assertEquals('example-name', $contents['name']);
    }

    /**
     * Test validating the name on set
     *
     * @return void
     */
    public function testNameValidation(): void
    {
        $packageJson = new PackageJson();

        $this->assertThrows(function () use ($packageJson) {
            $packageJson->setName('Test');
        }, \InvalidArgumentException::class, 'Package names must be lower case');

        $this->assertThrows(function () use ($packageJson) {
            $packageJson->setName('.test');
        }, \InvalidArgumentException::class, 'Package names must not start with . or _');

        $this->assertThrows(function () use ($packageJson) {
            $packageJson->setName('_test');
        }, \InvalidArgumentException::class, 'Package names must not start with . or _');

        $this->assertThrows(function () use ($packageJson) {
            $packageJson->setName('te~st');
        }, \InvalidArgumentException::class, 'Package names must not include special characters');

        $this->assertThrows(function () use ($packageJson) {
            $packageJson->setName('te*st');
        }, \InvalidArgumentException::class, 'Package names must not include special characters');

        $this->assertThrows(function () use ($packageJson) {
            $packageJson->setName('test!');
        }, \InvalidArgumentException::class, 'Package names must not include special characters');

        $this->assertThrows(function () use ($packageJson) {
            $packageJson->setName(sprintf('te%sst', str_repeat('s', 214)));
        }, \InvalidArgumentException::class, 'Package names must not be longer than 214 characters');

        $this->assertThrows(function () use ($packageJson) {
            $packageJson->setName('test ');
        }, \InvalidArgumentException::class, 'Package names must not include whitespace');

        $this->assertThrows(function () use ($packageJson) {
            $packageJson->setName(' test');
        }, \InvalidArgumentException::class, 'Package names must not include whitespace');
    }

    /**
     * Test checking package workspace exists
     *
     * @return void
     */
    public function testHasWorkspace(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        $this->assertTrue($packageJson->hasWorkspace('themes/demo'));
        $this->assertFalse($packageJson->hasWorkspace('themes/test'));
    }

    /**
     * Test adding workspace package
     *
     * @return void
     */
    public function testAddWorkspace(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        $this->assertFalse($packageJson->hasWorkspace('themes/test'));
        $packageJson->addWorkspace('themes/test');
        $this->assertTrue($packageJson->hasWorkspace('themes/test'));

        // Create blank source
        $packageJson = new PackageJson();
        $this->assertFalse($packageJson->hasWorkspace('themes/test'));
        $packageJson->addWorkspace('themes/test');
        $this->assertTrue($packageJson->hasWorkspace('themes/test'));
    }

    /**
     * Test removing workspace package
     *
     * @return void
     */
    public function testRemoveWorkspace(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        $this->assertTrue($packageJson->hasWorkspace('themes/demo'));
        $packageJson->removeWorkspace('themes/demo');
        $this->assertFalse($packageJson->hasWorkspace('themes/demo'));

        // Create blank source
        $packageJson = new PackageJson();
        $packageJson->removeWorkspace('themes/demo');
        $this->assertFalse($packageJson->hasWorkspace('themes/demo'));
    }

    /**
     * Test when adding a workspace package, it removes the package from ignored workspace packages
     *
     * @return void
     */
    public function testAddWorkspaceRemovesIgnoredPackage(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        $this->assertFalse($packageJson->hasWorkspace('modules/backend'));
        $this->assertTrue($packageJson->hasIgnoredPackage('modules/backend'));

        $packageJson->addWorkspace('modules/backend');

        $this->assertTrue($packageJson->hasWorkspace('modules/backend'));
        $this->assertFalse($packageJson->hasIgnoredPackage('modules/backend'));
    }

    /**
     * Test checking ignore package workspace exists
     *
     * @return void
     */
    public function testHasIgnoredPackage(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        $this->assertTrue($packageJson->hasIgnoredPackage('modules/backend'));
        $this->assertFalse($packageJson->hasIgnoredPackage('modules/test'));

        // Create blank source
        $packageJson = new PackageJson();
        $this->assertFalse($packageJson->hasIgnoredPackage('modules/backend'));
        $this->assertFalse($packageJson->hasIgnoredPackage('modules/test'));
    }

    /**
     * Test adding ignore workspace package
     *
     * @return void
     */
    public function testAddIgnoredPackage(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        $this->assertFalse($packageJson->hasIgnoredPackage('themes/example'));
        $packageJson->addIgnoredPackage('themes/example');
        $this->assertTrue($packageJson->hasIgnoredPackage('themes/example'));

        // Create blank source
        $packageJson = new PackageJson();
        $packageJson->addIgnoredPackage('themes/example');
        $this->assertTrue($packageJson->hasIgnoredPackage('themes/example'));
    }

    /**
     * Test removing ignore workspace package
     *
     * @return void
     */
    public function testRemoveIgnoredPackage(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        $this->assertTrue($packageJson->hasIgnoredPackage('modules/system'));
        $packageJson->removeIgnoredPackage('modules/system');
        $this->assertFalse($packageJson->hasIgnoredPackage('modules/system'));

        // Create blank source
        $packageJson = new PackageJson();
        $packageJson->removeIgnoredPackage('modules/system');
        $this->assertFalse($packageJson->hasIgnoredPackage('modules/system'));
    }

    /**
     * Test when adding an ignore workspace package, it removes the package from workspace packages
     *
     * @return void
     */
    public function testAddIgnoredPackageRemovesWorkspace(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        $this->assertTrue($packageJson->hasWorkspace('themes/demo'));
        $this->assertFalse($packageJson->hasIgnoredPackage('themes/demo'));

        $packageJson->addIgnoredPackage('themes/demo');

        $this->assertFalse($packageJson->hasWorkspace('themes/demo'));
        $this->assertTrue($packageJson->hasIgnoredPackage('themes/demo'));
    }

    /**
     * Test checking if package.json has deps
     *
     * @return void
     */
    public function testHasDependency(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');
        // Check that deps exist
        $this->assertTrue($packageJson->hasDependency('test'));
        $this->assertTrue($packageJson->hasDependency('test-dev'));
        // Check that deps don't exist
        $this->assertFalse($packageJson->hasWorkspace('test-dev2'));
        $this->assertFalse($packageJson->hasWorkspace('testx'));
    }

    /**
     * Test adding dependencies, when overwriting check that package is moved from devDeps to deps or revsersed
     *
     * @return void
     */
    public function testAddDependency(): void
    {
        // Test adding packages
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');

        $packageJson->addDependency('winter', '4.0.1', dev: false)
            ->addDependency('winter-dev', '4.0.1', dev: true);

        $this->assertArrayNotHasKey('winter', $packageJson->getContents()['devDependencies']);
        $this->assertArrayHasKey('winter', $packageJson->getContents()['dependencies']);
        $this->assertArrayNotHasKey('winter-dev', $packageJson->getContents()['dependencies']);
        $this->assertArrayHasKey('winter-dev', $packageJson->getContents()['devDependencies']);

        // Test adding packages with overwrites
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');

        // Should do nothing as test is already in deps not dev deps
        $packageJson->addDependency('test', '6.0.1', dev: true, overwrite: false);

        $this->assertArrayHasKey('test', $packageJson->getContents()['dependencies']);
        $this->assertArrayNotHasKey('test', $packageJson->getContents()['devDependencies']);
        $this->assertEquals('^1.0.0', $packageJson->getContents()['dependencies']['test']);

        // Should move package from dev deps to deps with new version
        $packageJson->addDependency('test', '6.0.1', dev: true, overwrite: true);

        $this->assertArrayNotHasKey('test', $packageJson->getContents()['dependencies']);
        $this->assertArrayHasKey('test', $packageJson->getContents()['devDependencies']);
        $this->assertEquals('6.0.1', $packageJson->getContents()['devDependencies']['test']);

        // Create blank source
        $packageJson = new PackageJson();
        // Add a non-dev dependency
        $packageJson->addDependency('winter', '4.0.1', dev: false);
        // Add a dev dependency
        $packageJson->addDependency('winter-dev', '4.0.1', dev: true);
    }

    /**
     * Test removing dependencies
     *
     * @return void
     */
    public function testRemoveDependency(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');

        // Check package removed from dev deps
        $this->assertArrayHasKey('test-dev', $packageJson->getContents()['devDependencies'] ?? []);
        $packageJson->removeDependency('test-dev');
        $this->assertArrayNotHasKey('test-dev', $packageJson->getContents()['devDependencies'] ?? []);

        // Check package removed from deps
        $this->assertArrayHasKey('test', $packageJson->getContents()['dependencies'] ?? []);
        $packageJson->removeDependency('test');
        $this->assertArrayNotHasKey('test', $packageJson->getContents()['dependencies'] ?? []);

        // Create blank source
        $packageJson = new PackageJson();
        $packageJson->removeDependency('test-dev');
        $this->assertArrayNotHasKey('test-dev', $packageJson->getContents()['devDependencies'] ?? []);
    }

    /**
     * Test checking if a script exists in a package.json
     *
     * @return void
     */
    public function testHasScript(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');

        $this->assertTrue($packageJson->hasScript('foo'));
        $this->assertTrue($packageJson->hasScript('example'));
        $this->assertTrue($packageJson->hasScript('test'));

        $this->assertFalse($packageJson->hasScript('bar'));
        $this->assertFalse($packageJson->hasScript('winter'));
        $this->assertFalse($packageJson->hasScript('testing'));
    }

    /**
     * Test getting the value of a script by name
     *
     * @return void
     */
    public function testGetScript(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');

        $this->assertEquals('bar ./test', $packageJson->getScript('foo'));
        $this->assertEquals('example test', $packageJson->getScript('example'));
        $this->assertEquals('testing', $packageJson->getScript('test'));

        $packageJson = new PackageJson();
        $this->assertNull($packageJson->getScript('foo'));
    }

    /**
     * Test getting the value of a script by name
     *
     * @return void
     */
    public function testAddScript(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');

        $packageJson->addScript('winter', 'winter ./testing');

        $this->assertTrue($packageJson->hasScript('winter'));
        $this->assertEquals('winter ./testing', $packageJson->getScript('winter'));

        $contents = $packageJson->getContents();

        $this->assertTrue(isset($contents['scripts']['winter']));
        $this->assertEquals('winter ./testing', $contents['scripts']['winter']);

        $packageJson = new PackageJson();
        $packageJson->addScript('winter', 'winter ./testing');
        $this->assertTrue($packageJson->hasScript('winter'));
    }

    /**
     * Test removing scripts from package.json
     *
     * @return void
     */
    public function testRemoveScript(): void
    {
        $packageJson = new PackageJson(__DIR__ . '/../fixtures/npm/package-test.json');

        $this->assertTrue($packageJson->hasScript('foo'));
        $packageJson->removeScript('foo');
        $this->assertFalse($packageJson->hasScript('foo'));

        $this->assertTrue($packageJson->hasScript('example'));
        $packageJson->removeScript('example');
        $this->assertFalse($packageJson->hasScript('example'));


        $packageJson = new PackageJson();
        $packageJson->removeScript('foo');
        $this->assertFalse($packageJson->hasScript('foo'));
    }

    /**
     * Test saving, when saving with a file path set on init and passing a file path on save. Fails when no path given
     *
     * @return void
     */
    public function testSave(): void
    {
        $srcFile = __DIR__ . '/../fixtures/npm/package-test.json';
        $backupFile = __DIR__ . '/../fixtures/npm/package-test.json.back';

        // Backup config file
        copy($srcFile, $backupFile);

        // Make a change and save the file, overwriting
        $packageJson = new PackageJson($srcFile);
        $this->assertNull($packageJson->getName());
        $packageJson->setName('testing');
        $packageJson->save();

        // Validate overwrite worked
        $packageJson = new PackageJson($srcFile);
        $this->assertEquals('testing', $packageJson->getName());

        // Restore the config file
        copy($backupFile, $srcFile);
        // Remove backup file
        unlink($backupFile);

        // Test saving file to new path
        $testFile = __DIR__ . '/../fixtures/npm/package-test.json.test';
        $packageJson = new PackageJson($srcFile);
        $this->assertNull($packageJson->getName());
        $packageJson->setName('testing');
        $packageJson->save($testFile);

        // Validate new file path exists and contains change
        $packageJson = new PackageJson($testFile);
        $this->assertEquals('testing', $packageJson->getName());

        // Remove test file
        unlink($testFile);

        // Validate that a file save with no path throws an error
        $this->assertThrows(function () {
            $packageJson = new PackageJson();
            $packageJson->setName('should-fail');
            $packageJson->save();
        }, \RuntimeException::class, 'Unable to save, no path given');
    }
}
