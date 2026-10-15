<?php

namespace System\Tests\Classes;

use Config;
use System\Classes\CombineAssets;
use System\Classes\SystemController;
use System\Tests\Bootstrap\TestCase;

class SystemControllerTest extends TestCase
{
    protected string $themeDir;

    public function setUp(): void
    {
        parent::setUp();

        // Must live under base_path(), which CombineAssets uses as the asset root.
        $this->themeDir = base_path('storage/framework/cache/combine-tests/theme-' . bin2hex(random_bytes(4)));
        mkdir($this->themeDir . '/assets', 0777, true);
        file_put_contents($this->themeDir . '/assets/broken.scss', '.x { color: red; ');
    }

    public function tearDown(): void
    {
        \File::deleteDirectory($this->themeDir);

        parent::tearDown();
    }

    public function testCombineHidesCompilerErrorsOutsideDebugMode()
    {
        Config::set('app.debug', false);

        $response = (new SystemController)->combine($this->combineBrokenAsset());

        $this->assertSame(500, $response->getStatusCode());
        $this->assertStringContainsString(trans('system::lang.combiner.error'), $response->getContent());
        $this->assertStringNotContainsString('unclosed block', $response->getContent());
    }

    public function testCombineShowsCompilerErrorsInDebugMode()
    {
        Config::set('app.debug', true);

        $response = (new SystemController)->combine($this->combineBrokenAsset());

        $this->assertSame(500, $response->getStatusCode());
        $this->assertStringContainsString('unclosed block', $response->getContent());
    }

    /**
     * Registers a combined asset that fails to compile and returns its combiner file name.
     */
    protected function combineBrokenAsset(): string
    {
        return basename(CombineAssets::combine(['assets/broken.scss'], $this->themeDir));
    }
}
