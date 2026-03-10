<?php

namespace Backend\Tests\Models;

use Backend\Models\EditorSetting;
use System\Tests\Bootstrap\PluginTestCase;

class EditorSettingTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Reset the cached instance so each test starts fresh
        \System\Behaviors\SettingsModel::clearInternalCache();
    }

    public function tearDown(): void
    {
        // Clean up the settings record
        EditorSetting::instance()->resetDefault();
        \System\Behaviors\SettingsModel::clearInternalCache();

        parent::tearDown();
    }

    /**
     * Test that renderCss output does not contain script tags even when
     * malicious CSS using LESS escape syntax is stored in the database.
     */
    public function testRenderCssStripsScriptTags()
    {
        $maliciousStyles = '.x { content: ~"</style><script>alert(1)</script><style>"; }';

        EditorSetting::set('html_custom_styles', $maliciousStyles);

        \System\Behaviors\SettingsModel::clearInternalCache();
        \Illuminate\Support\Facades\Cache::forget(EditorSetting::instance()->cacheKey);

        $renderedCss = EditorSetting::renderCss();

        $this->assertStringNotContainsString('<script>', $renderedCss);
        $this->assertStringNotContainsString('</script>', $renderedCss);
        $this->assertStringNotContainsString('</style>', $renderedCss);
    }

    /**
     * Test that normal CSS content is preserved through renderCss.
     */
    public function testRenderCssPreservesNormalCss()
    {
        $normalStyles = '.my-class { color: blue; font-weight: bold; }';

        EditorSetting::set('html_custom_styles', $normalStyles);

        \System\Behaviors\SettingsModel::clearInternalCache();
        \Illuminate\Support\Facades\Cache::forget(EditorSetting::instance()->cacheKey);

        $renderedCss = EditorSetting::renderCss();

        $this->assertStringContainsString('color', $renderedCss);
        $this->assertStringContainsString('font-weight', $renderedCss);
        $this->assertDoesNotMatchRegularExpression('/<[a-z\/!]/', $renderedCss);
    }
}
