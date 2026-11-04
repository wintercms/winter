<?php namespace System\Twig\SecurityPolicy;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Enumerable;

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
 * The forwarding guard itself lives on SafeProxy, shared with SafePaginator.
 *
 * @package winter\wn-system-module
 */
class SafeCollection extends SafeProxy implements ArrayAccess, Countable, IteratorAggregate, Arrayable, Jsonable
{
    /**
     * @var Enumerable The wrapped collection (Collection or LazyCollection).
     */
    protected $collection;

    /**
     * Constructor
     */
    public function __construct(Enumerable $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @inheritDoc
     */
    protected function getProxiedObject()
    {
        return $this->collection;
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
