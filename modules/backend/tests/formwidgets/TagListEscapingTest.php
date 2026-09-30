<?php

namespace Backend\Tests\FormWidgets;

use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\FormWidgets\TagList;
use Backend\Models\User;
use Backend\Models\UserGroup;
use DOMDocument;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * When a TagList is rendered in preview mode, or as a read-only / disabled field, the
 * partial swaps the editable <select> for a display list plus hidden inputs that carry
 * the values through a save. Those hidden inputs interpolate the stored tag into a
 * double-quoted `value` attribute, so they have to encode it exactly as the sibling
 * <li> and the editable <option> in the same partial already do.
 *
 * Tags are free text by default (TagList::$customTags) and getSaveValue() applies no
 * filtering, so the value reaching the partial is not constrained by the widget.
 *
 * @see modules/backend/formwidgets/taglist/partials/_taglist.php
 */
class TagListEscapingTest extends PluginTestCase
{
    /**
     * A value that would close the `value="` attribute and open an element of its own if
     * it were emitted verbatim. Deliberately free of commas so it survives the string mode
     * separator, and free of `&` so the expected round-trip value is unambiguous.
     */
    protected const PAYLOAD = '"><script>window.pwned=1</script>';

    protected function makeWidget(FormField $field, array $config = []): TagList
    {
        return new TagList(new Controller(), $field, array_merge([
            'model' => new User,
        ], $config));
    }

    protected function makeField(string $name, $value, array $properties = []): FormField
    {
        $field = new FormField($name, $name);
        $field->valueFrom = $name;
        $field->value = $value;

        foreach ($properties as $property => $propertyValue) {
            $field->{$property} = $propertyValue;
        }

        return $field;
    }

    protected function parse(string $html): DOMDocument
    {
        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        // Without an explicit charset DOMDocument assumes ISO-8859-1 and mangles UTF-8 tags.
        $doc->loadHTML(
            '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>'
            . '<body>' . $html . '</body></html>'
        );
        libxml_clear_errors();

        return $doc;
    }

    /** Every element name present in the rendered markup. */
    protected function elementsIn(string $html): array
    {
        $names = [];
        foreach ($this->parse($html)->getElementsByTagName('*') as $element) {
            $names[] = strtolower($element->nodeName);
        }

        return $names;
    }

    /**
     * The `value` of every hidden input, entity-decoded by the HTML parser exactly as a
     * browser would decode it before posting the form back.
     */
    protected function hiddenInputValues(string $html): array
    {
        $values = [];
        foreach ($this->parse($html)->getElementsByTagName('input') as $input) {
            if ($input->getAttribute('type') === 'hidden') {
                $values[] = $input->getAttribute('value');
            }
        }

        return $values;
    }

    /** No element the partial does not emit itself may appear in the output. */
    protected function assertNoInjectedMarkup(string $html): void
    {
        $this->assertStringNotContainsString(self::PAYLOAD, $html, 'The value must never be emitted verbatim');
        $this->assertSame(
            [],
            array_values(array_diff(array_unique($this->elementsIn($html)), ['html', 'head', 'meta', 'body', 'ul', 'li', 'input'])),
            'The stored value introduced an element into the rendered document'
        );
    }

    public function testPreviewModeEscapesTheArrayHiddenInputs(): void
    {
        $field = $this->makeField('tags', [self::PAYLOAD, 'ok']);
        $html = $this->makeWidget($field, ['mode' => 'array', 'previewMode' => true])->render();

        $this->assertNoInjectedMarkup($html);
        $this->assertSame([self::PAYLOAD, 'ok'], $this->hiddenInputValues($html));
    }

    public function testPreviewModeEscapesTheScalarHiddenInput(): void
    {
        $field = $this->makeField('tags', self::PAYLOAD);
        $html = $this->makeWidget($field, ['previewMode' => true])->render();

        $this->assertNoInjectedMarkup($html);
        $this->assertSame([self::PAYLOAD], $this->hiddenInputValues($html));
    }

    public static function displayOnlyFieldProvider(): array
    {
        return [
            'read-only field' => ['readOnly'],
            'disabled field' => ['disabled'],
        ];
    }

    /**
     * The same branch is taken outside preview mode, on an ordinary create/update form, so
     * the sink is not reachable only through the `preview` controller action.
     *
     * @dataProvider displayOnlyFieldProvider
     */
    public function testReadOnlyAndDisabledFieldsEscapeTheHiddenInputs(string $property): void
    {
        $field = $this->makeField('tags', [self::PAYLOAD], [$property => true]);
        $html = $this->makeWidget($field, ['mode' => 'array'])->render();

        $this->assertNoInjectedMarkup($html);
        $this->assertSame([self::PAYLOAD], $this->hiddenInputValues($html));
    }

