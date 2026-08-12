<?php namespace System\Classes\Octane;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseTransactionsManager;
use System\Classes\PluginManager;
use System\Twig\Loader as TwigLoader;
use Throwable;
use Winter\Storm\Contracts\ResetsWorkerState;
use Winter\Storm\Exception\ErrorHandler;
use Winter\Storm\Halcyon\Model as HalcyonModel;

/**
 * Discards the state a single operation produced, so it cannot reach the next one.
 *
 * Laravel Octane clones the application container per operation and throws the clone away
 * afterwards, which covers container bindings made during the operation. It does not cover static
 * properties, nor mutable state inside objects that were resolved into the base container before
 * the first request, and those are where Winter keeps most of its request-scoped data.
 *
 * This listener is attached to the start of every operation rather than the end. Cleaning up
 * afterwards is not sufficient on its own: an exception that escapes the HTTP kernel skips
 * ApplicationGateway::terminate(), so RequestTerminated — and with it every listener registered
 * against Octane's OperationTerminated contract — never fires. Running at the start guarantees a
 * clean slate even after an operation that failed partway through.
 *
 * Everything here must be idempotent, and must not discard registration data such as navigation
 * definitions, permission callbacks or extension registries. Those are built once per worker, and
 * dropping them would leave the worker permanently degraded rather than merely stale.
 *
 * @package winter\wn-system-module
 */
class ResetsRequestState
{
    /**
     * Handle an Octane operation event.
     *
     * @param mixed $event
     * @return void
     */
    public function handle($event): void
    {
        $sandbox = $event->sandbox ?? ($event->app ?? null);
        $base = $event->app ?? $sandbox;

        if (!$sandbox instanceof Application) {
            return;
        }

        $this->forgetExecutionContext($base, $sandbox);
        $this->resetCoreState();
        $this->resetManagers($base, $sandbox);
        $this->resetStaticCaches();
        $this->rollBackTransactions($base, $sandbox);
        $this->flushRouteController($sandbox);
        $this->resetPlugins();
    }

    /**
     * Forget the resolved execution context from both containers.
     *
     * The context is derived from the request path and cached as a singleton, so the first request
     * a worker serves would otherwise decide whether every later request is treated as a back-end
     * one. It has to be forgotten from the base container as well as the sandbox: a worker boots
     * from the CLI, so the value resolved during boot comes from the synthetic console request and
     * lives on the base container that every sandbox is cloned from.
     *
     * @param \Illuminate\Contracts\Foundation\Application|null $base
     * @param \Illuminate\Contracts\Foundation\Application $sandbox
     * @return void
     */
    protected function forgetExecutionContext($base, Application $sandbox): void
    {
        $sandbox->forgetInstance('execution.context');

        if ($base !== null && $base !== $sandbox) {
            $base->forgetInstance('execution.context');
        }
    }

    /**
     * Reset the framework-level state that Storm exposes a reset for.
     *
     * @return void
     */
    protected function resetCoreState(): void
    {
        /*
         * Applied masks are expected to balance, but an exception between applyMask() and
         * removeMask() leaves one applied and grows the layer stack.
         */
        ErrorHandler::resetMaskState();

        /*
         * The request-level record cache only. External cache stores keep their own invalidation
         * rules and are deliberately left alone.
         */
        HalcyonModel::flushRequestCache();

        /*
         * Defensive: System\Twig\Engine restores this in a finally block, but the gate gives
         * arbitrary local file inclusion when left open, so it is worth asserting per operation.
         */
        TwigLoader::$allowInclude = false;
    }

