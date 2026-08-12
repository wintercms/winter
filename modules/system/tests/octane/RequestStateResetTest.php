<?php

namespace System\Tests\Octane;

use Illuminate\Http\Request;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\WorkerErrorOccurred;
use RuntimeException;
use System\Tests\Bootstrap\PersistentWorkerTestCase;
use System\Twig\Loader as TwigLoader;
use Winter\Storm\Auth\Manager as AuthManager;
use Winter\Storm\Exception\ErrorHandler;

/**
 * State produced by one operation must not be visible to the next.
 *
 * These run through Octane's real ApplicationGateway against a single application instance, so a
 * leak here is the same leak a worker would exhibit in production.
 */
class RequestStateResetTest extends PersistentWorkerTestCase
{
    public function testTheWorkerServesSeveralRequestsFromOneApplication()
    {
        $this->addWorkerRoute('_worker/ok', fn () => 'ok');

        $first = $this->dispatchWorkerRequest('/_worker/ok');
        $second = $this->dispatchWorkerRequest('/_worker/ok');

        $this->assertEquals(200, $first->getStatusCode());
        $this->assertEquals(200, $second->getStatusCode());
        $this->assertEquals('ok', $first->getContent());
        $this->assertEquals('ok', $second->getContent());
    }

    /**
     * Each operation gets its own sandbox, and the base application is restored afterwards.
     */
    public function testEachOperationRunsInItsOwnSandbox()
    {
        $seen = [];

        $this->addWorkerRoute('_worker/sandbox', function () use (&$seen) {
            $seen[] = spl_object_id(\Illuminate\Container\Container::getInstance());

            return 'ok';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/sandbox', 'GET'),
            Request::create('/_worker/sandbox', 'GET')
        );

        $this->assertCount(2, $seen);
        $this->assertNotEquals($seen[0], $seen[1], 'each operation must run in its own sandbox');
        $this->assertNotContains(
            spl_object_id($this->workerApplication),
            $seen,
            'operations must not run against the base application'
        );
    }

    /**
     * The execution context is derived from the request path and cached as a singleton, so without
     * a reset the first request a worker serves decides it for every later request.
     */
    public function testExecutionContextIsRederivedForEachRequest()
    {
        $contexts = [];

        $backendUri = trim((string) $this->app['config']->get('cms.backendUri', 'backend'), '/');

        $this->addWorkerRoute('_worker/context', function () use (&$contexts) {
            $contexts[] = app('execution.context');

            return 'ok';
        });
        $this->addWorkerRoute($backendUri . '/_worker/context', function () use (&$contexts) {
            $contexts[] = app('execution.context');

            return 'ok';
        });

        $this->dispatchWorkerRequests(
            Request::create('/' . $backendUri . '/_worker/context', 'GET'),
            Request::create('/_worker/context', 'GET'),
            Request::create('/' . $backendUri . '/_worker/context', 'GET')
        );

        $this->assertSame(
            ['back-end', 'front-end', 'back-end'],
            $contexts,
            'the context must follow each request rather than the first one served'
        );
    }

    /**
     * The auth manager is resolved into the base container at boot, so the sandbox never discards
     * it and a resolved user would otherwise be visible to the next request.
     */
    public function testAuthenticationStateDoesNotCrossRequests()
    {
        $seen = [];

        $this->addWorkerRoute('_worker/auth-set', function () {
            $manager = AuthManager::instance();
            $reflection = new \ReflectionObject($manager);
            $property = $reflection->getProperty('user');
            $property->setAccessible(true);
            $property->setValue($manager, new \stdClass());

            return 'set';
        });

        $this->addWorkerRoute('_worker/auth-read', function () use (&$seen) {
            $manager = AuthManager::instance();
            $reflection = new \ReflectionObject($manager);
            $property = $reflection->getProperty('user');
            $property->setAccessible(true);
            $seen[] = $property->getValue($manager);

            return 'read';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/auth-set', 'GET'),
            Request::create('/_worker/auth-read', 'GET')
        );

        $this->assertCount(1, $seen);
        $this->assertNull($seen[0], 'a user resolved in one request must not be visible in the next');
    }

    /**
     * An unbalanced mask left by a throwing request would otherwise be applied to the next
     * request's exception.
     */
    public function testErrorHandlerMasksDoNotCrossRequests()
    {
        $activeMasks = [];

        $this->addWorkerRoute('_worker/mask-apply', function () {
            ErrorHandler::applyMask(new RuntimeException('mask from a previous request'));

            return 'applied';
        });

        $this->addWorkerRoute('_worker/mask-read', function () use (&$activeMasks) {
            $property = (new \ReflectionClass(ErrorHandler::class))->getProperty('activeMask');
            $property->setAccessible(true);
            $activeMasks[] = $property->getValue();

            return 'read';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/mask-apply', 'GET'),
            Request::create('/_worker/mask-read', 'GET')
        );

        $this->assertCount(1, $activeMasks);
        $this->assertNull($activeMasks[0], 'an applied mask must not survive the request boundary');
    }

    public function testTwigIncludeGateIsClosedAtTheStartOfEachRequest()
    {
        $gates = [];

        $this->addWorkerRoute('_worker/gate-open', function () {
            TwigLoader::$allowInclude = true;

            return 'opened';
        });

        $this->addWorkerRoute('_worker/gate-read', function () use (&$gates) {
            $gates[] = TwigLoader::$allowInclude;

            return 'read';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/gate-open', 'GET'),
            Request::create('/_worker/gate-read', 'GET')
        );

        $this->assertSame([false], $gates, 'the include gate must be closed for every new request');
    }

    /**
     * No stock Octane listener unwinds an abandoned transaction, so an open one would hold its
     * snapshot open for the next operation.
     */
    public function testAbandonedTransactionsAreRolledBack()
    {
        $levels = [];

        $this->addWorkerRoute('_worker/tx-open', function () {
            app('db')->connection()->beginTransaction();

            return 'opened';
        });

        $this->addWorkerRoute('_worker/tx-read', function () use (&$levels) {
            $levels[] = app('db')->connection()->transactionLevel();

            return 'read';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/tx-open', 'GET'),
            Request::create('/_worker/tx-read', 'GET')
        );

        $this->assertSame([0], $levels, 'an abandoned transaction must be rolled back to depth zero');
    }

    public function testStagedTransactionCallbacksDoNotCrossRequests()
    {
        $pending = [];

        $this->addWorkerRoute('_worker/tx-callback', function () {
            $connection = app('db')->connection();
            $connection->beginTransaction();
            $connection->afterCommit(fn () => null);

            return 'staged';
        });

        $this->addWorkerRoute('_worker/tx-callback-read', function () use (&$pending) {
            $pending[] = app('db.transactions')->getPendingTransactions()->count();

            return 'read';
        });

        $this->dispatchWorkerRequests(
            Request::create('/_worker/tx-callback', 'GET'),
            Request::create('/_worker/tx-callback-read', 'GET')
        );

        $this->assertSame([0], $pending, 'staged transaction callbacks must not outlive their operation');
    }

    /**
     * The A13 correction: an exception that escapes the HTTP kernel skips
     * ApplicationGateway::terminate(), so RequestTerminated is never dispatched and cleanup
     * attached only to that event would not run.
     */
    public function testAnEscapingExceptionSkipsRequestTerminatedButStillCleansUp()
    {
        $events = [];

        $this->app['events']->listen(RequestTerminated::class, function () use (&$events) {
            $events[] = 'RequestTerminated';
        });
        $this->app['events']->listen(WorkerErrorOccurred::class, function () use (&$events) {
            $events[] = 'WorkerErrorOccurred';
        });

        $gates = [];

        \Laravel\Octane\Facades\Octane::route('GET', '/_worker/escape', function () {
            TwigLoader::$allowInclude = true;

            throw new RuntimeException('escaping failure');
        });

        $this->addWorkerRoute('_worker/after-escape', function () use (&$gates) {
            $gates[] = TwigLoader::$allowInclude;

            return 'read';
        });

        /*
         * Dispatched separately so the event assertions cover only the failing operation. The
         * follow-up request succeeds and legitimately dispatches RequestTerminated.
         */
        $results = $this->dispatchWorkerRequests(Request::create('/_worker/escape', 'GET'));

        $this->assertInstanceOf(RuntimeException::class, $results[0]);
        $this->assertContains('WorkerErrorOccurred', $events);
        $this->assertNotContains(
            'RequestTerminated',
            $events,
            'an escaping exception must skip terminate(), which is why cleanup runs on the way in'
        );

        $this->dispatchWorkerRequests(Request::create('/_worker/after-escape', 'GET'));
        $this->assertSame(
            [false],
            $gates,
            'state left behind by a failed operation must still be cleared for the next one'
        );
    }
}
