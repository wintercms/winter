<?php

namespace Backend\Tests\Octane;

use Backend\Classes\BackendController;
use Backend\Classes\NavigationManager;
use Backend\Classes\WidgetManager;
use Backend\Facades\BackendAuth as BackendAuthFacade;
use Illuminate\Http\Request;
use System\Classes\SettingsManager;
use System\Tests\Bootstrap\PersistentWorkerTestCase;

/**
 * The backend managers cache lists after filtering them by the authenticated user's permissions,
 * and they are resolved into the base container while providers register, so the request sandbox
 * never discards them.
 *
 * Without a reset the first user a worker serves decides what every later user sees. These tests
 * run through Octane's real gateway, so a failure here is the failure a worker would exhibit.
 */
class CrossUserStateTest extends PersistentWorkerTestCase
{
    /**
     * Resolve the managers against the base application before any request is dispatched.
     *
     * This is the precondition that makes the leak possible, and it is what production does: the
     * module providers resolve these managers while registering, so they live on the container every
     * request sandbox is cloned from and flushing the sandbox cannot discard them. A manager first
     * resolved inside a sandbox would be discarded with it, which would mask the defect.
     */
    protected function bootWorker(): \Illuminate\Foundation\Application
    {
        $alreadyBooted = $this->workerApplication !== null;

        $app = parent::bootWorker();

        if (!$alreadyBooted) {
            NavigationManager::instance($app);
            WidgetManager::instance($app);
            SettingsManager::instance($app);
        }

        return $app;
    }

    protected function readProtected(object $target, string $property)
    {
        $prop = (new \ReflectionObject($target))->getProperty($property);
        $prop->setAccessible(true);

        return $prop->getValue($target);
    }

