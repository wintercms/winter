<?php

namespace Backend\Tests\Classes;

use Backend\Classes\BackendController;
use Backend\Tests\Fixtures\Models\UserFixture;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme as CmsTheme;
use Illuminate\Support\Facades\Request;
use System\Models\EventLog;
use System\Models\MailLayout;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File;

/**
 * Regression coverage for GHSA-p2ch-c2c3-4xm5.
 *
 * AJAX handlers (`onFoo`, `index_onFoo`) must not be reachable as backend page actions, in any
 * spelling, while ordinary page actions and AJAX dispatch keep working.
 */
class HandlerDispatchSecurityTest extends PluginTestCase
{
    protected $canaryPaths = [];

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.enableCsrfProtection', true);
        Config::set('cms.backendUri', 'backend');
    }

    public function tearDown(): void
    {
        foreach ($this->canaryPaths as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        EventLog::truncate();

        parent::tearDown();
    }

    //
    // Helpers
    //

    protected function parseAction(string $segment): string
    {
        $controller = new BackendController();
        $method = new \ReflectionMethod($controller, 'parseAction');
        $method->setAccessible(true);

        return $method->invoke($controller, $segment);
    }

    protected function useTestTheme(): CmsTheme
    {
        Config::set('cms.activeTheme', 'test');
        Config::set('cms.themesPath', '/modules/cms/tests/fixtures/themes');
        CmsTheme::resetCache();

        return CmsTheme::load('test');
    }

    protected function seedCanaryPage(CmsTheme $theme, string $fileName): string
    {
        $page = CmsPage::inTheme($theme);
        $page->fileName = $fileName;
        $page->title = 'CSRF canary';
        $page->url = '/' . str_replace('.htm', '', $fileName);
        $page->markup = '<p>canary</p>';
        $page->save();

        $path = $theme->getPath() . '/pages/' . $fileName;
        $this->canaryPaths[] = $path;
        $this->assertTrue(File::exists($path), "Precondition: {$fileName} written to disk");

        return $path;
    }

    protected function seedEventLog(): void
    {
        EventLog::truncate();
        EventLog::add('csrf canary A');
        EventLog::add('csrf canary B');
    }

    protected function canaryCount(): int
    {
        return EventLog::where('message', 'like', 'csrf canary%')->count();
    }

    //
    // Blocked: handler names must never be reachable as page actions
    //

    /** The reported primitive, plus its siblings across all three modules. */
    public function testControllerDeclaredHandlersAreNotReachable()
    {
        $cases = [
            [new \System\Controllers\EventLogs(), 'index_onEmptyLog'],
            [new \System\Controllers\RequestLogs(), 'index_onEmptyLog'],
            [new \System\Controllers\MailLayouts(), 'update_onResetDefault'],
            [new \System\Controllers\MailTemplates(), 'onTest'],
            [new \System\Controllers\Settings(), 'update_onResetDefault'],
            [new \Backend\Controllers\Users(), 'update_onUnsuspendUser'],
            [new \Backend\Controllers\Users(), 'update_onImpersonateUser'],
            [new \Backend\Controllers\Preferences(), 'index_onResetDefault'],
            [new \Cms\Controllers\Index(), 'onDelete'],
            [new \Cms\Controllers\Index(), 'onDeleteTemplates'],
            [new \Cms\Controllers\Index(), 'onSave'],
            [new \Cms\Controllers\ThemeOptions(), 'update_onResetDefault'],
        ];

        foreach ($cases as [$controller, $handler]) {
            $this->assertTrue(
                $controller->methodExists($handler),
                get_class($controller) . "::{$handler} must exist for this test to mean anything"
            );
            $this->assertFalse(
                $controller->actionExists($handler),
                get_class($controller) . "::{$handler} must not be reachable as a page action"
            );
        }
    }

    /**
     * PHP method names are case-insensitive, so both spellings resolve to the same handler --
     * which is why the guard has to compare the *resolved* name. The lowercase one is the case
     * that defeats a guard comparing the requested string. Other casings collapse onto these
     * same two, so they are not repeated.
     *
     * The assertTrue() is load-bearing: it proves the spelling really resolves, so that the
     * assertFalse() beside it cannot pass merely because the method was not found.
     */
    public function testNoCasingOfAHandlerNameIsReachable()
    {
        $controller = new \System\Controllers\EventLogs();

        foreach (['index_onEmptyLog', 'index_onemptylog'] as $spelling) {
            $this->assertTrue(
                method_exists($controller, $spelling),
                "Precondition: {$spelling} resolves to the handler"
            );
            $this->assertFalse(
                $controller->actionExists($spelling),
                "{$spelling} must not be reachable"
            );
        }
    }

    /**
     * parseAction() lowercases dashed segments, which can turn an arbitrary URL into an
     * all-lowercase name that still resolves to a mixed-case handler.
     */
    public function testDashedSpellingsCannotLaunderAHandlerName()
    {
        $controller = new \System\Controllers\EventLogs();

        // The laundering step is real: this parses to a lowercase name that does resolve.
        $this->assertEquals('index_onemptylog', $this->parseAction('index-onemptylog'));
        $this->assertTrue(method_exists($controller, $this->parseAction('index-onemptylog')));

        // One segment per distinct parseAction() result: the laundered name that resolves, and
        // the two shapes that normalise to something which does not. Extra dash placements all
        // collapse onto these.
        foreach (['index-onemptylog', 'index-on-empty-log', 'index_on-emptylog'] as $segment) {
            $this->assertFalse(
                $controller->actionExists($this->parseAction($segment)),
                "Dashed segment '{$segment}' must not reach a handler"
            );
        }
    }

    /**
     * Behaviour handlers. Extension methods are looked up case-sensitively, so only the
     * canonical spelling resolves at all -- hence the methodExists() assertions below.
     */
    public function testBehaviourProvidedHandlersAreNotReachable()
    {
        $users = new \Backend\Controllers\Users();

        // FormController / ListController / RelationController handlers.
        foreach (['update_onDelete', 'update_onSave', 'create_onSave', 'index_onDelete'] as $handler) {
            $this->assertTrue($users->methodExists($handler), "Precondition: {$handler} exists");
            $this->assertFalse($users->actionExists($handler), "{$handler} must not be reachable");
        }

        // A behaviour that declares no $actions has no allowlist to fall back on, so its
        // handlers rely entirely on the name guard. Several ecosystem plugins are in this
        // position, and it is the case most likely to regress.
        $unguarded = new DispatchProbeBehaviourController();

        $this->assertTrue($unguarded->methodExists('onBar'), 'Precondition: the behaviour supplies it');
        $this->assertFalse(
            $unguarded->actionExists('onBar'),
            'a handler on a behaviour without $actions must still be blocked'
        );

        foreach (['onbar', 'ONBAR'] as $spelling) {
            $this->assertFalse(
                $unguarded->methodExists($spelling),
                'extension lookup is case-sensitive, so no other casing resolves'
            );
            $this->assertFalse($unguarded->actionExists($spelling));
        }
    }

    /** Public helpers that were reachable as URLs by accident. */
    public function testCamelCaseHelpersAreNoLongerRoutable()
    {
        $cases = [
            [new \Backend\Controllers\Files(), 'getThumbUrl'],
            [new \System\Controllers\Settings(), 'formRender'],
            [new \System\Controllers\MailBrandSettings(), 'renderSampleMessage'],
        ];

        foreach ($cases as [$controller, $method]) {
            $this->assertTrue($controller->methodExists($method), "Precondition: {$method} exists");
            $this->assertFalse($controller->actionExists($method), "{$method} must not be routable");
        }
    }

    //
    // Blocked, end to end: token-less GETs must not mutate anything.
    // One test per way a handler can receive input -- none, a path segment, the query string.
    //

    public function testTokenlessGetDoesNotTruncateTheEventLog()
    {
        $this->seedEventLog();
        $this->actingAs((new UserFixture)->withPermission('system.access_logs', true));

        foreach (['index_onEmptyLog', 'index_onemptylog'] as $segment) {
            $status = $this->get("backend/system/eventlogs/{$segment}")->getStatusCode();
            $this->assertEquals(404, $status, "GET {$segment} must 404");
            $this->assertEquals(2, $this->canaryCount(), "GET {$segment} must not truncate");
        }
    }

    public function testTokenlessGetDoesNotDeleteACmsTemplate()
    {
        $theme = $this->useTestTheme();
        $path = $this->seedCanaryPage($theme, 'csrf-canary.htm');

        $this->actingAs((new UserFixture)->asSuperUser());

        // Cms\Controllers\Index reads its input from Request::input(), which reads the query
        // string, so the post() helper's method gate does not apply here.
        $status = $this->get('backend/cms/index/onDelete?' . http_build_query([
            'theme' => 'test',
            'templateType' => 'page',
            'templatePath' => 'csrf-canary.htm',
        ]))->getStatusCode();

        $this->assertEquals(404, $status);
        $this->assertTrue(File::exists($path), 'A token-less GET must not delete a CMS page');
    }

    public function testTokenlessGetDoesNotResetAMailLayout()
    {
        $layout = MailLayout::first();
        $this->assertNotNull($layout, 'Precondition: a mail layout exists');

        $layout->content_html = '<p>CUSTOMISED BY OPERATOR</p>';
        $layout->save();

        $this->actingAs((new UserFixture)->asSuperUser());

        $status = $this->get('backend/system/maillayouts/update_onResetDefault/' . $layout->id)->getStatusCode();

        $this->assertEquals(404, $status);
        $this->assertEquals(
            '<p>CUSTOMISED BY OPERATOR</p>',
            MailLayout::find($layout->id)->content_html,
            'A token-less GET must not reset a mail layout'
        );
    }

    //
    // Still works: nothing legitimate may regress
    //

    public function testLowercasePageActionsStillResolve()
    {
        $users = new \Backend\Controllers\Users();

        // index comes from ListController, create/update/preview from FormController --
        // all still exposed through each behaviour's $actions allowlist.
        foreach (['index', 'create', 'update', 'preview'] as $action) {
            $this->assertTrue($users->actionExists($action), "Page action {$action} must still resolve");
        }

        $this->assertTrue((new \Cms\Controllers\Index())->actionExists('index'));
        $this->assertTrue((new \System\Controllers\EventLogs())->actionExists('index'));
    }

    /**
     * Asserts dispatch, not rendering: a 404 would mean a valid page action was rejected,
     * whereas a 500 is unrelated breakage this test should not be hostage to.
     */
    public function testBackendPagesStillDispatch()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        foreach ([
            'backend/backend/users',
            'backend/backend/userroles',
            'backend/backend/usergroups',
            'backend/system/eventlogs',
            'backend/system/settings',
            'backend/system/maillayouts',
            'backend/backend/myaccount',
            'backend/backend/preferences',
        ] as $url) {
            $this->assertNotEquals(
                404,
                $this->get($url)->getStatusCode(),
                "{$url} must still dispatch -- a 404 means the guard rejected a valid page action"
            );
        }
    }

    /** Handlers must stay callable the way the framework actually calls them. */
    public function testAjaxHandlerDispatchStillWorks()
    {
        Config::set('cms.enableCsrfProtection', false);
        $this->seedEventLog();
        $this->actingAs((new UserFixture)->asSuperUser());

        $response = $this->post('backend/system/eventlogs', [], [
            'X-WINTER-REQUEST-HANDLER' => 'onEmptyLog',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $this->canaryCount(), 'AJAX dispatch must still reach the handler');
    }

    /** Dashed URLs keep resolving, now to snake_case rather than camelCase. */
    public function testDashedUrlsResolveToSnakeCase()
    {
        $this->assertEquals('my_action', $this->parseAction('my-action'));
        $this->assertEquals('index', $this->parseAction('index'));
        $this->assertEquals('index_onemptylog', $this->parseAction('index-onemptylog'));

        $this->assertTrue((new DispatchProbeSnakeController())->actionExists('coming_soon'));
        $this->assertTrue((new DispatchProbeFlatController())->actionExists('comingsoon'));
        $this->assertFalse((new DispatchProbeCamelController())->actionExists('comingSoon'));
        $this->assertFalse((new DispatchProbeCamelController())->actionExists('comingsoon'));
    }

    /**
     * Handlers that read post() were always reachable but inert on a GET. They are now
     * unreachable as well; either way they must not mutate.
     */
    public function testPostGatedHandlersRemainInert()
    {
        $this->useTestTheme();
        $path = themes_path('test');
        $existedBefore = File::exists($path);

        $this->actingAs((new UserFixture)->asSuperUser());
        $this->get('backend/cms/themes/index_onDelete?theme=test');

        $this->assertEquals($existedBefore, File::exists($path));
    }

    //
    // CMS frontend: structurally immune, pinned so it stays that way
    //

    /**
     * The frontend has no page-action dispatch; both handler entry points are POST-gated.
     * Included here so a change to either gate fails alongside the backend coverage.
     */
    public function testFrontendAjaxHandlerRequiresPost()
    {
        $controller = new \Cms\Classes\Controller();

        $headers = [
            'X-WINTER-REQUEST-HANDLER' => 'onTest',
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        Request::swap($this->makeRequest('POST', $headers));
        $this->assertEquals('onTest', $controller->getAjaxHandler(), 'positive control: XHR POST dispatches');

        Request::swap($this->makeRequest('GET', $headers));
        $this->assertNull($controller->getAjaxHandler(), 'a GET must never yield an AJAX handler');

        Request::swap($this->makeRequest('GET', [], ['_handler' => 'onTest']));
        $this->assertNull(post('_handler'), 'the _handler postback is unreachable over GET');

        Request::swap($this->makeRequest('POST', [], ['_handler' => 'onTest']));
        $this->assertEquals('onTest', post('_handler'), 'positive control: POST does supply _handler');
    }

    protected function makeRequest(string $method, array $headers = [], array $params = [])
    {
        $request = \Illuminate\Http\Request::create('/ajax-test', $method, $params);

        foreach ($headers as $key => $value) {
            $request->headers->set($key, $value);
        }

        return $request;
    }

    //
    // Known residual, pinned deliberately
    //

    /**
     * Documents a deliberate boundary: an all-lowercase behaviour method stays routable,
     * because such a name is indistinguishable from an ordinary page action. Declaring
     * $actions closes it. Tightening this would break legitimate names like onboarding().
     */
    public function testAllLowercaseBehaviourMethodRemainsRoutable()
    {
        $this->assertTrue(
            (new DispatchProbeBehaviourController())->actionExists('onfoo'),
            'documents the boundary'
        );
        $this->assertFalse(
            (new DispatchProbeBehaviourController())->actionExists('onBar'),
            'the conventional spelling is still blocked'
        );
        $this->assertFalse(
            (new DispatchProbeGuardedController())->actionExists('onfoo'),
            '$actions closes it regardless of casing'
        );
    }
}

//
// Fixtures. Each casing needs its own class: PHP method names are case-insensitive, so
// comingSoon() and comingsoon() cannot coexist in one class.
//

class DispatchProbeCamelController extends \Backend\Classes\Controller
{
    public function comingSoon()
    {
    }
}

class DispatchProbeSnakeController extends \Backend\Classes\Controller
{
    public function coming_soon()
    {
    }
}

class DispatchProbeFlatController extends \Backend\Classes\Controller
{
    public function comingsoon()
    {
    }
}

class DispatchProbeBehaviour extends \Backend\Classes\ControllerBehavior
{
    public function onfoo()
    {
    }

    public function onBar()
    {
    }
}

class DispatchProbeGuardedBehaviour extends \Backend\Classes\ControllerBehavior
{
    protected $actions = [];

    public function onfoo()
    {
    }
}

class DispatchProbeBehaviourController extends \Backend\Classes\Controller
{
    public $implement = [DispatchProbeBehaviour::class];
}

class DispatchProbeGuardedController extends \Backend\Classes\Controller
{
    public $implement = [DispatchProbeGuardedBehaviour::class];
}
