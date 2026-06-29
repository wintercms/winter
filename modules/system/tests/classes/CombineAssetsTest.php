<?php

namespace System\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use Cms\Classes\Theme;
use System\Classes\CombineAssets;

class CombineAssetsTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        CombineAssets::resetCache();
    }

    //
    // Tests
    //

    public function testCombiner()
    {
        $combiner = CombineAssets::instance();

        /*
         * Supported file extensions should exist
         */
        $jsExt = $cssExt = self::getProtectedProperty($combiner, 'jsExtensions');
        $this->assertIsArray($jsExt);

        $cssExt = self::getProtectedProperty($combiner, 'cssExtensions');
        $this->assertIsArray($cssExt);

        /*
         * Check service methods
         */
        $this->assertTrue(method_exists($combiner, 'combine'));
        $this->assertTrue(method_exists($combiner, 'resetCache'));
    }

    public function testCombine()
    {
        $combiner = CombineAssets::instance();

        $url = $combiner->combine(
            [
                'assets/css/style1.css',
                'assets/css/style2.css'
            ],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $this->assertRegExp('/\w+[-]\d+/i', $url); // Must contain hash-number

        $url = $combiner->combine(
            [
                'assets/js/script1.js',
                'assets/js/script2.js'
            ],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $this->assertRegExp('/\w+[-]\d+/i', $url); // Must contain hash-number
    }

    public function testPutCache()
    {
        $sampleId = md5('testhash');
        $sampleStore = ['version' => 12345678];
        $samplePath = '/tests/fixtures/Cms/themes/test';

        $combiner = CombineAssets::instance();
        $value = self::callProtectedMethod($combiner, 'putCache', [$sampleId, $sampleStore]);

        $this->assertTrue($value);
    }

    public function testGetTargetPath()
    {
        $combiner = CombineAssets::instance();

        $value = self::callProtectedMethod($combiner, 'getTargetPath', ['/combine']);
        $this->assertEquals('combine/', $value);

        $value = self::callProtectedMethod($combiner, 'getTargetPath', ['/index.php/combine']);
        $this->assertEquals('index-php/combine/', $value);
    }

    public function testMakeCacheId()
    {
        $sampleResources = ['assets/css/style1.css', 'assets/css/style2.css'];
        $samplePath = base_path() . '/modules/system/tests/fixtures/cms/themes/test';

        $combiner = CombineAssets::instance();
        self::setProtectedProperty($combiner, 'localPath', $samplePath);

        $value = self::callProtectedMethod($combiner, 'getCacheKey', [$sampleResources]);
        $this->assertEquals(md5($samplePath.implode('|', $sampleResources)), $value);
    }

    public function testResetCache()
    {
        $combiner = CombineAssets::instance();
        $this->assertNull($combiner->resetCache());
    }

    /**
     * Regression for GHSA-58fp-mcx6-7qf9. A writable theme `.less` file containing
     * `@import (inline) "<absolute-path>"` must not disclose server files.
     */
    public function testLessCompilerBlocksAbsolutePathImport()
    {
        [$themeDir, $secretPath] = $this->setupLessLeakFixture(
            '@import (inline) "%SECRET%"; .x { color: red; }'
        );

        try {
            $css = $this->compileLessTo($themeDir, 'assets/less/poc.less');
            $this->assertStringNotContainsString('APP_KEY', $css);
            $this->assertStringNotContainsString('combine-leak-canary', $css);
        } finally {
            $this->teardownLessLeakFixture($themeDir, $secretPath);
        }
    }

    /**
     * Regression for the relative-traversal path of GHSA-58fp-mcx6-7qf9. less.php's
     * auto-added `currentDirectory` import_dir entry resolves `..` traversal
     * natively; without the key-collision override in LessImportResolver, a theme
     * `.less` could still escape via `@import (inline) "../../../etc/passwd"`.
     */
    public function testLessCompilerBlocksRelativeTraversalImport()
    {
        // From themeDir/assets/less/poc.less, traverse up enough to escape the
        // theme tree, the themes root, and out to the secret file the fixture
        // wrote at sys_get_temp_dir().
        [$themeDir, $secretPath] = $this->setupLessLeakFixture(
            '@import (inline) "' . str_repeat('../', 20) . ltrim($this->lastSecretPath, '/') . '"; .x { color: red; }'
        );

        try {
            $css = $this->compileLessTo($themeDir, 'assets/less/poc.less');
            $this->assertStringNotContainsString('APP_KEY', $css);
            $this->assertStringNotContainsString('combine-leak-canary', $css);
        } finally {
            $this->teardownLessLeakFixture($themeDir, $secretPath);
        }
    }

    /**
     * Legitimate same-tree `@import "partial.less"` must still resolve through
     * the gate, otherwise we've broken every theme that uses partials.
     */
    public function testLessCompilerAllowsLegitimatePartial()
    {
        $themeDir = $this->makeTempThemeDir();
        $mainPath = $themeDir . '/assets/less/main.less';
        $partialPath = $themeDir . '/assets/less/partial.less';
        file_put_contents($partialPath, '.partial-marker { color: orange; }');
        file_put_contents($mainPath, '@import "partial.less"; .main-marker { color: blue; }');

        try {
            $css = $this->compileLessTo($themeDir, 'assets/less/main.less');
            $this->assertStringContainsString('partial-marker', $css);
            $this->assertStringContainsString('main-marker', $css);
        } finally {
            \File::deleteDirectory($themeDir);
        }
    }

    /** @var string */
    protected $lastSecretPath = '';

    /**
     * @return array{0:string,1:string} [theme dir, secret path]
     */
    protected function setupLessLeakFixture(string $pocTemplate): array
    {
        $themeDir = $this->makeTempThemeDir();
        $secretPath = tempnam(sys_get_temp_dir(), 'combine-leak-canary-');
        file_put_contents($secretPath, "APP_KEY=do-not-leak-via-combiner\n");
        $this->lastSecretPath = $secretPath;

        $poc = str_replace('%SECRET%', $secretPath, $pocTemplate);
        file_put_contents($themeDir . '/assets/less/poc.less', $poc);

        return [$themeDir, $secretPath];
    }

    protected function teardownLessLeakFixture(string $themeDir, string $secretPath): void
    {
        @unlink($secretPath);
        \File::deleteDirectory($themeDir);
    }

    protected function makeTempThemeDir(): string
    {
        // Must live under base_path() because Assetic's FileAsset enforces that
        // the source be within the configured root, which CombineAssets sets to
        // public_path() (equal to base_path() in this install). Using sys_get_temp_dir()
        // would trigger "source is not in the root directory" errors.
        $themeDir = base_path('storage/framework/cache/security-tests/theme-' . bin2hex(random_bytes(4)));
        mkdir($themeDir . '/assets/less', 0777, true);
        return $themeDir;
    }

    protected function compileLessTo(string $themeDir, string $relativeAsset): string
    {
        $dest = sys_get_temp_dir() . '/winter-combine-out-' . bin2hex(random_bytes(4)) . '.css';
        try {
            CombineAssets::instance()->combineToFile([$relativeAsset], $dest, $themeDir);
            return file_get_contents($dest) ?: '';
        } finally {
            @unlink($dest);
        }
    }

}
