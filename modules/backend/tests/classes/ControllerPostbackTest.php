<?php

namespace Backend\Tests\Classes;

use Backend\Controllers\Auth;
use Backend\Tests\Fixtures\Controllers\PostbackTestController;
use Backend\Tests\Fixtures\Models\UserFixture;
use Illuminate\Support\Facades\Request;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Support\Facades\Config;

class ControllerPostbackTest extends PluginTestCase
{
    /**
     * Builds a mock Request that simulates an AJAX POST with the given handler.
     */
    protected function configAjaxRequestMock(string $handler)
    {
        $requestMock = $this
            ->getMockBuilder('Illuminate\Http\Request')
            ->disableOriginalConstructor()
            ->onlyMethods(['ajax', 'method', 'header', 'secure', 'path', 'getScheme', 'getHost', 'getPort', 'getBaseUrl'])
            ->getMock();

        $map = [
            ['X_WINTER_REQUEST_HANDLER', null, $handler],
            ['X_WINTER_REQUEST_PARTIALS', null, ''],
            ['X-CSRF-TOKEN', null, null],
            ['X-XSRF-TOKEN', null, null],
        ];

        $requestMock->expects($this->any())->method('ajax')->willReturn(true);
        $requestMock->expects($this->any())->method('method')->willReturn('POST');
        $requestMock->expects($this->any())->method('header')->willReturnMap($map);
        $requestMock->expects($this->any())->method('secure')->willReturn(false);
        $requestMock->expects($this->any())->method('path')->willReturn('backend/auth/signin');
        $requestMock->expects($this->any())->method('getScheme')->willReturn('http');
        $requestMock->expects($this->any())->method('getHost')->willReturn('localhost');
        $requestMock->expects($this->any())->method('getPort')->willReturn(80);
        $requestMock->expects($this->any())->method('getBaseUrl')->willReturn('');

        return $requestMock;
    }

    /**
     * Builds a mock Request that simulates a non-AJAX POST with _handler in POST data.
     */
    protected function configPostbackRequestMock(string $handler)
    {
        $requestMock = $this
            ->getMockBuilder('Illuminate\Http\Request')
            ->disableOriginalConstructor()
            ->onlyMethods(['ajax', 'method', 'header', 'post', 'input', 'secure', 'path', 'getScheme', 'getHost', 'getPort', 'getBaseUrl'])
            ->getMock();

        $requestMock->expects($this->any())->method('ajax')->willReturn(false);
        $requestMock->expects($this->any())->method('method')->willReturn('POST');
        $requestMock->expects($this->any())->method('header')->willReturn(null);
        $requestMock->expects($this->any())->method('secure')->willReturn(false);
        $requestMock->expects($this->any())->method('path')->willReturn('backend/auth/signin');
        $requestMock->expects($this->any())->method('getScheme')->willReturn('http');
        $requestMock->expects($this->any())->method('getHost')->willReturn('localhost');
        $requestMock->expects($this->any())->method('getPort')->willReturn(80);
        $requestMock->expects($this->any())->method('getBaseUrl')->willReturn('');

        $postData = ['_handler' => $handler];
        $requestMock->expects($this->any())->method('post')->willReturnCallback(
            function ($key = null, $default = null) use ($postData) {
                return $key === null ? $postData : ($postData[$key] ?? $default);
            }
        );
        $requestMock->expects($this->any())->method('input')->willReturnCallback(
            function ($key = null, $default = null) use ($postData) {
                return $key === null ? $postData : ($postData[$key] ?? $default);
            }
        );

        return $requestMock;
    }

    //
    // AJAX header path — validates handler name (existing behavior)
    //

    public function testAjaxPathRejectsInvalidHandlerName(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        Config::set('cms.enableCsrfProtection', false);

        // Build controller with real Request, then swap for mock before run()
        $controller = new Auth;
        Request::swap($this->configAjaxRequestMock('update_onDelete'));

        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Invalid AJAX handler name: update_onDelete.');
        $controller->run('signin');
    }

