<?php

namespace System\Tests\Traits;

use System\Traits\ViewMaker;
use TestCase;

class ViewMakerStub
{
    use ViewMaker; // Needed for guessViewPath(), which is used to set default assetPath

    public function __construct()
    {
        $this->layoutPath = $this->guessViewPath('/layouts');
    }
}

class ViewMakerTest extends TestCase
{
    private $stub;
    private $relativePath;

    protected function normalizePath(string $path): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    public function setUp() : void
    {
        $this->createApplication();
        $this->stub = new ViewMakerStub();
        $this->relativePath = $this->normalizePath('tests/unit/system/traits/viewmakerstub');
    }

    public function testViewPaths()
    {
        // Tests guessViewPath() and guessViewPathFrom()
        $this->assertEquals(
            $this->normalizePath(base_path($this->relativePath)),
            $this->normalizePath($this->stub->guessViewPath())
        );

        // Request a view path first to set the default
        $path = $this->stub->getViewPath('_overridden.htm');
        $this->assertEquals(
            $this->normalizePath(base_path("$this->relativePath/_overridden.htm")),
            $this->normalizePath($path)
        );

        // Test addViewPath() & getViewPaths()
        $overridePath = "~/{$this->relativePath}override";
        $this->stub->addViewPath($overridePath);
        $this->assertEquals(
            [
                $this->normalizePath($overridePath),
                $this->normalizePath(base_path($this->relativePath)),
            ],
            array_map(function ($path) {
                return $this->normalizePath($path);
            }, $this->stub->getViewPaths())
        );

        // Test override taking effect
        $this->assertEquals('overridden', trim($this->stub->makePartial('overridden')));

        // Test getViewPath()
        $paths = [
            'non_existant' => 'non_existant',
            'view.htm' => base_path($this->relativePath . '/view.htm'),
            '_overridden.htm' => base_path($this->relativePath . 'override/_overridden.htm'),
            "~/{$this->relativePath}/symbols.htm" => base_path($this->relativePath . '/symbols.htm'),
        ];

        foreach ($paths as $path => $expected) {
            $this->assertEquals($this->normalizePath($expected), $this->normalizePath($this->stub->getViewPath($path)));
        }
    }

    public function testMakePartial()
    {
        $sectionContent = '<!-- Section -->' . PHP_EOL
        . '<div class="field-section">' . PHP_EOL
        . '            <h4>label</h4>' . PHP_EOL
        . '    ' . PHP_EOL
        . '            <p class="help-block">comment</p>' . PHP_EOL
        . '    </div>';

        // Test various paths that are accepted when rendering a partial
        $partials = [
            'relative_no_ext' => 'relative no ext',
            'folder/no_ext' => 'folder no ext',
            "{$this->relativePath}/relative_no_ext" => 'relative no ext',
            "~/{$this->relativePath}/_relative_no_ext.htm" => 'relative no ext',
            "~/{$this->relativePath}/symbols.htm" => 'symbols content',
            'modules/backend/widgets/form/partials/field_section' => $sectionContent,
            '~/modules/backend/widgets/form/partials/_field_section.htm' => $sectionContent,
        ];

        foreach ($partials as $partial => $expected) {
            $contents = $this->stub->makePartial($partial, [
                'field' => (object) [
                    'label' => 'label',
                    'comment' => 'comment',
                    'commentHtml' => false
                ],
            ]);
            $this->assertEquals($expected, trim($contents));
        }
    }

    public function testMakeView()
    {
        $this->stub->layout = 'default';
        $this->assertEquals('layout contents-view contents', trim($this->stub->makeView('view')));
    }

    public function testMakeViewContent()
    {
        // No layout set, return contents unmodified
        $this->stub->layout = '';
        $this->assertEquals('input', $this->stub->makeViewContent('input'));

        // Layout set, return contents rendered in layout
        $this->stub->layout = 'default';
        $this->assertEquals('layout contents-input', trim($this->stub->makeViewContent('input')));
    }

    public function testMakeLayout()
    {
        $this->stub->layout = 'default';
        $this->assertEquals('layout contents-', trim($this->stub->makeLayout()));
    }

    public function testMakeLayoutPartial()
    {
        $this->assertEquals('layout partial contents', trim($this->stub->makeLayoutPartial('layout_partial')));
    }
}
