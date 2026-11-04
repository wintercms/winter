<?php

namespace System\Twig;

use InvalidArgumentException;
use Twig\Sandbox\SecurityNotAllowedPropertyError;
use Twig\Sandbox\SecurityPolicy as TwigSecurityPolicy;
use Twig\Sandbox\SecurityPolicyInterface;
use Winter\Storm\Database\Builder as DbBuilder;
use Winter\Storm\Database\Model as DbModel;
use Winter\Storm\Database\QueryBuilder as DbQueryBuilder;
use Winter\Storm\Support\Str;

/**
 * SandboxPolicy is the security policy used by sandboxed Twig environments.
 *
 * It is an allowlist: a template may only use the tags, filters, functions, tests and object
 * methods named below, or added to them by the caller. Everything else is refused, including
 * every markup extension a plugin registers, so adding a Twig extension never widens a sandbox
 * by accident.
 *
 * Reading data off an object is allowed, calling behaviour on one is not. Twig checks a property
 * read before it falls back to a getter (see Twig\Extension\CoreExtension::getAttribute()), so
 * `{{ user.email }}` reads an attribute while `{{ user.delete() }}` and `{{ user.fullName }}`
 * (a `getFullName()` getter) both go through the method allowlist and are refused. That makes the
 * contract for a caller a single rule: **pass only the values you are willing to expose**, because
 * what is passed in is readable and nothing else is reachable.
 *
 * A model is the exception to "everything passed in is readable", because Winter's models declare
 * `$attributes` public: `{{ user.attributes }}` would hand back the raw column bag, which is the
 * one read that defeats the model's own `$hidden` list. On a model the policy therefore allows
 * only what the model reports as data - see isModelData() below.
 *
 * @package winter\wn-system-module
 * @author Winter CMS
 */
final class SandboxPolicy implements SecurityPolicyInterface
{
    /**
     * @var string[] Tags a sandboxed template may use.
     *
     * Everything that pulls in another template is left out (`extends`, `embed`, `include`,
     * `use`, `import`, `from`, `block`), because the template name is the untrusted part and
     * any template the loader can resolve would become readable.
     */
    public const TAGS = [
        'apply',
        'autoescape',
        'filter',
        'for',
        'if',
        'set',
        'spaceless',
        'verbatim',
        'with',
    ];

    /**
     * @var string[] Filters a sandboxed template may use.
     *
     * Twig's higher-order filters (`sort`, `map`, `filter`, `reduce`, `find`, `column`) are
     * included: Twig refuses a callable that is not a Closure in sandbox mode itself, so a
     * template cannot smuggle a PHP function name through them.
     */
    public const FILTERS = [
        // Twig
        'abs',
        'batch',
        'capitalize',
        'column',
        'convert_encoding',
        'date',
        'date_modify',
        'default',
        'e',
        'escape',
        'filter',
        'find',
        'first',
        'format',
        'join',
        'json_encode',
        'keys',
        'last',
        'length',
        'lower',
        'map',
        'merge',
        'nl2br',
        'number_format',
        'raw',
        'reduce',
        'replace',
        'reverse',
        'round',
        'shuffle',
        'slice',
        'sort',
        'spaceless',
        'split',
        'striptags',
        'title',
        'trim',
        'upper',
        'url_encode',

        // Winter
        'app',
        'asset',
        'camel',
        'finish',
        'imageHeight',
        'imageWidth',
        'md',
        'md_line',
        'md_safe',
        'media',
        'plural',
        'resize',
        'singular',
        'slug',
        'snake',
        'studly',
        'time_since',
        'time_tense',
        'trans',
        'transchoice',
    ];

