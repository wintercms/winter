<?php

namespace Backend\Tests;

use Backend\Classes\NavigationManager;
use Backend\ServiceProvider;
use ReflectionProperty;
use System\Tests\Bootstrap\TestCase;

class ServiceProviderTest extends TestCase
{
    public function tearDown(): void
    {
        $this->app->forgetInstance('Laravel\Octane\Contracts\Client');
        NavigationManager::forgetInstance();

        parent::tearDown();
    }

    public function testBackendNavigationIsNotRegisteredOutsideTheBackend()
    {
        $this->assertFalse($this->app->runningInBackend());

        $this->assertSame([], $this->registerAndGetNavigationCallbacks());
    }

    public function testBackendNavigationIsRegisteredInAnOctaneWorker()
    {
        $this->app->instance('Laravel\Octane\Contracts\Client', new \stdClass());

        $this->assertNotEmpty($this->registerAndGetNavigationCallbacks());
    }

    protected function registerAndGetNavigationCallbacks(): array
    {
        NavigationManager::forgetInstance();

        (new ServiceProvider($this->app))->register();

        $navigation = NavigationManager::instance();

        return (new ReflectionProperty($navigation, 'callbacks'))->getValue($navigation);
    }
}
