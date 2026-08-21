<?php

namespace System\Twig;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model as DbModel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Enumerable;
use System\Twig\SecurityPolicy\SafeCollection;
use System\Twig\SecurityPolicy\SafePaginator;
use Twig\Markup;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Sandbox\SecurityNotAllowedMethodError;
use Twig\Sandbox\SecurityNotAllowedPropertyError;
use Twig\Sandbox\SecurityPolicyInterface;
use Twig\Template;
use Winter\Storm\Halcyon\Builder as HalcyonBuilder;
use Winter\Storm\Halcyon\Datasource\DatasourceInterface;
use Winter\Storm\Halcyon\Model as HalcyonModel;

/**
 * SecurityPolicy globally blocks accessibility of certain methods and properties.
 *
 * The policy is a blocklist, but it models the real PHP forwarding behaviour of the
 * database layer via $blockedForwarders: because `Model::__call` transparently forwards
 * to the Eloquent Builder, which forwards to the Query Builder, a method blocked on the
 * Query Builder is also blocked when reached through a Model, Eloquent Builder or Relation.
 * This is what makes the blocklist complete instead of a game of whack-a-mole.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges, Luke Towers, Ben Thomson
 */
final class SecurityPolicy implements SecurityPolicyInterface
{
    /**
     * @var array<string, string[]> List of forbidden methods, grouped by applicable instance.
     */
    protected $blockedMethods = [
        '*' => [
            // Prevent accessing Twig itself
            'getTwig',

            // Prevent extensions of any objects
            'addDynamicMethod',
            'addDynamicProperty',
            'extendClassWith',
            'implementClassWith',
            'getClassExtension',
            'extendableSet',

            // Prevent directly invoking the magic/extension call machinery
            'extend',
            'extendableCall',
            'extendableCallStatic',
            'extendableExtendCallback',
            'extensionExtendCallback',
            '__call',
            '__callStatic',
            '__invoke',

            // Prevent Laravel Macroable injection
            'macro',
            'mixin',

            // Prevent binding to, or firing, events
            'bindEvent',
            'bindEventOnce',
            'fireEvent',
            'fireSystemEvent',
        ],

        // Prevent some controller methods. The controller is a fixed, known object; these
        // methods run nested page cycles, render arbitrary partials, or read files.
        Controller::class => [
            'runPage',
            'renderPage',
            'getLoader',
            'run',
            'combineAssets',
            'renderPartial',
            'renderContent',
        ],

        // Prevent model data modification. Methods that forward to the query layer
        // (increment, decrement, touch, getConnection, ...) are covered transitively by
        // $blockedForwarders; only methods that physically live on the Model are listed here.
        DbModel::class => [
            'fill',
            'forceFill',
            'setAttribute',
            'setRawAttributes',
            'save',
            'saveQuietly',
            'saveOrFail',
            'push',
            'pushQuietly',
            'update',
            'updateQuietly',
            'updateOrFail',
            'delete',
            'deleteQuietly',
            'deleteOrFail',
            'forceDelete',
            'destroy',
            'forceDestroy',
            'restore',
            'restoreQuietly',
            'getQuery',
            // Re-pointing the table/connection would allow reading arbitrary tables/databases
            'setTable',
            'setConnection',
            'on',
            'onWriteConnection',
            'setKeyName',
            'setKeyType',
            'setIncrementing',
            'setPerPage',
            'setDateFormat',
            'offsetSet',
            'offsetUnset',
            // Disabling mass-assignment / event protection, or executing callbacks
            'unguard',
            'reguard',
            'unguarded',
            'withoutEvents',
            'withoutTouching',
            'withoutTouchingOn',
            // getConnectionResolver returns the DatabaseManager, whose __call proxies raw SQL
            'getConnectionResolver',
            'setConnectionResolver',
            'unsetConnectionResolver',
            'flushEventListeners',
            'getEventDispatcher',
            'setEventDispatcher',
            'unsetEventDispatcher',
        ],

        EloquentBuilder::class => [
            'forceDelete',
            'create',
            'createQuietly',
            'forceCreate',
            'forceCreateQuietly',
            'firstOrCreate',
            'createOrFirst',
            'updateOrCreate',
            'incrementOrCreate',
            'fillAndInsert',
            'fillAndInsertOrIgnore',
            'fillAndInsertGetId',
            'touch',
            'update',
            'delete',
            'upsert',
        ],

        QueryBuilder::class => [
            'insert',
            'insertOrIgnore',
            'insertGetId',
            'insertUsing',
            'insertOrIgnoreUsing',
            'update',
            'updateFrom',
            'updateOrInsert',
            'upsert',
            'delete',
            'truncate',
            'increment',
            'incrementEach',
            'incrementQuietly',
            'decrement',
            'decrementEach',
            'decrementQuietly',
            // Re-pointing the table
            'from',
            'fromRaw',
            'fromSub',
            // Connection / raw SQL
            'getConnection',
            'toRawSql',
            'selectRaw',
            'whereRaw',
            'orWhereRaw',
            'havingRaw',
            'orHavingRaw',
            'orderByRaw',
            'groupByRaw',
            'joinSub',
            'leftJoinSub',
            'rightJoinSub',
            'crossJoinSub',
            'raw',
            'rawValue',
            'dd',
            'dump',
            'ddRawSql',
            // callable-typed executors (string callables would execute)
            'when',
            'unless',
            'each',
            'eachById',
            'chunk',
            'chunkById',
            'chunkByIdDesc',
            'chunkMap',
            'tap',
            'pipe',
        ],

        Relation::class => [
            'attach',
            'detach',
            'sync',
            'syncWithPivotValues',
            'syncWithoutDetaching',
            'toggle',
            'updateExistingPivot',
            'save',
            'saveQuietly',
            'saveMany',
            'saveManyQuietly',
            'create',
            'createQuietly',
            'createMany',
            'createManyQuietly',
            'forceCreate',
            'forceCreateQuietly',
            'push',
            'update',
            'updateOrCreate',
            'firstOrCreate',
            'firstOrNew',
            'createOrFirst',
            'delete',
            'forceDelete',
            'associate',
            'dissociate',
            'make',
            'makeMany',
        ],

        HalcyonModel::class => [
            'fill',
            'setAttribute',
            'setRawAttributes',
            'setSettingsAttribute',
            'setFileNameAttribute',
            'save',
            'push',
            'update',
            'delete',
            'forceDelete',
            'getQuery',
            'getDatasource',
        ],

        HalcyonBuilder::class => [
            'insert',
            'update',
            'delete',
            'forceDelete',
            'truncate',
        ],

        DatasourceInterface::class => [
            'insert',
            'update',
            'delete',
            'forceDelete',
            'write',
            'usingSource',
            'pushToSource',
            'removeFromSource',
            'select',
            'selectOne',
        ],

        Theme::class => [
            'setDirName',
            'registerHalcyonDatasource',
            'getDatasource',
            'writeConfig',
            'removeCustomData',
        ],
    ];

