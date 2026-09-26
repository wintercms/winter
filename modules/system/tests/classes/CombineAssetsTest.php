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
     * A `.less` file imported from a subdirectory is parsed with its own directory as
     * the current one, so its imports must be confined as well as the entry asset's.
     */
    public function testLessCompilerBlocksTraversalFromNestedImport()
    {
        [$themeDir, $secretPath] = $this->setupNestedLessLeakFixture(
            '@import (inline) "../../../../%SECRET%"; .x { color: red; }'
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
     * `data-uri()` reads the file it is given and inlines it into the compiled CSS, so
     * it must be confined the same way as `@import`, including from a nested file.
     */
    public function testLessCompilerBlocksDataUriFileRead()
    {
        [$themeDir, $secretPath] = $this->setupNestedLessLeakFixture(
            '.x { background: data-uri("text/plain", "../../../../%SECRET%"); }'
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
     * Legitimate multi-level partial chains (an asset importing a partial from a
     * subdirectory, which imports its own neighbour) must keep resolving.
     */
    public function testLessCompilerAllowsNestedPartialChain()
    {
        $themeDir = $this->makeTempThemeDir();
        mkdir($themeDir . '/assets/less/sub');
        file_put_contents($themeDir . '/assets/less/main.less', '@import "sub/child.less"; .main-marker { color: blue; }');
        file_put_contents($themeDir . '/assets/less/sub/child.less', '@import "deeper.less"; .child-marker { color: green; }');
        file_put_contents($themeDir . '/assets/less/sub/deeper.less', '.deeper-marker { color: purple; }');

        try {
            $css = $this->compileLessTo($themeDir, 'assets/less/main.less');
            $this->assertStringContainsString('main-marker', $css);
            $this->assertStringContainsString('child-marker', $css);
            $this->assertStringContainsString('deeper-marker', $css);
        } finally {
            \File::deleteDirectory($themeDir);
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

    /**
     * Regression for GHSA-2223-f22x-24cq. A writable theme `.js` asset containing
     * `=include ../../../.env` must not disclose server files through the combiner,
     * whose output is served unauthenticated via the `combine/{file}` route.
     */
    public function testJavascriptImporterBlocksTraversalImport()
    {
        $themeDir = $this->makeTempThemeDir();
        // A real `.js` secret outside the theme subtree (but under base_path so
        // Assetic's FileAsset root check passes). It escapes via `..` traversal but
        // lands outside every allowed import root, so it must not be inlined.
        $secretPath = dirname($themeDir) . '/js-secret-' . bin2hex(random_bytes(4)) . '.js';
        file_put_contents($secretPath, 'var LEAK = "combine-leak-canary";');
        file_put_contents(
            $themeDir . '/assets/poc.js',
            "/*\n=include ../../" . basename($secretPath) . "\n*/\n"
        );

        try {
            $js = $this->compileJsTo($themeDir, 'assets/poc.js');
            $this->assertStringNotContainsString('combine-leak-canary', $js);
        } finally {
            @unlink($secretPath);
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * The `.js`-only extension gate must block disclosure of non-JS files (e.g.
     * `.env`) before any path resolution, even inside an otherwise reachable tree.
     */
    public function testJavascriptImporterBlocksDisallowedExtension()
    {
        $themeDir = $this->makeTempThemeDir();
        $secretPath = dirname($themeDir) . '/js-secret-' . bin2hex(random_bytes(4)) . '.env';
        file_put_contents($secretPath, "APP_KEY=combine-leak-canary\n");
        file_put_contents(
            $themeDir . '/assets/poc.js',
            "/*\n=include ../../" . basename($secretPath) . "\n*/\n"
        );

        try {
            $js = $this->compileJsTo($themeDir, 'assets/poc.js');
            $this->assertStringNotContainsString('combine-leak-canary', $js);
        } finally {
            @unlink($secretPath);
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * An `=include` inside an included file must be confined the same way as one in
     * the entry asset.
     */
    public function testJavascriptImporterBlocksTraversalFromNestedInclude()
    {
        $themeDir = $this->makeTempThemeDir();
        mkdir($themeDir . '/assets/sub', 0777, true);
        $secretPath = dirname($themeDir) . '/js-secret-' . bin2hex(random_bytes(4)) . '.js';
        file_put_contents($secretPath, 'var LEAK = "combine-leak-canary";');
        file_put_contents($themeDir . '/assets/poc.js', "/*\n=include sub/child.js\n*/\nvar MAIN = 1;");
        file_put_contents(
            $themeDir . '/assets/sub/child.js',
            "/*\n=include ../../../" . basename($secretPath) . "\n*/\nvar CHILD = 1;"
        );

        try {
            $js = $this->compileJsTo($themeDir, 'assets/poc.js');
            $this->assertStringNotContainsString('combine-leak-canary', $js);
            // The legitimate half of the chain must still have been inlined.
            $this->assertStringContainsString('var CHILD = 1;', $js);
        } finally {
            @unlink($secretPath);
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * Legitimate same-tree `=include partial.js` must still resolve, otherwise the
     * hardening would break every asset that composes its own bundle.
     */
    public function testJavascriptImporterAllowsSameTreeInclude()
    {
        $themeDir = $this->makeTempThemeDir();
        file_put_contents($themeDir . '/assets/partial.js', 'var PARTIAL = "partial-marker";');
        file_put_contents($themeDir . '/assets/main.js', "/*\n=include partial.js\n*/\nvar MAIN = 1;");

        try {
            $js = $this->compileJsTo($themeDir, 'assets/main.js');
            $this->assertStringContainsString('partial-marker', $js);
        } finally {
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * A `file://` import is not a filesystem path the root check can judge, so it must
     * be left for the browser rather than loaded by the combiner.
     */
    public function testCssImportFilterBlocksFileSchemeImport()
    {
        [$themeDir, $secretPath] = $this->setupCssLeakFixture(
            '@import url("file://%SECRET%");' . "\n" . '.x { color: red; }'
        );

        try {
            $css = $this->compileCssTo($themeDir, 'assets/poc.css');
            $this->assertStringNotContainsString('combine-leak-canary', $css);
        } finally {
            $this->teardownLessLeakFixture($themeDir, $secretPath);
        }
    }

    /**
     * Likewise for any other stream wrapper, such as `php://`.
     */
    public function testCssImportFilterBlocksPhpFilterSchemeImport()
    {
        [$themeDir, $secretPath] = $this->setupCssLeakFixture(
            '@import url("php://filter/read=convert.base64-encode/resource=%SECRET%");' . "\n" . '.x { color: red; }'
        );

        try {
            $css = $this->compileCssTo($themeDir, 'assets/poc.css');
            $this->assertStringNotContainsString('combine-leak-canary', $css);
            $this->assertStringNotContainsString(base64_encode('APP_KEY=combine-leak-canary'), $css);
        } finally {
            $this->teardownLessLeakFixture($themeDir, $secretPath);
        }
    }

    /**
     * A remote `@import` must be left in the output for the browser to resolve, not
     * fetched and inlined by the server. Assetic replaces a fetched import with the
     * response body (empty on failure), so the URL surviving shows it was not fetched.
     */
    public function testCssImportFilterDoesNotFetchRemoteImport()
    {
        $themeDir = $this->makeTempCssThemeDir();
        // Port 1 on loopback: no listener, so a fetch attempt fails fast and silently
        // (HttpAsset is constructed with $ignoreErrors) rather than hanging on DNS.
        file_put_contents(
            $themeDir . '/assets/poc.css',
            '@import url("http://127.0.0.1:1/remote.css");' . "\n" . '.x { color: red; }'
        );

        try {
            $css = $this->compileCssTo($themeDir, 'assets/poc.css');
            $this->assertStringContainsString('http://127.0.0.1:1/remote.css', $css);
        } finally {
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * A protocol-relative `//host/path` import is also a URL, even when its path part
     * happens to look like a location inside an allowed root.
     */
    public function testCssImportFilterDoesNotFetchProtocolRelativeImport()
    {
        $themeDir = $this->makeTempCssThemeDir();
        $target = '/' . str_replace('\\', '/', $themeDir) . '/assets/partial.css';
        file_put_contents($themeDir . '/assets/partial.css', '.partial-marker { color: orange; }');
        file_put_contents($themeDir . '/assets/poc.css', '@import url("' . $target . '");' . "\n" . '.x { color: red; }');

        try {
            $css = $this->compileCssTo($themeDir, 'assets/poc.css');
            $this->assertStringContainsString($target, $css);
        } finally {
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * Relative `..` traversal out of the allowed roots must be refused.
     */
    public function testCssImportFilterBlocksRelativeTraversalImport()
    {
        [$themeDir, $secretPath] = $this->setupCssLeakFixture(
            '@import url("' . str_repeat('../', 20) . '%SECRET_RELATIVE%");' . "\n" . '.x { color: red; }'
        );

        try {
            $css = $this->compileCssTo($themeDir, 'assets/poc.css');
            $this->assertStringNotContainsString('combine-leak-canary', $css);
        } finally {
            $this->teardownLessLeakFixture($themeDir, $secretPath);
        }
    }

    /**
     * The same holds for an `@import` inside an imported stylesheet.
     */
    public function testCssImportFilterBlocksTraversalFromNestedImport()
    {
        $themeDir = $this->makeTempCssThemeDir();
        mkdir($themeDir . '/assets/sub', 0777, true);
        $secretPath = tempnam(sys_get_temp_dir(), 'wn-sec-');
        file_put_contents($secretPath, "APP_KEY=combine-leak-canary\n");
        file_put_contents($themeDir . '/assets/poc.css', '@import url("sub/child.css");');
        file_put_contents(
            $themeDir . '/assets/sub/child.css',
            '@import url("' . str_repeat('../', 20) . ltrim($secretPath, '/') . '");' . "\n" . '.child-marker { color: red; }'
        );

        try {
            $css = $this->compileCssTo($themeDir, 'assets/poc.css');
            $this->assertStringNotContainsString('combine-leak-canary', $css);
            $this->assertStringContainsString('child-marker', $css);
        } finally {
            @unlink($secretPath);
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * Legitimate same-tree `@import` must still be inlined, otherwise the hardening
     * has broken every stylesheet that composes itself from partials.
     */
    public function testCssImportFilterAllowsSameTreeImport()
    {
        $themeDir = $this->makeTempCssThemeDir();
        file_put_contents($themeDir . '/assets/partial.css', '.partial-marker { color: orange; }');
        file_put_contents(
            $themeDir . '/assets/main.css',
            '@import url("partial.css");' . "\n" . '.main-marker { color: blue; }'
        );

        try {
            $css = $this->compileCssTo($themeDir, 'assets/main.css');
            $this->assertStringContainsString('partial-marker', $css);
            $this->assertStringContainsString('main-marker', $css);
        } finally {
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * SCSS `@import` resolution must stay within the allowed import roots, the same
     * as the LESS and JavaScript importers above.
     */
    public function testScssCompilerBlocksRelativeTraversalImport()
    {
        $themeDir = $this->makeTempThemeDir();
        $secretPath = dirname($themeDir) . '/scss-secret-' . bin2hex(random_bytes(4)) . '.scss';
        file_put_contents($secretPath, '.leak { content: "combine-leak-canary"; }');
        file_put_contents(
            $themeDir . '/assets/poc.scss',
            '@import "../../' . basename($secretPath, '.scss') . '"; .x { color: red; }'
        );

        try {
            $css = $this->compileScssTo($themeDir, 'assets/poc.scss');
            $this->assertStringNotContainsString('combine-leak-canary', $css);
        } finally {
            @unlink($secretPath);
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * Legitimate same-tree `@import "partial"` must still resolve, including a
     * partial that imports another partial of its own.
     */
    public function testScssCompilerAllowsLegitimatePartial()
    {
        $themeDir = $this->makeTempThemeDir();
        mkdir($themeDir . '/assets/sub', 0777, true);
        file_put_contents($themeDir . '/assets/sub/_deep.scss', '.deep-marker { color: purple; }');
        file_put_contents($themeDir . '/assets/_partial.scss', '@import "sub/deep"; .partial-marker { color: orange; }');
        file_put_contents($themeDir . '/assets/main.scss', '@import "partial"; .main-marker { color: blue; }');

        try {
            $css = $this->compileScssTo($themeDir, 'assets/main.scss');
            $this->assertStringContainsString('deep-marker', $css);
            $this->assertStringContainsString('partial-marker', $css);
            $this->assertStringContainsString('main-marker', $css);
        } finally {
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * Imports from outside the asset's own directory must keep working when they
     * land in one of the roots CombineAssets allows (themes, plugins, modules).
     */
    public function testScssCompilerAllowsImportFromAllowedRoot()
    {
        $themeDir = $this->makeTempThemeDir();
        $sharedDir = themes_path('scss-import-root-test-' . bin2hex(random_bytes(4)));
        mkdir($sharedDir, 0777, true);
        file_put_contents($sharedDir . '/_shared.scss', '.shared-marker { color: green; }');
        // SCSS string literals treat a backslash as an escape, so use forward slashes
        // for the absolute path on Windows.
        file_put_contents(
            $themeDir . '/assets/main.scss',
            '@import "' . str_replace('\\', '/', $sharedDir) . '/shared"; .main-marker { color: blue; }'
        );

        try {
            $css = $this->compileScssTo($themeDir, 'assets/main.scss');
            $this->assertStringContainsString('shared-marker', $css);
            $this->assertStringContainsString('main-marker', $css);
        } finally {
            \File::deleteDirectory($sharedDir);
            \File::deleteDirectory($themeDir);
        }
    }

    /**
     * Writes a `.css` proof-of-concept asset plus a secret file outside every allowed
     * import root. `%SECRET%` is substituted with the secret's absolute path and
     * `%SECRET_RELATIVE%` with its path relative to the filesystem root, for building
     * `..`-traversal payloads.
     *
     * @return array{0:string,1:string} [theme dir, secret path]
     */
    protected function setupCssLeakFixture(string $pocTemplate): array
    {
        $themeDir = $this->makeTempCssThemeDir();
        // The name carries no canary: a rejected `@import` is emitted verbatim, so a
        // recognisable filename in the statement would look like a leak of the file's
        // contents.
        $secretPath = tempnam(sys_get_temp_dir(), 'wn-sec-');
        file_put_contents($secretPath, "APP_KEY=combine-leak-canary\n");

        $poc = str_replace(
            ['%SECRET%', '%SECRET_RELATIVE%'],
            [$secretPath, ltrim($secretPath, '/')],
            $pocTemplate
        );
        file_put_contents($themeDir . '/assets/poc.css', $poc);

        return [$themeDir, $secretPath];
    }

    /**
     * A theme directory under the real `themes_path()`, so that the combiner's CSS
     * import validator — which confines imports to themes/plugins/modules — sees the
     * fixture the way it sees a real theme asset.
     */
    protected function makeTempCssThemeDir(): string
    {
        $themeDir = themes_path('wn-sec-test-' . bin2hex(random_bytes(4)));
        mkdir($themeDir . '/assets', 0777, true);
        return $themeDir;
    }

    protected function compileCssTo(string $themeDir, string $relativeAsset): string
    {
        $dest = sys_get_temp_dir() . '/winter-combine-out-' . bin2hex(random_bytes(4)) . '.css';
        try {
            CombineAssets::instance()->combineToFile([$relativeAsset], $dest, $themeDir);
            return file_get_contents($dest) ?: '';
        } finally {
            @unlink($dest);
        }
    }

    protected function compileScssTo(string $themeDir, string $relativeAsset): string
    {
        $dest = sys_get_temp_dir() . '/winter-combine-out-' . bin2hex(random_bytes(4)) . '.css';
        try {
            CombineAssets::instance()->combineToFile([$relativeAsset], $dest, $themeDir);
            return file_get_contents($dest) ?: '';
        } finally {
            @unlink($dest);
        }
    }

    protected function compileJsTo(string $themeDir, string $relativeAsset): string
    {
        $dest = sys_get_temp_dir() . '/winter-combine-out-' . bin2hex(random_bytes(4)) . '.js';
        try {
            CombineAssets::instance()->combineToFile([$relativeAsset], $dest, $themeDir);
            return file_get_contents($dest) ?: '';
        } finally {
            @unlink($dest);
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

    /**
     * Builds a theme whose `assets/less/poc.less` imports `assets/less/sub/child.less`,
     * with `$childTemplate` as the child's contents and `%SECRET%` replaced by the
     * basename of a canary file written just outside the theme tree (and outside every
     * allowed import root).
     *
     * @return array{0:string,1:string} [theme dir, secret path]
     */
    protected function setupNestedLessLeakFixture(string $childTemplate): array
    {
        $themeDir = $this->makeTempThemeDir();
        $secretPath = dirname($themeDir) . '/less-secret-' . bin2hex(random_bytes(4)) . '.txt';
        file_put_contents($secretPath, "APP_KEY=combine-leak-canary\n");

        mkdir($themeDir . '/assets/less/sub');
        file_put_contents($themeDir . '/assets/less/poc.less', '@import "sub/child.less";');
        file_put_contents(
            $themeDir . '/assets/less/sub/child.less',
            str_replace('%SECRET%', basename($secretPath), $childTemplate)
        );

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
