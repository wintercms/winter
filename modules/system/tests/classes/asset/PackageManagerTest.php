<?php

namespace System\Tests\Classes\Asset;

use InvalidArgumentException;
use System\Classes\Asset\PackageManager;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File;

class PackageManagerTest extends TestCase
{
    protected mixed $originalAllowDeepSymlinks = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->originalAllowDeepSymlinks = Config::get('develop.allowDeepSymlinks');
    }

    public function tearDown(): void
    {
        Config::set('develop.allowDeepSymlinks', $this->originalAllowDeepSymlinks);

        parent::tearDown();
    }

    /**
     * A package whose compilable config lives inside the project should be
     * registered with a base_path()-relative path.
     */
    public function testRegisterInProjectPackage(): void
    {
        $relative = 'modules/system/tests/fixtures/themes/assettest';

        PackageManager::instance()->registerPackage(
            'test-inproject',
            base_path($relative) . '/vite.config.mjs',
            'vite'
        );

        $package = PackageManager::instance()->getPackages('vite')['test-inproject'] ?? null;

        $this->assertNotNull($package);
        $this->assertSame($relative, $package['path']);
    }

    /**
     * In-project packages must resolve identically regardless of the
     * "develop.allowDeepSymlinks" setting - the gate only affects symlinked packages.
     */
    public function testRegisterInProjectPackageIsUnaffectedByTheGate(): void
    {
        foreach ([false, true] as $allowDeepSymlinks) {
            Config::set('develop.allowDeepSymlinks', $allowDeepSymlinks);

            $suffix = var_export($allowDeepSymlinks, true);
            $name = 'test-inproject-gate-' . $suffix;

            $this->withInProjectPackage('wn-inproject-gate-' . $suffix, function ($dir, $relative) use ($name) {
                PackageManager::instance()->registerPackage($name, $dir . '/vite.config.mjs', 'vite');

                $package = PackageManager::instance()->getPackages('vite')[$name] ?? null;

                $this->assertNotNull($package);
                $this->assertSame($relative, $package['path']);
            });
        }
    }

    /**
     * With "develop.allowDeepSymlinks" enabled, a package registered from a symlink that
     * lives under the project (a common local development setup for plugins/themes) must
     * keep the in-project symlink path rather than the realpath the symlink resolves to -
     * otherwise the package is neither registerable nor web-servable.
     */
    public function testRegisterSymlinkedPackageKeepsInProjectPath(): void
    {
        Config::set('develop.allowDeepSymlinks', true);

        $this->withSymlinkedPackage(function (string $linkDir, string $linkRelative) {
            PackageManager::instance()->registerPackage(
                'test-symlinked',
                $linkDir . '/vite.config.mjs',
                'vite'
            );

            $package = PackageManager::instance()->getPackages('vite')['test-symlinked'] ?? null;

            $this->assertNotNull($package);
            // The stored path is the in-project symlink location, not the realpath.
            $this->assertSame($linkRelative, $package['path']);
            $this->assertStringNotContainsString(
                basename(realpath(sys_get_temp_dir())),
                $package['path']
            );
        });
    }

    /**
     * With "develop.allowDeepSymlinks" disabled (the default), the symlink handling is
     * gated off and the realpath behaviour is kept: the symlink resolves outside
     * base_path() and the package is rejected. This pins the gate so that standard
     * installs are unaffected by the symlink support.
     */
    public function testSymlinkedPackageIsRejectedWhenDeepSymlinksDisabled(): void
    {
        Config::set('develop.allowDeepSymlinks', false);

        $this->withSymlinkedPackage(function (string $linkDir) {
            $this->expectException(InvalidArgumentException::class);

            PackageManager::instance()->registerPackage(
                'test-symlinked-disabled',
                $linkDir . '/vite.config.mjs',
                'vite'
            );
        });
    }

    /**
     * A package whose path genuinely does not exist must throw, rather than being
     * silently skipped.
     */
    public function testRegisterPackageWithMissingPathThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PackageManager::instance()->registerPackage(
            'test-missing',
            base_path('this/path/does/not/exist') . '/vite.config.mjs',
            'vite'
        );
    }

    /**
     * Creates a real package directory inside the project and hands it to the given
     * callback. Cleans up afterwards.
     */
    protected function withInProjectPackage(string $dirName, callable $callback): void
    {
        $relative = 'modules/system/tests/fixtures/' . $dirName;
        $dir = base_path($relative);

        File::deleteDirectory($dir);
        File::makeDirectory($dir, 0755, true);
        File::put($dir . '/vite.config.mjs', "export default {};\n");
        File::put($dir . '/package.json', '{"name":"' . $dirName . '"}' . "\n");

        try {
            $callback($dir, $relative);
        } finally {
            File::deleteDirectory($dir);
        }
    }

    /**
     * Creates a real package directory outside the project, symlinked to a location
     * inside it, and hands the symlink to the given callback. Cleans up afterwards.
     */
    protected function withSymlinkedPackage(callable $callback): void
    {
        $realDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'wn-symlinked-pkg';
        $linkRelative = 'modules/system/tests/fixtures/wn-symlinked-pkg';
        $linkDir = base_path($linkRelative);

        File::deleteDirectory($realDir);
        if (is_link($linkDir)) {
            @unlink($linkDir);
        }

        File::makeDirectory($realDir, 0755, true);
        File::put($realDir . '/vite.config.mjs', "export default {};\n");
        File::put($realDir . '/package.json', "{\"name\":\"wn-symlinked-pkg\"}\n");

        if (!@symlink($realDir, $linkDir)) {
            File::deleteDirectory($realDir);
            $this->markTestSkipped('Unable to create symlinks in this environment.');
        }

        try {
            $callback($linkDir, $linkRelative);
        } finally {
            @unlink($linkDir);
            File::deleteDirectory($realDir);
        }
    }
}
