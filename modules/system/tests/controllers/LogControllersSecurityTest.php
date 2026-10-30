<?php

namespace System\Tests\Controllers;

use Backend\Models\User;
use Backend\Models\UserRole;
use Cms\Controllers\ThemeLogs;
use Cms\Models\ThemeLog;
use System\Controllers\EventLogs;
use System\Controllers\RequestLogs;
use System\Models\EventLog;
use System\Models\RequestLog;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\DB;

/**
 * The three log screens implement FormController for its preview action alone: they list records and
 * preview one, and the only write they offer is emptying the log. The behaviour's record-scoped
 * create and update actions were routable all the same, so `system.access_logs` -- "View system
 * logs" -- also carried the ability to rewrite an existing entry and insert a fabricated one.
 *
 * Rewriting matters because the entry keeps its original user_id: the history then attributes a
 * change to an account that never made it, which emptying the log cannot do.
 *
 * $guarded drops those actions from routing, leaving index and preview.
 */
class LogControllersSecurityTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('app.debug', true);
        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        // Registered behind runningInBackend(), false under PHPUnit; without them these screens
        // cannot render and the positive controls below would pass for the wrong reason.
        $this->invokeProviderMethod(new \Backend\ServiceProvider($this->app), 'registerBackendWidgets');
        $this->invokeProviderMethod(new \System\ServiceProvider($this->app), 'registerBackendSettings');
        $this->invokeProviderMethod(new \System\ServiceProvider($this->app), 'registerBackendNavigation');

        Model::unguard();

        ThemeLog::create([
            'type' => 'update',
            'theme' => 'test',
            'template' => 'pages/home.htm',
            'content' => 'ORIGINAL',
            'old_content' => 'BEFORE',
            'user_id' => 1,
        ]);

        EventLog::create([
            'level' => 'error',
            'message' => 'ORIGINAL',
        ]);

        RequestLog::create([
            'status_code' => 404,
            'url' => 'http://localhost/original',
            'count' => 1,
        ]);

        $role = UserRole::create([
            'name' => 'Log viewer',
            'code' => 'log_viewer',
            'permissions' => ['system.access_logs' => 1],
        ]);
        $user = User::create([
            'first_name' => 'Log',
            'last_name' => 'Viewer',
            'login' => 'logviewer',
            'email' => 'logviewer@test.test',
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'role_id' => $role->id,
        ]);

        Model::reguard();

        $this->actingAs($user);
    }

    protected function invokeProviderMethod($provider, string $method): void
    {
        $reflection = new \ReflectionMethod($provider, $method);
        $reflection->setAccessible(true);
        $reflection->invoke($provider);
    }

    /**
     * controller class, backend url, model class, form array name, the field a rewrite would change
     */
    public static function logScreenProvider(): array
    {
        return [
            'theme logs' => [ThemeLogs::class, 'backend/cms/themelogs', ThemeLog::class, 'ThemeLog', 'content'],
            'event logs' => [EventLogs::class, 'backend/system/eventlogs', EventLog::class, 'EventLog', 'message'],
            'request logs' => [RequestLogs::class, 'backend/system/requestlogs', RequestLog::class, 'RequestLog', 'url'],
        ];
    }

    /**
     * @dataProvider logScreenProvider
     */
    public function testWriteActionsAreNotRoutable(string $controllerClass, string $url): void
    {
        $controller = new $controllerClass;

        foreach (['create', 'update'] as $action) {
            $this->assertTrue($controller->methodExists($action), "Precondition: {$action} exists");
            $this->assertFalse($controller->actionExists($action), "{$action} must not be routable on {$url}");
        }

        $this->assertTrue($controller->actionExists('index'), 'index must still route');
        $this->assertTrue($controller->actionExists('preview'), 'preview must still route');
    }

    /**
     * @dataProvider logScreenProvider
     */
    public function testTheWriteActionsAreNotServed(string $controllerClass, string $url, string $modelClass): void
    {
        $entry = $modelClass::first();

        $this->assertEquals(404, $this->get($url . '/update/' . $entry->getKey())->getStatusCode());
        $this->assertEquals(404, $this->get($url . '/create')->getStatusCode());
    }

    /**
     * With the action gone the handler has nowhere to be dispatched from, which is what the backend
     * reports rather than a permission error.
     */
    protected function assertHandlerWasNotServed($response, string $handler): void
    {
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString(
            "AJAX handler '{$handler}' was not found.",
            $response->getContent()
        );
    }

    /**
     * The reported request: the save handler of the inherited update action, rewriting an entry that
     * keeps pointing at the user who really made the change.
     *
     * @dataProvider logScreenProvider
     */
    public function testALogEntryCannotBeRewritten(
        string $controllerClass,
        string $url,
        string $modelClass,
        string $arrayName,
        string $field
    ): void {
        $entry = $modelClass::first();
        $original = $entry->getAttribute($field);

        $response = $this->post($url . '/update/' . $entry->getKey(), [
            $arrayName => [$field => 'FORGED-BY-LOG-READER'],
        ], [
            'X-WINTER-REQUEST-HANDLER' => 'onSave',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(
            $original,
            $modelClass::find($entry->getKey())->getAttribute($field),
            "A log entry's {$field} was rewritten through {$url}"
        );
        $this->assertHandlerWasNotServed($response, 'onSave');
    }

    /**
     * @dataProvider logScreenProvider
     */
    public function testALogEntryCannotBeForged(
        string $controllerClass,
        string $url,
        string $modelClass,
        string $arrayName,
        string $field
    ): void {
        $response = $this->post($url . '/create', [
            $arrayName => [$field => 'FORGED-BY-LOG-READER'],
        ], [
            'X-WINTER-REQUEST-HANDLER' => 'onSave',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        // Counted by content rather than by rows: a refused request is itself logged to the event
        // log, so the row count moves for reasons that have nothing to do with the forgery.
        $this->assertEquals(
            0,
            $modelClass::where($field, 'FORGED-BY-LOG-READER')->count(),
            "A log entry was inserted through {$url}"
        );
        $this->assertHandlerWasNotServed($response, 'onSave');
    }

    /**
     * @dataProvider logScreenProvider
     */
    public function testASingleLogEntryCannotBeDeleted(
        string $controllerClass,
        string $url,
        string $modelClass
    ): void {
        $entry = $modelClass::first();

        $response = $this->post($url . '/update/' . $entry->getKey(), [], [
            'X-WINTER-REQUEST-HANDLER' => 'onDelete',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertNotNull(
            $modelClass::find($entry->getKey()),
            "A log entry was deleted through {$url}"
        );
        $this->assertHandlerWasNotServed($response, 'onDelete');
    }

    /**
     * Nothing legitimate regressed: the screens still list, preview and empty their log.
     *
     * @dataProvider logScreenProvider
     */
    public function testTheLogScreensStillWork(
        string $controllerClass,
        string $url,
        string $modelClass
    ): void {
        $entry = $modelClass::first();

        $this->assertEquals(200, $this->get($url)->getStatusCode(), "{$url} must still list");
        $this->assertEquals(
            200,
            $this->get($url . '/preview/' . $entry->getKey())->getStatusCode(),
            "{$url} must still preview"
        );

        $response = $this->post($url, [], [
            'X-WINTER-REQUEST-HANDLER' => 'onEmptyLog',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $modelClass::count(), "{$url} must still empty its log");
    }

    /**
     * The entries seeded above are the ones the assertions above read, so a silent failure to seed
     * would make them pass for the wrong reason.
     */
    public function testTheSeededEntriesExist(): void
    {
        $this->assertEquals(1, DB::table('cms_theme_logs')->count());
        $this->assertEquals(1, DB::table('system_event_logs')->count());
        $this->assertEquals(1, DB::table('system_request_logs')->count());
    }
}
