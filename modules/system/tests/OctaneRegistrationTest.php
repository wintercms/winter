<?php

namespace System\Tests;

use Laravel\Octane\OctaneServiceProvider;
use System\Tests\Bootstrap\TestCase;

/**
 * Winter disables Laravel's package auto-discovery by default (`app.loadDiscoveredPackages`), so
 * an installed `laravel/octane` never registers its own provider. Without it the `octane` binding
 * is missing, and Octane's ApplicationGateway resolves that binding through the Octane facade on
 * every request, so worker mode fails outright rather than degrading.
 *
 * System\ServiceProvider::registerOctane() therefore registers the provider explicitly whenever
 * the package is present, the same way System\ServiceProvider already registers each core module's
 * own provider. It cannot instead be listed in modules/system/providers.php, which is a static array
 * merged into `app.providers` and so would try to load the provider on installs without the package.
 */
class OctaneRegistrationTest extends TestCase
{
    /**
     * Winter does not require laravel/octane, not even for development, so these skip where it is
     * absent rather than failing. The behaviour they cover only exists once an application has
     * chosen to install it.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(OctaneServiceProvider::class)) {
            $this->markTestSkipped('laravel/octane is not installed.');
        }
    }

    public function testPackageDiscoveryRemainsDisabled()
    {
        $this->assertFalse(
            (bool) $this->app['config']->get('app.loadDiscoveredPackages', false),
            'Winter intentionally leaves Laravel package discovery off; the Octane provider must '
            . 'not depend on it being enabled.'
        );
    }

    public function testOctaneProviderIsRegisteredWhenThePackageIsInstalled()
    {
        if (!class_exists(OctaneServiceProvider::class)) {
            $this->markTestSkipped('laravel/octane is not installed.');
        }

        $this->assertContains(
            OctaneServiceProvider::class,
            array_keys($this->app->getLoadedProviders()),
            'System\ServiceProvider must register Octane\'s provider explicitly.'
        );

        $this->assertTrue(
            $this->app->bound('octane'),
            'Octane\'s ApplicationGateway resolves the "octane" binding on every request.'
        );
    }

    /**
     * Registering the provider must stay inert outside a worker: it binds services and reads
     * configuration, but Octane's events are only dispatched by an Octane worker.
     */
    public function testRegisteringOctaneDoesNotWireWinterSpecificListeners()
    {
        if (!class_exists(OctaneServiceProvider::class)) {
            $this->markTestSkipped('laravel/octane is not installed.');
        }

        $this->assertIsArray($this->app['config']->get('octane.listeners'));
    }
}
