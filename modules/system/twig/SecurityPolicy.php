<?php

namespace System\Twig;

use Cms\Classes\CmsCompoundObject;
use Cms\Classes\ComponentBase;
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
use Illuminate\Session\Store;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Stringable;
use System\Twig\SecurityPolicy\SafeCollection;
use System\Twig\SecurityPolicy\SafePaginator;
use System\Twig\SecurityPolicy\SafeSession;
use Twig\Markup;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Sandbox\SecurityNotAllowedMethodError;
use Twig\Sandbox\SecurityNotAllowedPropertyError;
use Twig\Sandbox\SecurityPolicyInterface;
use Twig\Template;
use Winter\Storm\Database\Attach\File as AttachFile;
use Winter\Storm\Halcyon\Builder as HalcyonBuilder;
use Winter\Storm\Halcyon\Datasource\DatasourceInterface;
use Winter\Storm\Halcyon\Model as HalcyonModel;

/**
 * SecurityPolicy globally blocks accessibility of certain methods and properties.
 *
 * The policy is a blocklist, but it models the real PHP forwarding behaviour of the objects
 * a template can reach via $blockedForwarders: because `Model::__call` transparently forwards
 * to the Eloquent Builder, which forwards to the Query Builder, a method blocked on the
 * Query Builder is also blocked when reached through a Model, Eloquent Builder or Relation.
 * The same applies to the Halcyon model (forwards to the Halcyon Builder) and to components
 * (forward to the CMS controller). This is what makes the blocklist complete instead of a
 * game of whack-a-mole.
 *
 * Every class that a template can reach and whose `__call` (or a thin proxy method) hands the
 * call to another object MUST be registered in $blockedForwarders, otherwise the destination's
 * blocklist is silently skipped.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges, Luke Towers, Ben Thomson
 */
final class SecurityPolicy implements SecurityPolicyInterface
{
    /**
     * @var string[] The session surface a template may use.
     */
    protected const SESSION_METHODS = [
        'put',
        'get',
        'has',
        'forget',
        'flush',
        'pull',
    ];

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

            // Prevent Laravel Macroable injection. The query builder aliases Macroable::__call
            // to a *public* macroCall(), which dispatches on the macro name it is handed, so
            // the policy only ever sees "macrocall" - block the alias exactly like __call.
            'macro',
            'mixin',
            'macroCall',

