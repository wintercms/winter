<?php

namespace Backend\Tests\Models;

use Backend\Models\BrandSetting;
use System\Tests\Bootstrap\PluginTestCase;

class BrandSettingTest extends PluginTestCase
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
        BrandSetting::instance()->resetDefault();
        \System\Behaviors\SettingsModel::clearInternalCache();

        parent::tearDown();
    }

    /**
     * Test that renderCss output does not contain script tags even when
     * malicious CSS using LESS escape syntax is stored in the database.
     */
    public function testRenderCssStripsScriptTags()
    {
        $maliciousCss = '.x { content: ~"</style><script>alert(1)</script><style>"; }';

        BrandSetting::set('custom_css', $maliciousCss);

        \System\Behaviors\SettingsModel::clearInternalCache();
        \Illuminate\Support\Facades\Cache::forget(BrandSetting::instance()->cacheKey);

        $renderedCss = BrandSetting::renderCss();

        $this->assertStringNotContainsString('<script>', $renderedCss);
        $this->assertStringNotContainsString('</script>', $renderedCss);
        $this->assertStringNotContainsString('</style>', $renderedCss);
    }

    /**
     * Test that normal CSS content is preserved through renderCss.
     */
    public function testRenderCssPreservesNormalCss()
    {
        $normalCss = '.my-class { color: red; font-size: 14px; }';

        BrandSetting::set('custom_css', $normalCss);

        \System\Behaviors\SettingsModel::clearInternalCache();
        \Illuminate\Support\Facades\Cache::forget(BrandSetting::instance()->cacheKey);

        $renderedCss = BrandSetting::renderCss();

        $this->assertStringContainsString('color', $renderedCss);
        $this->assertStringContainsString('font-size', $renderedCss);
        $this->assertDoesNotMatchRegularExpression('/<[a-z\/!]/', $renderedCss);
    }

    /**
     * Regression for GHSA-58fp-mcx6-7qf9. A user-supplied `@import (inline)`
     * directive in `custom_css` must not be able to disclose server files.
     */
    public function testRenderCssBlocksImportAttack()
    {
        $tmpSecret = tempnam(sys_get_temp_dir(), 'brandsetting-leak-canary-');
        file_put_contents($tmpSecret, "APP_KEY=do-not-leak-via-brandsetting\n");

        try {
            BrandSetting::set('custom_css', '@import (inline) "' . $tmpSecret . '";');

            \System\Behaviors\SettingsModel::clearInternalCache();
            \Illuminate\Support\Facades\Cache::forget(BrandSetting::instance()->cacheKey);

            $renderedCss = BrandSetting::renderCss();

            $this->assertStringNotContainsString('APP_KEY', $renderedCss);
            $this->assertStringNotContainsString('do-not-leak-via-brandsetting', $renderedCss);
        } finally {
            @unlink($tmpSecret);
        }
    }
}
