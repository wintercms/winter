<?php namespace System\Twig;

use App;
use Exception;
use File;
use InvalidArgumentException;
use Twig\Source as TwigSource;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface as TwigLoaderInterface;
use Winter\Storm\Support\Str;

/**
 * This class implements a Twig template loader for the core system and backend.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class Loader implements TwigLoaderInterface
{
    /**
     * @var bool Allow any local file
     */
    public static $allowInclude = false;

    /**
     * @var array Cache
     */
    protected $cache = [];

    /**
     * Gets the path of a view file.
     */
    protected function findTemplate(string $name): string
    {
        $finder = App::make('view')->getFinder();

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (static::$allowInclude === true && File::isFile($name)) {
            return $this->cache[$name] = $name;
        }

        try {
            $path = $finder->find($name);
        } catch (InvalidArgumentException $ex) {
            if (Str::contains($ex->getMessage(), 'not found')) {
                throw new LoaderError($ex->getMessage());
            }
            throw $ex;
        }

        return $this->cache[$name] = $path;
    }

    /**
     * Returns the Twig content string.
     * This step is cached internally by Twig.
     */
    public function getSourceContext(string $name): TwigSource
    {
        return new TwigSource(File::get($this->findTemplate($name)), $name);
    }

    /**
     * Returns the Twig cache key.
     */
    public function getCacheKey(string $name): string
    {
        return $this->findTemplate($name);
    }

    /**
     * Determines if the content is fresh.
     */
    public function isFresh(string $name, int $time): bool
    {
        return File::lastModified($this->findTemplate($name)) <= $time;
    }

    /**
     * Returns the file name of the loaded template.
     */
    public function getFilename(string $name): string
    {
        return $this->findTemplate($name);
    }

    /**
     * Checks that the template exists.
     */
    public function exists(string $name): bool
    {
        try {
            $this->findTemplate($name);
            return true;
        }
        catch (Exception $exception) {
            return false;
        }
    }
}
