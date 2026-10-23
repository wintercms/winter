<?php namespace System\Twig\SecurityPolicy;

use Illuminate\Support\Traits\ForwardsCalls;

/**
 * SafeProxy is the shared base of the sandbox proxies that SecurityPolicy casts a method
 * receiver to (see SecurityPolicy::castMethodObjectToSafeObject).
 *
 * Twig's security policy is never given method *arguments*, so a method that executes a
 * callable, or instantiates a class named by a string, cannot be judged by name alone. The
 * receiver is therefore cast to a proxy before the call and the arguments are neutralised
 * here, on the way through.
 *
 * The lists live on this base class deliberately. Laravel's paginators forward every method
 * they do not implement to their own collection (AbstractPaginator::__call and
 * AbstractCursorPaginator::__call are both `forwardCallTo($this->getCollection(), ...)`), so
 * the entire collection surface is reachable through a paginator too. Keeping a second,
 * hand-maintained copy of the list on the paginator proxy is what let mapInto()/pipeInto()
 * stay reachable there after they were blocked on collections, so there is one copy and every
 * proxy applies it.
 *
 * @package winter\wn-system-module
 */
abstract class SafeProxy
{
    use ForwardsCalls;

    /**
     * @var string[] Methods where a string argument is an attribute/key name (not a callback).
     * For these, string values are preserved; non-string callables are still stripped.
     * Safe because Laravel's useAsCallable() never treats a string as a callback.
     */
    protected $hybridCallableArgs = [
        'contains',
        'containsstrict',
        'doesntcontain',
        'groupby',
        'keyby',
        'implode',
        'search',
        'sortby',
        'sortbydesc',
        'unique',
        'duplicates',
        'partition',
        // CmsObjectCollection / Storm Collection: property and key names, never callbacks.
        'lists',
        'where',
        'wherecomponent',
    ];

    /**
     * @var array<string, array<int|string>> Arguments the callee only ever reads as a name,
     * never invokes, as the positions and parameter names of those arguments. Their value is
     * left alone — including an array of names, which stripCallables() would otherwise null out
     * because a two-element array of strings is callable whenever the first names a class that
     * can dispatch the second.
     *
     * CmsObjectCollection::withComponent() is the method that needs this: it takes a component
     * name, or a list of them, and only its *second* argument is a callback, so the carve-out
     * cannot be method-wide. Component names collide with callables readily — `[session]` is
     * the component Winter.User ships, and `session()` is a Laravel helper.
     */
    protected $literalArgs = [
        'withcomponent' => [0, 'components'],
    ];

    /**
     * @var string[] Methods taking a multi-sort specification, i.e. a list of [key, direction]
     * pairs. Collection::sortBy() and sortByDesc() hand an array argument that is not itself
     * callable to sortByMany(), which reads each pair as data.
     */
    protected $multiSortArgs = [
        'sortby',
        'sortbydesc',
    ];

    /**
     * @var string[] Methods that instantiate arbitrary classes or dispatch statically from a
     * string argument (not caught by is_callable stripping), so they are blocked outright.
     */
    protected $blockedMethods = [
        'mapinto',
        'pipeinto',
        'toresourcecollection',
    ];

    /**
     * Returns the wrapped object that calls are forwarded to.
     *
     * @return object
     */
    abstract protected function getProxiedObject();

    /**
     * Forward all other calls to the wrapped object, stripping callable arguments first.
     */
    public function __call($method, $parameters)
    {
        $normalized = strtolower($method);

        if (in_array($normalized, $this->blockedMethods)) {
            return $this;
        }

        foreach ($parameters as $position => $param) {
            if ($this->isLiteralArgument($normalized, $position)) {
                continue;
            }

            $parameters[$position] = $this->stripCallables($param, $normalized);
        }

        return $this->forwardCallTo($this->getProxiedObject(), $method, $parameters);
    }

    /**
     * Recursively null out any callable value at any depth. Hybrid methods keep string
     * values (used as attribute names) but still drop non-string callables.
     *
     * The callable test runs before the array recursion because an array is itself a
     * callable when it is a [class|object, method] pair — `['Illuminate\Support\Facades\File',
     * 'sharedGet']` is exactly as executable as the string form, and Laravel's
     * useAsCallable() only ever exempts strings, so the hybrid carve-out never applies to it.
     */
    protected function stripCallables($value, string $method, int $depth = 0)
    {
        if (is_callable($value) && !$this->isNameArgument($value, $method, $depth)) {
            return null;
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->stripCallables($item, $method, $depth + 1);
            }
        }

        return $value;
    }

    /**
     * Whether a value the callee could invoke is one it only ever reads as a name.
     *
     * Two shapes qualify. A string, for the methods listed in $hybridCallableArgs: Laravel's
     * useAsCallable() is `!is_string($value) && is_callable($value)`, so a string is never
     * invoked there. And a two-element list of strings directly inside a multi-sort
     * specification: Collection::sortByMany() reads that pair's first element with data_get()
     * and only invokes it when it is not a string, so the pair itself is data. A callable
     * nested deeper than that pair - `sortBy([[[Class, method], 'asc']])` - is invoked, which
     * is why this is bounded by depth rather than allowed anywhere in the argument.
     *
     * @param mixed $value
     */
    protected function isNameArgument($value, string $method, int $depth): bool
    {
        if (is_string($value)) {
            return in_array($method, $this->hybridCallableArgs);
        }

        return $depth === 1
            && in_array($method, $this->multiSortArgs)
            && is_array($value)
            && count($value) === 2
            && array_key_exists(0, $value)
            && array_key_exists(1, $value)
            && is_string($value[0])
            && is_string($value[1]);
    }

    /**
     * Whether the given argument of the given method is one the callee only reads as a name.
     *
     * @param string $method
     * @param int|string $position The argument's position, or its name where the template
     * passed it as a named argument
     */
    protected function isLiteralArgument(string $method, $position): bool
    {
        return in_array($position, $this->literalArgs[$method] ?? [], true);
    }
}
