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
     * The "Markup Classes" tab feeds the RichEditor's editor dropdowns, which build their
     * markup by string concatenation with no encoding of their own: the class name lands in
     * two attribute values and the label lands in both an attribute value and element
     * content. Both halves therefore have to be constrained here.
     *
     * A class name that could not survive that concatenation intact is refused outright
     * rather than rewritten, so the editor never receives a class the stored row does not
     * actually name.
     *
     * @dataProvider styleSettingKeysProvider
     */
    public function testGetConfiguredStylesEncodesAClassNameThatWouldEndItsAttribute($settingKey)
    {
        EditorSetting::set($settingKey, [
            ['class_label' => 'Fine', 'class_name' => 'a" onmouseover="b'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        // The editor concatenates the name into a double quoted attribute, so what matters is
        // that the quote cannot close it. The row is kept: the browser decodes the entity when
        // the editor reads the attribute back, so the configured name still applies.
        $this->assertSame(
            ['a&quot; onmouseover=&quot;b' => 'Fine'],
            EditorSetting::getConfiguredStyles($settingKey)
        );
    }

    /**
     * The label is element content and a title attribute value, so it is encoded rather
     * than refused: it has no restricted shape and it displays the same either way.
     *
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

    /**
     * Paragraph formats are emitted as a literal element name wrapping the label, so a tag
     * that is not a bare element name is refused.
     */
    public function testGetConfiguredFormatsRefusesATagThatIsNotAnElementName()
    {
        EditorSetting::set('html_paragraph_formats', [
            ['format_label' => 'Fine', 'format_tag' => 'img src=y onerror=alert(2)'],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        $this->assertSame([], EditorSetting::getConfiguredFormats('html_paragraph_formats'));
    }

    /**
     * The format label is encoded for the same reason the style label is.
     */
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

    /**
     * Constraining the values must leave legitimate markup classes and paragraph formats
     * intact.
     */
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
     * A class attribute holds a whitespace separated list of tokens, and a token may
     * legitimately contain characters that are not CSS identifier characters. All of these
     * work in the editor today and have to keep reaching it byte for byte, rather than
     * being rewritten into a class nobody configured.
     *
     * @dataProvider legitimateClassNameProvider
     */
    public function testGetConfiguredStylesPreservesLegitimateClassNames($className)
    {
        EditorSetting::set('html_style_paragraph', [
            ['class_label' => 'Styled', 'class_name' => $className],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        // Encoded, not constrained: `&` and `<` in a Tailwind arbitrary value survive as
        // entities and the browser decodes them again when the editor reads the attribute.
        $this->assertSame(
            [e($className) => 'Styled'],
            EditorSetting::getConfiguredStyles('html_style_paragraph')
        );
    }

    /**
     * A custom element name is required to contain a hyphen, so a hyphenated format tag has
     * to survive or custom element paragraph formats cannot be configured at all.
     */
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
     * Both halves of a row are read out of a `datatable` field, which applies no type
     * coercion of its own, so a row can hold a value that is not a string. Reading it must
     * skip the row: these methods are called while any form containing a RichEditor field
     * is rendered, so raising there would take down more than the settings page.
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
     * A class attribute ignores whitespace around the token list it holds, and so does
     * jQuery when it applies one, so a name typed with a stray leading or trailing space is
     * delivered without it rather than costing the row. The `datatable` field these rows
     * come from does not trim its own inputs.
     *
     * @dataProvider surroundingWhitespaceProvider
     */
    public function testGetConfiguredStylesKeepsAClassNameAsItWasWritten($className)
    {
        EditorSetting::set('html_style_paragraph', [
            ['class_label' => 'Gray', 'class_name' => $className],
        ]);

        \System\Behaviors\SettingsModel::clearInternalCache();

        // Stored configuration is handed on as written. A class attribute ignores whitespace
        // around the list it holds, so surrounding whitespace makes no difference to the
        // browser and is not worth rewriting the configured value over.
        $this->assertSame(
            [$className => 'Gray'],
            EditorSetting::getConfiguredStyles('html_style_paragraph')
        );
    }

    /**
     * The same for a paragraph format tag, which is emitted as an element name and so could
     * not carry the space either way.
     */
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

    /**
     * Any whitespace run separates the tokens of a class list, not just a space, so a list
     * written across two lines names both classes exactly as a browser reads it.
     */
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

    /**
     * An underscore is carried by an element name as readily as a hyphen is.
     */
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

    /**
     * Leaving one row out must cost that row and nothing else. These methods return the
     * caller's default when the configuration matches the shipped one, so the row that is
     * left out may not be what makes the rest of the configuration look shipped: that would
     * drop the whole dropdown rather than one entry in it.
     */
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

        // Every row is kept; only a row this cannot render as text at all is left out, which
        // testGetConfiguredStylesLeavesOutARowItCannotRender covers.
        $this->assertSame([
            'oc-text-bordered' => 'Bordered',
            'oc-text-gray' => 'Gray',
            'oc-text-spaced' => 'Spaced',
            'oc-text-uppercase' => 'Uppercase',
            'a&quot;b' => 'Encoded',
        ], EditorSetting::getConfiguredStyles('html_style_paragraph', 'THE DEFAULT'));
    }

    /**
     * The same for paragraph formats.
     */
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

        // Those six are exactly the shipped default, and getConfiguredArray() answers a
        // configuration equal to the default with the caller's default -- which is that same
        // list -- so the assertion below adds a seventh row to keep this about the refused one.
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
     * Invalidation for the two cases above: a configuration that really is the shipped one
     * still reads back as the caller's default, which is how the RichEditor tells the editor
     * to use its own styles.
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
            // Arbitrary-variant utility classes, which is what the vocabulary of a Tailwind
            // based backend skin looks like. None of these can open an element or end an
            // attribute value from inside the double quoted value it is written into.
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