    /**
     * Reset the core managers that cache per-user data.
     *
     * These are all resolved into the base container while providers register, so the sandbox never
     * discards them. Each clears only what an operation produced; registration callbacks, aliases
     * and definitions are preserved, since those are built once per worker.
     *
     * Only managers that have actually been resolved are touched. Singleton::instance() binds and
     * make()s on demand, so calling it unconditionally would construct every manager at every request
     * boundary — including ones this install never uses. A manager that was never resolved holds no
     * state to discard, so skipping it is also the correct result and not merely the cheaper one.
     *
     * @param \Illuminate\Contracts\Foundation\Application|null $base
     * @param \Illuminate\Contracts\Foundation\Application $sandbox
     * @return void
     */
    protected function resetManagers($base, Application $sandbox): void
    {
        foreach ([
            \Backend\Classes\NavigationManager::class,
            \Backend\Classes\WidgetManager::class,
            \System\Classes\SettingsManager::class,
            \System\Classes\MailManager::class,
            \Cms\Classes\ComponentManager::class,
        ] as $manager) {
            if (class_exists($manager) && $this->isResolved($manager, $base, $sandbox)) {
                $manager::instance()->resetWorkerState();
            }
        }

        /*
         * Backend dispatch writes the resolved action and route parameters onto the controller
         * class itself, so a later request would otherwise attempt the previous request's action.
         */
        if (class_exists(\Backend\Classes\BackendController::class, false)) {
            \Backend\Classes\BackendController::$action = null;
            \Backend\Classes\BackendController::$params = [];
        }

        /*
         * Storm's auth manager caches the resolved user, impersonator and throttle models. The
         * backend subclass is the instance Winter actually serves requests with.
         */
        if (class_exists(\Backend\Classes\AuthManager::class)
            && $this->isResolved(\Backend\Classes\AuthManager::class, $base, $sandbox)) {
            \Backend\Classes\AuthManager::instance()->resetWorkerState();
        }
    }

    /**
     * Whether a singleton has already been resolved in either container.
     *
     * The base container is the one that matters, since that is where module providers resolve these
     * managers and therefore where a leaked instance would live. The sandbox is checked too because it
     * is a shallow clone: a manager first resolved inside the current operation appears there.
     *
     * @param class-string $manager
     * @param \Illuminate\Contracts\Foundation\Application|null $base
     * @param \Illuminate\Contracts\Foundation\Application $sandbox
     * @return bool
     */
    protected function isResolved(string $manager, $base, Application $sandbox): bool
    {
        return ($base !== null && $base->resolved($manager)) || $sandbox->resolved($manager);
    }

    /**
     * Per-request caches held in static properties, as class => [property => value after reset].
     *
     * These are memoised for the duration of one request by design, but a static property outlives
     * the operation that filled it. Reaching them by reflection keeps the change to a single place
     * rather than adding a reset method to a dozen unrelated classes; the accompanying manifest test
     * asserts every entry still exists, so a rename fails a test instead of silently disabling a
     * reset.
     *
     * @var array<class-string, array<string, mixed>>
     */
    protected const STATIC_CACHES = [
        // Front-end request context. The CMS controller assigns itself here on construction, so a
        // stale instance exposes the previous request's page, layout, theme and variables to
        // anything reaching Controller::getController() off its own call path.
        \Cms\Classes\Controller::class => ['instance' => null],

        // Theme settings models and their values, keyed by theme.
        \Cms\Models\ThemeData::class => ['instances' => []],

        // Parsed file contents and source markers, which survive edits to those files.
        \Cms\Classes\CodeParser::class => ['cache' => []],

        // Whether the base Snowboard asset has been emitted. Left set, later responses omit it.
        \Cms\Twig\SnowboardNode::class => ['baseLoaded' => false],

        // Database-backed settings models. A save in one request is otherwise invisible to the next.
        \System\Behaviors\SettingsModel::class => ['instances' => []],

        // Event and configuration derived image sources. Reset to an empty array rather than null to
        // match the declared default, so the next read does not have to tolerate a type it never
        // sees under PHP-FPM.
        \System\Classes\ImageResizer::class => ['availableSources' => []],

        // Request-shared view globals, which can be returned to a different user.
        \System\Helpers\View::class => ['globalVarCache' => []],

        // Database-backed mail layouts, parameters and plugin versions.
        \System\Models\MailLayout::class => ['codeCache' => []],
        \System\Models\Parameter::class => ['cache' => []],
        \System\Models\PluginVersion::class => ['versionCache' => null],
    ];

    /**
     * Per-request caches declared by a trait, as trait => [property => value after reset].
     *
     * These cannot be reset the way STATIC_CACHES entries are. A trait's static property is a
     * template: every class the trait is flattened into receives its own independent copy, so
     * assigning to the property through the trait is silently a no-op. The using classes have to be
     * found and reset individually, which resolveTraitUsers() does.
     *
     * @var array<class-string, array<string, mixed>>
     */
    protected const TRAIT_STATIC_CACHES = [
        // A class-global current page name written during URL generation. Left set, the next
        // operation generates URLs against the previous operation's page.
        \Cms\Traits\UrlMaker::class => ['urlPageName' => null],

        // Backend widget preferences. Backend\Traits\PreferenceMaker scopes the in-memory key to the
        // user, so this is about bounding growth rather than preventing a leak.
        \Backend\Traits\PreferenceMaker::class => ['preferenceCache' => []],
    ];

