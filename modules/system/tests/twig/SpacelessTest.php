<?php

namespace System\Tests\Twig;

use System\Tests\Bootstrap\TestCase;

class SpacelessTest extends TestCase
{
    public function testSpacelessNode()
    {
        $twig = $this->app->make('twig.environment');
        $template = $twig->createTemplate(
            '<p>Test</p>

            {% spaceless %}
                <p class="test-1 test-2">This is a sentence.</p>

                <p> This is another sentence.</p>
            {% endspaceless %}'
        );

        $this->assertEquals(
            str_replace(
                "\r\n",
                "\n",
                '<p>Test</p>

            <p class="test-1 test-2">This is a sentence.</p><p> This is another sentence.</p>',
            ),
            str_replace(
                "\r\n",
                "\n",
                trim($template->render())
            )
        );
    }
}
