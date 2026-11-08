<?php

namespace System\Tests\Twig;

use Mockery;
use System\Classes\MailManager;
use System\Tests\Bootstrap\TestCase;
use System\Tests\Concerns\AssertsNoDeprecations;

class MailPartialTest extends TestCase
{
    use AssertsNoDeprecations;

    public function setUp(): void
    {
        parent::setUp();

        // Echo each partial call back so the test sees exactly what the tag passed to the manager.
        $manager = Mockery::mock(MailManager::class);
        $manager->shouldReceive('renderPartial')->andReturnUsing(function ($code, array $params = []) {
            return '['.$code.':'.json_encode($params).']';
        });
        $this->app->instance(MailManager::class, $manager);
    }

    public function testPartialTagWithParamsAndBody(): void
    {
        $twig = $this->app->make('twig.environment.mailer');

        $output = $this->assertNoDeprecations(function () use ($twig) {
            return $twig->createTemplate(
                "{% partial 'header' title=name %}|"
                . "{% partial 'button' url='http://example.com' body %}Click <b>{{ name }}</b>{% endpartial %}|"
                . "{% set out %}{% partial 'panel' body %}{% for i in items %}{{ i }}{% endfor %}{% endpartial %}{% endset %}{{ out }}"
            )->render(['name' => '<World>', 'items' => ['a', 'b']]);
        });

        $this->assertSame(
            '[header:{"title":"<World>"}]|'
            . '[button:{"body":"Click <b>&lt;World&gt;<\/b>","url":"http:\/\/example.com"}]|'
            . '[panel:{"body":"ab"}]',
            $output
        );
    }
}
