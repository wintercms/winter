<?php

namespace Cms\Tests\Twig;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Cms\Twig\Extension as CmsTwigExtension;
use Cms\Twig\Loader as CmsTwigLoader;
use Cms\Twig\SnowboardNode;
use System\Classes\MarkupManager;
use System\Tests\Bootstrap\TestCase;
use System\Tests\Concerns\AssertsNoDeprecations;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Flash;

/**
 * Renders every CMS Twig tag, guarding both their output and against Twig deprecations
 * (yield readiness, parser and node APIs) creeping back in.
 */
class TagsTest extends TestCase
{
    use AssertsNoDeprecations;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.themesPath', '/modules/cms/tests/fixtures/themes');
        Config::set('cms.twigNoCache', true);

        // The base scripts are only emitted by the first {% snowboard %} compiled in a process.
        SnowboardNode::$baseLoaded = false;
    }

    public function testTagsRenderWithoutDeprecations(): void
    {
        $controller = $this->makeController();

        $output = $this->assertNoDeprecations(function () use ($controller) {
            return $this->renderTags($controller->getTwig(), 'default');
        });

        $this->assertSame($this->expectedOutput(), $output);
    }

    public function testTagsRenderTheSameWithUseYield(): void
    {
        $controller = $this->makeController();

        // use_yield makes Twig reject any node not flagged #[YieldReady], and breaks output-buffer capture.
        $twig = MarkupManager::makeBaseTwigEnvironment(new CmsTwigLoader, ['use_yield' => true]);
        $twig->addExtension($extension = new CmsTwigExtension);
        $extension->setController($controller);

        $output = $this->assertNoDeprecations(function () use ($twig) {
            return $this->renderTags($twig, 'yield');
        });

        $this->assertSame($this->expectedOutput(), $output);
    }

    protected function makeController(): Controller
    {
        $controller = new Controller(Theme::load('test'));
        // Running a page gives the controller the page and component state the tags render against.
        $controller->run('/component-custom-render');

        return $controller;
    }

    protected function renderTags($twig, string $variant): string
    {
        Flash::success('Saved');
        Flash::error('Failed');

        // The variant comment keeps each environment compiling its own template class.
        $template = '{# '.$variant.' #}'.file_get_contents(__DIR__.'/../fixtures/twig/tags.htm');

        $output = $twig->createTemplate($template)->render([
            'name' => '<World>',
            'items' => ['a', 'b'],
            'when' => \Carbon\Carbon::parse('2026-03-04 05:06:07'),
            'interval' => new \DateInterval('P3D'),
        ]);

        // The cache-busting build number depends on what earlier tests stored, and the asset tags
        // end lines with PHP_EOL (and Windows checkouts give the fixture CRLF), so compare on LF.
        $output = preg_replace('/\?v=[^"]*"/', '?v=BUILD"', $output);

        return str_replace("\r\n", "\n", $output);
    }

    protected function expectedOutput(): string
    {
        return str_replace("\r\n", "\n", file_get_contents(__DIR__.'/../fixtures/twig/tags-expected.txt'));
    }
}
