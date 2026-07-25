<?php namespace System\Twig\SecurityPolicy;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * SafeCollection is a collection proxy that is safe to use in a Twig sandbox.
 *
 * Collections are handed to templates everywhere, and their higher-order methods
 * (map, each, filter, reduce, ...) execute arbitrary callables — `things.map('system')`
 * would run `system()`. Twig's security policy cannot inspect method *arguments*, so
 * instead the receiver is cast to this proxy (by the custom GetAttrNode) before the call,
 * and every callable argument is nulled out before being forwarded. Callables are unusable
 * in Twig anyway, so nothing legitimate is lost.
 *
 * @package winter\wn-system-module
 */
class SafeCollection implements ArrayAccess, Countable, IteratorAggregate, Arrayable, Jsonable
{
    use ForwardsCalls;

    /**
     * @var Enumerable The wrapped collection (Collection or LazyCollection).
     */
    protected $collection;

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
     * Constructor
     */
    public function __construct(Enumerable $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Forward all other calls to the collection, stripping callable arguments first.
     */
    public function __call($method, $parameters)
    {
        if (in_array(strtolower($method), $this->blockedMethods)) {
            return $this;
        }

        $normalized = strtolower($method);
        foreach ($parameters as &$param) {
            $param = $this->stripCallables($param, $normalized);
        }
        unset($param);

        return $this->forwardCallTo($this->collection, $method, $parameters);
    }

    /**
     * Recursively null out any callable value at any depth. Hybrid methods keep string
     * values (used as attribute names) but still drop non-string callables.
     */
    protected function stripCallables($value, string $method)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->stripCallables($item, $method);
            }
            return $value;
        }

        if (
            is_callable($value) &&
            (!in_array($method, $this->hybridCallableArgs) || !is_string($value))
        ) {
            return null;
        }

        return $value;
    }

    public function getIterator(): Traversable
    {
        return $this->collection->getIterator();
    }

    public function offsetExists($offset): bool
    {
        return $this->collection instanceof ArrayAccess
            ? $this->collection->offsetExists($offset)
            : false;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->collection instanceof ArrayAccess
            ? $this->collection->offsetGet($offset)
            : null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($this->collection instanceof ArrayAccess) {
            $this->collection->offsetSet($offset, $value);
        }
    }

    public function offsetUnset($offset): void
    {
        if ($this->collection instanceof ArrayAccess) {
            $this->collection->offsetUnset($offset);
        }
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function toArray()
    {
        return $this->collection->toArray();
    }

    public function toJson($options = 0)
    {
        return $this->collection->toJson($options);
    }

    public function __toString(): string
    {
        return $this->collection->toJson();
    }
}
