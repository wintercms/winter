<?php namespace System\Classes;

use Illuminate\Support\Facades\Facade;
use System\Twig\Extension as SystemTwigExtension;
use System\Twig\GetAttrAdjuster;
use System\Twig\Loader as SystemTwigLoader;
use System\Twig\SecurityPolicy as TwigSecurityPolicy;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\SandboxExtension;
use Twig\Loader\LoaderInterface;
use Twig\Sandbox\SecurityNotAllowedMethodError;
use Twig\TokenParser\AbstractTokenParser as TwigTokenParser;
use Twig\TwigFilter as TwigSimpleFilter;
use Twig\TwigFunction as TwigSimpleFunction;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Support\Str;

/**
 * This class manages Twig functions, token parsers and filters.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MarkupManager
{
    use \Winter\Storm\Support\Traits\Singleton;

    const EXTENSION_FILTER = 'filters';
    const EXTENSION_FUNCTION = 'functions';
    const EXTENSION_TOKEN_PARSER = 'tokens';

    /**
     * @var array Cache of registration callbacks.
     */
    protected $callbacks = [];

    /**
     * @var array Globally registered extension items
     */
    protected $items;

    /**
     * @var \System\Classes\PluginManager
     */
    protected $pluginManager;

    /**
     * @var string[] Parameter names that mark a callback the callee may invoke or store, for
     * the callbacks Laravel and Storm leave untyped. Lower case; compared against the
     * lower-cased parameter name.
     */
    protected const CALLBACK_PARAMETER_NAMES = [
        'callback',
        'callbacks',
        'callable',
        'closure',
        'factory',
        'fn',
        'groupby',
        'handler',
        'keyby',
        'macro',
        'mixin',
        'resolver',
        'sortby',
    ];

    /**
     * @var array<string, array> Cache of the parameter shape of the methods wildcard markup
     * extensions have resolved to, keyed by "class::method". See listWildcardParameters().
     */
    protected $wildcardParameters = [];

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->pluginManager = PluginManager::instance();
    }

    /**
     * Make an instance of the base TwigEnvironment to extend further
     */
    public static function makeBaseTwigEnvironment(?LoaderInterface $loader = null, array $options = []): TwigEnvironment
    {
        if (!$loader) {
            $loader = new SystemTwigLoader();
        }

        $options = array_merge([
            'auto_reload' => true,
        ], $options);

        $twig = new TwigEnvironment($loader, $options);
        $twig->addExtension(new SystemTwigExtension);
        $twig->addExtension(new SandboxExtension(new TwigSecurityPolicy, true));
        $twig->addNodeVisitor(new GetAttrAdjuster);
        return $twig;
    }

    /**
     * Loads all of the registered Twig extensions
     */
    protected function loadExtensions(): void
    {
        // Load Module extensions
        foreach ($this->callbacks as $callback) {
            $callback($this);
        }

        // Load Plugin extensions
        $plugins = $this->pluginManager->getPlugins();

        foreach ($plugins as $id => $plugin) {
            $items = $plugin->registerMarkupTags();
            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $type => $definitions) {
                if (!is_array($definitions)) {
                    continue;
                }

                $this->registerExtensions($type, $definitions);
            }
        }
    }

    /**
     * Registers a callback function that defines simple Twig extensions.
     * The callback function should register menu items by calling the manager's
     * `registerFunctions`, `registerFilters`, `registerTokenParsers` function.
     * The manager instance is passed to the callback function as an argument. Usage:
     *
     *     MarkupManager::registerCallback(function ($manager) {
     *         $manager->registerFilters([...]);
     *         $manager->registerFunctions([...]);
     *         $manager->registerTokenParsers([...]);
     *     });
     *
     */
    public function registerCallback(callable $callback): void
    {
        $this->callbacks[] = $callback;
    }

    /**
     * Registers the Twig extension items.
     * $type must be one of self::EXTENSION_TOKEN_PARSER, self::EXTENSION_FILTER, or self::EXTENSION_FUNCTION
     * $definitions is of the format of [$extensionName => $associativeExtensionOptions]
     */
    public function registerExtensions(string $type, array $definitions): void
    {
        if ($this->items === null) {
            $this->items = [];
        }

        if (!array_key_exists($type, $this->items)) {
            $this->items[$type] = [];
        }

        foreach ($definitions as $name => $definition) {
            switch ($type) {
                case self::EXTENSION_TOKEN_PARSER:
                    $this->items[$type][] = $definition;
                    break;
                case self::EXTENSION_FILTER:
                case self::EXTENSION_FUNCTION:
                    $this->items[$type][$name] = $definition;
                    break;
            }
        }
    }

    /**
     * Registers a Twig Filter
     */
    public function registerFilters(array $definitions): void
    {
        $this->registerExtensions(self::EXTENSION_FILTER, $definitions);
    }

    /**
     * Registers a Twig Function
     */
    public function registerFunctions(array $definitions): void
    {
        $this->registerExtensions(self::EXTENSION_FUNCTION, $definitions);
    }

    /**
     * Registers a Twig Token Parser
     */
    public function registerTokenParsers(array $definitions): void
    {
        $this->registerExtensions(self::EXTENSION_TOKEN_PARSER, $definitions);
    }

    /**
     * Returns a list of the registered Twig extensions of a type.
     * @param $type string The Twig extension type
     * @return array
     */
    public function listExtensions($type)
    {
        $results = [];

        if ($this->items === null) {
            $this->loadExtensions();
        }

        if (isset($this->items[$type]) && is_array($this->items[$type])) {
            $results = $this->items[$type];
        }

        return $results;
    }

    /**
     * Returns a list of the registered Twig filters.
     * @return array
     */
    public function listFilters()
    {
        return $this->listExtensions(self::EXTENSION_FILTER);
    }

    /**
     * Returns a list of the registered Twig functions.
     * @return array
     */
    public function listFunctions()
    {
        return $this->listExtensions(self::EXTENSION_FUNCTION);
    }

    /**
     * Returns a list of the registered Twig token parsers.
     * @return array
     */
    public function listTokenParsers()
    {
        return $this->listExtensions(self::EXTENSION_TOKEN_PARSER);
    }

    /**
     * Makes a set of Twig functions for use in a twig extension.
     * @param  array $functions Current collection
     * @return array
     */
    public function makeTwigFunctions($functions = [])
    {
        $defaultOptions = ['is_safe' => ['html']];
        if (!is_array($functions)) {
            $functions = [];
        }

        foreach ($this->listFunctions() as $name => $callable) {
            $options = [];
            if (is_array($callable) && isset($callable['options'])) {
                $options = $callable['options'];
                $callable = $callable['callable'] ?? $callable[0];

                if (isset($options['is_safe']) && !is_array($options['is_safe'])) {
                    if (is_string($options['is_safe'])) {
                        $options['is_safe'] = [$options['is_safe']];
                    } else {
                        $options['is_safe'] = [];
                    }
                }
            }
            $options = array_merge($defaultOptions, $options);

            /*
             * Handle a wildcard function
             */
            if (strpos($name, '*') !== false && $this->isWildCallable($callable)) {
                $callable = $this->makeWildcardCallable($callable);
            }

            if (!is_callable($callable)) {
                throw new SystemException(sprintf('The markup function (%s) for %s is not callable.', json_encode($callable), $name));
            }

            $functions[] = new TwigSimpleFunction($name, $callable, $options);
        }

        return $functions;
    }

    /**
     * Makes a set of Twig filters for use in a twig extension.
     * @param  array $filters Current collection
     * @return array
     */
    public function makeTwigFilters($filters = [])
    {
        $defaultOptions = ['is_safe' => ['html']];
        if (!is_array($filters)) {
            $filters = [];
        }

        foreach ($this->listFilters() as $name => $callable) {
            $options = [];
            if (is_array($callable) && isset($callable['options'])) {
                $options = $callable['options'];
                $callable = $callable['callable'] ?? $callable[0];

                if (isset($options['is_safe']) && !is_array($options['is_safe'])) {
                    if (is_string($options['is_safe'])) {
                        $options['is_safe'] = [$options['is_safe']];
                    } else {
                        $options['is_safe'] = [];
                    }
                }
            }
            $options = array_merge($defaultOptions, $options);

            /*
             * Handle a wildcard function
             */
            if (strpos($name, '*') !== false && $this->isWildCallable($callable)) {
                $callable = $this->makeWildcardCallable($callable);
            }

            if (!is_callable($callable)) {
                throw new SystemException(sprintf('The markup filter (%s) for %s is not callable.', json_encode($callable), $name));
            }

            $filters[] = new TwigSimpleFilter($name, $callable, $options);
        }

        return $filters;
    }

    /**
     * Makes a set of Twig token parsers for use in a twig extension.
     * @param  array $parsers Current collection
     * @return array
     */
    public function makeTwigTokenParsers($parsers = [])
    {
        if (!is_array($parsers)) {
            $parsers = [];
        }

        $extraParsers = $this->listTokenParsers();
        foreach ($extraParsers as $obj) {
            if (!$obj instanceof TwigTokenParser) {
                continue;
            }

            $parsers[] = $obj;
        }

        return $parsers;
    }

    /**
     * Builds the dispatcher for a wildcard markup extension (`str_*`, `array_*`, ...).
     *
     * A wildcard exposes every public method of the target class to templates, and Twig's
     * sandbox is never given the arguments of a function call. Both halves of that are
     * guarded here: the resolved method may not be one that turns a template-supplied
     * string into a PHP callable.
     */
    protected function makeWildcardCallable($callable): \Closure
    {
        return function ($name) use ($callable) {
            $arguments = array_slice(func_get_args(), 1);
            $method = $this->isWildCallable($callable, Str::camel($name));

            $this->checkWildcardMethod($method, $arguments);

            return call_user_func_array($method, $arguments);
        };
    }

    /**
     * Refuses a wildcard markup extension call that would hand a template-supplied value
     * to PHP as a callable.
     *
     * Three ways that happens: the Macroable mutators bind a callable to a name the same
     * wildcard can then invoke; an argument that is itself a callable value is executed or
     * stored by whatever it is passed to, whether or not the parameter is typed; and a method
     * declaring a callable parameter runs even a plain string passed to it. None has a
     * legitimate use from a template, since Twig cannot produce a PHP callable of its own.
     *
     * @param callable $method The wildcard resolved to a concrete callable
     * @param array $arguments The arguments the template supplied
     * @throws SecurityNotAllowedMethodError
     */
    protected function checkWildcardMethod($method, array $arguments): void
    {
        $name = is_array($method) ? end($method) : $method;
        $class = is_array($method) ? reset($method) : null;
        $origin = is_string($class) ? $class : $name;

        if (in_array(strtolower($name), ['macro', 'mixin', 'flushmacros'], true)) {
            throw new SecurityNotAllowedMethodError(
                sprintf('Calling "%s" through a wildcard markup extension is blocked.', $name),
                $origin,
                $name
            );
        }

        $shape = $this->listWildcardParameters($method);

        // Not conditioned on the callee's *type*: Laravel leaves its hybrid callback parameters
        // untyped (Arr::sort(), Arr::sortDesc(), Arr::keyBy()), and its setters
        // (Str::createUuidsUsing(), Url::setSessionResolver()) store the callable to run later,
        // so a type-driven check misses both.
        foreach ($arguments as $position => $argument) {
            if ($this->hasCallableValue($argument, !$this->isCallbackPosition($shape, $position))) {
                throw new SecurityNotAllowedMethodError(
                    sprintf('Passing a callback to "%s" through a wildcard markup extension is blocked.', $name),
                    $origin,
                    $name
                );
            }
        }

        // Strings are ordinary data to most helpers - str_replace('trim', ...) and
        // array_get($x, 'count') both pass one that is_callable() - so a string is refused
        // only where the callee declares the parameter as a callable and so would run it.
        // Laravel's untyped hybrids never run a string: useAsCallable() is
        // `!is_string($value) && is_callable($value)`.
        foreach ($shape['callable'] as $position) {
            if (($arguments[$position] ?? null) !== null) {
                throw new SecurityNotAllowedMethodError(
                    sprintf('Passing a callback to "%s" through a wildcard markup extension is blocked.', $name),
                    $origin,
                    $name
                );
            }
        }
    }

    /**
     * Whether the given argument position of a resolved wildcard callable is one the callee
     * could invoke: a parameter it declares as a callable, one it names as a callback, or a
     * position its signature does not describe at all.
     *
     * @param array $shape As returned by listWildcardParameters()
     */
    protected function isCallbackPosition(array $shape, int $position): bool
    {
        if ($shape['declared'] === null) {
            return true;
        }

        if ($position >= $shape['declared']) {
            // A variadic signature describes every further argument with its last parameter, so
            // those positions are classified like it rather than as unknown. Arr::crossJoin()
            // is the one variadic target, and it invokes nothing.
            return $shape['variadic'] === null
                || in_array($shape['variadic'], $shape['callback'], true);
        }

        return in_array($position, $shape['callback'], true);
    }

    /**
     * Checks whether a value is, or contains at any depth, a PHP callable that is not a
     * plain string.
     *
     * An array is tested as a callable itself before being walked, so a [class, method]
     * pair is caught whole rather than element by element (each element is a harmless
     * string on its own).
     *
     * One array shape is ambiguous: a two-element list of strings is how a template writes an
     * ordinary short list, and is_callable() is true for any such list whose first element
     * names a class that can dispatch the second - which every aliased facade does, through
     * __callStatic, so `["File", "name"]` and `["Log", "Debug"]` are both callable. Reading
     * that as data is what `$pairsAreData` is for: it is set everywhere except the positions
     * a callee could invoke, so the shape keeps working as the list it almost always is
     * (`html_ul(post.tags)`, `array_only(map, keys)`) without being accepted anywhere it could
     * run. Every other callable shape - a Closure, an invokable object, a pair holding an
     * object - is refused in any position and at any depth, because a template has no
     * legitimate use for one.
     *
     * @param mixed $value
     * @param bool $pairsAreData Whether to read a two-element list of strings as data
     */
    protected function hasCallableValue($value, bool $pairsAreData = false): bool
    {
        if (is_string($value)) {
            return false;
        }

        if (is_callable($value) && !($pairsAreData && $this->isStringPair($value))) {
            return true;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->hasCallableValue($item, $pairsAreData)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Whether the given value is a two-element list of strings, the one callable shape a
     * template also writes as ordinary data.
     *
     * @param mixed $value
     */
    protected function isStringPair($value): bool
    {
        return is_array($value)
            && count($value) === 2
            && array_key_exists(0, $value)
            && array_key_exists(1, $value)
            && is_string($value[0])
            && is_string($value[1]);
    }

    /**
     * Describes the parameters of a resolved wildcard callable:
     *
     * - `callable`: the positions declared as `callable`/`Closure`, i.e. the ones that would
     *   execute even a plain string;
     * - `callback`: those, plus the positions the callee *names* as a callback. Laravel leaves
     *   its hybrid callbacks untyped, so the name is the only thing left to go on -
     *   `Arr::sort($array, $callback)`, `Arr::sortDesc($array, $callback)` and
     *   `Arr::keyBy($array, $keyBy)` are the three in the wildcard targets, and they are also
     *   the three that run an array callable;
     * - `declared`: how many parameters the signature describes, or null if it could not be
     *   read. An argument beyond that is treated as a callback position, since nothing says
     *   what the callee does with it.
     *
     * @param callable $method
     * @return array{callable: int[], callback: int[], declared: int|null, variadic: int|null}
     */
    protected function listWildcardParameters($method): array
    {
        $class = is_array($method) ? reset($method) : null;

        // Facades resolve their methods through __callStatic, so reflect the underlying instance
        if (is_string($class) && is_subclass_of($class, Facade::class)) {
            $method = [$class::getFacadeRoot(), end($method)];
        }

        $key = is_array($method)
            ? (is_object($method[0]) ? get_class($method[0]) : $method[0]) . '::' . $method[1]
            : $method;

        if (array_key_exists($key, $this->wildcardParameters)) {
            return $this->wildcardParameters[$key];
        }

        try {
            $reflection = is_array($method)
                ? new \ReflectionMethod($method[0], $method[1])
                : new \ReflectionFunction($method);
        } catch (\ReflectionException $ex) {
            return $this->wildcardParameters[$key] = [
                'callable' => [],
                'callback' => [],
                'declared' => null,
                'variadic' => null,
            ];
        }

        $shape = [
            'callable' => [],
            'callback' => [],
            'declared' => $reflection->getNumberOfParameters(),
            'variadic' => null,
        ];

        foreach ($reflection->getParameters() as $position => $parameter) {
            $type = $parameter->getType();
            $types = $type instanceof \ReflectionNamedType ? [$type] : ($type?->getTypes() ?? []);

            foreach ($types as $namedType) {
                if (in_array($namedType->getName(), ['callable', 'Closure'], true)) {
                    $shape['callable'][] = $position;
                    break;
                }
            }

            if (
                in_array($position, $shape['callable'], true)
                || in_array(strtolower($parameter->getName()), static::CALLBACK_PARAMETER_NAMES, true)
            ) {
                $shape['callback'][] = $position;
            }

            if ($parameter->isVariadic()) {
                $shape['variadic'] = $position;
            }
        }

        return $this->wildcardParameters[$key] = $shape;
    }

    /**
     * Tests if a callable type contains a wildcard, also acts as a
     * utility to replace the wildcard with a string.
     * @param  callable  $callable
     * @param  string|bool $replaceWith
     * @return mixed
     */
    protected function isWildCallable($callable, $replaceWith = false)
    {
        $isWild = false;

        if (is_string($callable) && strpos($callable, '*') !== false) {
            $isWild = $replaceWith ? str_replace('*', $replaceWith, $callable) : true;
        }

        if (is_array($callable)) {
            if (is_string($callable[0]) && strpos($callable[0], '*') !== false) {
                if ($replaceWith) {
                    $isWild = $callable;
                    $isWild[0] = str_replace('*', $replaceWith, $callable[0]);
                }
                else {
                    $isWild = true;
                }
            }

            if (!empty($callable[1]) && strpos($callable[1], '*') !== false) {
                if ($replaceWith) {
                    $isWild = $isWild ?: $callable;
                    $isWild[1] = str_replace('*', $replaceWith, $callable[1]);
                }
                else {
                    $isWild = true;
                }
            }
        }

        return $isWild;
    }
}
