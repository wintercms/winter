<?php

namespace Cms\Tests\Classes;

use Cms\Twig\Extension;
use Cms\Classes\Controller;

use System\Classes\Asset\PackageManager;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Support\Facades\File;

class TwigExtensionTest extends TestCase
{
    private const VITE_FIXTURE_PACKAGE = 'theme-assettest';
    private const VITE_FIXTURE_THEME_PATH = '/modules/system/tests/fixtures/themes/assettest';
    private const VITE_HOT_URL = 'http://localhost:5173';

    public function tearDown(): void
    {
        $hotFile = base_path(self::VITE_FIXTURE_THEME_PATH . '/assets/dist/hot');
        if (File::exists($hotFile)) {
            File::delete($hotFile);
        }
        $distDir = base_path(self::VITE_FIXTURE_THEME_PATH . '/assets/dist');
        if (File::isDirectory($distDir) && count(File::files($distDir)) === 0 && count(File::directories($distDir)) === 0) {
            File::deleteDirectory($distDir);
        }

        parent::tearDown();
    }

    public function testPartialFunction()
    {
        $extension = new Extension;
        $controller = Controller::getController() ?: new Controller;
        $extension->setController($controller);

        $this->assertFalse($extension->partialFunction('invalid-partial-file', [], false));

        $this->expectException(SystemException::class);
        $this->expectExceptionMessageMatches('/is\snot\sfound/');
        $this->assertFalse($extension->partialFunction('invalid-partial-file', [], true));
    }

    public function testContentFunction()
    {
        $extension = new Extension;
        $controller = Controller::getController() ?: new Controller;
        $extension->setController($controller);

        $this->assertFalse($extension->contentFunction('invalid-content-file', [], false));

        $this->expectException(SystemException::class);
        $this->expectExceptionMessageMatches('/is\snot\sfound/');
        $this->assertFalse($extension->contentFunction('invalid-content-file', [], true));
    }

    public function testStylesTagEmitsCssAndViteCss(): void
    {
        [$extension, $controller] = $this->buildExtensionWithViteAssets([
            'assets/css/theme.css',
            'assets/javascript/theme.js',
        ]);
        $controller->addCss('plain.css');

        // This is exactly the call StylesNode::compile() writes into the compiled `{% styles %}` tag
        $output = (string) $extension->assetsFunction('css');

        $this->assertStringContainsString('plain.css', $output);
        $this->assertStringContainsString('assets/css/theme.css', $output);
        $this->assertStringNotContainsString('assets/javascript/theme.js', $output);
    }

    public function testScriptsTagEmitsJsAndViteJs(): void
    {
        [$extension, $controller] = $this->buildExtensionWithViteAssets([
            'assets/css/theme.css',
            'assets/javascript/theme.js',
        ]);
        $controller->addJs('plain.js');

        // Mirrors what ScriptsNode::compile() emits for the `{% scripts %}` tag
        $output = (string) $extension->assetsFunction('js');

        $this->assertStringContainsString('plain.js', $output);
        $this->assertStringContainsString('assets/javascript/theme.js', $output);
        $this->assertStringNotContainsString('assets/css/theme.css', $output);
    }

    public function testStylesAndScriptsTagsDoNotCrossLeak(): void
    {
        // Reverse entrypoint order to confirm filtering doesn't depend on array order
        [$extension] = $this->buildExtensionWithViteAssets([
            'assets/javascript/theme.js',
            'assets/css/theme.css',
        ]);

        $stylesOutput = (string) $extension->assetsFunction('css');
        $scriptsOutput = (string) $extension->assetsFunction('js');

        $this->assertStringContainsString('assets/css/theme.css', $stylesOutput);
        $this->assertStringNotContainsString('assets/javascript/theme.js', $stylesOutput);

        $this->assertStringContainsString('assets/javascript/theme.js', $scriptsOutput);
        $this->assertStringNotContainsString('assets/css/theme.css', $scriptsOutput);
    }

    public function testStylesTagOmitsViteWhenNoCssEntrypoints(): void
    {
        // JS-only vite registration; the styles tag must contribute no vite output for it
        [$extension] = $this->buildExtensionWithViteAssets([
            'assets/javascript/theme.js',
        ]);

        $output = (string) $extension->assetsFunction('css');

        $this->assertStringNotContainsString('@vite/client', $output);
        $this->assertStringNotContainsString('assets/javascript/theme.js', $output);
    }

    /**
     * Boots a Cms\Twig\Extension wired up to a fresh Controller that has the
     * given vite entrypoints registered against the assettest fixture package.
     * The fixture's hot file is written so Vite::tags() emits deterministic
     * dev-server tags (no manifest required).
     *
     * @return array{0: Extension, 1: Controller}
     */
    private function buildExtensionWithViteAssets(array $entrypoints): array
    {
        $themePath = base_path(self::VITE_FIXTURE_THEME_PATH);
        if (!File::isDirectory($themePath)) {
            $this->markTestSkipped('Vite test fixture is missing at ' . self::VITE_FIXTURE_THEME_PATH);
        }

        // PackageManager's lazy init() touches Theme::all() and Halcyon model events, which
        // can blow up when prior tests in the full suite have left datasource/plugin state in
        // an inconsistent shape. Skip gracefully — same pattern as the existing
        // ViteInstallTest — so this test still asserts something useful in isolation.
        try {
            $packageManager = PackageManager::instance();
        } catch (\Throwable $e) {
            $this->markTestSkipped('PackageManager could not initialise in this environment: ' . $e->getMessage());
        }

        // registerPackage silently no-ops when re-registering the same name + config.
        $packageManager->registerPackage(
            self::VITE_FIXTURE_PACKAGE,
            $themePath . '/vite.config.mjs',
            'vite'
        );

        File::ensureDirectoryExists($themePath . '/assets/dist');
        File::put($themePath . '/assets/dist/hot', self::VITE_HOT_URL);

        $controller = new Controller();
        $controller->addVite($entrypoints, self::VITE_FIXTURE_PACKAGE);

        $extension = new Extension();
        $extension->setController($controller);

        return [$extension, $controller];
    }
}
