<?php

namespace Backend\Tests\Widgets;

use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\FormWidgets\RichEditor;
use Backend\Models\EditorSetting;
use Backend\Models\User;
use Backend\Widgets\Filter;
use DOMDocument;
use DOMXPath;
use Event;
use Winter\Storm\Filesystem\PathResolver;
use System\Behaviors\SettingsModel;
use System\Tests\Bootstrap\PluginTestCase;
use System\Traits\ViewMaker;

/**
 * Renders a backend partial by absolute path through the real view renderer.
 */
class PartialRenderer
{
    use ViewMaker;

    public function render(string $path, array $params = []): string
    {
        return (string) $this->makePartial($path, $params);
    }
}

/**
 * Backend partials render most values raw, so anything that is not itself markup is encoded where it is emitted.
 */
class BackendMarkupEscapingTest extends PluginTestCase
{
    protected const PAYLOAD = '"><img src=x onerror="window.pwned=1';

    public function tearDown(): void
    {
        SettingsModel::clearInternalCache();

        parent::tearDown();
    }

    protected function renderPartial(string $path, array $params = []): string
    {
        return (new PartialRenderer)->render(PathResolver::standardize(base_path($path)), $params);
    }

    /**
     * A switch scope's value is whatever `onFilterUpdate()` stored in the widget session; unlike the checkbox and
     * number scopes beside it, nothing coerces it on the way in.
     */
    public function testSwitchScopeValueIsEscapedOnRender()
    {
        $filter = new Filter(null, [
            'model' => new User,
            'arrayName' => 'array',
            'scopes' => [
                'approved' => [
                    'type' => 'switch',
                    'label' => 'Approved',
                    'conditions' => ['is_superuser <> true', 'is_superuser = true'],
                ],
            ],
        ]);
        $filter->render();

        $scope = $filter->getScope('approved');
        $filter->setScopeValue($scope, static::PAYLOAD);

        $html = (string) $filter->renderScopeElement($scope);

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('data-checked="&quot;&gt;&lt;img', $html);
    }

    public function testSwitchScopeRendersItsDocumentedStates()
    {
        $filter = new Filter(null, [
            'model' => new User,
            'arrayName' => 'array',
            'scopes' => ['approved' => ['type' => 'switch', 'label' => 'Approved']],
        ]);
        $filter->render();

        $scope = $filter->getScope('approved');

        $filter->setScopeValue($scope, '2');
        $this->assertStringContainsString('data-checked="2"', (string) $filter->renderScopeElement($scope));

        $filter->setScopeValue($scope, null);
        $this->assertStringContainsString('data-checked="0"', (string) $filter->renderScopeElement($scope));
    }

    /**
     * The import column names are the first row of the uploaded CSV, so they are file content.
     */
    public function testImportColumnNameIsEscaped()
    {
        $html = $this->renderPartial(
            'modules/backend/behaviors/importexportcontroller/partials/_column_sample_form.php',
            [
                'columnName' => static::PAYLOAD,
                'columnData' => ['sample'],
            ]
        );

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('&lt;img', $html);
    }

    public function testImportColumnNamePreservesOrdinaryText()
    {
        $html = $this->renderPartial(
            'modules/backend/behaviors/importexportcontroller/partials/_column_sample_form.php',
            [
                'columnName' => 'Given name',
                'columnData' => ['Jane'],
            ]
        );

        $this->assertStringContainsString('<strong>Given name</strong>', $html);
        $this->assertStringContainsString('Jane', $html);
    }

    public function testPageLinkTitlesAreEscaped()
    {
        Event::listen('backend.richeditor.listTypes', fn () => ['test-type' => 'Test']);
        Event::listen('backend.richeditor.getTypeInfo', function ($type) {
            if ($type !== 'test-type') {
                return;
            }

            return [
                '/parent' => [
                    'title' => static::PAYLOAD,
                    'links' => ['/child' => 'Child page'],
                ],
            ];
        });

        $widget = new RichEditor(new Controller, new FormField('test', 'Test'));
        $links = static::callProtectedMethod($widget, 'getPageLinksArray');

        $names = array_column($links, 'name');

        $this->assertNotContains(static::PAYLOAD, $names);
        $this->assertContains('&quot;&gt;&lt;img src=x onerror=&quot;window.pwned=1', $names);

        // The nesting indent is markup by design and must survive, unlike the title it prefixes.
        $this->assertContains(str_repeat('&nbsp;', 4) . 'Child page', $names);
    }

    /**
     * The link URL is the array key a `backend.richeditor.getTypeInfo` listener returns.
     */
    public function testPageLinkUrlsAreEscaped()
    {
        $this->listenForPageLinks(['/parent' . static::PAYLOAD => ['title' => 'Parent page']]);

        $html = (string) (new RichEditor(new Controller, new FormField('test', 'Test')))
            ->onLoadPageLinksForm();

        $this->assertStringNotContainsString('<img', $html);

        // The parser decodes the attribute back to the URL the listener returned, so the link still round-trips.
        $this->assertContains('/parent' . static::PAYLOAD, $this->optionValues($html));
    }

    /**
     * Invalidation case: an ordinary page link still renders its URL and its title.
     */
    public function testOrdinaryPageLinksStillRender()
    {
        $this->listenForPageLinks(['/about-us' => ['title' => 'About us']]);

        $html = (string) (new RichEditor(new Controller, new FormField('test', 'Test')))
            ->onLoadPageLinksForm();

        $this->assertStringContainsString('value="/about-us"', $html);
        $this->assertStringContainsString('>About us</option>', $html);
        $this->assertContains('/about-us', $this->optionValues($html));
    }

