<?php

class ViewMakerStub
{
    use System\Traits\ViewMaker; // Needed for guessViewPath(), which is used to set default assetPath

    public function __construct()
    {
        $this->layoutPath = $this->guessViewPath('/layouts');
    }
}

class ViewMakerTest extends TestCase
{
    private $stub;
    private $relativePath;

    public function setUp() : void
    {
        $this->createApplication();
        $this->stub = new ViewMakerStub();
        $this->relativePath = 'tests/unit/system/traits/viewmakerstub';
    }

    public function testViewPaths()
    {
        // Tests guessViewPath() and guessViewPathFrom()
        $this->assertEquals(base_path($this->relativePath), $this->stub->guessViewPath());

        // Request a view path first to set the default
        $path = $this->stub->getViewPath('_overridden.php');
        $this->assertEquals(base_path("$this->relativePath/_overridden.php"), $path);

        // Test addViewPath() & getViewPaths()
        $overridePath = "~/{$this->relativePath}override";
        $this->stub->addViewPath($overridePath);
        $this->assertEquals(
            [
                $overridePath,
                base_path($this->relativePath),
            ],
            $this->stub->getViewPaths()
        );

        // Test override taking effect
        $this->assertEquals('overridden', $this->stub->makePartial('overridden'));

        // Test override of PHP base file with HTM overriding file
        $this->assertEquals('overridden', $this->stub->makePartial('can_override_php_with_htm'));

        // Test getViewPath()
        $paths = [
            'non_existant' => 'non_existant',
            'view.php' => base_path($this->relativePath . '/view.php'),
            '_overridden.php' => base_path($this->relativePath . 'override/_overridden.php'),
            '_can_override_php_with_htm.php' => base_path($this->relativePath . 'override/_can_override_php_with_htm.htm'),
            "~/{$this->relativePath}/symbols.php" => base_path($this->relativePath . '/symbols.php'),
        ];

        foreach ($paths as $path => $expected) {
            $this->assertEquals($expected, $this->stub->getViewPath($path));
        }
    }

    public function testMakePartial()
    {
        // Test various paths that are accepted when rendering a partial
        $partials = [
            'relative_no_ext' => 'relative no ext',
            'folder/no_ext' => 'folder no ext',
            "~/{$this->relativePath}/symbols.htm" => 'symbols content',
            "~/{$this->relativePath}/symbols.php" => 'symbols content',
            "~/{$this->relativePath}/specific.htm" => 'explicit htm path, but actually php',
        ];

        foreach ($partials as $partial => $expected) {
            $contents = $this->stub->makePartial($partial);
            $this->assertEquals($expected, $contents);
        }
    }

    public function testMakeView()
    {
        $this->stub->layout = 'default';
        $this->assertEquals('layout contents-view contents', $this->stub->makeView('view'));
    }

    public function testMakeViewContent()
    {
        // No layout set, return contents unmodified
        $this->stub->layout = '';
        $this->assertEquals('input', $this->stub->makeViewContent('input'));

        // Layout set, return contents rendered in layout
        $this->stub->layout = 'default';
        $this->assertEquals('layout contents-input', $this->stub->makeViewContent('input'));
    }

    public function testMakeLayout()
    {
        $this->stub->layout = 'default';
        $this->assertEquals('layout contents-', $this->stub->makeLayout());
    }

    public function testMakeLayoutPartial()
    {
        $this->assertEquals('layout partial contents', $this->stub->makeLayoutPartial('layout_partial'));
    }

    // public function testGetViewPath()
    // {
    //     /**
    //      * Input paths:
    //      *  - basename
    //      *  - relative/folder/basename
    //      *  - filename.ext
    //      *  - relative/folder/filename.ext
    //      *  - ~/modules/system/symbol/filename.htm
    //      *  - $/plugins/path/symbol/filename.htm
    //      */

    //     $paths = [
    //         'relative_no_ext',
    //         'relative.htm',
    //         'relative.php',

    //         'relative/folder/no_ext',
    //         'relative/folder/ext.htm',
    //         'relative/folder/ext.php',

    //         '~/base_path/ext.htm',
    //         '~/base_path/ext.php',
    //         '$/plugin_path/ext.htm',
    //         '$/plugin_path/ext.php',
    //     ];
    // }
}
