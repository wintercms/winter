<?php namespace Cms\Twig;

use Block;
use Cms\Classes\Controller;
use Event;
use System\Classes\Asset\Vite;
use Twig\Extension\AbstractExtension as TwigExtension;
use Twig\TwigFilter as TwigSimpleFilter;
use Twig\TwigFunction as TwigSimpleFunction;

/**
 * The CMS Twig extension class implements the basic CMS Twig functions and filters.
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class Extension extends TwigExtension
{
    /**
     * The instanciated CMS controller
     */
    protected Controller $controller;

    /**
     * Sets the CMS controller instance
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Gets the CMS controller instance
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * Returns an array of functions to add to the existing list.
     */
    public function getFunctions(): array
    {
        $options = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigSimpleFunction('page', [$this, 'pageFunction'], $options),
            new TwigSimpleFunction('partial', [$this, 'partialFunction'], $options),
            new TwigSimpleFunction('content', [$this, 'contentFunction'], $options),
            new TwigSimpleFunction('component', [$this, 'componentFunction'], $options),
            new TwigSimpleFunction('placeholder', [$this, 'placeholderFunction'], ['is_safe' => ['html']]),
            new TwigSimpleFunction('viteReactRefresh', [$this, 'viteReactRefreshFunction'], $options),
            new TwigSimpleFunction('vite', [$this, 'viteFunction'], $options),
        ];
    }

    /**
     * Returns an array of filters this extension provides.
     */
    public function getFilters(): array
    {
        $options = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigSimpleFilter('page', [$this, 'pageFilter'], $options),
            new TwigSimpleFilter('theme', [$this, 'themeFilter'], $options),
        ];
    }

    /**
     * Returns an array of token parsers this extension provides.
     */
    public function getTokenParsers(): array
    {
        return [
            new PageTokenParser,
            new PartialTokenParser,
            new ContentTokenParser,
            new PutTokenParser,
            new PlaceholderTokenParser,
            new DefaultTokenParser,
            new FrameworkTokenParser,
            new SnowboardTokenParser,
            new ComponentTokenParser,
            new FlashTokenParser,
            new ScriptsTokenParser,
            new StylesTokenParser,
        ];
    }

    /**
     * Renders a page; used in the layout code to output the requested page.
     */
    public function pageFunction(): string
    {
        return $this->controller->renderPage();
    }

    /**
     * Renders the requested partial with the provided parameters. Optionally throw an exception if the partial cannot be found
     */
    public function partialFunction(string $name, array $parameters = [], bool $throwException = false): string|bool
    {
        return $this->controller->renderPartial($name, $parameters, $throwException);
    }

    /**
     * Renders the requested content file.
     */
    public function contentFunction(string $name, array $parameters = [], bool $throwException = false): string|bool
    {
        return $this->controller->renderContent($name, $parameters, $throwException);
    }

    /**
     * Renders a component's default partial.
     */
    public function componentFunction(string $name, array $parameters = []): string
    {
        return $this->controller->renderComponent($name, $parameters);
    }

    /**
     * Renders registered assets of a given type or all types if $type not provided
     */
    public function assetsFunction(string $type = null): ?string
    {
        return $this->controller->makeAssets($type);
    }

    /**
     * Renders placeholder content, without removing the block, must be called before the placeholder tag itself
     */
    public function placeholderFunction(string $name, string $default = null): ?string
    {
        if (($result = Block::get($name)) === null) {
            return null;
        }

        $result = str_replace('<!-- X_WINTER_DEFAULT_BLOCK_CONTENT -->', trim($default), $result);
        return $result;
    }

    /**
     * Returns the relative URL for the provided page
     *
     * @param mixed $name Specifies the Cms Page file name.
     * @param array|bool $parameters Route parameters to consider in the URL. If boolean will be used as the value for $routePersistence
     * @param bool $routePersistence Set to false to exclude the existing routing parameters from the generated URL
     */
    public function pageFilter($name, $parameters = [], $routePersistence = true): ?string
    {
        return $this->controller->pageUrl($name, $parameters, $routePersistence);
    }

    /**
     * Converts supplied URL to a theme URL relative to the website root. If the URL provided is an
     * array then the files will be combined.
     *
     * @param mixed $url Specifies the input to be turned into a URL (arrays will be passed to the AssetCombiner)
     */
    public function themeFilter($url): string
    {
        return $this->controller->themeUrl($url);
    }

    /**
     * Generates Vite tags via Laravel's Vite Object.
     */
    public function viteFunction(array $entrypoints, string $package, ?string $buildDirectory = null): \Illuminate\Support\HtmlString
    {
        return Vite::tags($entrypoints, $package, $buildDirectory);
    }

    /**
     * Generates Vite React Refresh tags via Laravel's Vite Object.
     */
    public function viteReactRefreshFunction(string $package, ?string $buildDirectory = null): ?\Illuminate\Support\HtmlString
    {
        return Vite::reactRefreshTag($package, $buildDirectory);
    }

    /**
     * Opens a layout block.
     */
    public function startBlock(string $name): void
    {
        Block::startBlock($name);
    }

    /**
     * Returns a layout block contents (or null if it doesn't exist) and removes the block.
     */
    public function displayBlock(string $name, string $default = null): ?string
    {
        if (($result = Block::placeholder($name)) === null) {
            return $default;
        }

        /**
         * @event cms.block.render
         * Provides an opportunity to modify the rendered block content
         *
         * Example usage:
         *
         *     Event::listen('cms.block.render', function ((string) $name, (string) $result) {
         *         if ($name === 'myBlockName') {
         *             return 'my custom content';
         *         }
         *     });
         *
         */
        if ($event = Event::fire('cms.block.render', [$name, $result], true)) {
            $result = $event;
        }

        $result = str_replace('<!-- X_WINTER_DEFAULT_BLOCK_CONTENT -->', trim($default), $result);
        return $result;
    }

    /**
     * Closes a layout block.
     */
    public function endBlock($append = true): void
    {
        Block::endBlock($append);
    }
}