    /**
     * @var string[] Functions a sandboxed template may use.
     *
     * The wildcard markup extensions (`array_*`, `str_*`, `url_*`, `html_*`, `form_*`) are left
     * out: each of them resolves a template-supplied name to a method on a facade, and several
     * of those methods invoke an argument, so between them they are an arbitrary-function call.
     * `constant`, `enum`, `enum_cases`, `source`, `include`, `template_from_string`, `dump` and
     * `attribute` are left out as reflection and disclosure surface. `input`, `post` and `get`
     * are left out because request input has no meaning where these templates render.
     */
    public const FUNCTIONS = [
        // Twig
        'cycle',
        'date',
        'max',
        'min',
        'random',
        'range',

        // Winter
        'action',
        'app',
        'asset',
        'imageHeight',
        'imageWidth',
        'link_to',
        'link_to_action',
        'link_to_asset',
        'link_to_route',
        'media',
        'resize',
        'route',
        'secure_asset',
        'secure_url',
        'url',
    ];

    /**
     * @var string[] Tests a sandboxed template may use. Twig marks most of these always allowed
     * in a sandbox from 3.28; they are listed so the policy holds on earlier 3.x too. The
     * `constant` test is left out for the same reason as the `constant` function.
     */
    public const TESTS = [
        'defined',
        'divisible by',
        'empty',
        'even',
        'iterable',
        'mapping',
        'none',
        'null',
        'odd',
        'same as',
        'sequence',
        'true',
    ];

    /**
     * @var array<string, string[]> Methods a sandboxed template may call, keyed by class or
     * interface. Both of these are parameterless interface methods with no side effects, and
     * both are reached by ordinary templates: `{{ order.created_at }}` string-coerces a date
     * through __toString(), and `{{ order.items.count }}` is how a template counts a relation.
     */
    public const METHODS = [
        \Stringable::class => ['__toString'],
        \Countable::class => ['count'],
    ];

    /**
     * @var string[] The keys accepted by the constructor's $allow argument.
     */
    protected const ALLOW_KEYS = ['tags', 'filters', 'functions', 'tests', 'methods'];

    /**
     * @var TwigSecurityPolicy Twig's own allowlist policy, enforcing everything but properties.
     */
    protected TwigSecurityPolicy $policy;

