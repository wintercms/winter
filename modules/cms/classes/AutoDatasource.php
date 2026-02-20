<?php

namespace Cms\Classes;

use ApplicationException;
use Cache;
use Exception;
use Winter\Storm\Halcyon\Datasource\Datasource;
use Winter\Storm\Halcyon\Datasource\DatasourceInterface;
use Winter\Storm\Halcyon\Exception\DeleteFileException;
use Winter\Storm\Halcyon\Model;
use Winter\Storm\Halcyon\Processors\Processor;
use Winter\Storm\Support\Facades\Config;

/**
 * Datasource that loads from other data sources automatically
 *
 * @package winter\wn-cms-module
 * @author Luke Towers
 */
class AutoDatasource extends Datasource implements DatasourceInterface
{
    /**
     * @var array The available datasource instances
     */
    protected $datasources = [];

    /**
     * @var string The cache key to use for this datasource instance
     */
    protected $cacheKey = 'halcyon-datastore-auto';

    /**
     * @var array Local cache of paths available in the datasources
     */
    protected $pathCache = [];

    /**
     * @var boolean Flag on whether the cache should respect refresh requests
     */
    protected $allowCacheRefreshes = true;

    /**
     * @var string The key for the datasource to perform CRUD operations on
     */
    public $activeDatasourceKey = '';

    /**
     * @var bool Flag to indicate that we're in "single datasource mode"
     */
    protected $singleDatasourceMode = false;

    /**
     * Create a new datasource instance.
     *
     * @param array $datasources Array of datasources to utilize. Lower indexes = higher priority ['datasourceName' => $datasource]
     * @return void
     */
    public function __construct(array $datasources, ?string $cacheKey = null)
    {
        $this->datasources = $datasources;

        if ($cacheKey) {
            $this->cacheKey = $cacheKey;
        }

        $this->activeDatasourceKey = array_keys($datasources)[0];

        $this->populateCache();

        $this->postProcessor = new Processor;
    }

    /**
     * Append a datasource to the end of the list of datasources
     */
    public function appendDatasource(string $key, DatasourceInterface $datasource): void
    {
        $this->datasources[$key] = $datasource;
        $this->pathCache[] = $this->fetchPathCache($datasource);
    }

    /**
     * Prepend a datasource to the beginning of the list of datasources
     */
    public function prependDatasource(string $key, DatasourceInterface $datasource): void
    {
        $this->datasources = array_prepend($this->datasources, $datasource, $key);
        $this->pathCache = array_prepend($this->pathCache, $this->fetchPathCache($datasource), $key);
    }

    /**
     * Returns the in memory path cache map
     */
    public function getPathCache(): array
    {
        return $this->pathCache;
    }

    /**
     * Populate the local cache of paths available in each datasource
     *
     * @param boolean $refresh Default false, set to true to force the cache to be rebuilt
     */
    public function populateCache(bool $refresh = false): void
    {
        $pathCache = [];
        foreach ($this->datasources as $datasource) {
            // Allow AutoDatasource instances to handle their own internal caching
            if ($datasource instanceof AutoDatasource) {
                $datasource->populateCache($refresh);
                $pathCache[] = array_merge(...array_reverse($datasource->getPathCache()));
                continue;
            }

            // Remove any existing cache data
            if ($refresh && $this->allowCacheRefreshes) {
                Cache::forget($datasource->getPathsCacheKey());
            }

            // Load the cache
            $pathCache[] = $this->fetchPathCache($datasource);
        }
        $this->pathCache = $pathCache;
    }

    protected function fetchPathCache(DatasourceInterface $datasource): array
    {
        $pathCache = [];
        if (Config::get('app.debug', false)) {
            $pathCache = $datasource->getAvailablePaths();
        } else {
            $pathCache = Cache::rememberForever($datasource->getPathsCacheKey(), function () use ($datasource) {
                return $datasource->getAvailablePaths();
            });
        }
        return $pathCache;
    }