    /**
     * Classes that use a trait in TRAIT_STATIC_CACHES, as trait => list of classes.
     *
     * Resolved lazily and kept for the life of the worker, since a class cannot stop using a trait.
     *
     * @var array<class-string, list<class-string>>
     */
    protected static array $traitUsers = [];

    /**
     * How many declared classes resolveTraitUsers() has already examined.
     *
     * @var int
     */
    protected static int $examinedClassCount = 0;

    /**
     * Clear the per-request caches held in static properties.
     *
     * @return void
     */
    protected function resetStaticCaches(): void
    {
        foreach (static::STATIC_CACHES as $class => $properties) {
            if (!class_exists($class)) {
                continue;
            }

            $this->resetStaticProperties($class, $properties);
        }

        $this->resetTraitStaticCaches();

        /*
         * The active and edit theme are memoised in statics and also cached externally. Only the
         * in-memory copy is cleared: the external entries have their own invalidation, and forgetting
         * them every operation would defeat the cache entirely.
         */
        if (class_exists(\Cms\Classes\Theme::class)) {
            \Cms\Classes\Theme::resetCache(true);
        }
    }

    /**
     * Clear the per-request caches declared by traits, on every class that uses one.
     *
     * @return void
     */
    protected function resetTraitStaticCaches(): void
    {
        $this->resolveTraitUsers();

        foreach (static::TRAIT_STATIC_CACHES as $trait => $properties) {
            foreach (static::$traitUsers[$trait] ?? [] as $class) {
                $this->resetStaticProperties($class, $properties);
            }
        }
    }

    /**
     * Find the classes using each trait in TRAIT_STATIC_CACHES, examining only classes not seen yet.
     *
     * Plugins are free to use these traits, so the set cannot be hard-coded, and autoloading means it
     * is not complete until the class in question has been touched. get_declared_classes() only ever
     * appends, so remembering how far the last pass got makes each subsequent operation examine just
     * the classes autoloaded since. The first pass, during the worker's first operation, is the only
     * one that walks the whole list.
     *
     * @return void
     */
    protected function resolveTraitUsers(): void
    {
        $declared = get_declared_classes();

        if (count($declared) === static::$examinedClassCount) {
            return;
        }

        $fresh = array_slice($declared, static::$examinedClassCount);

        foreach ($fresh as $class) {
            /*
             * Classifying one class must not abort the pass. Defensive, and known to be so: everything
             * reached here — class_parents(), class_uses(), ReflectionClass — is being asked about a
             * class that is already declared, so no throw is expected and no test demonstrates one. It
             * is guarded because of how it would fail if it ever did: the exception would escape the
             * request boundary and take the operation down, and every later class in the batch would go
             * unclassified. A class that cannot be reflected cannot hold a resettable static either, so
             * skipping it is the correct outcome rather than a swallowed error.
             */
            try {
                $uses = class_uses_recursive($class);

                foreach (static::TRAIT_STATIC_CACHES as $trait => $properties) {
                    if (!in_array($trait, $uses, true)) {
                        continue;
                    }

                    /*
                     * A subclass of a using class does not get its own copy of the static, it shares
                     * the storage declared on its ancestor, so only the class the trait was flattened
                     * into is worth recording. Resetting the subclass too would write the same slot
                     * twice.
                     */
                    if ($this->declaresAnyOf($class, array_keys($properties))) {
                        static::$traitUsers[$trait][] = $class;
                    }
                }
            }
            catch (Throwable $ex) {
                continue;
            }
        }

        /*
         * The snapshot taken above, not a fresh count. A class declared while this ran is therefore
         * picked up by the next pass, since it is beyond the snapshot rather than behind the cursor.
         */
        static::$examinedClassCount = count($declared);
    }