    /**
     * @param array $allow Widens the baseline, e.g. `['filters' => ['my_filter'], 'methods' =>
     * [MyModel::class => ['fullName']]]`. Accepts the keys `tags`, `filters`, `functions`,
     * `tests` and `methods`; any other key is a mistake and throws.
     * @throws InvalidArgumentException
     */
    public function __construct(array $allow = [])
    {
        if ($unknown = array_diff(array_keys($allow), static::ALLOW_KEYS)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown Twig sandbox allowance "%s", expected one of: %s.',
                implode('", "', $unknown),
                implode(', ', static::ALLOW_KEYS)
            ));
        }

        $methods = static::METHODS;
        foreach ($allow['methods'] ?? [] as $class => $classMethods) {
            $methods[$class] = array_merge($methods[$class] ?? [], (array) $classMethods);
        }

        $this->policy = new TwigSecurityPolicy(
            array_merge(static::TAGS, $allow['tags'] ?? []),
            array_merge(static::FILTERS, $allow['filters'] ?? []),
            $methods,
            [],
            array_merge(static::FUNCTIONS, $allow['functions'] ?? []),
            array_merge(static::TESTS, $allow['tests'] ?? [])
        );

        // Opt in to Twig 4's behaviour now: without this the `extends` and `use` tags and the
        // `parent`, `block` and `attribute` functions are allowed whether or not they are listed.
        $this->policy->setStrict(true);
    }

    /**
     * {@inheritDoc}
     */
    public function checkSecurity($tags, $filters, $functions, array $tests = []): void
    {
        $this->policy->checkSecurity($tags, $filters, $functions, $tests);
    }

    /**
     * {@inheritDoc}
     */
    public function checkMethodAllowed($obj, $method): void
    {
        if ($this->isNullAttributeRead($obj, $method)) {
            return;
        }

        $this->policy->checkMethodAllowed($obj, $method);
    }

    /**
     * Whether this is Twig falling back to a method call because reading an attribute came up
     * empty, rather than a template calling behaviour.
     *
     * Eloquent reports a column holding null as absent - Model::offsetExists() is
     * `!is_null($this->getAttribute($key))` - so `{{ user.middle_name }}` lands on the method
     * check named after the column (see Twig\Extension\CoreExtension::getAttribute()). Allowing
     * that keeps a null column rendering as nothing, which is what it did before this policy:
     * the model has no method of that name, __call() rejects it, and Twig swallows the
     * BadMethodCallException. Without it a mail template works while a column has a value and
     * throws once it does not, which is the worst way to find out.
     *
     * It stays that one case: the name has to be data the property check would have allowed, and
     * nothing in Model::__call()'s dispatch chain may answer it, so the call provably ends at the
     * BadMethodCallException that chain throws. `{{ user.truncate }}` is refused even on a model
     * with a column of that name.
     *
     * @param object $obj
     * @param string $method
     */
    protected function isNullAttributeRead($obj, $method): bool
    {
        if (!$obj instanceof DbModel || !$this->isModelData($obj, (string) $method)) {
            return false;
        }

        return !$this->isAnsweredByModel($obj, (string) $method);
    }

    /**
     * Whether anything in Model::__call()'s dispatch chain answers this name, in which case the
     * null-attribute read above would not be a read at all - it would run that behaviour, with
     * whatever arguments the template chose to pass.
     *
     * One arm per step of the chain, which is Winter's Model::__call() -> extendableCall() ->
     * Eloquent\Model::__call() -> Builder::__call() -> QueryBuilder::__call(): Winter's `extend()`
     * special case, the model's own methods and the ones its extensions add, a scope reached by
     * its bare name, the two builders the model forwards to and their macros, and the query
     * builder's dynamic `where<Column>()`.
     */
    protected function isAnsweredByModel(DbModel $obj, string $name): bool
    {
        return $name === 'extend'
            || $obj->methodExists($name)
            || $obj->methodExists('scope' . ucfirst($name))
            || method_exists(DbBuilder::class, $name)
            || DbBuilder::hasGlobalMacro($name)
            || method_exists(DbQueryBuilder::class, $name)
            || DbQueryBuilder::hasMacro($name)
            || Str::startsWith($name, 'where');
    }

    /**
     * Reading a property, an array key or an Eloquent attribute off a value the caller passed in
     * is allowed: it returns data the caller chose to expose and has no side effect of its own.
     * Behaviour stays shut, because Twig only reaches a getter or any other method after this
     * check has passed (see the class docblock).
     *
     * A model is read through isModelData() instead, because not everything readable on one is
     * data the caller meant to expose.
     *
     * @param object $obj
     * @param string $property
     */
    public function checkPropertyAllowed($obj, $property): void
    {
        if ($obj instanceof DbModel && !$this->isModelData($obj, (string) $property)) {
            throw new SecurityNotAllowedPropertyError(
                sprintf('Reading "%s" on a "%s" object is not allowed.', $property, $obj::class),
                $obj::class,
                (string) $property
            );
        }
    }

    /**
     * Whether a model carries this name as data, which on a model is narrower than "readable".
     *
     * Winter's models declare `$attributes`, the relation definitions, `$implement` and
     * `$sessionKey` public, so all of them are property reads Twig would allow; `$attributes` in
     * particular hands back every column, including the ones `$hidden` exists to keep out of
     * output. So the question asked here is the model's own: is this an attribute, a cast, an
     * accessor or a relation, and does `$hidden`/`$visible` allow it out?
     */
    protected function isModelData(DbModel $obj, string $name): bool
    {
        if (in_array($name, $obj->getHidden(), true)) {
            return false;
        }

        if (($visible = $obj->getVisible()) && !in_array($name, $visible, true)) {
            return false;
        }

        return $obj->hasAttribute($name)
            || $obj->relationLoaded($name)
            || $obj->hasRelation($name);
    }
}
