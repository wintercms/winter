<?php

namespace System\Tests\Twig;

use System\Tests\Bootstrap\TestCase;

class FilterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->twig = $this->app->make('twig.environment');
    }

    public function testFilterUpper()
    {
        // Run the Twig Filter tests
        // @see https://github.com/twigphp/Twig/commit/d475a92c83d13951fbcadd20e64b8ebca82bfc7e
        $tests = [
            // "filter" tag applies a filter on its children
            'filter_upper' => [
                'template' => <<<TEMPLATE
{% filter upper %}
Some text with a {{ var }}
{% endfilter %}
TEMPLATE,
                'data' => ['var' => 'var'],
                'expect' => 'SOME TEXT WITH A VAR',
            ],

            // "filter" tag applies a filter on its children
            'filter_jsonencode' => [
                'template' => '{% filter json_encode|raw %}test{% endfilter %}',
                'data' => [],
                'expect' => '"test"',
            ],

            // "filter" tags accept multiple chained filters
            'filter_multiple' => [
                'template' => <<<TEMPLATE
{% filter lower|title %}
  {{ var }}
{% endfilter %}
TEMPLATE,
                'data' => ['var' => 'VAR'],
                'expect' => '    Var',
            ],

            // "filter" tags can be nested at will
            'filter_multiple' => [
                'template' => <<<TEMPLATE
{% filter lower|title %}
  {{ var }}
  {% filter upper %}
    {{ var }}
  {% endfilter %}
  {{ var }}
{% endfilter %}
TEMPLATE,
                'data' => ['var' => 'VAR'],
                'expect' => <<<EXPECT
  Var
      Var
    Var
EXPECT,
            ],

            // "filter" tag applies the filter on "for" tags
            'filter_for' => [
                'template' => <<<TEMPLATE
{% filter upper %}
{% for item in items %}
{{ item }}
{% endfor %}
{% endfilter %}
TEMPLATE,
                'data' => ['items' => ['a', 'b']],
                'expect' => <<<EXPECT
A
B
EXPECT,
            ],

            // "filter" tag applies the filter on "if" tags
            'filter_for' => [
                'template' => <<<TEMPLATE
{% filter upper %}
{% if items %}
{{ items|join(', ') }}
{% endif %}

{% if items.3 is defined %}
FOO
{% else %}
{{ items.1 }}
{% endif %}

{% if items.3 is defined %}
FOO
{% elseif items.1 %}
{{ items.0 }}
{% endif %}

{% endfilter %}
TEMPLATE,
                'data' => ['items' => ['a', 'b']],
                'expect' => <<<EXPECT
A, B

B

A
EXPECT,
            ],
        ];

        foreach ($tests as $name => $test) {
            $template = $this->twig->createTemplate($test['template']);
            $this->assertEquals(
                str_replace(
                    "\r\n",
                    "\n",
                    trim($test['expect']),
                ),
                str_replace(
                    "\r\n",
                    "\n",
                    trim($template->render($test['data']))
                )
            );
        }
    }

    public function testFilterMdNull()
    {
        $template = $this->twig->createTemplate('{% filter md %}{{ value }}{% endfilter %}');

        $this->assertEquals('', $template->render(['value' => null]));
    }

    public function testFilterMdSafeNull()
    {
        $template = $this->twig->createTemplate('{% filter md_safe %}{{ value }}{% endfilter %}');

        $this->assertEquals('', $template->render(['value' => null]));
    }

    public function testFilterMdLineNull()
    {
        $template = $this->twig->createTemplate('{% filter md_line %}{{ value }}{% endfilter %}');

        $this->assertEquals('', $template->render(['value' => null]));
    }
}
