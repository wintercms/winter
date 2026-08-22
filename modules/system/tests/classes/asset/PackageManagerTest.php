<?php

namespace System\Tests\Classes\Asset;

use InvalidArgumentException;
use System\Classes\Asset\PackageManager;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\File;

class PackageManagerTest extends TestCase
{
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
     * A package registered from a symlink that lives under the project (a common
     * local development setup for plugins/themes) must keep the in-project symlink
     * path rather than the realpath the symlink resolves to — otherwise the package
     * is neither registerable nor web-servable. Regression test for symlinked plugins.
     */
    public function testRegisterSymlinkedPackageKeepsInProjectPath(): void
    {
        // A real package directory outside the project...
        $realDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'wn-symlinked-pkg';
        // ...symlinked to a location inside the project.
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
        } finally {
            @unlink($linkDir);
            File::deleteDirectory($realDir);
        }
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
}
