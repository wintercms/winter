<?php

namespace Backend\Tests\Widgets;

use Backend\Widgets\Table;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * Regression coverage for GHSA-hq84-x37p-j6q5.
 *
 * The Table widget partial renders the request's `search` value inside a
 * <script type="text/template"> block. <script> is an HTML raw-text context, so the
 * surrounding value="..." quoting is not a boundary: a literal </script> in the query
 * string terminated the template early and injected attacker markup into the backend
 * document. The value must therefore be HTML-encoded on output.
 *
 * @see modules/backend/widgets/table/partials/_table.php
 */
class TableSearchEscapingTest extends PluginTestCase
{
    /**
     * The partial emits exactly two template blocks: [data-table-toolbar] and
     * [data-table-toolbar-search]. Any extra closing tag in the output means a payload
     * introduced a raw-text terminator of its own.
     */
    const EXPECTED_SCRIPT_CLOSERS = 2;

    const SEARCH_TEMPLATE_OPENER = '<script type="text/template" data-table-toolbar-search>';

    /**
     * Sets the ?search= value seen by the get() helper, which reads Request::query().
     */
    protected function setSearchQuery(string $value): void
    {
        $this->app['request']->query->set('search', $value);
    }

    protected function renderTable(array $config = []): string
    {
        $table = new Table(null, array_merge([
            'dataSource' => 'client',
            'columns' => [
                'title' => ['title' => 'Title'],
            ],
        ], $config));

        return $table->render();
    }

    /**
     * Returns everything rendered after the search template's closing tag. On a correctly
     * escaped output this is only the partial's own trailing markup.
     */
    protected function markupAfterSearchTemplate(string $html): string
    {
        $openerPos = strpos($html, self::SEARCH_TEMPLATE_OPENER);
        $this->assertNotFalse($openerPos, 'Search template block should be present');

        $afterOpener = substr($html, $openerPos + strlen(self::SEARCH_TEMPLATE_OPENER));

        // End tags are case-insensitive and may carry whitespace before the ">", so a
        // literal "</script>" search would miss </ScRiPt> and "</script >" terminators
        // and report a clean result for payloads that do in fact break out.
        $this->assertSame(
            1,
            preg_match('~</script\s*>~i', $afterOpener, $m, PREG_OFFSET_CAPTURE),
            'Search template should be closed'
        );

        return substr($afterOpener, $m[0][1] + strlen($m[0][0]));
    }

    public static function rawTextTerminatorProvider(): array
    {
        return [
            'plain closing tag' => ['</script><meta name="probe-plain">'],
            'mixed case' => ['</ScRiPt><meta name="probe-case">'],
            'trailing space' => ['</script ><meta name="probe-space">'],
            'trailing tab' => ["</script\t><meta name=\"probe-tab\">"],
            'trailing newline' => ["</script\n><meta name=\"probe-newline\">"],
            'attribute breakout' => ['"><img src=x onerror=alert(1)>'],
            'script element' => ['</script><script>alert(1)</script>'],
        ];
    }

    /**
     * @dataProvider rawTextTerminatorProvider
     */
    public function testSearchValueCannotTerminateTheScriptTemplate(string $payload)
    {
        $this->setSearchQuery($payload);

        $html = $this->renderTable();

        $this->assertStringNotContainsString(
            $payload,
            $html,
            'The raw payload must never be reflected verbatim'
        );

        $this->assertSame(
            self::EXPECTED_SCRIPT_CLOSERS,
            substr_count($html, '</script>'),
            'Payload introduced an extra raw-text terminator into the output'
        );
    }

    /**
     * @dataProvider rawTextTerminatorProvider
     */
    public function testPayloadCannotEscapeIntoDocumentMarkup(string $payload)
    {
        $this->setSearchQuery($payload);

        $escaped = $this->markupAfterSearchTemplate($this->renderTable());

        $this->assertStringNotContainsString('probe-', $escaped, 'Marker escaped the template');
        $this->assertStringNotContainsString('<img', $escaped, 'Image element escaped the template');
        $this->assertStringNotContainsString('alert(1)', $escaped, 'Script payload escaped the template');
    }

    /**
     * The template is emitted unconditionally by the partial -- it does not depend on the
     * `searching` option -- so the sink must be safe in both states. `searching` defaults
     * to false, which was the configuration most affected instances shipped with.
     */
    public function testEscapingAppliesRegardlessOfSearchingOption()
    {
        foreach ([true, false] as $searching) {
            $this->setSearchQuery('</script><meta name="probe-toggle">');

            $html = $this->renderTable(['searching' => $searching]);

            $this->assertStringNotContainsString(
                '</script><meta',
                $html,
                'Sink must be escaped with searching=' . var_export($searching, true)
            );
            $this->assertSame(
                self::EXPECTED_SCRIPT_CLOSERS,
                substr_count($html, '</script>'),
                'Unexpected terminator with searching=' . var_export($searching, true)
            );
        }
    }

    public function testAngleBracketsAndQuotesAreEncoded()
    {
        $this->setSearchQuery('<>"\'&');

        $html = $this->renderTable();

        $this->assertStringContainsString('value="&lt;&gt;&quot;&#039;&amp;"', $html);
    }

    /**
     * Guards against a fix that escapes but mangles ordinary input.
     */
    public function testOrdinarySearchTextIsPreserved()
    {
        $this->setSearchQuery('hello world');

        $this->assertStringContainsString('value="hello world"', $this->renderTable());
    }

    /**
     * Guards against a fix that breaks non-ASCII search terms.
     */
    public function testUnicodeSearchTextIsPreserved()
    {
        $this->setSearchQuery('héllo 世界 😀');

        $this->assertStringContainsString('value="héllo 世界 😀"', $this->renderTable());
    }

    /**
     * Control: a payload with no raw-text terminator was never able to break out, so it
     * must not be counted as evidence that escaping works. If this ever fails, the tests
     * above are measuring something other than the raw-text boundary.
     */
    public function testEscapedSlashControlNeverEscapedTheTemplate()
    {
        $this->setSearchQuery('<\\/script><meta name="probe-control">');

        $escaped = $this->markupAfterSearchTemplate($this->renderTable());

        $this->assertStringNotContainsString('probe-control', $escaped);
    }
}
