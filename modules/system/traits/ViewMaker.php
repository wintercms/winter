<?php namespace System\Traits;

use File;
use Lang;
use Block;
use SystemException;
use Throwable;
use Config;

/**
 * View Maker Trait
 * Adds view based methods to a class
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
trait ViewMaker
{
    /**
     * @var array A list of variables to pass to the page.
     */
    public $vars = [];

    /**
     * @var string|array Specifies a path to the views directory.
     */
    protected $viewPath;

    /**
     * @var string Specifies a path to the layout directory.
     */
    protected $layoutPath;

    /**
     * @var string Layout to use for the view.
     */
    public $layout;

    /**
     * @var bool Prevents the use of a layout.
     */
    public $suppressLayout = false;

    /**
     * Prepends a path on the available view path locations.
     *
     * @param array|string $path
     * @return void
     */
    public function prependViewPath(array|string $path): void
    {
        $this->viewPath = (array) $this->viewPath;

        if (is_array($path)) {
            $this->viewPath = array_merge($path, $this->viewPath);
        } else {
            array_unshift($this->viewPath, $path);
        }
    }

    /**
     * Append a path on the available view path locations.
     *
     * @param array|string $path
     * @return void
     */
    public function appendViewPath(array|string $path): void
    {
        $this->viewPath = (array) $this->viewPath;

        if (is_array($path)) {
            $this->viewPath = array_merge($this->viewPath, $path);
        } else {
            $this->viewPath[] = $path;
        }
    }

    /**
     * Prepends a path on the available view path locations.
     *
     * @deprecated Use prependViewPath()
     */
    public function addViewPath(string|array $path): void
    {
        $this->prependViewPath($path);
    }

    /**
     * Returns the active view path locations.
     */
    public function getViewPaths(): array
    {
        return (array) $this->viewPath;
    }

    /**
     * Render a partial file contents located in the views folder.
     * @return mixed Partial contents or false if not throwing an exception.
     */
    public function makePartial(string $partial, array $params = [], bool $throwException = true)
    {
        $notRealPath = realpath($partial) === false || is_dir($partial) === true;
        if (!File::isPathSymbol($partial) && $notRealPath) {
            $folder = strpos($partial, '/') !== false ? dirname($partial) . '/' : '';
            $partial = $folder . '_' . strtolower(basename($partial));
        }

        $partialPath = $this->getViewPath($partial);

        if (!File::exists($partialPath)) {
            if ($throwException) {
                throw new SystemException(Lang::get('backend::lang.partial.not_found_name', ['name' => $partialPath]));
            }

            return false;
        }

        return $this->makeFileContents($partialPath, $params);
    }

    /**
     * Loads the specified view. Applies the layout if one is set.
     * The view file must have the .php extension (or ".htm" for historical reasons) and be located in the views directory
     */
    public function makeView(string $view): string
    {
        $viewPath = $this->getViewPath(strtolower($view));
        $contents = $this->makeFileContents($viewPath);
        return $this->makeViewContent($contents);
    }

    /**
     * Renders supplied contents inside a layout.
     */
    public function makeViewContent(string $contents, ?string $layout = null): string
    {
        if ($this->suppressLayout || $this->layout == '') {
            return $contents;
        }

        // Append any undefined block content to the body block
        Block::set('undefinedBlock', $contents);
        Block::append('body', Block::get('undefinedBlock'));
        return $this->makeLayout($layout);
    }

    /**
     * Render a layout, defaulting to the layout propery specified on the class
     * @return string|bool The layout contents, or false.
     */
    public function makeLayout(?string $name = null, array $params = [], bool $throwException = true): string|bool
    {
        $layout = $name ?? $this->layout;
        if ($layout == '') {
            return '';
        }

        $layoutPath = $this->getViewPath($layout, $this->layoutPath);

        if (!File::exists($layoutPath)) {
            if ($throwException) {
                throw new SystemException(Lang::get('cms::lang.layout.not_found_name', ['name' => $layoutPath]));
            }

            return false;
        }

        return $this->makeFileContents($layoutPath, $params);
    }

    /**
     * Renders a layout partial
     */
    public function makeLayoutPartial(string $partial, array $params = []): string
    {
        if (!File::isLocalPath($partial) && !File::isPathSymbol($partial)) {
            $folder = strpos($partial, '/') !== false ? dirname($partial) . '/' : '';
            $partial = $folder . '_' . strtolower(basename($partial));
        }

        return $this->makeLayout($partial, $params);
    }

    /**
     * Locates a file based on its definition. The file name can be prefixed with a
     * symbol (~|$) to return in context of the application or plugin base path,
     * otherwise it will be returned in context of this object view path.
     *
     * If the fileName cannot be found it will be returned unmodified.
     */
    public function getViewPath(string $fileName, string|array|null $viewPaths = null): string
    {
        $input = $fileName;
        $allowedExtensions = ['php', 'htm'];

        if (!isset($this->viewPath)) {
            $this->viewPath = $this->guessViewPath();
        }

        if (!$viewPaths) {
            $viewPaths = $this->viewPath;
        }

        if (!is_array($viewPaths)) {
            $viewPaths = [$viewPaths];
        }

        // Check the path for an extension
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!empty($ext)) {
            if (!in_array($ext, $allowedExtensions)) {
                throw new SystemException("$ext is not a valid View extension");
            }

            // Remove the extension from the fileName
            $fileName = substr($fileName, 0, strrpos($fileName, '.'));
        }

        // Check if this a path relative to the view paths
        foreach ($viewPaths as $path) {
            $absolutePath = File::symbolizePath($path);
            foreach ($allowedExtensions as $ext) {
                $viewPath = $absolutePath . DIRECTORY_SEPARATOR . $fileName . ".$ext";
                if (File::isFile($viewPath)) {
                    return $viewPath;
                }
            }
        }

        // Next, check if this is a local path reference
        $absolutePath = File::symbolizePath($fileName);
        foreach ($allowedExtensions as $ext) {
            $viewPath = $absolutePath . ".$ext";

            if (
                File::isLocalPath($viewPath)
                || (
                    !Config::get('cms.restrictBaseDir', true)
                    && realpath($viewPath) !== false
                )
            ) {
                return $viewPath;
            }
        }

        return $input;
    }

    /**
     * Includes a file path using output buffering, making the provided vars available.
     */
    public function makeFileContents(string $filePath, array $extraParams = []): string
    {
        if (!strlen($filePath) ||
            !File::isFile($filePath) ||
            (!File::isLocalPath($filePath) && Config::get('cms.restrictBaseDir', true))
        ) {
            return '';
        }

        if (!is_array($extraParams)) {
            $extraParams = [];
        }

        $vars = array_merge($this->vars, $extraParams);

        $obLevel = ob_get_level();

        ob_start();

        extract($vars);

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            include $filePath;
        }
        catch (Throwable $e) {
            $this->handleViewException($e, $obLevel);
        }

        return ob_get_clean();
    }

    /**
     * Handle a view exception.
     */
    protected function handleViewException(Throwable $e, int $obLevel): void
    {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw $e;
    }

    /**
     * Guess the package path for the called class.
     * @param string $suffix An extra path to attach to the end
     * @param bool $isPublic Returns public path instead of an absolute one
     */
    public function guessViewPath(string $suffix = '', bool $isPublic = false): ?string
    {
        $class = get_called_class();
        return $this->guessViewPathFrom($class, $suffix, $isPublic);
    }

    /**
     * Guess the package path from a specified class.
     * @param string $class Class to guess path from.
     * @param string $suffix An extra path to attach to the end
     * @param bool $isPublic Returns public path instead of an absolute one
     */
    public function guessViewPathFrom(string $class, string $suffix = '', bool $isPublic = false): ?string
    {
        $classFolder = strtolower(class_basename($class));
        $classFile = realpath(dirname(File::fromClass($class)));
        $guessedPath = $classFile ? $classFile . DIRECTORY_SEPARATOR . $classFolder . $suffix : null;
        return $isPublic ? File::localToPublic($guessedPath) : $guessedPath;
    }
}
