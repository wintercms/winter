<?php

namespace Backend\Tests\Widgets;

use Backend\Models\User;
use Backend\Widgets\Form;
use DOMDocument;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * Field partials render the record's value straight into markup, so every field type that
 * emits a value has to escape it itself. Nothing constrains a dropdown value to its option
 * list server side -- Form::getSaveData() stores the posted value verbatim -- so the value
 * reaching these partials is not constrained by the field definition either.
 *
 * The dropdown partial's preview / read-only branch emits that value into the `value`
 * attribute of a hidden input; these cases hold it to the same encoding as every sibling
 * field type in the same directory.
 */
class FormFieldEscapingTest extends PluginTestCase
{
    /**
     * A value that, emitted verbatim, would close the double-quoted attribute it lands in,
     * open an element, then re-open an attribute so the trailing quote of the original sink
     * is swallowed. Inert in a text context, markup in an attribute context.
     */
    protected const PAYLOAD = '"><script>window.pwned=1</script><b onmouseover="window.pwned=1" x="';

    /** Field types whose partials render a free-form record value. */
    public static function fieldTypeProvider(): array
    {
        return [
            'dropdown' => ['dropdown'],
            'text' => ['text'],
            'number' => ['number'],
            'email' => ['email'],
            'url' => ['url'],
            'tel' => ['tel'],
            'textarea' => ['textarea'],
            'radio' => ['radio'],
            'checkboxlist' => ['checkboxlist'],
            'balloon-selector' => ['balloon-selector'],
        ];
    }

    protected function renderField(string $type, $value, bool $preview = true, array $config = []): string
    {
        $record = new User;
        $record->email = $value;

        $form = new Form(null, [
            'model' => $record,
            'arrayName' => 'array',
            'fields' => [
                'email' => $config + [
                    'type' => $type,
                    'label' => 'Email',
                    'options' => ['first' => 'First option', 'second' => 'Second option'],
                ],
            ],
        ]);

        $form->previewMode = $preview;

        return (string) $form->renderField('email', ['useContainer' => false]);
    }

    /** Returns every element name present in the rendered markup. */
    protected function elementsIn(string $html): array
    {
        return array_map(
            fn ($element) => strtolower($element->nodeName),
            iterator_to_array($this->parse($html)->getElementsByTagName('*'))
        );
    }

    /** Returns every attribute name present in the rendered markup. */
    protected function attributesIn(string $html): array
    {
        $names = [];
        foreach ($this->parse($html)->getElementsByTagName('*') as $element) {
            foreach ($element->attributes as $attribute) {
                $names[] = strtolower($attribute->name);
            }
        }

        return $names;
    }

    protected function parse(string $html): DOMDocument
    {
        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<html><body>' . $html . '</body></html>');
        libxml_clear_errors();

        return $doc;
    }

    /**
     * @dataProvider fieldTypeProvider
     */
    public function testAPreviewedFieldValueCannotInjectMarkup(string $type): void
    {
        $html = $this->renderField($type, self::PAYLOAD);

        $handlers = array_filter($this->attributesIn($html), fn ($name) => str_starts_with($name, 'on'));

        $this->assertSame([], array_values($handlers), "The {$type} field emitted an event handler attribute");
        $this->assertNotContains('script', $this->elementsIn($html), "The {$type} field emitted a script element");
    }

    /**
     * @dataProvider fieldTypeProvider
     */
    public function testAReadOnlyFieldValueCannotInjectMarkup(string $type): void
    {
        $html = $this->renderField($type, self::PAYLOAD, preview: false, config: ['readOnly' => true]);

        $handlers = array_filter($this->attributesIn($html), fn ($name) => str_starts_with($name, 'on'));

        $this->assertSame([], array_values($handlers), "The read-only {$type} field emitted an event handler attribute");
        $this->assertNotContains('script', $this->elementsIn($html), "The read-only {$type} field emitted a script element");
    }

    /** The dropdown sink, asserted on the markup rather than only on the parse result. */
    public function testTheDropdownPreviewEscapesItsHiddenInputValue(): void
    {
        $html = $this->renderField('dropdown', self::PAYLOAD);

        $this->assertStringContainsString('type="hidden"', $html, 'The preview branch still emits the hidden input');
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&quot;&gt;&lt;script&gt;', $html, 'The value must be entity encoded');
    }

    /** Nothing regressed: a legitimate option still renders its label and round-trips its value. */
    public function testAnOrdinaryDropdownValueStillRenders(): void
    {
        $html = $this->renderField('dropdown', 'second');

        $this->assertStringContainsString('Second option', $html, 'The option label is still shown');
        $this->assertStringContainsString('value="second"', $html, 'The value still round-trips through the hidden input');
    }

    /** An option key containing HTML metacharacters is encoded, not mangled: it decodes back to itself. */
    public function testALegitimateOptionKeyWithMetacharactersRoundTrips(): void
    {
        $html = $this->renderField('dropdown', 'a&b', config: ['options' => ['a&b' => 'A and B']]);

        $this->assertStringContainsString('value="a&amp;b"', $html);
        $this->assertSame('a&b', $this->parse($html)->getElementsByTagName('input')->item(0)->getAttribute('value'));
        $this->assertStringContainsString('A and B', $html, 'The option label is still shown');
    }

    /** Nothing regressed: the editable dropdown is untouched by the preview branch fix. */
    public function testAnEditableDropdownStillRendersItsOptions(): void
    {
        $html = $this->renderField('dropdown', 'second', preview: false);

        $this->assertStringContainsString('<select', $html);
        $this->assertStringContainsString('First option', $html);
        $this->assertStringContainsString('Second option', $html);
    }
}
