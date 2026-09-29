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
        \Illuminate\Support\Facades\Cache::forget(BrandSetting::instance()->cacheKey);
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
     * Regression for GHSA-5cwr-5jxg-pcf6. renderCss() caches the raw compiler
     * output, so sanitizing only the cache-miss return leaves every later cache
     * hit unsanitized. The first render primes the cache; the second is the one
     * that used to emit active markup into the backend <style> block.
     */
    public function testRenderCssStripsScriptTagsOnCacheHit()
    {
        $maliciousCss = '.x { content: ~"</style><script>alert(1)</script><style>"; }';

        BrandSetting::set('custom_css', $maliciousCss);

        \System\Behaviors\SettingsModel::clearInternalCache();
        \Illuminate\Support\Facades\Cache::forget(BrandSetting::instance()->cacheKey);

        // Cache miss, primes the cache
        BrandSetting::renderCss();

        // Cache hit
        $renderedCss = BrandSetting::renderCss();

        $this->assertStringNotContainsString('<script>', $renderedCss);
        $this->assertStringNotContainsString('</script>', $renderedCss);
        $this->assertStringNotContainsString('</style>', $renderedCss);
    }

    /**
     * A cache entry poisoned before GHSA-5cwr-5jxg-pcf6 was patched is not
     * cleared by upgrading, so it must still be sanitized when read back.
     */
    public function testRenderCssStripsScriptTagsFromExistingCacheEntry()
    {
        \Illuminate\Support\Facades\Cache::forever(
            BrandSetting::instance()->cacheKey,
            '.x{content:</style><script>alert(1)</script><style>}'
        );

        $renderedCss = BrandSetting::renderCss();

        $this->assertStringNotContainsString('<script>', $renderedCss);
        $this->assertStringNotContainsString('</script>', $renderedCss);
        $this->assertStringNotContainsString('</style>', $renderedCss);
    }

    /**
     * Test that normal CSS content is preserved through renderCss, on both the
     * cache miss and the cache hit that follows it.
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

        // Sanitizing the cache hit must not alter legitimate CSS
        $this->assertEquals($renderedCss, BrandSetting::renderCss());
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