    /**
     * Whether the class itself declares any of the given properties, rather than inheriting them.
     *
     * @param class-string $class
     * @param list<string> $properties
     * @return bool
     */
    protected function declaresAnyOf(string $class, array $properties): bool
    {
        $reflection = new \ReflectionClass($class);

        foreach ($properties as $property) {
            if (!$reflection->hasProperty($property)) {
                continue;
            }

            if ($reflection->getProperty($property)->getDeclaringClass()->getName() === $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign the given values to static properties of a class, skipping any that do not apply.
     *
     * @param class-string $class
     * @param array<string, mixed> $properties
     * @return void
     */
    protected function resetStaticProperties(string $class, array $properties): void
    {
        $reflection = new \ReflectionClass($class);

        foreach ($properties as $property => $value) {
            if (!$reflection->hasProperty($property)) {
                continue;
            }

            $reflected = $reflection->getProperty($property);

            if (!$reflected->isStatic()) {
                continue;
            }

            $reflected->setValue(null, $value);
        }
    }

    /**
     * Roll every connection back to depth zero and discard staged transaction callbacks.
     *
     * No stock Octane listener unwinds an abandoned transaction. Leaving one open holds the
     * connection's snapshot open for the next operation, and because `db.transactions` is one of
     * the services Octane pre-warms into the base container, its staged callback records outlive
     * the operation that registered them and would run against a later one.
     *
     * @param \Illuminate\Contracts\Foundation\Application|null $base
     * @param \Illuminate\Contracts\Foundation\Application $sandbox
     * @return void
     */
    protected function rollBackTransactions($base, Application $sandbox): void
    {
        if (!$sandbox->resolved('db')) {
            return;
        }

        $connections = $sandbox->make('db')->getConnections();

        foreach ($connections as $connection) {
            while ($connection->transactionLevel() > 0) {
                try {
                    $connection->rollBack(0);
                }
                catch (Throwable $ex) {
                    /*
                     * The connection cannot be returned to a known state, so it must not be handed
                     * to the next operation. Disconnecting forces a reconnect on next use.
                     */
                    $connection->disconnect();
                    break;
                }
            }
        }

        /*
         * Replace the warmed manager rather than reaching into its records, then re-attach it to
         * every live connection so later transactions register against the new instance.
         */
        $manager = new DatabaseTransactionsManager();

        foreach ([$base, $sandbox] as $container) {
            if ($container !== null && $container->bound('db.transactions')) {
                $container->instance('db.transactions', $manager);
            }
        }

        foreach ($connections as $connection) {
            $connection->setTransactionManager($manager);
        }
    }

    /**
     * Clear the controller cached on the current route.
     *
     * ApplicationGateway::terminate() does this after a successful operation, but it is never
     * reached when an exception escapes the kernel, which leaves the previous operation's
     * controller instance attached to the route.
     *
     * @param \Illuminate\Contracts\Foundation\Application $sandbox
     * @return void
     */
    protected function flushRouteController(Application $sandbox): void
    {
        if (!$sandbox->resolved('router')) {
            return;
        }

        $route = $sandbox->make('router')->getCurrentRoute();

        if ($route !== null && method_exists($route, 'flushController')) {
            $route->flushController();
        }
    }

    /**
     * Invoke the reset contract on every loaded plugin that provides it.
     *
     * Plugin register() and boot() run once per worker, so a plugin that caches request-derived data
     * has no other opportunity to clear it.
     *
     * A plugin qualifies by implementing ResetsWorkerState or simply by declaring resetWorkerState().
     * The second form exists because `implements` is resolved when the class is loaded, so a plugin
     * naming the contract cannot be loaded at all by a Winter whose Storm predates it -- the plugin
     * would not merely lose its reset, it would fatal on every request under PHP-FPM as well. A plugin
     * that must run on both cannot afford to name the interface, and refusing to reset it would leave
     * exactly the state this class exists to clear. Accepting the method keeps the contract meaningful
     * where it is available without making it a condition of loading.
     *
     * A plugin that throws aborts the whole reset and fails the operation. That is deliberate, and it
     * is the opposite of what the rest of this class does for classes it cannot reflect. The
     * difference is what is known: a class that cannot be reflected holds no resettable state, whereas
     * a plugin that declared itself resettable and then failed has left state behind whose owner is
     * unknown. Continuing would serve the operation from state that may belong to another user, which
     * is the failure this class exists to prevent, so the operation is failed instead.
     *
     * The consequence is intended too. Because the reset runs at the start of every operation, a
     * plugin that always throws makes the worker fail every request rather than quietly degrade, which
     * is what forces it to be fixed.
     *
     * @return void
     * @throws \RuntimeException
     */
    protected function resetPlugins(): void
    {
        $pluginManager = PluginManager::instance();

        foreach ($pluginManager->getPlugins() as $identifier => $plugin) {
            if (!$plugin instanceof ResetsWorkerState && !method_exists($plugin, 'resetWorkerState')) {
                continue;
            }

            try {
                $plugin->resetWorkerState();
            }
            catch (Throwable $ex) {
                throw new \RuntimeException(sprintf(
                    'Plugin %s failed to reset its worker state: %s',
                    $identifier,
                    $ex->getMessage()
                ), 0, $ex);
            }
        }
    }
}
