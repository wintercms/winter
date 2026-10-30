<?php

namespace Cms\Tests\Twig;

use Cms\Twig\Extension as CmsTwigExtension;
use Cms\Twig\SnowboardNode;
use System\Classes\CombineAssets;
use System\Classes\MarkupManager;
use System\Models\Parameter;
use System\Tests\Bootstrap\PluginTestCase;
use Twig\Loader\ArrayLoader;
use Twig\Source;

/**
 * Covers the `{% framework %}` and `{% snowboard %}` tags, both of which append the stored
 * core build identifier to every asset URL they emit as a cache-busting query string.
 *
 * Both tags generate PHP source rather than rendering directly, and that source is cached to
 * disk and executed on every subsequent request, so the build identifier -- a stored, free-form
 * value -- has to be emitted through the compiler's own string representation.
 */
class AssetTagsTest extends PluginTestCase
{
    /**
     * A stored build identifier that is not a plain version string. `UpdateManager::setBuild()`
     * refuses to store this, but the parameter store itself is free-form, so the tags must not
     * depend on that check.
     */
    private const AWKWARD_BUILD = "1'.strtoupper('marker').'";

    public function setUp(): void
    {
        parent::setUp();

        // Each test compiles the base Snowboard payload, which is only emitted once per request.
        SnowboardNode::$baseLoaded = false;
    }

    protected function makeTwig(array $templates = []): \Twig\Environment
    {
        $twig = MarkupManager::makeBaseTwigEnvironment(new ArrayLoader($templates));
        $twig->addExtension(new CmsTwigExtension());

        return $twig;
    }

    /**
     * Renders template markup through a CMS Twig environment.
     *
     * A compiled template class is keyed on its source and stays declared for the rest of the
     * process, so each call must be given distinct markup to force a fresh compile. The marker
     * is a Twig comment and contributes nothing to the output.
     */
    protected function render(string $markup, string $marker): string
    {
        return $this->makeTwig()->createTemplate($markup . '{# ' . $marker . ' #}')->render();
    }

    protected function compile(string $markup, string $name): string
    {
        return $this->makeTwig([$name => $markup])->compileSource(new Source($markup, $name));
    }

    public function testFrameworkTagEmitsTheBuildIdentifierAsData(): void
    {
        Parameter::set(['system::core.build' => self::AWKWARD_BUILD]);

        $source = $this->compile('{% framework extras %}', 'framework_awkward');
        $output = $this->render('{% framework extras %}', 'framework_awkward');

        $this->assertStringNotContainsString("strtoupper('marker')", $source);
        $this->assertStringNotContainsString('MARKER', $output);
        $this->assertStringContainsString('?v=' . self::AWKWARD_BUILD, $output);
    }

    public function testSnowboardTagEmitsTheBuildIdentifierAsData(): void
    {
        Parameter::set(['system::core.build' => self::AWKWARD_BUILD]);

        $source = $this->compile('{% snowboard all %}', 'snowboard_awkward');
        SnowboardNode::$baseLoaded = false;
        $output = $this->render('{% snowboard all %}', 'snowboard_awkward');

        $this->assertStringNotContainsString("strtoupper('marker')", $source);
        $this->assertStringNotContainsString('MARKER', $output);
        $this->assertStringContainsString('?v=' . self::AWKWARD_BUILD, $output);
    }

    public function testFrameworkTagStillCacheBustsWithAVersionBuild(): void
    {
        Parameter::set(['system::core.build' => '1.2.14']);

        $output = $this->render('{% framework extras %}', 'framework_version');

        $this->assertStringContainsString('/modules/system/assets/js/framework.js?v=1.2.14"></script>', $output);
        $this->assertStringContainsString('/modules/system/assets/js/framework.extras.js?v=1.2.14"></script>', $output);
        $this->assertStringContainsString('/modules/system/assets/css/framework.extras.css?v=1.2.14">', $output);
    }

    public function testSnowboardTagStillCacheBustsWithAVersionBuild(): void
    {
        Parameter::set(['system::core.build' => '1.2.14']);

        $output = $this->render('{% snowboard all %}', 'snowboard_version');

        $this->assertStringContainsString('data-module="snowboard-manifest"', $output);
        $this->assertStringContainsString('/modules/system/assets/js/build/manifest.js?v=1.2.14"></script>', $output);
        $this->assertStringContainsString('/snowboard/build/snowboard.vendor.js?v=1.2.14"></script>', $output);
        $this->assertStringContainsString('/snowboard/build/snowboard.base.js?v=1.2.14"></script>', $output);
        $this->assertStringContainsString('data-module="request"', $output);
        $this->assertStringContainsString('data-module="attr"', $output);
        $this->assertStringContainsString('data-module="extras"', $output);
    }

    /** The minified variants are still chosen at render time, not compile time. */
    public function testFrameworkTagHonoursTheMinifySettingAtRuntime(): void
    {
        Parameter::set(['system::core.build' => '1.2.14']);

        $combiner = CombineAssets::instance();
        $original = $combiner->useMinify;

        try {
            $combiner->useMinify = true;
            $minified = $this->render('{% framework extras %}', 'framework_minified');

            $combiner->useMinify = false;
            $plain = $this->render('{% framework %}', 'framework_plain');
        } finally {
            $combiner->useMinify = $original;
        }

        $this->assertStringContainsString('framework.combined-min.js?v=1.2.14', $minified);
        $this->assertStringContainsString('framework.extras-min.css?v=1.2.14', $minified);
        $this->assertStringContainsString('/modules/system/assets/js/framework.js?v=1.2.14', $plain);
    }
}