    /**
     * Check to see if the specified datasource has the provided Halcyon Model
     */
    public function sourceHasModel(string $source, Model $model): bool
    {
        if (!$model->exists) {
            return false;
        }

        $result = false;

        $sourcePaths = $this->getSourcePaths($source);

        if (!empty($sourcePaths)) {
            // Generate the path
            list($name, $extension) = $model->getFileNameParts();
            $path = $this->makeFilePath($model->getObjectTypeDirName(), $name, $extension);

            // Deleted paths are included as being handled by a datasource
            // The functionality built on this will need to make sure they
            // include deleted records when actually performing syncing actions
            if (isset($sourcePaths[$path])) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Get the available paths for the specified datasource key
     */
    public function getSourcePaths(string $source): array
    {
        $result = [];

        $keys = array_keys($this->datasources);
        if (in_array($source, $keys)) {
            // Get the datasource's cache index key
            $cacheIndex = array_search($source, $keys);

            // Return the available paths
            $result = $this->pathCache[$cacheIndex];
        }

        return $result;
    }

    /**
     * Forces all operations in a provided closure to run within a selected datasource.
     *
     * @throws ApplicationException if the provided datasource key doesn't exist
     */
    public function usingSource(string $source, \Closure $closure): mixed
    {
        if (!array_key_exists($source, $this->datasources)) {
            throw new ApplicationException('Invalid datasource specified.');
        }

        // Setup the datasource for single source mode
        $previousSource = $this->activeDatasourceKey;
        $this->activeDatasourceKey = $source;
        $this->singleDatasourceMode = true;

        // Execute the callback
        $return = $closure->call($this);

        // Restore the datasource to auto mode
        $this->singleDatasourceMode = false;
        $this->activeDatasourceKey = $previousSource;

        return $return;
    }

    /**
     * Push the provided model to the specified datasource
     */
    public function pushToSource(Model $model, string $source): void
    {
        $this->usingSource($source, function () use ($model) {
            $datasource = $this->getActiveDatasource();

            // Get the path parts
            $dirName = $model->getObjectTypeDirName();
            list($fileName, $extension) = $model->getFileNameParts();

            // Get the file content
            $content = $datasource->getPostProcessor()->processUpdate($model->newQuery(), []);

            // Perform an update on the selected datasource (will insert if it doesn't exist)
            $this->update($dirName, $fileName, $extension, $content);
        });
    }

    /**
     * Remove the provided model from the specified datasource
     */
    public function removeFromSource(Model $model, string $source): void
    {
        $this->usingSource($source, function () use ($model) {
            $datasource = $this->getActiveDatasource();

            // Get the path parts
            $dirName = $model->getObjectTypeDirName();
            list($fileName, $extension) = $model->getFileNameParts();

            // Perform a forced delete on the selected datasource to ensure it's removed
            $this->forceDelete($dirName, $fileName, $extension);
        });
    }

    /**
     * Get the appropriate datasource for the provided path
     */
    protected function getDatasourceForPath(string $path): DatasourceInterface
    {
        // Always return the active datasource when singleDatasourceMode is enabled
        if ($this->singleDatasourceMode) {
            return $this->getActiveDatasource();
        }

        // Default to the last datasource provided
        $datasourceIndex = count($this->datasources) - 1;

        $isDeleted = false;

        foreach ($this->pathCache as $i => $paths) {
            if (isset($paths[$path])) {
                $datasourceIndex = $i;

                // Set isDeleted to the inverse of the the path's existance flag
                $isDeleted = !$paths[$path];

                // Break on first datasource that can handle the path
                break;
            }
        }

        if ($isDeleted) {
            throw new Exception("$path is deleted");
        }

        $datasourceIndex = array_keys($this->datasources)[$datasourceIndex];

        return $this->datasources[$datasourceIndex];
    }

    /**
     * Get all valid paths for the provided directory, removing any paths marked as deleted
     *
     * @param string $dirName
     * @param array $options Array of options, [
     *                          'extensions' => ['htm', 'md', 'twig'], // Extensions to search for
     *                          'fileMatch'  => '*gr[ae]y',            // Shell matching pattern to match the filename against using the fnmatch function
     *                      ];
     * @return array $paths ["$dirName/path/1.md", "$dirName/path/2.md"]
     */
    protected function getValidPaths(string $dirName, array $options = []): array
    {
        // Initialize result set
        $paths = [];

        // Reverse the order of the sources so that earlier
        // sources are prioritized over later sources
        $pathsCache = array_reverse($this->pathCache);

        // Get paths available in the provided dirName, allowing proper prioritization of earlier datasources
        foreach ($pathsCache as $datasourceKey => $sourcePaths) {
            // Only look at the active datasource if singleDatasourceMode is enabled
            if ($this->singleDatasourceMode && $datasourceKey !== $this->activeDatasourceKey) {
                continue;
            }

            $paths = array_merge($paths, array_filter($sourcePaths, function ($path) use ($dirName, $options) {
                $basePath = $dirName . '/';

                $inPath = starts_with($path, $basePath);

                // Check the fileMatch if provided as an option
                $fnMatch = !empty($options['fileMatch']) ? fnmatch($options['fileMatch'], str_after($path, $basePath)) : true;

                // Check the extension if provided as an option
                $validExt = !empty($options['extensions']) && is_array($options['extensions']) ? in_array(pathinfo($path, PATHINFO_EXTENSION), $options['extensions']) : true;

                return $inPath && $fnMatch && $validExt;
            }, ARRAY_FILTER_USE_KEY));
        }

        // Filter out 'deleted' paths:
        $paths = array_filter($paths, function ($value) {
            return (bool) $value;
        });

        // Return just an array of paths
        return array_keys($paths);
    }

    /**
     * Helper to make file path.
     */
    protected function makeFilePath(string $dirName, string $fileName, string $extension): string
    {
        return ltrim($dirName . '/' . $fileName . '.' . $extension, '/');
    }

    /**
     * Get the datasource for use with CRUD operations
     */
    protected function getActiveDatasource(): DatasourceInterface
    {
        return $this->datasources[$this->activeDatasourceKey];
    }

    /**
     * @inheritDoc
     */
    public function selectOne(string $dirName, string $fileName, string $extension): ?array
    {
        try {
            $path = $this->makeFilePath($dirName, $fileName, $extension);
            $result = $this->getDatasourceForPath($path)->selectOne($dirName, $fileName, $extension);

            // if result = null, this means that
            // - a: The requested record doesn't exist
            // - b: The requested record exists, but is marked deleted
            // - c: The requested record is reported to exist in a datasource that it doesn't actually exist in
            if (is_null($result)) {
                foreach ($this->pathCache as $paths) {
                    // If the path is reported to exist here (and isn't marked deleted) even though the previous attempt
                    // returned nothing, then the paths cache needs to be rebuilt and we should try again
                    if (@$paths[$path]) {
                        $this->populateCache(true);
                        $result = $this->getDatasourceForPath($path)->selectOne($dirName, $fileName, $extension);
                        break;
                    }
                }
            }
        } catch (Exception $ex) {
            $result = null;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function select(string $dirName, array $options = []): array
    {
        // Handle fileName listings through just the cache
        if (@$options['columns'] === ['fileName']) {
            // Return just filenames of the valid paths for this directory
            $results = array_values(array_map(function ($path) use ($dirName) {
                return ['fileName' => str_after($path, $dirName . '/')];
            }, $this->getValidPaths($dirName, $options)));

        // Retrieve full listings from datasources directly
        } else {
            // Initialize result set
            $sourceResults = [];

            // Reverse the order of the sources so that earlier
            // sources are prioritized over later sources
            $datasources = array_reverse($this->datasources);

            foreach ($datasources as $datasource) {
                $sourceResults = array_merge($sourceResults, $datasource->select($dirName, $options));
            }

            // Remove duplicate results prioritizing results from earlier datasources
            $sourceResults = collect($sourceResults)->keyBy('fileName');

            // Get a list of valid filenames from the list of valid paths for this directory
            $validFiles = array_map(function ($path) use ($dirName) {
                return str_after($path, $dirName . '/');
            }, $this->getValidPaths($dirName, $options));

            // Filter out deleted paths
            $results = array_values($sourceResults->filter(function ($value, $key) use ($validFiles) {
                return in_array($key, $validFiles);
            })->all());
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function insert(string $dirName, string $fileName, string $extension, string $content): int
    {
        // Insert only on the active datasource
        $result = $this->getActiveDatasource()->insert($dirName, $fileName, $extension, $content);

        // Refresh the cache
        $this->populateCache(true);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function update(string $dirName, string $fileName, string $extension, string $content, $oldFileName = null, $oldExtension = null): int
    {
        $searchFileName = $oldFileName ?: $fileName;
        $searchExt = $oldExtension ?: $extension;

        // Ensure that files that are being renamed have their old names marked as deleted prior to inserting the renamed file
        // Also ensure that the cache only gets updated at the end of this operation instead of twice, once here and again at the end
        if ($searchFileName !== $fileName || $searchExt !== $extension) {
            $this->allowCacheRefreshes = false;
            $this->delete($dirName, $searchFileName, $searchExt);
            $this->allowCacheRefreshes = true;
        }

        $datasource = $this->getActiveDatasource();

        if (!empty($datasource->selectOne($dirName, $searchFileName, $searchExt))) {
            $result = $datasource->update($dirName, $fileName, $extension, $content, $oldFileName, $oldExtension);
        } else {
            $result = $datasource->insert($dirName, $fileName, $extension, $content);
        }

        // Refresh the cache
        $this->populateCache(true);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $dirName, string $fileName, string $extension): bool
    {
        try {
            // Delete from only the active datasource
            if ($this->forceDeleting) {
                $success = $this->getActiveDatasource()->forceDelete($dirName, $fileName, $extension);
            } else {
                $success = $this->getActiveDatasource()->delete($dirName, $fileName, $extension);
            }
        }
        catch (Exception $ex) {
            // Only attempt to do an insert-delete when not force deleting the record
            if (!$this->forceDeleting) {
                // Check to see if this is a valid path to delete
                $path = $this->makeFilePath($dirName, $fileName, $extension);

                if (in_array($path, $this->getValidPaths($dirName))) {
                    // Retrieve the current record
                    $record = $this->selectOne($dirName, $fileName, $extension);

                    // Insert the current record into the active datasource so we can mark it as deleted
                    $this->insert($dirName, $fileName, $extension, $record['content']);

                    // Perform the deletion on the newly inserted record
                    $success = $this->delete($dirName, $fileName, $extension);
                } else {
                    throw (new DeleteFileException)->setInvalidPath($path);
                }
            }
        }

        // Refresh the cache
        $this->populateCache(true);

        return $success;
    }

    /**
     * @inheritDoc
     */
    public function lastModified(string $dirName, string $fileName, string $extension): ?int
    {
        return $this->getDatasourceForPath($this->makeFilePath($dirName, $fileName, $extension))->lastModified($dirName, $fileName, $extension);
    }

    /**
     * @inheritDoc
     */
    public function makeCacheKey($name = ''): string
    {
        $key = '';

        foreach ($this->datasources as $datasource) {
            $key .= $datasource->makeCacheKey($name) . '-';
        }
        $key .= $name;

        return hash('crc32b', $key);
    }

    /**
     * @inheritDoc
     */
    public function getPathsCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * @inheritDoc
     */
    public function getAvailablePaths(): array
    {
        $paths = [];
        $datasources = array_reverse($this->datasources);
        foreach ($datasources as $datasource) {
            $paths = array_merge($paths, $datasource->getAvailablePaths());
        }
        return $paths;
    }
}
