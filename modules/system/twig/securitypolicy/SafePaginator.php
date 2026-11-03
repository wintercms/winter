<?php namespace System\Twig\SecurityPolicy;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * SafePaginator is a paginator proxy that is safe to use in a Twig sandbox.
 *
 * Paginators expose `through(callable)` (on both AbstractPaginator and
 * AbstractCursorPaginator), which executes an arbitrary callable over the items. This proxy
 * forwards every method to the wrapped paginator but strips callable arguments first, exactly
 * like SafeCollection. All the rendering/navigation methods (render, links, currentPage,
 * total, items, url, ...) keep working because they take no callables.
 *
 * @package winter\wn-system-module
 */
class SafePaginator implements ArrayAccess, Countable, IteratorAggregate
{
    use ForwardsCalls;

    /**
     * @var \Illuminate\Pagination\AbstractPaginator|\Illuminate\Pagination\AbstractCursorPaginator
     */
    protected $paginator;

    /**
     * Constructor
     */
    public function __construct($paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Forward all calls to the paginator, stripping callable arguments first.
     */
    public function __call($method, $parameters)
    {
        foreach ($parameters as &$param) {
            $param = $this->stripCallables($param);
        }
        unset($param);

        return $this->forwardCallTo($this->paginator, $method, $parameters);
    }

    /**
     * Recursively null out any callable value at any depth.
     */
    protected function stripCallables($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->stripCallables($item);
            }
            return $value;
        }

        return is_callable($value) ? null : $value;
    }

    public function getIterator(): Traversable
    {
        return $this->paginator->getIterator();
    }

    public function offsetExists($offset): bool
    {
        return $this->paginator->offsetExists($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->paginator->offsetGet($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->paginator->offsetSet($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->paginator->offsetUnset($offset);
    }

    public function count(): int
    {
        return $this->paginator->count();
    }

    public function __toString(): string
    {
        return (string) $this->paginator->render();
    }
}