            // Prevent executing a callable passed as an argument. The policy is never given
            // method arguments, so the dispatch primitives that Laravel's Tappable and
            // Conditionable traits add to objects throughout the framework (and to anything
            // a plugin hands a template) have to be blocked by name.
            'tap',
            'pipe',
            'when',
            'unless',

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
            // fromQuery() hands its first argument straight to Connection::select(), which
            // prepares and executes it - arbitrary SQL, and because the statement runs before
            // the rows are fetched, arbitrary writes as well
            'fromQuery',
            // Storm's search helper interpolates every column name it is given into a raw
            // expression (Winter\Storm\Database\Builder::searchWhereInternal)
            'searchWhere',
            'orSearchWhere',
            // a "Class:arg1,arg2" cast is instantiated when the attribute is read
            // (HasAttributes::resolveCasterClass), which is the arbitrary-instantiation
            // primitive SafeProxy blocks on mapInto()/pipeInto()
            'withCasts',
            // the aggregate function name is emitted verbatim; withCount()/withSum()/... stay
            // usable because they pass a fixed name
            'withAggregate',
            // repointing the underlying query is the Eloquent-side equivalent of setTable()
            'setQuery',
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
            // Reaching a second table. The *Sub variants below are blocked because their string
            // argument is raw SQL; the plain family is blocked because it reads a table the
            // model does not own, which is the same outcome as the repointing methods above.
            'join',
            'joinWhere',
            'leftJoin',
            'leftJoinWhere',
            'rightJoin',
            'rightJoinWhere',
            'crossJoin',
            'union',
            'unionAll',
            // Connection / raw SQL
            'getConnection',
            'toRawSql',
            'selectRaw',
            // selectSub passes a plain string straight through as raw SQL (Builder::parseSub)
            'selectSub',
            // Storm's selectConcat() wraps every non-identifier part in a quoted literal without
            // escaping it (Query\Grammars\Concerns\SelectConcatenations::compileConcat)
            'selectConcat',
            // the aggregate function name is emitted verbatim (Grammar::compileAggregate); the
            // count/sum/avg/min/max wrappers stay usable because they pass a fixed name
            'aggregate',
            'numericAggregate',
            // a string lock is emitted verbatim after the select (MySqlGrammar::compileLock,
            // PostgresGrammar::compileLock); sharedLock()/lockForUpdate() pass a bool
            'lock',
            // the seed is interpolated into RAND() (MySqlGrammar::compileRandom)
            'inRandomOrder',
            // the index name is interpolated into the index hint (MySqlGrammar::compileIndexHint)
            'useIndex',
            'forceIndex',
            'ignoreIndex',
            // the operator is emitted verbatim and, unlike where(), never validated
            // (Grammar::whereRowValues)
            'whereRowValues',
            'orWhereRowValues',
            // where clauses are compiled by their 'type' key, so a plain array literal can ask
            // for the Raw type and supply the SQL itself (Grammar::compileWheresToArray)
            'mergeWheres',
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
            // callable-typed executors (string callables would execute); the generic
            // tap/pipe/when/unless group is blocked globally above.
            // beforeQuery() accepts any callable and applyBeforeQueryCallbacks() invokes it as
            // soon as the query is compiled
            'beforeQuery',
            'each',
            'eachById',
            'chunk',
            'chunkById',
            'chunkByIdDesc',
            'chunkMap',
        ],

        // Attachments are handed to templates all the time. Every from*() method copies data
        // the template names into the publicly served uploads disk, and getDisk() hands out the
        // filesystem adapter for that disk outright. Reading an attachment's own metadata and
        // contents is untouched.
        AttachFile::class => [
            'fromPost',
            'fromFile',
            'fromStorage',
            'fromData',
            'fromUrl',
            'setDataAttribute',
            'deleteThumbs',
            'getDisk',
        ],

        // A paginator renders a view, so the methods that decide *which* view, and the view
        // factory itself, are the application's to call and not a template's. SafePaginator
        // refuses the same names (SafePaginator::VIEW_CONFIG_METHODS) for a call the sandbox
        // casts to the proxy; these two rows cover the receiver that is not cast, an
        // argument-less attribute access.
        AbstractPaginator::class => SafePaginator::VIEW_CONFIG_METHODS,
        AbstractCursorPaginator::class => SafePaginator::VIEW_CONFIG_METHODS,

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
            // Re-pointing the directory would allow reading files outside of the theme
            'from',
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

        // Str::of() hands templates a Stringable, whose whole when*() family takes a
        // callback as its first argument and invokes it with the (template-supplied)
        // string. Untyped parameters, so only the names identify them.
        Stringable::class => [
            'whenContains',
            'whenContainsAll',
            'whenDoesntEndWith',
            'whenDoesntStartWith',
            'whenEmpty',
            'whenNotEmpty',
            'whenEndsWith',
            'whenExactly',
            'whenNotExactly',
            'whenIs',
            'whenIsAscii',
            'whenIsMatch',
            'whenIsUlid',
            'whenIsUuid',
            'whenStartsWith',
            'whenTest',
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
        // Halcyon\Model::__call falls back to $this->newQuery(), so the CMS page, layout and
        // partial objects reach every method of the Halcyon Builder (insert writes a template
        // file straight to the theme datasource, without going through the model's save).
        HalcyonModel::class => HalcyonBuilder::class,
        // ComponentBase::__call falls back to $this->controller, and renderPartial() proxies
        // there explicitly, so a component reaches the controller's blocked methods.
        ComponentBase::class => Controller::class,
    ];

    /**
     * @var array<string, string[]> List of allowed methods, grouped by applicable instance.
     * An empty list denies every method on that type (deny-all lock).
     */
    protected $allowedMethods = [
        // The raw manager or store is only reached by an argument-less attribute access, which
        // is not cast; every call with arguments goes through SafeSession, which guards the keys
        // too. All three need the entry: the session surface is the same whichever object a
        // template was handed.
        SessionManager::class => self::SESSION_METHODS,
        Store::class => self::SESSION_METHODS,
        SafeSession::class => self::SESSION_METHODS,
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
     * @param string|null $method The method about to be called, needed for receivers whose
     * __call dispatches onto a different object depending on the method name.
     * @return mixed
     */
    public function castMethodObjectToSafeObject($object, $method = null)
    {
        if ($object instanceof Enumerable) {
            return new SafeCollection($object);
        }

        if ($object instanceof AbstractPaginator || $object instanceof AbstractCursorPaginator) {
            return new SafePaginator($object);
        }

        // CmsCompoundObject::__call dispatches its passthru methods (sortBy, withComponent, ...)
        // straight onto the collection of every object of that type, so the proxy has to be
        // applied to that collection: casting the model itself would leave the call unguarded.
        if ($object instanceof CmsCompoundObject && in_array($method, $object->getPassthruMethods(), true)) {
            return new SafeCollection($object->get());
        }

        if ($object instanceof SessionManager || $object instanceof Store) {
            return new SafeSession($object);
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