    /**
     * @var array<string, string> Maps a class to the class its __call forwards to, so the
     * sandbox enforces the destination's blocklist for a method reached through the source.
     * The chain is walked transitively (Model -> Eloquent Builder -> Query Builder).
     */
    protected $blockedForwarders = [
        EloquentBuilder::class => QueryBuilder::class,
        DbModel::class => EloquentBuilder::class,
        Relation::class => EloquentBuilder::class,
    ];

    /**
     * @var array<string, string[]> List of allowed methods, grouped by applicable instance.
     * An empty list denies every method on that type (deny-all lock).
     */
    protected $allowedMethods = [
        SessionManager::class => [
            'put',
            'get',
            'has',
            'forget',
            'flush',
            'pull',
        ],
        // Locked down entirely: no template legitimately calls raw database or event objects.
        ConnectionInterface::class => [],
        ConnectionResolverInterface::class => [],
        Dispatcher::class => [],
    ];

    /**
     * @var array<string, string[]> List of forbidden properties, grouped by applicable instance.
     */
    protected $blockedProperties = [
        Theme::class => [
            'datasource',
        ],
    ];

    /**
     * @var string[] Twig functions that are not allowed (info-disclosure surface).
     */
    protected $blockedFunctions = [
        'source',
        'constant',
        'enum_cases',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $properties = [
            'blockedMethods',
            'allowedMethods',
            'blockedProperties',
        ];

        foreach ($properties as $property) {
            foreach ($this->{$property} as $type => $values) {
                $this->{$property}[$type] = array_map('strtolower', $values);
            }
        }

        $this->blockedFunctions = array_map('strtolower', $this->blockedFunctions);
    }

