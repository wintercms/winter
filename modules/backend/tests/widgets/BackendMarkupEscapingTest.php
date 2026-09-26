<?php

namespace Backend\Tests\Widgets;

use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\FormWidgets\RichEditor;
use Backend\Models\User;
use Backend\Widgets\Filter;
use DOMDocument;
use Event;
use Winter\Storm\Filesystem\PathResolver;
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
 * Backend partials render most values raw, so every value that is not itself markup has to
 * be encoded at the point it is emitted. These cases cover three such values that were
 * emitted without it while a sibling line in the same partial encoded its own.
 */
class BackendMarkupEscapingTest extends PluginTestCase
{
    protected const PAYLOAD = '"><img src=x onerror="window.pwned=1';

    protected function renderPartial(string $path, array $params = []): string
    {
        return (new PartialRenderer)->render(PathResolver::standardize(base_path($path)), $params);
    }

    /**
     * The switch scope's value is whatever `onFilterUpdate()` stored in this user's widget
     * session; unlike the checkbox and number scopes beside it, nothing coerces it on the
     * way in. The partial therefore has to encode it on the way out, as the sibling
     * `_scope_text.php` already does with its own value.
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
     * The import column names are the first row of the uploaded CSV, i.e. file content. The
     * sample values beneath them were already encoded; the name was not.
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

    /**
     * The page link label is a deliberate `&nbsp;` indent concatenated with a stored page
     * title, and the partial emits it raw so the indent renders. Only the indent may be
     * markup, so the title has to be encoded where the two are joined.
     */
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

        // The nesting indent is markup and must survive
        $this->assertContains(str_repeat('&nbsp;', 4) . 'Child page', $names);
    }

    /**
     * The link URL is the array key a `backend.richeditor.getTypeInfo` listener returns, so
     * the partial has to encode it into the option's `value` attribute.
     */
    public function testPageLinkUrlsAreEscaped()
    {
        $this->listenForPageLinks(['/parent' . static::PAYLOAD => ['title' => 'Parent page']]);

        $html = (string) (new RichEditor(new Controller, new FormField('test', 'Test')))
            ->onLoadPageLinksForm();

        $this->assertStringNotContainsString('<img', $html);

        // The parser decodes the attribute back to exactly the URL the listener returned,
        // so the selected link still round-trips.
        $this->assertContains('/parent' . static::PAYLOAD, $this->optionValues($html));
    }

    /**
     * Invalidation: an ordinary page link still renders its URL and its title.
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
     * `onFilterUpdate()` assigns the posted value to a switch scope as it arrives, and a
     * request can post an array. The partial reads that back out of the widget session on
     * every later render of the list, so it has to render one of its three states rather
     * than raise: a scope holding something it cannot mean would otherwise cost the whole
     * list page.
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
     * A `backend.richeditor.getTypeInfo` listener supplies the link tree, and the title of a
     * link is its own to shape. One malformed title may not take the dialog down with it:
     * every listener's links are gathered in one pass, and the dialog is how a page link is
     * inserted at all.
     */
    /**
     * Invalidation: a title that is an object which stringifies still renders its text. A
     * listener can legitimately return one - a translated or wrapped string - and reading it as
     * "not text" would silently drop the row.
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
     * Registers a page link type whose info is the given tree of links.
     */
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
     * The `value` of every option, entity decoded by the parser exactly as a browser would
     * decode it before submitting the form.
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
