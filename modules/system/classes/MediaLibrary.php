<?php namespace System\Classes;

use Str;
use Lang;
use Cache;
use Config;
use Storage;
use Url;
use System\Models\MediaItem;
use System\Models\Parameter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\StorageAttributes;
use Winter\Storm\Argon\Argon;
use Winter\Storm\Exception\ApplicationException ;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Filesystem\Definitions as FileDefinitions;

/**
 * Provides abstraction level for the Media Library operations.
 * Implements the library caching features and security checks.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MediaLibrary
{
    use \Winter\Storm\Support\Traits\Singleton;

    const SORT_BY_TITLE = 'title';
    const SORT_BY_SIZE = 'size';
    const SORT_BY_MODIFIED = 'modified';
    const SORT_DIRECTION_ASC = 'asc';
    const SORT_DIRECTION_DESC = 'desc';

    /**
     * @var string Cache key
     */
    protected $cacheKey = 'system-media-library-contents';

    /**
     * @var string Relative or absolute URL of the Library root folder.
     */
    protected $storagePath;

    /**
     * @var string The root Library folder path.
     */
    protected $storageFolder;

    /**
     * @var mixed A reference to the Media Library disk.
     */
    protected $storageDisk;

    /**
     * @var array Contains a list of files and directories to ignore.
     * The list can be customized with cms.storage.media.ignore configuration option.
     */
    protected $ignoreNames;

    /**
     * @var array Contains a list of regex patterns to ignore in files and directories.
     * The list can be customized with cms.storage.media.ignorePatterns configuration option.
     */
    protected $ignorePatterns;

    /**
     * @var int Cache for the storage folder name length.
     */
    protected $storageFolderNameLength;

    /**
     * @var array Scanned meta, used to compare a subsequent scan to act only on changes.
     */
    protected $scannedMeta;

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->storageFolder = self::validatePath(Config::get('cms.storage.media.folder', 'media'), true);
        $this->storagePath = rtrim(Config::get('cms.storage.media.path', '/storage/app/media'), '/');

        $this->ignoreNames = Config::get('cms.storage.media.ignore', FileDefinitions::get('ignoreFiles'));

        $this->ignorePatterns = Config::get('cms.storage.media.ignorePatterns', ['^\..*']);

        $this->storageFolderNameLength = strlen($this->storageFolder);
    }

    /**
     * Set the cache key
     *
     * @param string $cacheKey The key to set as the cache key for this instance
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    /**
     * Get the cache key
     *
     * @return string The cache key to set as the cache key for this instance
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * Returns a list of folders and files in a Library folder.
     *
     * @param string $folder Specifies the folder path relative the the Library root.
     * @param mixed $sortBy Determines the sorting preference.
     * Supported values are 'title', 'size', 'lastModified' (see SORT_BY_XXX class constants), FALSE (to disable sorting), or an associative array with a 'by' key and a 'direction' key: ['by' => SORT_BY_XXX, 'direction' => SORT_DIRECTION_XXX].
     * @param string $filter Determines the document type filtering preference.
     * Supported values are 'image', 'video', 'audio', 'document' (see FILE_TYPE_XXX constants of MediaLibraryItem class).
     * @param boolean $ignoreFolders Determines whether folders should be suppressed in the result list.
     * @return array Returns an array of MediaLibraryItem objects.
     */
    public function listFolderContents($path = '/', $sortBy = 'title', $filter = null, $ignoreFolders = false)
    {
        try {
            $folder = MediaItem::folder($path);
        } catch (ModelNotFoundException $e) {
            return [];
        }

        return $folder->contents($sortBy, $filter, $ignoreFolders);
    }

    /**
     * Finds files in the Library.
     * @param string $searchTerm Specifies the search term.
     * @param mixed $sortBy Determines the sorting preference.
     * Supported values are 'title', 'size', 'lastModified' (see SORT_BY_XXX class constants), FALSE (to disable sorting), or an associative array with a 'by' key and a 'direction' key: ['by' => SORT_BY_XXX, 'direction' => SORT_DIRECTION_XXX].
     * @param string $filter Determines the document type filtering preference.
     * Supported values are 'image', 'video', 'audio', 'document' (see FILE_TYPE_XXX constants of MediaLibraryItem class).
     * @return array Returns an array of MediaLibraryItem objects.
     */
    public function findFiles($searchTerm, $sortBy = 'title', $filter = null)
    {
        return MediaItem::getRoot()->search($searchTerm, $sortBy, $filter);
    }

    /**
     * Deletes a file from the Library.
     * @param array $paths A list of file paths relative to the Library root to delete.
     */
    public function deleteFiles($paths)
    {
        $fullPaths = [];
        foreach ($paths as $path) {
            $path = self::validatePath($path);
            $fullPaths[] = $this->getMediaPath($path);
        }

        return $this->getStorageDisk()->delete($fullPaths);
    }

    /**
     * Deletes a folder from the Library.
     * @param string $path Specifies the folder path relative to the Library root.
     */
    public function deleteFolder($path)
    {
        $path = self::validatePath($path);
        $fullPaths = $this->getMediaPath($path);

        return $this->getStorageDisk()->deleteDirectory($fullPaths);
    }

    /**
     * Determines if a file with the specified path exists in the library.
     * @param string $path Specifies the file path relative the the Library root.
     * @return boolean Returns TRUE if the file exists.
     */
    public function exists($path)
    {
        $path = self::validatePath($path);
        $fullPath = $this->getMediaPath($path);

        return $this->getStorageDisk()->exists($fullPath);
    }

    /**
     * Determines if a folder with the specified path exists in the library.
     * @param string $path Specifies the folder path relative the the Library root.
     * @return boolean Returns TRUE if the folder exists.
     */
    public function folderExists($path)
    {
        $folderName = basename($path);
        $folderPath = dirname($path);

        $path = self::validatePath($folderPath);
        $fullPath = $this->getMediaPath($path);

        $folders = $this->getStorageDisk()->directories($fullPath);
        foreach ($folders as $folder) {
            if (basename($folder) == $folderName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a list of all directories in the Library, optionally excluding some of them.
     * @param array $exclude A list of folders to exclude from the result list.
     * The folder paths should be specified relative to the Library root.
     * @return array
     */
    public function listAllDirectories($exclude = [])
    {
        return array_map(function ($item) {
            return $item->path;
        }, MediaItem::getRoot()->folders($exclude));
    }

    /**
     * Returns a file contents.
     * @param string $path Specifies the file path relative the the Library root.
     * @return string Returns the file contents
     */
    public function get($path)
    {
        $path = self::validatePath($path);
        $fullPath = $this->getMediaPath($path);
        return $this->getStorageDisk()->get($fullPath);
    }

    /**
     * Puts a file to the library.
     * @param string $path Specifies the file path relative the the Library root.
     * @param string $contents Specifies the file contents.
     * @return boolean
     */
    public function put($path, $contents)
    {
        $path = self::validatePath($path);
        $fullPath = $this->getMediaPath($path);
        return $this->getStorageDisk()->put($fullPath, $contents);
    }

    /**
     * Moves a file to another location.
     * @param string $oldPath Specifies the original path of the file.
     * @param string $newPath Specifies the new path of the file.
     * @return boolean
     */
    public function moveFile($oldPath, $newPath, $isRename = false)
    {
        $oldPath = self::validatePath($oldPath);
        $fullOldPath = $this->getMediaPath($oldPath);

        $newPath = self::validatePath($newPath);
        $fullNewPath = $this->getMediaPath($newPath);

        return $this->getStorageDisk()->move($fullOldPath, $fullNewPath);
    }

    /**
     * Copies a folder.
     * @param string $originalPath Specifies the original path of the folder.
     * @param string $newPath Specifies the new path of the folder.
     * @return boolean
     */
    public function copyFolder($originalPath, $newPath)
    {
        $disk = $this->getStorageDisk();

        $copyDirectory = function ($srcPath, $destPath) use (&$copyDirectory, $disk) {
            $srcPath = self::validatePath($srcPath);
            $fullSrcPath = $this->getMediaPath($srcPath);

            $destPath = self::validatePath($destPath);
            $fullDestPath = $this->getMediaPath($destPath);

            if (!$disk->makeDirectory($fullDestPath)) {
                return false;
            }

            $folderContents = $this->scanFolderContents($fullSrcPath);

            foreach ($folderContents['folders'] as $dirInfo) {
                if (!$copyDirectory($dirInfo->path, $destPath.'/'.basename($dirInfo->path))) {
                    return false;
                }
            }

            foreach ($folderContents['files'] as $fileInfo) {
                $fullFileSrcPath = $this->getMediaPath($fileInfo->path);

                if (!$disk->copy($fullFileSrcPath, $fullDestPath.'/'.basename($fileInfo->path))) {
                    return false;
                }
            }

            return true;
        };

        return $copyDirectory($originalPath, $newPath);
    }

    /**
     * Moves a folder.
     * @param string $originalPath Specifies the original path of the folder.
     * @param string $newPath Specifies the new path of the folder.
     * @return boolean
     */
    public function moveFolder($originalPath, $newPath)
    {
        if (Str::lower($originalPath) !== Str::lower($newPath)) {
            // If there is no risk that the directory was renamed
            // by just changing the letter case in the name -
            // copy the directory to the destination path and delete
            // the source directory.

            if (!$this->copyFolder($originalPath, $newPath)) {
                return false;
            }

            $this->deleteFolder($originalPath);
        }
        else {
            // If there's a risk that the directory name was updated
            // by changing the letter case - swap source and destination
            // using a temporary directory with random name.

            $tempraryDirPath = $this->generateRandomTmpFolderName(dirname($originalPath));

            if (!$this->copyFolder($originalPath, $tempraryDirPath)) {
                $this->deleteFolder($tempraryDirPath);

                return false;
            }

            $this->deleteFolder($originalPath);

            return $this->moveFolder($tempraryDirPath, $newPath);
        }

        return true;
    }

    /**
     * Creates a folder.
     * @param string $path Specifies the folder path.
     * @return boolean
     */
    public function makeFolder($path)
    {
        $path = self::validatePath($path);
        $fullPath = $this->getMediaPath($path);

        return $this->getStorageDisk()->makeDirectory($fullPath);
    }

    /**
     * Resets the Library cache.
     *
     * The cache stores the library table of contents locally in order to optimize
     * the performance when working with remote storages. The default cache TTL is
     * 10 minutes. The cache is deleted automatically when an item is added, changed
     * or deleted. This method allows to reset the cache forcibly.
     */
    public function resetCache()
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Checks if file path doesn't contain any substrings that would pose a security threat.
     * Throws an exception if the path is not valid.
     * @param string $path Specifies the path.
     * @param boolean $normalizeOnly Specifies if only the normalization, without validation should be performed.
     * @return string Returns a normalized path.
     */
    public static function validatePath($path, $normalizeOnly = false)
    {
        $path = str_replace('\\', '/', $path);
        $path = '/'.trim($path, '/');

        if ($normalizeOnly) {
            return $path;
        }

        /*
         * Validate folder names
         */
        $regexWhitelist = [
            '\w', // any word character
            preg_quote('@', '/'),
            preg_quote('.', '/'),
            '\s', // whitespace character
            preg_quote('-', '/'),
            preg_quote('_', '/'),
            preg_quote('/', '/'),
            preg_quote('(', '/'),
            preg_quote(')', '/'),
            preg_quote('[', '/'),
            preg_quote(']', '/'),
            preg_quote(',', '/'),
            preg_quote('=', '/'),
            preg_quote("'", '/'),
            preg_quote('&', '/'),
        ];

        if (!preg_match('/^[' . implode('', $regexWhitelist) . ']+$/iu', $path)) {
            throw new ApplicationException(Lang::get('system::lang.media.invalid_path', compact('path')));
        }

        $regexDirectorySeparator = preg_quote('/', '#');
        $regexDot = preg_quote('.', '#');
        $regex = [
            // Beginning of path
            '(^'.$regexDot.'+?'.$regexDirectorySeparator.')',

            // Middle of path
            '('.$regexDirectorySeparator.$regexDot.'+?'.$regexDirectorySeparator.')',

            // End of path
            '('.$regexDirectorySeparator.$regexDot.'+?$)',
        ];

        /*
         * Validate invalid paths
         */
        $regex = '#'.implode('|', $regex).'#';
        if (preg_match($regex, $path) !== 0 || strpos($path, '://') !== false) {
            throw new ApplicationException(Lang::get('system::lang.media.invalid_path', compact('path')));
        }

        return $path;
    }

    /**
     * Helper that makes a URL for a media file.
     * @param string $file
     * @return string
     */
    public static function url($file)
    {
        return static::instance()->getPathUrl($file);
    }

    /**
     * Returns a public file URL.
     * @param string $path Specifies the file path relative the the Library root.
     * @return string
     */
    public function getPathUrl($path)
    {
        $path = $this->validatePath($path, true);

        $fullPath = $this->storagePath . implode("/", array_map("rawurlencode", explode("/", $path)));

        if (Config::get('cms.linkPolicy') === 'force') {
            return Url::to($fullPath);
        } else {
            return $fullPath;
        }
    }

    /**
     * Scans the disk and stores all metadata in the "media_items" table for performant traversing and filtering.
     *
     * Scanning, by default,  will be done in a synchronisation fashion - only metadata that needs to be updated
     * will be updated, in order to keep subsequent scans quicker. It does this by tracking the path and the
     * modification time, however, you may opt to force a full resync using the `$forceResync` parameter.
     *
     * @param MediaItem $folder The root media folder for this iteration. If `null`, the system assumes the root.
     * @param string|null $path The root path of this folder.
     * @param bool $forceResync If `true`, a full resync is done by truncating the "media_items" table.
     *
     * @return void
     */
    public function scan(MediaItem $folder = null, $path = null, $forceResync = false)
    {
        $isRoot = is_null($folder);

        if ($isRoot) {
            if ($forceResync) {
                MediaItem::truncate();
            }

            $this->scannedMeta = MediaItem::notRoot()
                ->get()
                ->pluck('modified_at', 'path')
                ->map(function ($item) {
                    return Argon::parse($item)->getTimestamp();
                })
                ->toArray();

            $rootMedia = $this->getMediaPath('/');
            $contents = $this->getStorageDisk()->listContents($rootMedia);
            $folder = MediaItem::getRoot();
        } else {
            $contents = $this->getStorageDisk()->listContents($path);
        }

        // Filter contents so that ignored filenames and patterns are applied
        $contents = $contents->filter(function (StorageAttributes $item) {
            return $this->isVisible($item->path());
        });

        /** @var StorageAttributes $item */
        foreach ($contents as $item) {
            $mediaPath = $this->getMediaRelativePath($item['path']);

            if ($item->type() === 'dir') {
                // Determine if we are adding a new directory
                if (!isset($this->scannedMeta[$mediaPath])) {
                    $subFolder = $this->createFolderMeta($folder, $item);
                } else {
                    $subFolder = MediaItem::where('path', $mediaPath)->first();
                    unset($this->scannedMeta[$mediaPath]);
                }

                if (!is_null($subFolder)) {
                    $this->scan($subFolder, $item['path']);
                }
                continue;
            }

            if (!isset($this->scannedMeta[$mediaPath])) {
                // New file detected
                $this->createFileMeta($folder, $item);
                continue;
            } elseif ($this->scannedMeta[$mediaPath] < $item->timestamp) {
                // File was modified
                MediaItem::where('path', $mediaPath)->delete();
                $this->createFileMeta($folder, $item);
            }

            unset($this->scannedMeta[$mediaPath]);
        }

        // Any scanned meta still in the list when we have looped through the root files are now deleted
        if ($isRoot && count($this->scannedMeta)) {
            foreach (array_keys($this->scannedMeta) as $path) {
                MediaItem::where('path', $path)->delete();
            }
        }

        // Update last scan parameter
        if ($isRoot) {
            Parameter::set('media::scan.last_scanned', Argon::now());
        }
    }

    /**
     * Creates a meta record for a folder in the "media_items" table, as a subfolder of the parent folder.
     */
    protected function createFolderMeta(MediaItem $parent, DirectoryAttributes $meta): MediaItem
    {
        $path = self::validatePath($meta->path());

        return $parent->children()->create([
            'name' => basename($path),
            'path' => $this->getMediaRelativePath($path),
            'type' => MediaLibraryItem::TYPE_FOLDER,
            'size' => 0,
            'modified_at' => Argon::createFromTimestamp($meta->lastModified()),
        ]);
    }

    /**
     * Creates a meta record for a file in the "media_items" table, as a child file of the parent folder.
     */
    protected function createFileMeta(MediaItem $parent, FileAttributes $meta): MediaItem
    {
        $path = self::validatePath($meta->path());
        $path = $this->getMediaRelativePath($path);

        // Create a temporary media library item instance
        $mediaItem = new MediaLibraryItem(
            $path,
            $meta->fileSize(),
            $meta->lastModified(),
            MediaLibraryItem::TYPE_FILE,
            $this->getPathUrl($path)
        );

        // Standard metadata
        $file = $parent->children()->make([
            'name' => basename($path),
            'path' => $path,
            'type' => MediaLibraryItem::TYPE_FILE,
            'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
            'size' => $meta->fileSize(),
            'modified_at' => Argon::createFromTimestamp($meta->lastModified()),
        ]);

        // Extra metadata
        $file->file_type = $mediaItem->getFileType();

        if ($mediaItem->getFileType() === MediaLibraryItem::FILE_TYPE_IMAGE) {
            $this->setImageMeta($file, $meta->path());
        }

        $file->save();

        return $file;
    }

    /**
     * Gets meta for images.
     *
     * This scans the image for the dimensions and stores them in the meta table.
     *
     * @param MediaItem $file
     * @param string $path
     * @return void
     */
    protected function setImageMeta(MediaItem $file, $path)
    {
        $size = @getimagesizefromstring($this->getStorageDisk()->read($path));
        if (!is_array($size)) {
            return;
        }

        $file->setMetadata([
            'width' => [
                'label' => 'system::lang.media.metadata_image_width',
                'order' => 200,
                'value' => $size[0],
            ],
            'height' => [
                'label' => 'system::lang.media.metadata_image_height',
                'order' => 220,
                'value' => $size[1],
            ]
        ]);
    }

    /**
     * Returns a file or folder path with the prefixed storage folder.
     * @param string $path Specifies a path to process.
     * @return string Returns a processed string.
     */
    public function getMediaPath($path)
    {
        return $this->storageFolder.$path;
    }

    /**
     * Returns path relative to the Library root folder.
     * @param string $path Specifies a path relative to the Library disk root.
     * @return string Returns the updated path.
     */
    protected function getMediaRelativePath($path)
    {
        $path = self::validatePath($path, true);

        if (substr($path, 0, $this->storageFolderNameLength) == $this->storageFolder) {
            return substr($path, $this->storageFolderNameLength);
        }

        throw new SystemException(sprintf('Cannot convert Media Library path "%s" to a path relative to the Library root.', $path));
    }

    /**
     * Determines if the path should be visible (not ignored).
     * @param string $path Specifies a path to check.
     * @return boolean Returns TRUE if the path is visible.
     */
    protected function isVisible($path)
    {
        $baseName = basename($path);

        if (in_array($baseName, $this->ignoreNames)) {
            return false;
        }

        foreach ($this->ignorePatterns as $pattern) {
            if (preg_match('/'.$pattern.'/', $baseName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Initializes a library item from file metadata and item type.
     * @param array $item Specifies the file metadata as returned by the storage adapter.
     * @param string $itemType Specifies the item type.
     * @return mixed Returns the MediaLibraryItem object or NULL if the item is not visible.
     */
    protected function initLibraryItem($item, $itemType)
    {
        $relativePath = $this->getMediaRelativePath($item['path']);

        if (!$this->isVisible($relativePath)) {
            return;
        }

        /*
         * S3 doesn't allow getting the last modified timestamp for folders,
         * so this feature is disabled - folders timestamp is always NULL.
         */
        if ($itemType === MediaLibraryItem::TYPE_FILE) {
            $lastModified = $item['timestamp'] ?? $this->getStorageDisk()->lastModified($item['path']);
        } else {
            $lastModified = null;
        }

        /*
         * The folder size (number of items) doesn't respect filters. That
         * could be confusing for users, but that's safer than displaying
         * zero items for a folder that contains files not visible with a
         * currently applied filter. -ab
         */
        if ($itemType === MediaLibraryItem::TYPE_FILE) {
            $size = $item['size'] ?? $this->getStorageDisk()->size($item['path']);
        } else {
            $size = $this->getFolderItemCount($item['path']);
        }

        $publicUrl = $this->getPathUrl($relativePath);

        return new MediaLibraryItem($relativePath, $size, $lastModified, $itemType, $publicUrl);
    }

    /**
     * Returns a number of items on a folder.
     * @param string $path Specifies the folder path relative to the storage disk root.
     * @return integer Returns the number of items in the folder.
     */
    protected function getFolderItemCount($path)
    {
        $folderItems = array_merge(
            $this->getStorageDisk()->files($path),
            $this->getStorageDisk()->directories($path)
        );

        $size = 0;
        foreach ($folderItems as $folderItem) {
            if ($this->isVisible($folderItem)) {
                $size++;
            }
        }

        return $size;
    }

    /**
     * Fetches the contents of a folder from the Library.
     * @param string $fullFolderPath Specifies the folder path relative the the storage disk root.
     * @return array Returns an array containing two elements - 'files' and 'folders', each is an array of MediaLibraryItem objects.
     */
    protected function scanFolderContents($fullFolderPath)
    {
        $result = [
            'files' => [],
            'folders' => []
        ];

        $contents = $this->getStorageDisk()->listContents($fullFolderPath);

        foreach ($contents as $content) {
            if ($content['type'] === 'file') {
                $type = MediaLibraryItem::TYPE_FILE;
                $key = 'files';
            } elseif ($content['type'] === 'dir') {
                $type = MediaLibraryItem::TYPE_FOLDER;
                $key = 'folders';
            }

            $libraryItem = $this->initLibraryItem($content, $type);
            if (!is_null($libraryItem)) {
                $result[$key][] = $libraryItem;
            }
        }

        return $result;
    }

    /**
     * Initializes and returns the Media Library disk.
     * This method should always be used instead of trying to access the
     * $storageDisk property directly as initializing the disc requires
     * communicating with the remote storage.
     * @return \Illuminate\Contracts\Filesystem\Filesystem Returns the storage disk object.
     */
    public function getStorageDisk(): FilesystemAdapter
    {
        if ($this->storageDisk) {
            return $this->storageDisk;
        }

        return $this->storageDisk = Storage::disk(
            Config::get('cms.storage.media.disk', 'local')
        );
    }

    protected function generateRandomTmpFolderName($location)
    {
        $temporaryDirBaseName = time();

        $tmpPath = $location.'/tmp-'.$temporaryDirBaseName;

        while ($this->folderExists($tmpPath)) {
            $temporaryDirBaseName++;
            $tmpPath = $location.'/tmp-'.$temporaryDirBaseName;
        }

        return $tmpPath;
    }
}