    public function testAjaxPathAcceptsValidHandlerName(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        Config::set('cms.enableCsrfProtection', false);

        $controller = new Auth;
        Request::swap($this->configAjaxRequestMock('onSave'));

        try {
            $controller->run('signin');
        } catch (SystemException $e) {
            // Handler not found is fine — name validation passed
            $this->assertStringNotContainsString('Invalid AJAX handler name', $e->getMessage());
            return;
        }
        $this->assertTrue(true);
    }

    //
    // Postback _handler path — validates handler name (our fix)
    //

    public function testPostbackPathRejectsInvalidHandlerName(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        Config::set('cms.enableCsrfProtection', false);

        $controller = new Auth;
        Request::swap($this->configPostbackRequestMock('update_onDelete'));

        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Invalid AJAX handler name: update_onDelete.');
        $controller->run('signin');
    }

    public function testPostbackPathRejectsMethodName(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        Config::set('cms.enableCsrfProtection', false);

        $controller = new Auth;
        Request::swap($this->configPostbackRequestMock('generatePermissionsField'));

        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Invalid AJAX handler name: generatePermissionsField.');
        $controller->run('signin');
    }

    public function testPostbackWithoutAResponseStillRendersThePage(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        Config::set('cms.enableCsrfProtection', false);

        $controller = new PostbackTestController;
        Request::swap($this->configPostbackRequestMock('onNoop'));

        $response = $controller->run('index');
        $content = is_object($response) && method_exists($response, 'getContent')
            ? $response->getContent()
            : (string) $response;

        $this->assertStringContainsString(
            'POSTBACK_FIXTURE_INDEX',
            $content,
            'A postback whose handler returns no response must still render the action page.'
        );
        $this->assertSame(1, $controller->handlerRuns, 'the handler must run exactly once');
        $this->assertSame(1, $controller->actionRuns, 'the action must run exactly once');
    }

    public function testWidgetPostbackDoesNotRunTheActionTwice(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        Config::set('cms.enableCsrfProtection', false);

        $controller = new PostbackTestController;
        Request::swap($this->configPostbackRequestMock('postbackwidget::onWidgetNoop'));

        $caught = null;

        try {
            $controller->run('index');
        }
        catch (SystemException $ex) {
            $caught = $ex;
        }

        $this->assertSame(
            1,
            $controller->actionRuns,
            'widget handler dispatch already ran the action; it must not run again'
        );
        $this->assertSame(1, $controller->widget->postbackwidget->handlerRuns, 'the widget handler must run exactly once');
        $this->assertInstanceOf(SystemException::class, $caught, 'the unrenderable postback must fail visibly');
        $this->assertStringContainsString('produced no response', $caught->getMessage());
    }

    public function testMissingHandlerDiscoveryDoesNotRunTheActionTwice(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        Config::set('cms.enableCsrfProtection', false);

        $controller = new PostbackTestController;
        Request::swap($this->configPostbackRequestMock('onNotDefinedAnywhere'));

        $caught = null;

        try {
            $controller->run('index');
        }
        catch (SystemException $ex) {
            $caught = $ex;
        }

        $this->assertSame(
            1,
            $controller->actionRuns,
            'handler discovery already ran the action while searching widgets; it must not run again'
        );
        $this->assertInstanceOf(SystemException::class, $caught, 'the unrenderable postback must fail visibly');
    }

    public function testHandlerNamingTheActionItselfDoesNotRunTwice(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        Config::set('cms.enableCsrfProtection', false);

        $controller = new PostbackTestController;
        Request::swap($this->configPostbackRequestMock('onSelfHandled'));

        $caught = null;

        try {
            $controller->run('onSelfHandled');
        }
        catch (SystemException $ex) {
            $caught = $ex;
        }

        $this->assertSame(
            1,
            $controller->actionRuns,
            'the handler already ran the method; the page action must not run it again'
        );
        $this->assertInstanceOf(SystemException::class, $caught, 'the unrenderable postback must fail visibly');
    }

    public function testPostbackPathRejectsActionPrefixedHandler(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        Config::set('cms.enableCsrfProtection', false);

        $controller = new Auth;
        Request::swap($this->configPostbackRequestMock('create_onSave'));

        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Invalid AJAX handler name: create_onSave.');
        $controller->run('signin');
    }
}