    /**
     * Check the provided arguments against this security policy
     *
     * @param array $tags Array of tags to be checked against the policy ['tag', 'tag2', 'etc']
     * @param array $filters Array of filters to be checked against the policy ['filter', 'filter2', 'etc']
     * @param array $functions Array of funtions to be checked against the policy ['function', 'function2', 'etc']
     * @throws SecurityNotAllowedFunctionError if a given function is not allowed
     */
    public function checkSecurity($tags, $filters, $functions): void
    {
        foreach ($functions as $function) {
            if (in_array(strtolower($function), $this->blockedFunctions)) {
                throw new SecurityNotAllowedFunctionError(sprintf('Function "%s" is not allowed.', $function), $function);
            }
        }
    }

    /**
     * Checks if a given property is permitted to be accessed on a given object
     *
     * @param object $obj
     * @param string $property
     * @throws SecurityNotAllowedPropertyError
     */
    public function checkPropertyAllowed($obj, $property): void
    {
        // No need to check Twig internal objects
        if ($obj instanceof Template || $obj instanceof Markup) {
            return;
        }

        $property = strtolower($property);

        foreach ($this->blockedProperties as $type => $properties) {
            if ($obj instanceof $type && in_array($property, $properties)) {
                $class = get_class($obj);
                throw new SecurityNotAllowedPropertyError(sprintf('Getting "%s" property in a "%s" object is blocked.', $property, $class), $class, $property);
            }
        }
    }

    /**
     * Checks if a given method is allowed to be called on a given object
     *
     * @param object $obj
     * @param string $method
     * @throws SecurityNotAllowedMethodError
     */
    public function checkMethodAllowed($obj, $method): void
    {
        // No need to check Twig internal objects
        if ($obj instanceof Template || $obj instanceof Markup) {
            return;
        }

        $method = strtolower($method);

        if (in_array($method, $this->blockedMethods['*'])) {
            $this->throwMethodError($obj, $method);
        }

        foreach ($this->allowedMethods as $type => $methods) {
            if ($obj instanceof $type && !in_array($method, $methods)) {
                $this->throwMethodError($obj, $method);
            }
        }

        foreach ($this->blockedMethods as $type => $methods) {
            if ($type === '*') {
                continue;
            }
            if ($obj instanceof $type && in_array($method, $methods)) {
                $this->throwMethodError($obj, $method);
            }
        }

        // Enforce the blocklists of any class this object's __call forwards to, transitively.
        // This closes the forwarding escape (e.g. `model.increment()` reaching the Query Builder).
        foreach ($this->blockedForwarders as $sourceClass => $targetClass) {
            if (!($obj instanceof $sourceClass)) {
                continue;
            }

            $cursor = $targetClass;
            $seen = [];
            while ($cursor !== null && !isset($seen[$cursor])) {
                $seen[$cursor] = true;
                if (in_array($method, $this->blockedMethods[$cursor] ?? [])) {
                    $this->throwMethodError($obj, $method);
                }
                $cursor = $this->blockedForwarders[$cursor] ?? null;
            }
        }
    }

    /**
     * Casts an object to a sandbox-safe proxy before a method is called on it in a template.
     * Used by the custom GetAttrNode to neutralise callable-passthrough on collections and
     * paginators (their higher-order methods would otherwise execute arbitrary callables).
     *
     * @param mixed $object
     * @return mixed
     */
    public function castMethodObjectToSafeObject($object)
    {
        if ($object instanceof Enumerable) {
            return new SafeCollection($object);
        }

        if ($object instanceof AbstractPaginator || $object instanceof AbstractCursorPaginator) {
            return new SafePaginator($object);
        }

        return $object;
    }

    /**
     * @param object $obj
     * @param string $method
     * @throws SecurityNotAllowedMethodError
     */
    protected function throwMethodError($obj, $method): void
    {
        $class = get_class($obj);
        throw new SecurityNotAllowedMethodError(sprintf('Calling "%s" method on a "%s" object is blocked.', $method, $class), $class, $method);
    }
}
