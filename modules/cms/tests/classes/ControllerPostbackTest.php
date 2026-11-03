<?php

namespace Cms\Tests\Classes;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Illuminate\Support\Facades\Request;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Halcyon\Model;
use Winter\Storm\Support\Facades\Config;

class ControllerPostbackTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.themesPath', '/modules/cms/tests/fixtures/themes');
        Model::clearBootedModels();
        Model::flushEventListeners();
    }

    /**
     * Builds a mock Request that simulates an AJAX POST with the given handler.
     */
    protected function configAjaxRequestMock(string $handler, $partials = false)
    {
        $requestMock = $this
            ->getMockBuilder('Illuminate\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['ajax', 'method', 'header'])
            ->getMock();

        $map = [
            ['X_WINTER_REQUEST_HANDLER', null, $handler],
            ['X_WINTER_REQUEST_PARTIALS', null, $partials],
        ];

        $requestMock->expects($this->any())
            ->method('ajax')
            ->will($this->returnValue(true));

        $requestMock->expects($this->any())
            ->method('method')
            ->will($this->returnValue('POST'));

        $requestMock->expects($this->any())
            ->method('header')
            ->will($this->returnValueMap($map));

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
            ->setMethods(['ajax', 'method', 'header', 'post', 'input'])
            ->getMock();

        $requestMock->expects($this->any())
            ->method('ajax')
            ->will($this->returnValue(false));

        $requestMock->expects($this->any())
            ->method('method')
            ->will($this->returnValue('POST'));

        $requestMock->expects($this->any())
            ->method('header')
            ->will($this->returnValue(null));

        $postData = ['_handler' => $handler];
        $requestMock->expects($this->any())
            ->method('post')
            ->will($this->returnCallback(function ($key = null, $default = null) use ($postData) {
                if ($key === null) {
                    return $postData;
                }
                return $postData[$key] ?? $default;
            }));

        $requestMock->expects($this->any())
            ->method('input')
            ->will($this->returnCallback(function ($key = null, $default = null) use ($postData) {
                if ($key === null) {
                    return $postData;
                }
                return $postData[$key] ?? $default;
            }));

        return $requestMock;
    }

    //
    // AJAX header path — validates handler name (existing behavior)
    //

    public function testAjaxPathRejectsInvalidHandlerName(): void
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Invalid AJAX handler name: update_onDelete.');

        Request::swap($this->configAjaxRequestMock('update_onDelete'));

        $theme = Theme::load('test');
        $controller = new Controller($theme);
        $controller->run('/ajax-test');
    }

    public function testAjaxPathAcceptsValidHandlerName(): void
    {
        // onTest exists on the ajax-test page, so this should not throw SystemException for invalid name
        // It may throw for other reasons (missing partials, etc.) but not for handler name validation
        Request::swap($this->configAjaxRequestMock('onTest', ''));

        $theme = Theme::load('test');
        $controller = new Controller($theme);

        try {
            $controller->run('/ajax-test');
        } catch (SystemException $e) {
            $this->assertStringNotContainsString('Invalid AJAX handler name', $e->getMessage());
        }
        $this->assertTrue(true);
    }

    //
    // Postback _handler path — validates handler name (our fix)
    //

    public function testPostbackPathRejectsInvalidHandlerName(): void
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Invalid AJAX handler name: update_onDelete.');

        Config::set('cms.enableCsrfProtection', false);
        Request::swap($this->configPostbackRequestMock('update_onDelete'));

        $theme = Theme::load('test');
        $controller = new Controller($theme);
        $controller->run('/ajax-test');
    }

    public function testPostbackPathRejectsMethodName(): void
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Invalid AJAX handler name: execPageCycle.');

        Config::set('cms.enableCsrfProtection', false);
        Request::swap($this->configPostbackRequestMock('execPageCycle'));

        $theme = Theme::load('test');
        $controller = new Controller($theme);
        $controller->run('/ajax-test');
    }

    public function testPostbackPathRejectsActionPrefixedHandler(): void
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Invalid AJAX handler name: index_onSave.');

        Config::set('cms.enableCsrfProtection', false);
        Request::swap($this->configPostbackRequestMock('index_onSave'));

        $theme = Theme::load('test');
        $controller = new Controller($theme);
        $controller->run('/ajax-test');
    }
}
