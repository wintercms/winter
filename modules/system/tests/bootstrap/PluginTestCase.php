<?php

namespace System\Tests\Bootstrap;

use Mail;
use Config;
use Artisan;
use Exception;
use ReflectionClass;
use Backend\Classes\AuthManager;
use Backend\Tests\Concerns\InteractsWithAuthentication;
use Mockery\MockInterface;
use System\Classes\PluginBase;
use System\Classes\PluginManager;
use System\Classes\UpdateManager;
use Winter\Storm\Database\Model as BaseModel;

/**
 * Plugin test case.
 *
 * The base test case that should be used for plugin tests. It instantiates the given plugin and
 * its dependencies, and ensures that the plugin is available for use within the tests.
 *
 * @package winter/wn-system-module
 */
abstract class PluginTestCase extends TestCase
{
    use InteractsWithAuthentication;

    /**
     * @var array Cache for storing which plugins have been loaded.
     */
    protected $pluginTestCaseLoadedPlugins = [];

    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app['cache']->setDefaultDriver('array');
        $app->setLocale('en');

        $app->singleton('backend.auth', function ($app) {
            $app['auth.loaded'] = true;

            return AuthManager::instance();
        });

        // Store database in memory by default unless otherwise specified
        if (!file_exists(base_path('config/testing/database.php'))) {
            $app['config']->set('database.connections.testing', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
            ]);
            $app['config']->set('database.default', 'testing');
        }

        // Set random encryption key
        $app['config']->set('app.key', bin2hex(random_bytes(16)));

        // Modify the plugin path away from the test context
        $app->setPluginsPath(realpath(base_path() . Config::get('cms.pluginsPath')));

        return $app;
    }

    /**
     * Perform test case set up.
     */
    public function setUp(): void
    {
        // Reload the plugin and update manager singletons
        PluginManager::forgetInstance();
        UpdateManager::forgetInstance();

        parent::setUp();

        // Reset loaded routes
        $this->app['router']->setRoutes(new \Illuminate\Routing\RouteCollection);

        // Run all migrations
        Artisan::call('winter:up');

        // Reset loaded plugins for this test
        $this->pluginTestCaseLoadedPlugins = [];

        // Detect the plugin being tested and load it automatically
        $pluginCode = $this->guessPluginCode();
        if (!is_null($pluginCode)) {
            $this->instantiatePlugin($pluginCode, false);
        }

        // Reload module routes
        foreach (Config::get('cms.loadModules', []) as $module) {
            include base_path("modules/" . strtolower($module) . "/routes.php");
        }

        // Disable mailing
        Mail::pretend();
    }

    /**
     * Flush event listeners and tear down.
     */
    public function tearDown(): void
    {
        $this->flushModelEventListeners();
        parent::tearDown();
        unset($this->app);
    }

    /**
     * Refreshes a plugin for testing.
     *
     * Since the test environment has loaded all the test plugins natively, this method will ensure
     * the desired plugin is loaded in the system before proceeding to migrate it.
     *
     * @deprecated v1.2.1 Use `instantiatePlugin()` instead.
     * @return void
     */
    protected function runPluginRefreshCommand($code, $throwException = true)
    {
        $this->instantiatePlugin((string) $code, (bool) $throwException);
    }

    /**
     * Instantiates a plugin for testing.
     *
     * @param string $code Plugin code.
     * @param boolean $throw Throw an exception if the plugin cannot be found.
     */
    protected function instantiatePlugin(string $code, bool $throw = true): void
    {
        // Check plugin code is valid
        if (!preg_match('/^[\w+]*\.[\w+]*$/', $code)) {
            if (!$throw) {
                return;
            }
            throw new Exception(sprintf('Invalid plugin code: "%s"', $code));
        }

        $manager = PluginManager::instance();
        $plugin = $manager->findByIdentifier($code);
        $firstLoad = !$plugin;

        // First time seeing this plugin, load it up
        if ($firstLoad) {
            $namespace = '\\'.str_replace('.', '\\', $code);
            $path = array_get(
                array_change_key_case($manager->getPluginNamespaces(), CASE_LOWER),
                strtolower($namespace)
            );

            if (!$path) {
                if (!$throw) {
                    return;
                }
                throw new Exception(sprintf('Unable to find plugin with code: "%s"', $code));
            }

            $plugin = $manager->loadPlugin($namespace, $path);
            $manager->registerPlugin($plugin);
        }

        $this->pluginTestCaseLoadedPlugins[$code] = $plugin;

        // Load any dependencies
        if (!empty($plugin->require)) {
            foreach ((array) $plugin->require as $dependency) {
                if (isset($this->pluginTestCaseLoadedPlugins[$dependency])) {
                    continue;
                }

                $this->instantiatePlugin($dependency);
            }
        }

        // Refresh the plugin's tables
        Artisan::call('plugin:refresh', ['plugin' => $code, '--force' => true]);

        // Boot the plugin if this is the first load
        if ($firstLoad) {
            $manager->bootPlugin($plugin);
        }
    }

    /**
     * Returns a plugin object from its code, useful for registering events, etc.
     */
    protected function getPluginObject($code = null): ?PluginBase
    {
        return $this->pluginTestCaseLoadedPlugins[$code]
            ?? $this->pluginTestCaseLoadedPlugins[$this->guessPluginCode()]
            ?? null;
    }

    /**
     * Flush model event listeners.
     *
     * The models in Winter use a static property to store their events. These will need to be
     * targeted and reset, ready for a new test cycle.
     *
     * Pivot models are an exception since they are internally managed.
     */
    protected function flushModelEventListeners(): void
    {
        foreach (get_declared_classes() as $class) {
            if ($class === 'Winter\Storm\Database\Pivot' || strtolower($class) === 'october\rain\database\pivot') {
                continue;
            }

            $reflectClass = new ReflectionClass($class);
            if (
                !$reflectClass->isInstantiable() ||
                !$reflectClass->isSubclassOf('Winter\Storm\Database\Model') ||
                $reflectClass->isSubclassOf('Winter\Storm\Database\Pivot') ||
                in_array(MockInterface::class, $reflectClass->getInterfaceNames())
            ) {
                continue;
            }

            $class::flushEventListeners();
        }

        BaseModel::flushEventListeners();
    }

    /**
     * Guesses the plugin code being tested.
     */
    protected function guessPluginCode(): ?string
    {
        $reflect = new ReflectionClass($this);
        $fqClass = $reflect->getName();
        $namespace = $reflect->getNamespaceName();

        if (empty($namespace)) {
            // Try to determine from the path instead
            $path = $reflect->getFilename();
            $basePath = $this->app->pluginsPath();

            if (!strpos($path, $basePath) === 0) {
                return null;
            }

            $pluginCode = ltrim(str_replace('\\', '/', substr($path, strlen($basePath))), '/');
            $pluginCode = implode('.', array_slice(explode('/', $pluginCode), 0, 2));
        } else {
            // Determine code from namespace
            $manager = PluginManager::instance();
            $pluginCode = $manager->getIdentifier($fqClass);
        }

        return $pluginCode;
    }
}
