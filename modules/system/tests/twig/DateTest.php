<?php

namespace System\Tests\Twig;

use System\Tests\Bootstrap\TestCase;
use System\Tests\Concerns\AssertsNoDeprecations;

class DateTest extends TestCase
{
    use AssertsNoDeprecations;

    public function setUp(): void
    {
        parent::setUp();

        $this->twig = $this->app->make('twig.environment');
    }

    public function testDateFunctionAndFilterRaiseNoDeprecations()
    {
        // Twig 3.9 deprecated the twig_date_converter() and twig_date_format_filter() helpers.
        $output = $this->assertNoDeprecations(function () {
            return $this->twig->createTemplate(
                "{{ date('2026-01-02 03:04:05')|date('Y-m-d H:i') }}|{{ '2026-01-02'|date('D j M') }}|{{ date()|date('Y') == 'now'|date('Y') ? 'now' }}"
            )->render([]);
        });

        $this->assertSame('2026-01-02 03:04|Fri 2 Jan|now', $output);
    }

    public function testDateFilterFormatsCarbonAndDateIntervals()
    {
        $output = $this->assertNoDeprecations(function () {
            return $this->twig->createTemplate("{{ when|date('Y-m-d') }}|{{ interval|date('%d days') }}")->render([
                'when' => \Carbon\Carbon::parse('2026-03-04 05:06:07'),
                'interval' => new \DateInterval('P3D'),
            ]);
        });

        $this->assertSame('2026-03-04|3 days', $output);
    }
}
