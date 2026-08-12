<?php

namespace System\Tests\Bootstrap;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Laravel\Octane\ApplicationGateway;
use Laravel\Octane\CurrentApplication;
use Laravel\Octane\Events\WorkerErrorOccurred;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Dispatches several requests through one application instance.
 *
 * Winter's ordinary TestCase builds a fresh application for every test method, which is the exact
 * opposite of how a persistent application server behaves and means no conventional test can
 * observe state crossing a request boundary. This base class boots once and then drives Octane's
 * real ApplicationGateway per request, mirroring what Laravel\Octane\Worker::handle() does:
 *
 *   - clone the application to form the request sandbox
 *   - point the container instance and facade root at that clone
 *   - dispatch through the gateway, then terminate it
 *   - dispatch WorkerErrorOccurred if the operation threw, as the worker does
 *   - flush the sandbox and restore the base application
 *
 * The gateway is used rather than a hand-written approximation so the assertions exercise Octane's
 * own event order, including the case where an exception escapes the HTTP kernel and
 * RequestTerminated is therefore never dispatched.
 *
 * @package winter\wn-system-module
 */
abstract class PersistentWorkerTestCase extends TestCase
{
    /**
     * The base application every request sandbox is cloned from.
     */
    protected ?Application $workerApplication = null;

    /**
     * Responses and exceptions from the most recent dispatchWorkerRequests() call, in order.
     *
     * @var array<int, \Symfony\Component\HttpFoundation\Response|\Throwable>
     */
    protected array $workerResults = [];

    /**
     * Octane is an optional dependency, so these tests only run where it is installed.
     *
     * Winter does not require laravel/octane, not even for development, because a persistent worker is
     * something an application opts into. The suite therefore has to stay green without it.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(\Laravel\Octane\Worker::class)) {
            $this->markTestSkipped('laravel/octane is not installed.');
        }
    }

    protected function tearDown(): void
    {
        $this->workerApplication = null;
        $this->workerResults = [];

        parent::tearDown();
    }

    /**
     * A no-op Octane client.
     *
     * Built here rather than declared at the bottom of this file on purpose. `implements` is resolved
     * when the class is loaded, so a file-scope class naming an Octane contract would make this file
     * unloadable wherever the package is absent, and the skip above would never get the chance to run.
     * An anonymous class is only evaluated when this method is called.
     *
     * @return \Laravel\Octane\Contracts\Client
     */
    protected function makeWorkerClient()
    {
        return new class implements \Laravel\Octane\Contracts\Client
        {
            public function marshalRequest(\Laravel\Octane\RequestContext $context): array
            {
                return [$context->data['request'] ?? Request::capture(), $context];
            }

            public function respond(\Laravel\Octane\RequestContext $context, \Laravel\Octane\OctaneResponse $response): void
            {
                //
            }

            public function error(Throwable $e, Application $app, Request $request, \Laravel\Octane\RequestContext $context): void
            {
                //
            }
        };
    }

    /**
     * Boot the worker once, pre-resolving whatever Octane would warm.
     */
    protected function bootWorker(): Application
    {
        if ($this->workerApplication !== null) {
            return $this->workerApplication;
        }

        /*
         * A real worker binds its client into the base container, and several stock Octane
         * listeners resolve it — StopWorkerIfNecessary does so while handling WorkerErrorOccurred.
         * Binding a no-op client keeps those listeners on their production code path.
         */
        $this->app->instance(\Laravel\Octane\Contracts\Client::class, $this->makeWorkerClient());

        foreach ((array) $this->app['config']->get('octane.warm', []) as $service) {
            if (is_string($service) && $this->app->bound($service)) {
                $this->app->make($service);
            }
        }

        return $this->workerApplication = $this->app;
    }

    /**
     * Dispatch requests through the worker, one sandbox each.
     *
     * @param \Illuminate\Http\Request ...$requests
     * @return array<int, \Symfony\Component\HttpFoundation\Response|\Throwable>
     */
    protected function dispatchWorkerRequests(Request ...$requests): array
    {
        $worker = $this->bootWorker();
        $this->workerResults = [];

        foreach ($requests as $request) {
            CurrentApplication::set($sandbox = clone $worker);
            Container::setInstance($sandbox);
            Facade::clearResolvedInstances();
            Facade::setFacadeApplication($sandbox);

            $gateway = new ApplicationGateway($worker, $sandbox);

            try {
                $response = $gateway->handle($request);
                $this->workerResults[] = $response;
                $gateway->terminate($request, $response);
            }
            catch (Throwable $exception) {
                /*
                 * Worker::handle() catches before reaching terminate(), so RequestTerminated is not
                 * dispatched on this path. Only WorkerErrorOccurred is.
                 */
                $sandbox['events']->dispatch(new WorkerErrorOccurred($exception, $sandbox));
                $this->workerResults[] = $exception;
            }
            finally {
                $sandbox->flush();

                CurrentApplication::set($worker);
                Container::setInstance($worker);
                Facade::clearResolvedInstances();
                Facade::setFacadeApplication($worker);

                unset($gateway, $sandbox);
            }
        }

        return $this->workerResults;
    }

    /**
     * Dispatch a single request and return its response.
     */
    protected function dispatchWorkerRequest(string $uri, string $method = 'GET', array $parameters = []): Response
    {
        [$result] = $this->dispatchWorkerRequests(Request::create($uri, $method, $parameters));

        if ($result instanceof Throwable) {
            throw $result;
        }

        return $result;
    }

    /**
     * Register a route on the base application, visible to every sandbox cloned from it.
     *
     * Routes are added ahead of the modules' own routes so a test route is not shadowed by the
     * CMS catch-all, which otherwise resolves unknown paths to a 404 page.
     */
    protected function addWorkerRoute(string $uri, callable $handler, string $method = 'GET'): void
    {
        $router = $this->bootWorker()->make('router');
        $routes = $router->getRoutes();

        $existing = [];
        foreach ($routes->getRoutes() as $route) {
            $existing[] = $route;
        }

        $newRoute = $router->newRoute([$method], $uri, $handler);

        /*
         * RouteCollection has no prepend, so rebuild it with the test route first.
         */
        $rebuilt = new \Illuminate\Routing\RouteCollection();
        $rebuilt->add($newRoute);

        foreach ($existing as $route) {
            $rebuilt->add($route);
        }

        $router->setRoutes($rebuilt);
    }
}
