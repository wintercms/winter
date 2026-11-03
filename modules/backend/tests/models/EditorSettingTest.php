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
        \Illuminate\Support\Facades\Cache::forget(EditorSetting::instance()->cacheKey);
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
     * Regression for GHSA-5cwr-5jxg-pcf6. renderCss() caches the raw compiler
     * output, so sanitizing only the cache-miss return leaves every later cache
     * hit unsanitized. The first render primes the cache; the second is the one
     * that used to emit active markup into the backend <style> block.
     */
    public function testRenderCssStripsScriptTagsOnCacheHit()
    {
        $maliciousStyles = '.x { content: ~"</style><script>alert(1)</script><style>"; }';

        EditorSetting::set('html_custom_styles', $maliciousStyles);

        \System\Behaviors\SettingsModel::clearInternalCache();
        \Illuminate\Support\Facades\Cache::forget(EditorSetting::instance()->cacheKey);

        // Cache miss, primes the cache
        EditorSetting::renderCss();

        // Cache hit
        $renderedCss = EditorSetting::renderCss();

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
            EditorSetting::instance()->cacheKey,
            '.fr-view .x{content:</style><script>alert(1)</script><style>}'
        );

        $renderedCss = EditorSetting::renderCss();

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
        $normalStyles = '.my-class { color: blue; font-weight: bold; }';

        EditorSetting::set('html_custom_styles', $normalStyles);

        \System\Behaviors\SettingsModel::clearInternalCache();
        \Illuminate\Support\Facades\Cache::forget(EditorSetting::instance()->cacheKey);

        $renderedCss = EditorSetting::renderCss();

        $this->assertStringContainsString('color', $renderedCss);
        $this->assertStringContainsString('font-weight', $renderedCss);
        $this->assertDoesNotMatchRegularExpression('/<[a-z\/!]/', $renderedCss);

        // Sanitizing the cache hit must not alter legitimate CSS
        $this->assertEquals($renderedCss, EditorSetting::renderCss());
    }

    /**
     * Regression for GHSA-58fp-mcx6-7qf9. A user-supplied `@import (inline)`
     * directive in `html_custom_styles` must not be able to disclose server files.
     */
    public function testRenderCssBlocksImportAttack()
    {
        $tmpSecret = tempnam(sys_get_temp_dir(), 'editorsetting-leak-canary-');
        file_put_contents($tmpSecret, "APP_KEY=do-not-leak-via-editorsetting\n");

        try {
            EditorSetting::set('html_custom_styles', '@import (inline) "' . $tmpSecret . '";');

            \System\Behaviors\SettingsModel::clearInternalCache();
            \Illuminate\Support\Facades\Cache::forget(EditorSetting::instance()->cacheKey);

            $renderedCss = EditorSetting::renderCss();

            $this->assertStringNotContainsString('APP_KEY', $renderedCss);
            $this->assertStringNotContainsString('do-not-leak-via-editorsetting', $renderedCss);
        } finally {
            @unlink($tmpSecret);
        }
    }

    /**
     * @dataProvider styleSettingKeysProvider
     */
    public function testGetConfiguredStylesEncodesAClassNameThatWouldEndItsAttribute($settingKey)
    {
        EditorSetting::set($settingKey, [
            ['class_label' => 'Fine', 'class_name' => 'a" onmouseover="b'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        // The row is kept, not dropped: the editor reads the attribute back through the DOM, which decodes it.
        $this->assertSame(
            ['a&quot; onmouseover=&quot;b' => 'Fine'],
            EditorSetting::getConfiguredStyles($settingKey)
        );
    }

    /**
     * @dataProvider styleSettingKeysProvider
     */
    public function testGetConfiguredStylesEncodesTheLabel($settingKey)
    {
        EditorSetting::set($settingKey, [
            ['class_label' => '<img src=x onerror=alert(1)>', 'class_name' => 'lead-paragraph'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame(
            ['lead-paragraph' => '&lt;img src=x onerror=alert(1)&gt;'],
            EditorSetting::getConfiguredStyles($settingKey)
        );
    }

    public function testGetConfiguredFormatsRefusesATagThatIsNotAnElementName()
    {
        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => 'Fine', 'format_tag' => 'img src=y onerror=alert(2)'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame([], EditorSetting::getConfiguredFormats('html_paragraph_formats'));
    }

    public function testGetConfiguredFormatsEncodesTheLabel()
    {
        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => '<img src=x onerror=alert(1)>', 'format_tag' => 'H5'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame(
            ['H5' => '&lt;img src=x onerror=alert(1)&gt;'],
            EditorSetting::getConfiguredFormats('html_paragraph_formats')
        );
    }

    public function testGetConfiguredStylesAndFormatsPreserveValidValues()
    {
        EditorSetting::set('html_style_paragraph', [
            ['class_label' => 'Lead paragraph', 'class_name' => 'lead_paragraph-1'],
            ['class_label' => 'Fish & chips', 'class_name' => 'oc-text-gray'],
        ]);

        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => 'Normal', 'format_tag' => 'N'],
            ['format_label' => 'Heading 5', 'format_tag' => 'H5'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        // `Fish &amp; chips` is what the editor inserts, and it displays as `Fish & chips`.
        $this->assertSame([
            'lead_paragraph-1' => 'Lead paragraph',
            'oc-text-gray' => 'Fish &amp; chips',
        ], EditorSetting::getConfiguredStyles('html_style_paragraph'));

        $this->assertSame([
            'N' => 'Normal',
            'H5' => 'Heading 5',
        ], EditorSetting::getConfiguredFormats('html_paragraph_formats'));
    }

    /**
     * @dataProvider legitimateClassNameProvider
     */
    public function testGetConfiguredStylesPreservesLegitimateClassNames($className)
    {
        EditorSetting::set('html_style_paragraph', [
            ['class_label' => 'Styled', 'class_name' => $className],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        // e(), not the raw name: the entities decode back to the configured class in the DOM.
        $this->assertSame(
            [e($className) => 'Styled'],
            EditorSetting::getConfiguredStyles('html_style_paragraph')
        );
    }

    public function testGetConfiguredFormatsPreservesAHyphenatedTag()
    {
        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => 'Call out', 'format_tag' => 'my-callout'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame(
            ['my-callout' => 'Call out'],
            EditorSetting::getConfiguredFormats('html_paragraph_formats')
        );
    }

    /**
     * The `datatable` field these rows come from applies no type coercion, so a row can hold a
     * non-string. These methods run for every form with a RichEditor field, so raising is not an option.
     *
     * @dataProvider nonScalarRowProvider
     */
    public function testGetConfiguredStylesAndFormatsSkipRowsThatAreNotScalar($styleRow, $formatRow)
    {
        EditorSetting::set('html_style_paragraph', [$styleRow]);
        EditorSetting::set('html_paragraph_formats', [$formatRow]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame([], EditorSetting::getConfiguredStyles('html_style_paragraph'));
        $this->assertSame([], EditorSetting::getConfiguredFormats('html_paragraph_formats'));
    }

    /**
     * @dataProvider surroundingWhitespaceProvider
     */
    public function testGetConfiguredStylesKeepsAClassNameAsItWasWritten($className)
    {
        EditorSetting::set('html_style_paragraph', [
            ['class_label' => 'Gray', 'class_name' => $className],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        // Not trimmed, unlike a format tag below: a class attribute ignores surrounding whitespace anyway.
        $this->assertSame(
            [$className => 'Gray'],
            EditorSetting::getConfiguredStyles('html_style_paragraph')
        );
    }

    public function testGetConfiguredFormatsTrimsSurroundingWhitespaceInATag()
    {
        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => 'Heading 1', 'format_tag' => 'H1 '],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame(
            ['H1' => 'Heading 1'],
            EditorSetting::getConfiguredFormats('html_paragraph_formats')
        );
    }

    public function testGetConfiguredStylesAcceptsANewlineSeparatedClassList()
    {
        EditorSetting::set('html_style_paragraph', [
            ['class_label' => 'Callout', 'class_name' => "callout\ncallout--lead"],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame(
            ["callout\ncallout--lead" => 'Callout'],
            EditorSetting::getConfiguredStyles('html_style_paragraph')
        );
    }

    public function testGetConfiguredFormatsPreservesAnUnderscoredTag()
    {
        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => 'Call out', 'format_tag' => 'my_callout'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame(
            ['my_callout' => 'Call out'],
            EditorSetting::getConfiguredFormats('html_paragraph_formats')
        );
    }

    public function testGetConfiguredStylesKeepsTheOtherRowsWhenOneIsLeftOut()
    {
        EditorSetting::set('html_style_paragraph', [
            ['class_label' => 'Bordered', 'class_name' => 'oc-text-bordered'],
            ['class_label' => 'Gray', 'class_name' => 'oc-text-gray'],
            ['class_label' => 'Spaced', 'class_name' => 'oc-text-spaced'],
            ['class_label' => 'Uppercase', 'class_name' => 'oc-text-uppercase'],
            ['class_label' => 'Encoded', 'class_name' => 'a"b'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        // Every row is kept; only a row that cannot be rendered as text at all is left out.
        $this->assertSame([
            'oc-text-bordered' => 'Bordered',
            'oc-text-gray' => 'Gray',
            'oc-text-spaced' => 'Spaced',
            'oc-text-uppercase' => 'Uppercase',
            'a&quot;b' => 'Encoded',
        ], EditorSetting::getConfiguredStyles('html_style_paragraph', 'THE DEFAULT'));
    }

    public function testGetConfiguredFormatsKeepsTheOtherRowsWhenOneIsLeftOut()
    {
        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => 'Normal', 'format_tag' => 'N'],
            ['format_label' => 'Heading 1', 'format_tag' => 'H1'],
            ['format_label' => 'Heading 2', 'format_tag' => 'H2'],
            ['format_label' => 'Heading 3', 'format_tag' => 'H3'],
            ['format_label' => 'Heading 4', 'format_tag' => 'H4'],
            ['format_label' => 'Code', 'format_tag' => 'PRE'],
            ['format_label' => 'Refused', 'format_tag' => 'h1 onmouseover=alert(1)'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        // The six valid rows are exactly the shipped default, which getConfiguredArray() answers
        // with the caller's default, so the refused row is retested below against a shorter list.
        $this->assertSame(
            'THE DEFAULT',
            EditorSetting::getConfiguredFormats('html_paragraph_formats', 'THE DEFAULT')
        );

        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => 'Normal', 'format_tag' => 'N'],
            ['format_label' => 'Heading 1', 'format_tag' => 'H1'],
            ['format_label' => 'Aside', 'format_tag' => 'ASIDE'],
            ['format_label' => 'Refused', 'format_tag' => 'h1 onmouseover=alert(1)'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame([
            'N' => 'Normal',
            'H1' => 'Heading 1',
            'ASIDE' => 'Aside',
        ], EditorSetting::getConfiguredFormats('html_paragraph_formats', 'THE DEFAULT'));
    }

    /**
     * Invalidation for the two above: a configuration that really is the shipped one still reads back as the default.
     */
    public function testAnUnchangedConfigurationStillReadsBackAsTheDefault()
    {
        EditorSetting::set('html_style_paragraph', [
            ['class_label' => 'Bordered', 'class_name' => 'oc-text-bordered'],
            ['class_label' => 'Gray', 'class_name' => 'oc-text-gray'],
            ['class_label' => 'Spaced', 'class_name' => 'oc-text-spaced'],
            ['class_label' => 'Uppercase', 'class_name' => 'oc-text-uppercase'],
        ]);

        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => 'Normal', 'format_tag' => 'N'],
            ['format_label' => 'Heading 1', 'format_tag' => 'H1'],
            ['format_label' => 'Heading 2', 'format_tag' => 'H2'],
            ['format_label' => 'Heading 3', 'format_tag' => 'H3'],
            ['format_label' => 'Heading 4', 'format_tag' => 'H4'],
            ['format_label' => 'Code', 'format_tag' => 'PRE'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame(
            'THE DEFAULT',
            EditorSetting::getConfiguredStyles('html_style_paragraph', 'THE DEFAULT')
        );
        $this->assertSame(
            'THE DEFAULT',
            EditorSetting::getConfiguredFormats('html_paragraph_formats', 'THE DEFAULT')
        );
    }

    public function surroundingWhitespaceProvider()
    {
        return [
            'a trailing space' => ['oc-text-gray '],
            'a leading space' => [' oc-text-gray'],
            'a trailing tab' => ["oc-text-gray\t"],
        ];
    }

    public function legitimateClassNameProvider()
    {
        return [
            'two classes' => ['callout callout--lead'],
            'a responsive utility class' => ['md:text-lg'],
            'a fraction utility class' => ['w-1/2'],
            'a dotted class' => ['a.b'],
            'an underscored and hyphenated class' => ['lead_paragraph-1'],
            'a non-ASCII class' => ['überschrift'],
            // Tailwind arbitrary-variant classes, which is what a Tailwind backend skin's vocabulary looks like.
            'an arbitrary child variant' => ['[&>p]:mt-4'],
            'an arbitrary state variant' => ['[&:hover]:underline'],
            'an arbitrary descendant variant' => ['[&_a]:text-blue-500'],
            'a nesting-style class' => ['callout&--lead'],
            'a class holding a backtick' => ['md`lg'],
        ];
    }

    public function nonScalarRowProvider()
    {
        return [
            'a label that is an array' => [
                ['class_label' => ['nested' => 'x'], 'class_name' => 'lead-paragraph'],
                ['format_label' => ['nested' => 'x'], 'format_tag' => 'H5'],
            ],
            'a name that is an array' => [
                ['class_label' => 'Lead', 'class_name' => ['nested' => 'x']],
                ['format_label' => 'Heading 5', 'format_tag' => ['nested' => 'x']],
            ],
        ];
    }

    public function styleSettingKeysProvider()
    {
        return [
            'image styles' => ['html_style_image'],
            'link styles' => ['html_style_link'],
            'paragraph styles' => ['html_style_paragraph'],
            'table styles' => ['html_style_table'],
            'table cell styles' => ['html_style_table_cell'],
        ];
    }
}