    /**
     * Relation mode stores tags as related models. hydrateRelationSaveValue() creates them
     * from free text whenever customTags is on, so the stored name is unconstrained there
     * too, and the scalar branch renders the whole related collection.
     */
    public function testRelationModeEscapesTheHiddenInput(): void
    {
        $user = User::firstOrFail();

        $saveField = $this->makeField('groups', null);
        $keys = $this->makeWidget($saveField, [
            'mode' => 'relation',
            'model' => $user,
        ])->getSaveValue([self::PAYLOAD, 'Normal']);

        $this->assertContains(self::PAYLOAD, UserGroup::lists('name'), 'The free-text tag should be stored verbatim');

        $user->groups()->sync($keys);
        $user->reload();

        $field = $this->makeField('groups', $user->groups);
        $html = $this->makeWidget($field, [
            'mode' => 'relation',
            'model' => $user,
            'previewMode' => true,
        ])->render();

        $this->assertNoInjectedMarkup($html);
    }

    /** Invalidation: ordinary tags must still be listed and still round-trip on save. */
    public function testOrdinaryTagsStillRenderAndRoundTrip(): void
    {
        $tags = ['winter', 'cms'];
        $field = $this->makeField('tags', $tags);
        $widget = $this->makeWidget($field, ['mode' => 'array', 'previewMode' => true]);
        $html = $widget->render();

        $this->assertStringContainsString('<li class="taglist__item">winter</li>', $html);
        $this->assertStringContainsString('value="winter"', $html);
        $this->assertSame($tags, $this->hiddenInputValues($html));
        $this->assertSame($tags, $widget->getSaveValue($this->hiddenInputValues($html)));
    }

    /**
     * Invalidation: escaping must not mangle characters that are legal in a tag. The
     * browser decodes the entities back to the original text before posting it.
     */
    public function testTagsWithSpecialCharactersRoundTrip(): void
    {
        $tags = ['Tom & Jerry', "O'Brien", 'héllo 世界 😀', '<b>'];
        $field = $this->makeField('tags', $tags);
        $widget = $this->makeWidget($field, ['mode' => 'array', 'previewMode' => true]);
        $html = $widget->render();

        $this->assertSame($tags, $this->hiddenInputValues($html));
        $this->assertSame($tags, $widget->getSaveValue($this->hiddenInputValues($html)));
    }

    /**
     * Invalidation: a stored value that is not valid UTF-8 still posts back. Encoding it with
     * htmlspecialchars() alone answers an invalid sequence with an empty string, which would
     * silently drop the tag on the next save; substituting the invalid bytes keeps the rest.
     */
    public function testATagThatIsNotValidUtf8StillPostsBack(): void
    {
        $tags = ["caf\xE9", 'ok'];
        $field = $this->makeField('tags', $tags);
        $widget = $this->makeWidget($field, ['mode' => 'array', 'previewMode' => true]);

        $values = $this->hiddenInputValues($widget->render());

        $this->assertCount(2, $values);
        $this->assertNotSame('', $values[0], 'the value must not be dropped');
        $this->assertStringContainsString('caf', $values[0]);
        $this->assertSame('ok', $values[1]);
    }

    /** Invalidation: string mode still posts back the exact stored string. */
    public function testStringModeRoundTrips(): void
    {
        $field = $this->makeField('tags', 'winter,cms');
        $widget = $this->makeWidget($field, ['previewMode' => true]);
        $html = $widget->render();

        $this->assertSame(['winter,cms'], $this->hiddenInputValues($html));
        $this->assertSame('winter,cms', $widget->getSaveValue($this->hiddenInputValues($html)));
    }

    /**
     * Control: the editable branch already escaped its option values, so it must be
     * unaffected by the fix and must not be what the assertions above are measuring.
     */
    public function testTheEditableFieldIsUnaffected(): void
    {
        $field = $this->makeField('tags', [self::PAYLOAD, 'ok']);
        $html = $this->makeWidget($field, ['mode' => 'array'])->render();

        $this->assertStringNotContainsString(self::PAYLOAD, $html);
        $this->assertSame(
            [],
            array_values(array_diff(array_unique($this->elementsIn($html)), ['html', 'head', 'meta', 'body', 'input', 'select', 'option'])),
            'The editable branch should not emit markup of its own'
        );
    }
}
