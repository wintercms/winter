<?php namespace System\Classes;

use System\Twig\Extension as SystemTwigExtension;
use System\Twig\GetAttrAdjuster;
use System\Twig\Loader as SystemTwigLoader;
use System\Twig\SecurityPolicy as TwigSecurityPolicy;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\SandboxExtension;
use Twig\Loader\LoaderInterface;
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
    protected $items = [];

    /**
     * @var \System\Classes\PluginManager
     */
    protected $pluginManager;

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
     * @param string $type The Twig extension type
     * @return array
     */
    public function listExtensions(string $type): array
    {
        if ($this->items === []) {
            $this->loadExtensions();
        }

        return is_array($this->items[$type] ?? null) ? $this->items[$type] : [];
    }

    /**
     * Returns a list of the registered Twig filters.
     * @return array
     */
    public function listFilters(): array
    {
        return $this->listExtensions(self::EXTENSION_FILTER);
    }

    /**
     * Returns a list of the registered Twig functions.
     * @return array
     */
    public function listFunctions(): array
    {
        return $this->listExtensions(self::EXTENSION_FUNCTION);
    }

    /**
     * Returns a list of the registered Twig token parsers.
     * @return array
     */
    public function listTokenParsers(): array
    {
        return $this->listExtensions(self::EXTENSION_TOKEN_PARSER);
    }

    /**
     * Makes a set of Twig functions for use in a twig extension.
     * @param array $functions Current collection
     * @return array
     */
    public function makeTwigFunctions(array $functions = []): array
    {
        return $this->makeTwigCallables(
            $this->listFunctions(),
            TwigSimpleFunction::class,
            ['is_safe' => ['html']],
            $functions
        );
    }

    /**
     * Makes a set of Twig filters for use in a twig extension.
     * @param array $filters Current collection
     * @return array
     */
    public function makeTwigFilters(array $filters = []): array
    {
        return $this->makeTwigCallables(
            $this->listFilters(),
            TwigSimpleFilter::class,
            ['is_safe' => ['html']],
            $filters
        );
    }

    /**
     * Makes a set of Twig token parsers for use in a twig extension.
     * @param array $parsers Current collection
     * @return array
     */
    public function makeTwigTokenParsers(array $parsers = []): array
    {
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
     * Tests if a callable type contains a wildcard, also acts as a
     * utility to replace the wildcard with a string.
     * @param callable $callable
     * @param string|bool $replaceWith
     * @return mixed
     */
    protected function isWildCallable($callable, $replaceWith = false)
    {
        $isWild = false;

        if (is_string($callable) && strpos($callable, '*') !== false) {
            $isWild = $replaceWith ? str_replace('*', $replaceWith, $callable) : true;
        } elseif (is_array($callable)) {
            // Check first element of array
            if (is_string($callable[0]) && strpos($callable[0], '*') !== false) {
                if ($replaceWith) {
                    $isWild = $callable;
                    $isWild[0] = str_replace('*', $replaceWith, $callable[0]);
                }
                else {
                    $isWild = true;
                }
            }

            // Check second element of array if it exists
            if (!empty($callable[1]) && is_string($callable[1]) && strpos($callable[1], '*') !== false) {
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

    /**
     * Shared logic for creating Twig callable (functions/filters).
     * @param array $definitions
     * @param string $twigClass
     * @param array $defaultOptions
     * @param array $collection
     * @return array
     */
    private function makeTwigCallables(array $definitions, string $twigClass, array $defaultOptions, array $collection = []): array
    {
        foreach ($definitions as $name => $callable) {
            $options = $defaultOptions;
            if (is_array($callable) && isset($callable['options'])) {
                $options = $this->normalizeOptions($callable['options'], $defaultOptions);
            }
            $callable = $this->normalizeCallable($callable);

            // Handle wildcard
            if (strpos($name, '*') !== false && $this->isWildCallable($callable)) {
                $callable = function ($name) use ($callable) {
                    $arguments = array_slice(func_get_args(), 1);
                    $method = $this->isWildCallable($callable, Str::camel($name));
                    return call_user_func_array($method, $arguments);
                };
            }

            if (!is_callable($callable)) {
                throw new SystemException(sprintf(
                    'The markup %s (%s) for %s is not callable.',
                    $twigClass,
                    json_encode($callable),
                    $name
                ));
            }

            $collection[] = new $twigClass($name, $callable, $options);
        }
        return $collection;
    }

    /**
     * Normalize a callable definition.
     * @param mixed $callable
     * @return callable
     */
    private function normalizeCallable($callable)
    {
        if (is_array($callable)) {
            if (!empty($callable['callable']) && is_callable($callable['callable'])) {
                return $callable['callable'];
            } elseif (!empty($callable[0]) && is_callable($callable[0])) {
                return $callable[0];
            }
        }

        return $callable;
    }

    /**
     * Normalize options for Twig extension definitions.
     * @param array $options
     * @param array $defaultOptions
     * @return array
     */
    private function normalizeOptions(array $options, array $defaultOptions): array
    {
        if (isset($options['is_safe']) && !is_array($options['is_safe'])) {
            $options['is_safe'] = is_string($options['is_safe']) ? [$options['is_safe']] : [];
        }

        return array_merge($defaultOptions, $options);
    }
}
