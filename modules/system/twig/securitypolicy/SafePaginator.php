<?php namespace System\Twig\SecurityPolicy;

use ArrayAccess;
use Countable;
use Illuminate\Support\Str;
use IteratorAggregate;
use Traversable;
use Twig\Sandbox\SecurityNotAllowedMethodError;

/**
 * SafePaginator is a paginator proxy that is safe to use in a Twig sandbox.
 *
 * Paginators expose `through(callable)` (on both AbstractPaginator and
 * AbstractCursorPaginator), which executes an arbitrary callable over the items, and they
 * forward every method they do not implement themselves to their underlying collection, which
 * puts the whole collection surface — `mapInto()` included — behind them. Both are handled by
 * the shared guard on SafeProxy, so the navigation methods (currentPage, total, items, url, ...)
 * keep working because they take no callables.
 *
 * `render()` and `links()` need a second guard: they include a view, and views are plain PHP
 * that runs outside the sandbox, so the name a template supplies is restricted to the paginator
 * views themselves. The methods that *configure* which view a paginator renders, and the view
 * factory itself, are refused outright for the same reason — see VIEW_CONFIG_METHODS. Those are
 * also listed against AbstractPaginator in SecurityPolicy::$blockedMethods, because an
 * argument-less attribute access reaches the paginator without being cast to this proxy.
 *
 * @package winter\wn-system-module
 */
class SafePaginator extends SafeProxy implements ArrayAccess, Countable, IteratorAggregate
{
    use ResolvesNamedArguments;

    /**
     * @var string The views Laravel registers for pagination, which a template may render a
     * paginator with by name.
     */
    protected const PAGINATION_NAMESPACE = 'pagination::';

    /**
     * @var string A view under any registered namespace's own `pagination` directory is a
     * pagination view as well, so a plugin or theme that ships its own can be named. Winter's
     * `system::pagination.simple-default` (Paginator::defaultSimpleView, set in
     * System\ServiceProvider) is the core case.
     */
    protected const PAGINATION_PATH_PATTERN = '/^[A-Za-z0-9_\-\.]+::pagination\./';

    /**
     * @var string[] Which view a paginator renders is the application's decision, made by a
     * service provider (Paginator::defaultView()), not a template's:
     *
     * - `defaultView()`, `defaultSimpleView()` and the `use*()` presets are *static* setters, so
     *   they sit outside the per-call check on render()/links() and their effect outlives the
     *   render they were called in.
     * - `viewFactory()` hands back the view factory itself, which resolves and renders a view
     *   from a name it is given, so the check on render()/links() does not apply to it at all.
     *
     * Lower case, since PHP method dispatch is case insensitive; SecurityPolicy lower-cases its
     * own copies of every list in its constructor.
     */
    public const VIEW_CONFIG_METHODS = [
        'viewfactory',
        'viewfactoryresolver',
        'defaultview',
        'defaultsimpleview',
        'usetailwind',
        'usebootstrap',
        'usebootstrapthree',
        'usebootstrapfour',
        'usebootstrapfive',
    ];

    /**
     * @var array<string, string[]> Methods that take a view name as their first argument,
     * against the parameter names the paginators declare for it — AbstractPaginator::render()
     * and ::links() and their cursor equivalents all declare `$view`, so a template may also
     * pass it by name. Lower case keys, since PHP method dispatch is case insensitive.
     */
    protected $viewMethods = [
        'render' => ['view'],
        'links' => ['view'],
    ];

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
     * @inheritDoc
     */
    protected function getProxiedObject()
    {
        return $this->paginator;
    }

    /**
     * Guard the view name, and the view configuration, before the call is forwarded.
     */
    public function __call($method, $parameters)
    {
        $normalized = strtolower($method);

        if (in_array($normalized, static::VIEW_CONFIG_METHODS)) {
            throw new SecurityNotAllowedMethodError(
                sprintf('Configuring a paginator\'s view with "%s" is blocked.', $method),
                static::class,
                $normalized
            );
        }

        if (array_key_exists($normalized, $this->viewMethods)) {
            $this->checkViewAllowed(
                $normalized,
                $this->resolveArgument($parameters, 0, $this->viewMethods[$normalized])
            );
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Throws unless the given view is one a paginator may be rendered with.
     *
     * A null view is the configured default, which is what almost every template uses. Any
     * other name has to sit inside one of the pagination namespaces, and may not walk out of
     * it again: the view finder turns the name into a path, so a relative segment would reach
     * an arbitrary PHP file.
     *
     * A dot is how a view name spells a directory, so what is accepted is the `pagination`
     * namespace Laravel registers, and any registered namespace's own `pagination` directory -
     * which is where Winter's `defaultSimpleView` lives, and where a plugin or theme shipping
     * its own pagination views puts them. A view anywhere else under a plugin namespace stays
     * refused: those run as PHP outside the sandbox. What is refused is anything that could leave them: `..` and
     * either path separator — rejecting both is what keeps the check correct on Windows, where
     * either one reaches the filesystem.
     *
     * @param mixed $view
     * @throws SecurityNotAllowedMethodError
     */
    protected function checkViewAllowed(string $method, $view): void
    {
        if (is_null($view)) {
            return;
        }

        $allowed = is_string($view)
            && (
                Str::startsWith($view, static::PAGINATION_NAMESPACE)
                || preg_match(static::PAGINATION_PATH_PATTERN, $view) === 1
            )
            && !Str::contains($view, ['..', '/', '\\']);

        if (!$allowed) {
            throw new SecurityNotAllowedMethodError(
                sprintf(
                    'Rendering a paginator with the "%s" view is blocked.',
                    is_string($view) ? $view : gettype($view)
                ),
                static::class,
                $method
            );
        }
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
