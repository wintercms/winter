<?php namespace System\Twig\SecurityPolicy;

/**
 * ResolvesNamedArguments reads an argument a sandbox proxy guards out of the parameters of a
 * `__call()`, whichever way the template passed it.
 *
 * `__call()` receives the arguments as an array and the proxies forward them with
 * `$object->$method(...$parameters)`. PHP spreads a string key as a *named* argument, so an
 * argument a template names rather than positions arrives as `['view' => '...']` and not as
 * `[0 => '...']`: the value still reaches the method, while a guard reading `$parameters[0]`
 * sees nothing at all. Twig hands the arguments over as exactly that array for `f.m(view="x")`,
 * `f.m(view: "x")`, `f.m(...{view: "x"})` and `attribute(f, "m", {view: "x"})`.
 *
 * The names a guard has to know are the ones the callee declares, because PHP raises an Error
 * for a name that matches no parameter of the callee, before the method body runs.
 *
 * @package winter\wn-system-module
 */
trait ResolvesNamedArguments
{
    /**
     * Returns the value passed in the given argument position, whether it was passed
     * positionally or under one of the parameter names the callee declares for it.
     *
     * @param array $parameters The arguments as __call() received them
     * @param int $position
     * @param string[] $names The callee's parameter names for that position
     * @return mixed
     */
    protected function resolveArgument(array $parameters, int $position, array $names)
    {
        if (array_key_exists($position, $parameters)) {
            return $parameters[$position];
        }

        foreach ($names as $name) {
            if (array_key_exists($name, $parameters)) {
                return $parameters[$name];
            }
        }

        return null;
    }
}
