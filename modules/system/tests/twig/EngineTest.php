<?php

namespace System\Tests\Twig;

use RuntimeException;
use System\Tests\Bootstrap\TestCase;
use System\Twig\Engine;
use System\Twig\Loader;
use Throwable;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TemplateWrapper;

class EngineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Loader::$allowInclude = false;
    }

    protected function tearDown(): void
    {
        Loader::$allowInclude = false;

        parent::tearDown();
    }

    /**
     * A successful render must leave the include gate exactly as it found it.
     */
    public function testIncludeGateIsRestoredAfterASuccessfulRender()
    {
        $engine = new Engine(new Environment(new ArrayLoader(['ok.htm' => 'rendered'])));

        $this->assertFalse(Loader::$allowInclude);
        $this->assertEquals('rendered', $engine->get('ok.htm'));
        $this->assertFalse(Loader::$allowInclude);
    }

    /**
     * The gate must also be restored when loading throws, otherwise arbitrary local file
     * inclusion stays enabled for the rest of the process.
     */
    public function testIncludeGateIsRestoredWhenLoadingThrows()
    {
        $engine = new Engine($this->makeThrowingEnvironment());

        $this->assertFalse(Loader::$allowInclude);

        try {
            $engine->get('missing.htm');
            $this->fail('The throwing environment should have propagated its exception.');
        }
        catch (RuntimeException $ex) {
            $this->assertEquals('Unable to load the template.', $ex->getMessage());
        }

        $this->assertFalse(
            Loader::$allowInclude,
            'A thrown load must not leave arbitrary local file inclusion enabled.'
        );
    }

    /**
     * The security consequence of the above: with the gate leaked, the loader resolves any
     * readable local path as a template.
     */
    public function testArbitraryLocalFilesCannotResolveAfterAThrownLoad()
    {
        $engine = new Engine($this->makeThrowingEnvironment());
        $localFile = base_path('composer.json');

        $this->assertFileExists($localFile);
        $this->assertFalse((new Loader())->exists($localFile));

        try {
            $engine->get('missing.htm');
        }
        catch (Throwable $ex) {
            // Expected; the assertion below covers the state it left behind.
        }

        $this->assertFalse(
            (new Loader())->exists($localFile),
            'An arbitrary readable file must not resolve as a template after a failed render.'
        );
    }

    /**
     * The gate is restored to its previous value rather than hard-coded to false, so a nested
     * render inside an outer render does not close the outer one's gate early.
     */
    public function testIncludeGateIsRestoredToItsPreviousValueNotForcedFalse()
    {
        $engine = new Engine($this->makeThrowingEnvironment());

        Loader::$allowInclude = true;

        try {
            $engine->get('missing.htm');
        }
        catch (Throwable $ex) {
            // Expected.
        }

        $this->assertTrue(
            Loader::$allowInclude,
            'The previous gate value must be restored, not replaced with false.'
        );
    }

    /**
     * An environment whose template loading always fails, standing in for a missing template or
     * a template containing a syntax error.
     */
    protected function makeThrowingEnvironment(): Environment
    {
        return new class (new ArrayLoader([])) extends Environment
        {
            public function load($name): TemplateWrapper
            {
                throw new RuntimeException('Unable to load the template.');
            }
        };
    }
}