    /**
     * `onFilterUpdate()` stores the posted value as it arrives and a request can post an array. The partial reads it
     * back on every later render of the list, so it falls back to a documented state rather than raising.
     */
    public function testSwitchScopeRendersADocumentedStateForAValueItCannotMean()
    {
        $filter = new Filter(null, [
            'model' => new User,
            'arrayName' => 'array',
            'scopes' => ['approved' => ['type' => 'switch', 'label' => 'Approved']],
        ]);
        $filter->render();

        $scope = $filter->getScope('approved');
        $filter->setScopeValue($scope, ['1']);

        $html = (string) $filter->renderScopeElement($scope);

        $this->assertStringContainsString('data-checked="0"', $html);
        $this->assertStringNotContainsString('Array', $html);
    }

    /**
     * Invalidation case: a listener may legitimately return a title that is a stringifiable object rather than a
     * string -- a translated or wrapped label -- and reading that as "not text" would silently drop the row.
     */
    public function testPageLinkWithATitleThatStringifiesStillRendersIt()
    {
        $this->listenForPageLinks([
            '/parent' => ['title' => new class {
                public function __toString(): string
                {
                    return 'Stringified page';
                }
            }],
        ]);

        $widget = new RichEditor(new Controller, new FormField('test', 'Test'));
        $links = static::callProtectedMethod($widget, 'getPageLinksArray');

        $this->assertContains('Stringified page', array_column($links, 'name'));
    }

    public function testPageLinkWithATitleThatIsNotScalarStillRendersTheDialog()
    {
        $this->listenForPageLinks([
            '/malformed' => ['title' => ['nested' => 'array']],
            '/about-us' => ['title' => 'About us'],
        ]);

        $html = (string) (new RichEditor(new Controller, new FormField('test', 'Test')))
            ->onLoadPageLinksForm();

        $this->assertStringContainsString('value="/about-us"', $html);
        $this->assertStringContainsString('>About us</option>', $html);
        $this->assertContains('/malformed', $this->optionValues($html));
        $this->assertStringNotContainsString('Array', $html);
    }

    /**
     * EditorSetting encodes the labels, but the editor reads them back out of a `data-*` attribute that the parser
     * decodes once on the way. What the editor is handed is therefore the model's output only if the attribute was
     * double encoded, which is the step the model tests cannot see.
     *
     * @dataProvider richEditorStyleAttributeProvider
     */
    public function testRichEditorStyleAttributesSurviveTheAttributeDecode($settingKey, $attribute)
    {
        $styles = $this->renderRichEditorStyles($settingKey, $attribute, static::PAYLOAD);

        $this->assertSame([e(static::PAYLOAD)], array_values($styles));
    }

    /**
     * The same decode applied to a quote: it has to come back as the entity the model wrote, or the JSON ends early
     * and the editor iterates the string one character per dropdown entry.
     */
    public function testRichEditorStyleAttributePreservesAQuotedLabel()
    {
        $styles = $this->renderRichEditorStyles('html_style_paragraph', 'data-paragraph-styles', 'He said "hi"');

        $this->assertSame(['lead' => 'He said &quot;hi&quot;'], $styles);
    }

    public function richEditorStyleAttributeProvider(): array
    {
        return [
            'image styles' => ['html_style_image', 'data-image-styles'],
            'link styles' => ['html_style_link', 'data-link-styles'],
            'paragraph styles' => ['html_style_paragraph', 'data-paragraph-styles'],
            'table styles' => ['html_style_table', 'data-table-styles'],
            'table cell styles' => ['html_style_table_cell', 'data-table-cell-styles'],
            'paragraph formats' => ['html_paragraph_formats', 'data-paragraph-format'],
        ];
    }

    /**
     * Stores one row under $settingKey, renders the real editor partial, and reads $attribute back the way a browser
     * does: decoded exactly once by the parser, then parsed as JSON.
     */
    protected function renderRichEditorStyles(string $settingKey, string $attribute, string $label): array
    {
        EditorSetting::set($settingKey, [
            $settingKey === 'html_paragraph_formats'
                ? ['format_tag' => 'lead', 'format_label' => $label]
                : ['class_name' => 'lead', 'class_label' => $label],
        ]);
        SettingsModel::clearInternalCache();

        $user = new User;
        $user->is_superuser = true;
        $this->actingAs($user);

        $html = (string) (new RichEditor(new Controller, new FormField('test', 'Test'), ['model' => $user]))->render();

        $this->assertStringNotContainsString('<img', $html);

        $document = new DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML('<html><body>' . $html . '</body></html>');
        libxml_clear_errors();

        $attributes = (new DOMXPath($document))->query('//@' . $attribute);
        $this->assertSame(1, $attributes->length);

        return json_decode($attributes->item(0)->value, true, 512, JSON_THROW_ON_ERROR);
    }

    protected function listenForPageLinks(array $links): void
    {
        Event::listen('backend.richeditor.listTypes', fn () => ['test-type' => 'Test']);
        Event::listen('backend.richeditor.getTypeInfo', function ($type) use ($links) {
            if ($type !== 'test-type') {
                return;
            }

            return $links;
        });
    }

    /**
     * Every option's `value`, entity decoded by the parser exactly as a browser would before submitting the form.
     */
    protected function optionValues(string $html): array
    {
        $document = new DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML('<html><body>' . $html . '</body></html>');
        libxml_clear_errors();

        $values = [];
        foreach ($document->getElementsByTagName('option') as $option) {
            $values[] = $option->getAttribute('value');
        }

        return $values;
    }
}
