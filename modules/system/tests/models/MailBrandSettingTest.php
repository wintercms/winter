<?php

namespace System\Tests\Models;

use System\Controllers\MailBrandSettings;
use System\Models\MailBrandSetting;
use System\Tests\Bootstrap\PluginTestCase;

class MailBrandSettingTest extends PluginTestCase
{
    /**
     * A CSS variable value that would close the generated ruleset and emit a literal
     * `</style>` followed by an element into the compiled stylesheet.
     */
    const BREAKOUT_VALUE = 'red; .xss::before { content: "</style><img src=x onerror=alert(1)>"; }';

    public function setUp(): void
    {
        parent::setUp();

        \System\Behaviors\SettingsModel::clearInternalCache();
    }

    public function tearDown(): void
    {
        \Illuminate\Support\Facades\Cache::forget(MailBrandSetting::instance()->cacheKey);
        MailBrandSetting::instance()->resetDefault();
        \System\Behaviors\SettingsModel::clearInternalCache();

        parent::tearDown();
    }

    /**
     * Stores a CSS variable value directly on the model. The colorpicker form widget
     * validates its own input, so this stands in for every write that does not go
     * through it: MailBrandSetting::set(), a plugin, a seeder or a direct DB write.
     */
    protected function storeValue(string $value): void
    {
        $setting = MailBrandSetting::instance();
        $setting->body_bg = $value;
        $setting->save();

        \System\Behaviors\SettingsModel::clearInternalCache();
        \Illuminate\Support\Facades\Cache::forget(MailBrandSetting::instance()->cacheKey);
    }

    /**
     * Regression for GHSA-58fp-mcx6-7qf9. MailBrandSetting takes no raw user CSS
     * string but flows user input through Less_Parser::ModifyVars(), whose
     * serializeVars() helper concatenates raw values into LESS source without
     * escaping. A CSS variable value of `red; @import (inline) "/path/to/secret"`
     * therefore reaches the parser as a working @import directive. The
     * SetImportDirs deny-all gate in compileCss() must close this vector
     * regardless of form-field validation strictness.
     */
    public function testCompileCssBlocksModifyVarsImportInjection()
    {
        $tmpSecret = tempnam(sys_get_temp_dir(), 'mailbrandsetting-leak-canary-');
        file_put_contents($tmpSecret, "APP_KEY=do-not-leak-via-modifyvars\n");

        try {
            // Bypass form-field validation by writing the malicious value directly
            // onto the model. This simulates a model-layer bypass or a future
            // weakening of the field validator.
            $setting = MailBrandSetting::instance();
            $setting->body_bg = 'red; @import (inline) "' . $tmpSecret . '"';
            $setting->save();

            \System\Behaviors\SettingsModel::clearInternalCache();

            $css = MailBrandSetting::compileCss();

            $this->assertStringNotContainsString('APP_KEY', $css);
            $this->assertStringNotContainsString('do-not-leak-via-modifyvars', $css);
        } finally {
            @unlink($tmpSecret);
        }
    }

    /**
     * The same serializeVars() concatenation also lets a CSS variable value close
     * the ruleset and emit a literal `</style>`. renderCss() is written raw into
     * the <style> block of the mail layout, which the Mail branding page renders
     * into a backend preview iframe, so the compiled output must carry no markup.
     */
    public function testRenderCssStripsTags()
    {
        $this->storeValue(self::BREAKOUT_VALUE);

        $renderedCss = MailBrandSetting::renderCss();

        $this->assertStringNotContainsString('</style', $renderedCss);
        $this->assertStringNotContainsString('<img', $renderedCss);
    }

    /**
     * renderCss() caches the raw compiler output, so stripping only on the cache-miss
     * return would leave every later cache hit unstripped -- the same mistake the sibling
     * models have already had corrected.
     */
    public function testRenderCssStripsTagsOnCacheHit()
    {
        $this->storeValue(self::BREAKOUT_VALUE);

        // Cache miss, primes the cache
        MailBrandSetting::renderCss();

        // Cache hit
        $renderedCss = MailBrandSetting::renderCss();

        $this->assertStringNotContainsString('</style', $renderedCss);
        $this->assertStringNotContainsString('<img', $renderedCss);
    }

    /**
     * A cache entry written before this change is not cleared by upgrading, so it must
     * still be stripped when it is read back.
     */
    public function testRenderCssStripsTagsFromExistingCacheEntry()
    {
        \Illuminate\Support\Facades\Cache::forever(
            MailBrandSetting::instance()->cacheKey,
            '.xss::before{content:"</style><img src=x onerror=alert(1)>"}'
        );

        $renderedCss = MailBrandSetting::renderCss();

        $this->assertStringNotContainsString('</style', $renderedCss);
        $this->assertStringNotContainsString('<img', $renderedCss);
    }

    /**
     * Stripping must not alter a legitimately compiled stylesheet, on the cache
     * miss or on the cache hit that follows it.
     */
    public function testRenderCssPreservesNormalCss()
    {
        $this->storeValue('#abcdef');

        $renderedCss = MailBrandSetting::renderCss();

        $this->assertStringContainsString('background-color:#abcdef', $renderedCss);
        $this->assertStringContainsString('.button-primary{background-color:#d66829', $renderedCss);
        $this->assertEquals(MailBrandSetting::compileCss(), $renderedCss);

        // Sanitizing the cache hit must not alter legitimate CSS either
        $this->assertEquals($renderedCss, MailBrandSetting::renderCss());
    }

    /**
     * End to end over the path the Mail branding page takes: the compiled CSS is emitted
     * raw into the mail layout's <style> block and the whole document is then reparsed by
     * CssToInlineStyles, which closes the raw-text <style> element at any `</style>` it
     * finds and promotes whatever follows to a node of its own.
     */
    public function testSampleMessageCarriesNoInjectedMarkup()
    {
        $this->storeValue(self::BREAKOUT_VALUE);

        $html = (new MailBrandSettings)->renderSampleMessage();

        $this->assertStringNotContainsString('onerror', $html);

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $this->assertCount(0, iterator_to_array($dom->getElementsByTagName('img')));
    }

    /**
     * Invalidation counterpart: a legitimate colour still reaches the rendered
     * message, and the sample message still renders in full.
     */
    public function testSampleMessageStillRendersLegitimateBranding()
    {
        $this->storeValue('#abcdef');

        $html = (new MailBrandSettings)->renderSampleMessage();

        $this->assertStringContainsString('background-color: #abcdef', $html);
        $this->assertStringContainsString('class="wrapper layout-default"', $html);
        $this->assertStringContainsString('class="button button-primary"', $html);
    }
}