    /**
     * loadItems() stores the permission-filtered menu, and only rebuilds it when it is null.
     */
    public function testNavigationItemsAreRebuiltForEachRequest()
    {
        $seen = [];

        $this->addWorkerRoute('_worker/nav', function () use (&$seen) {
            $manager = NavigationManager::instance();
            $prop = (new \ReflectionObject($manager))->getProperty('items');
            $prop->setAccessible(true);

            // What the previous request left behind.
            $seen[] = $prop->getValue($manager);

            // Stand in for the filtered result a real request would cache.
            $prop->setValue($manager, ['left-over-from-a-previous-user']);

            return 'ok';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/nav', 'GET'),
            Request::create('/_worker/nav', 'GET')
        );

        $this->assertCount(2, $seen);
        $this->assertNull($seen[1], 'a filtered navigation list must not be reused for the next user');
    }

    public function testNavigationContextDoesNotCrossRequests()
    {
        $seen = [];

        $this->addWorkerRoute('_worker/nav-context', function () use (&$seen) {
            $manager = NavigationManager::instance();
            $seen[] = $manager->getContext();

            $manager->setContext('Acme.Demo', 'previous-owner', 'previous-side-item');

            return 'ok';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/nav-context', 'GET'),
            Request::create('/_worker/nav-context', 'GET')
        );

        $this->assertCount(2, $seen);
        $this->assertNull(
            $seen[1]->mainMenuCode ?? null,
            'the active navigation context must not carry over to the next request'
        );
    }

    /**
     * listReportWidgets() unsets entries the current user cannot access directly from the cached
     * list, so a user without permission removed the widget for everyone the worker served next.
     */
    public function testReportWidgetsAreRebuiltForEachRequest()
    {
        $seen = [];

        $this->addWorkerRoute('_worker/widgets', function () use (&$seen) {
            $manager = WidgetManager::instance();
            $prop = (new \ReflectionObject($manager))->getProperty('reportWidgets');
            $prop->setAccessible(true);

            $seen[] = $prop->getValue($manager);

            // Stand in for a list a permission check has already pruned.
            $prop->setValue($manager, ['Acme\\ReportWidgets\\Survivor' => []]);

            return 'ok';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/widgets', 'GET'),
            Request::create('/_worker/widgets', 'GET')
        );

        $this->assertCount(2, $seen);
        $this->assertNull($seen[1], 'a pruned report widget list must not be reused for the next user');
    }

    /**
     * Registration data is built once per worker; discarding it would leave the worker with no
     * navigation or widgets at all, which is worse than serving stale ones.
     */
    public function testRegistrationDataSurvivesTheReset()
    {
        $navigationCallbacks = null;
        $widgetCallbacks = null;

        $this->addWorkerRoute('_worker/registration', function () use (&$navigationCallbacks, &$widgetCallbacks) {
            $navigationCallbacks = count($this->readProtected(NavigationManager::instance(), 'callbacks'));
            $widgetCallbacks = count($this->readProtected(WidgetManager::instance(), 'reportWidgetCallbacks'));

            return 'ok';
        });

        $before = count($this->readProtected(NavigationManager::instance(), 'callbacks'));

        $this->dispatchWorkerRequests(
            Request::create('/_worker/registration', 'GET'),
            Request::create('/_worker/registration', 'GET')
        );

        $this->assertSame(
            $before,
            $navigationCallbacks,
            'navigation registration callbacks must survive the reset'
        );
        $this->assertNotNull($widgetCallbacks);
    }

    public function testSettingsItemsAreRebuiltForEachRequest()
    {
        $seen = [];

        $this->addWorkerRoute('_worker/settings', function () use (&$seen) {
            $manager = SettingsManager::instance();
            $prop = (new \ReflectionObject($manager))->getProperty('items');
            $prop->setAccessible(true);

            $seen[] = $prop->getValue($manager);
            $prop->setValue($manager, ['left-over']);

            return 'ok';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/settings', 'GET'),
            Request::create('/_worker/settings', 'GET')
        );

        $this->assertCount(2, $seen);
        $this->assertNull($seen[1], 'a filtered settings list must not be reused for the next user');
    }

    /**
     * Backend dispatch writes the resolved action and parameters onto the controller class itself.
     */
    public function testBackendControllerActionStateDoesNotCrossRequests()
    {
        $seen = [];

        $this->addWorkerRoute('_worker/action', function () use (&$seen) {
            $seen[] = [BackendController::$action, BackendController::$params];

            BackendController::$action = 'previousAction';
            BackendController::$params = ['previous', 'params'];

            return 'ok';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/action', 'GET'),
            Request::create('/_worker/action', 'GET')
        );

        $this->assertCount(2, $seen);
        $this->assertNull($seen[1][0], 'the previous action must not survive into the next request');
        $this->assertSame([], $seen[1][1], 'the previous parameters must not survive either');
    }

    /**
     * Backend\Traits\PreferenceMaker memoises preferences in a static keyed by widget and controller.
     *
     * The storage model behind it is scoped to one user, but the in-memory key was not, so under a
     * worker the entry one user filled was returned to the next. The key now includes the user, which
     * fixes it for PHP-FPM as well: the same static is shared by every request a process serves.
     *
     * The trait is exercised through a stand-in rather than a real widget so the test does not need a
     * backend session, a controller or the database. What is being asserted is the cache key, and the
     * stand-in uses the real trait, so the real keying logic runs.
     */
    public function testPreferenceCacheIsScopedToTheUser()
    {
        $this->bootWorker();

        $widget = new PreferenceMakerStandIn();

        $keys = [];
        $storageKeys = [];

        foreach ([7, 9, null] as $userId) {
            BackendAuthFacade::swap(new StubAuthManager($userId));

            $keys[$userId ?? 'guest'] = $widget->cacheKey();
            $storageKeys[$userId ?? 'guest'] = $widget->storageKey();
        }

        BackendAuthFacade::clearResolvedInstances();

        $this->assertNotSame($keys[7], $keys[9], 'two users must not share a preference cache entry');
        $this->assertNotSame(
            $keys[7],
            $keys['guest'],
            'an authenticated user must not share a preference cache entry with an unauthenticated one'
        );

        $this->assertStringContainsString(
            $storageKeys[7],
            $keys[7],
            'the cache key must still contain the storage key, so the two stay in step'
        );

        $this->assertSame(
            array_values(array_unique($storageKeys)),
            [$storageKeys[7]],
            'the storage key must not vary by user, or preferences saved before this change are lost'
        );
        $this->assertStringNotContainsString(
            '7',
            $storageKeys[7],
            'the user must not have leaked into the storage key'
        );
    }
}

/**
 * Exercises Backend\Traits\PreferenceMaker's keying without a controller, session or database.
 *
 * Only the two key builders are exposed; both are the trait's own, so the composition under test is
 * the real one. The authenticated user reaches the trait through the BackendAuth facade, which the
 * test swaps for StubAuthManager.
 */
class PreferenceMakerStandIn
{
    use \Backend\Traits\PreferenceMaker;

    public function cacheKey(): string
    {
        return $this->getPreferenceCacheKey();
    }

    public function getId(): string
    {
        return 'stand-in';
    }

    public function storageKey(): string
    {
        return $this->getPreferenceKey();
    }
}

/**
 * Stands in for the backend auth manager, reporting whichever user the test is acting as.
 */
class StubAuthManager
{
    public function __construct(protected ?int $userId)
    {
    }

    public function getUser()
    {
        return $this->userId === null ? null : new StubUser($this->userId);
    }
}

/**
 * The narrowest thing PreferenceMaker asks of a user: its primary key.
 */
class StubUser
{
    public function __construct(protected int $id)
    {
    }

    public function getKey(): int
    {
        return $this->id;
    }
}
