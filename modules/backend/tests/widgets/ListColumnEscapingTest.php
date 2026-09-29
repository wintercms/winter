<?php

namespace Backend\Tests\Widgets;

use Backend\Models\User;
use Backend\Widgets\Lists;
use DOMDocument;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * The list body partial renders column values raw (`<?= $this->getColumnValue(...) ?>`), so
 * every column type that emits a record value into markup has to escape it itself.
 *
 * Regression coverage for GHSA-7mpf-4465-7fc2, where the image column interpolated an
 * unescaped URL into a single-quoted `src` attribute.
 */
class ListColumnEscapingTest extends PluginTestCase
{
    /**
     * Breaks out of a single-quoted attribute without needing whitespace: `/` is legal in a
     * URL path and is also legal after a quoted attribute value, so this survives
     * FILTER_VALIDATE_URL and still parses as a live event handler.
     */
    protected const PAYLOAD = "http://example.com/a.jpg'/onerror='window.pwned=1";

    /** Column types that render a free-form record value into markup. */
    public static function columnTypeProvider(): array
    {
        return [
            'text' => ['text'],
            'number' => ['number'],
            'image' => ['image'],
            'colorpicker' => ['colorpicker'],
            'switch' => ['switch'],
        ];
    }

    protected function renderColumn(string $type, $value): string
    {
        $record = new User;
        $record->email = $value;

        $list = new Lists(null, [
            'model' => new User,
            'arrayName' => 'array',
            'columns' => ['email' => ['type' => $type, 'label' => 'Email']],
        ]);

        $list->getColumns();

        return (string) $list->getColumnValue($record, $list->getColumn('email'));
    }

    /** Returns every attribute name present in the rendered markup. */
    protected function attributesIn(string $html): array
    {
        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<html><body>' . $html . '</body></html>');
        libxml_clear_errors();

        $names = [];
        foreach ($doc->getElementsByTagName('*') as $element) {
            foreach ($element->attributes as $attribute) {
                $names[] = strtolower($attribute->name);
            }
        }

        return $names;
    }

    /**
     * @dataProvider columnTypeProvider
     */
    public function testAColumnValueCannotInjectAnAttribute(string $type): void
    {
        $attributes = $this->attributesIn($this->renderColumn($type, self::PAYLOAD));

        $handlers = array_filter($attributes, fn ($name) => str_starts_with($name, 'on'));

        $this->assertSame([], array_values($handlers), "The {$type} column emitted an event handler attribute");
    }

    /** The reported case, asserted on the markup rather than only on the parse result. */
    public function testTheImageColumnEscapesTheUrl(): void
    {
        $html = $this->renderColumn('image', self::PAYLOAD);

        $this->assertStringNotContainsString("onerror='", $html);
        $this->assertStringContainsString('&#039;/onerror=', $html, 'The quote must be entity encoded');
        $this->assertSame(['src', 'width', 'height'], $this->attributesIn($html));
    }

    /** Config-supplied dimensions land in attributes too, so they are escaped as well. */
    public function testTheImageColumnEscapesItsDimensions(): void
    {
        $record = new User;
        $record->email = 'http://example.com/a.jpg';

        $list = new Lists(null, [
            'model' => new User,
            'arrayName' => 'array',
            'columns' => [
                'email' => [
                    'type' => 'image',
                    'label' => 'Email',
                    'width' => "50'/onerror='window.pwned=1",
                ],
            ],
        ]);

        $list->getColumns();
        $html = (string) $list->getColumnValue($record, $list->getColumn('email'));

        $this->assertSame(['src', 'width', 'height'], $this->attributesIn($html));
    }

    /** Nothing regressed: an ordinary value still renders as a usable image tag. */
    public function testAnOrdinaryImageUrlStillRenders(): void
    {
        $html = $this->renderColumn('image', 'http://example.com/a.jpg');

        $this->assertStringContainsString('http://example.com/a.jpg', $html);
        $this->assertSame(['src', 'width', 'height'], $this->attributesIn($html));
    }
}
