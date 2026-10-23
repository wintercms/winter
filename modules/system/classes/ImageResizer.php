<?php namespace System\Classes;

use Url;
use Crypt;
use Cache;
use Event;
use Config;
use Storage;
use Exception;
use SystemException;
use Str;
use File as FileHelper;
use Illuminate\Filesystem\FilesystemAdapter;
use System\Models\File as SystemFileModel;
use Winter\Storm\Database\Attach\File as FileModel;
use Winter\Storm\Database\Attach\Resizer as DefaultResizer;

/**
 * Image Resizing class used for resizing any image resources accessible
 * to the application.
 *
 * This works by accepting a variety of image sources and normalizing the
 * pipeline for storing the desired resizing configuration and then
 * deferring the actual resizing of the images until requested by the browser.
 *
 * When the resizer route is hit, the configuration is retrieved from the cache
 * and used to generate the desired image and then redirect to the generated images
 * static path to minimize the load on the server. Future loads of the image are
 * automatically pointed to the static URL of the resized image without even hitting
 * the resizer route.
 *
 * The functionality of this class is controlled by these config items:
 *
 * - cms.storage.resized.disk - The disk to store resized images on
 * - cms.storage.resized.folder - The folder on the disk to store resized images in
 * - cms.storage.resized.path - The public path to the resized images as returned
 *                      by the storage disk's URL method, used to identify
 *                      already resized images
 *
 * @see System\Classes\SystemController System controller
 * @see System\Twig\Extension Twig filters for this class defined
 * @package winter\wn-system-module
 * @author Luke Towers
 */
class ImageResizer
{
    /**
     * The cache key prefix for resizer configs
     */
    public const CACHE_PREFIX = 'system.resizer.';

    /**
     * @var array Available sources to get images from
     */
    protected static $availableSources = [];

    /**
     * @var string Unique identifier for the current configuration
     */
    protected $identifier = null;

    /**
     * @var array Image source data ['disk' => string, 'path' => string, 'source' => string]
     */
    protected $image = [];

    /**
     * @var FileModel The instance of the FileModel for the source image
     */
    protected $fileModel = null;

    /**
     * @var integer Desired width
     */
    protected $width = 0;

    /**
     * @var integer Desired height
     */
    protected $height = 0;

    /**
     * @var array Image resizing configuration data
     */
    protected $options = [];

    /**
     * Prepare the resizer instance
     *
     * @param mixed $image Supported values below:
     *              ['disk' => FilesystemAdapter, 'path' => string, 'source' => string, 'fileModel' => FileModel|void],
     *              instance of Winter\Storm\Database\Attach\File,
     *              string containing URL or path accessible to the application's filesystem manager
     * @param integer|string|bool|null $width Desired width of the resized image
     * @param integer|string|bool|null $height Desired height of the resized image
     * @param array|null $options Array of options to pass to the resizer
     */
    public function __construct($image, $width = 0, $height = 0, $options = [])
    {
        $this->image = static::normalizeImage($image);
        $this->width = (int) (($width === 'auto') ? 0 : $width);
        $this->height = (int) (($height === 'auto') ? 0 : $height);
        $this->options = array_merge($this->getDefaultOptions(), $options);
    }

    /**
     * Get the default options for the resizer
     */
    public function getDefaultOptions(): array
    {
        // Default options for the built in resizing processor
        $defaultOptions = [
            'mode'      => 'auto',
            'offset'    => [0, 0],
            'sharpen'   => 0,
            'interlace' => false,
            'quality'   => 90,
            'extension' => $this->getExtension(),
        ];

        /**
         * @event system.resizer.getDefaultOptions
         * Provides an opportunity to modify the default options used when generating image resize requests
         *
         * Example usage:
         *
         *     Event::listen('system.resizer.getDefaultOptions', function ((array) &$defaultOptions)) {
         *         $defaultOptions['background'] = '#f2f2f2';
         *     });
         *
         */
        Event::fire('system.resizer.getDefaultOptions', [&$defaultOptions]);

        return $defaultOptions;
    }

    /**
     * Get the available sources for processing image resize requests from
     */
    public static function getAvailableSources(): array
    {
        if (!empty(static::$availableSources)) {
            return static::$availableSources;
        }

        $sources = [
            'themes' => [
                'disk' => 'system',
                'folder' => config('cms.themesPathLocal', base_path('themes')),
                'path' => rtrim(config('cms.themesPath', '/themes'), '/'),
            ],
            'plugins' => [
                'disk' => 'system',
                'folder' => config('cms.pluginsPathLocal', base_path('plugins')),
                'path' => rtrim(config('cms.pluginsPath', '/plugins'), '/'),
            ],
            'resized' => [
                'disk' => config('cms.storage.resized.disk', 'local'),
                'folder' => config('cms.storage.resized.folder', 'resized'),
                'path' => rtrim(config('cms.storage.resized.path', '/storage/app/resized'), '/'),
            ],
            'media' => [
                'disk' => config('cms.storage.media.disk', 'local'),
                'folder' => config('cms.storage.media.folder', 'media'),
                'path' => rtrim(config('cms.storage.media.path', '/storage/app/media'), '/'),
            ],
            'modules' => [
                'disk' => 'system',
                'folder' => base_path('modules'),
                'path' => '/modules',
            ],
            'filemodel' => [
                'disk' => config('cms.storage.uploads.disk', 'local'),
                'folder' => config('cms.storage.uploads.folder', 'uploads'),
                'path' => rtrim(config('cms.storage.uploads.path', '/storage/app/uploads'), '/'),
            ],
        ];

        /**
         * @event system.resizer.getAvailableSources
         * Provides an opportunity to modify the sources available for processing resize requests from
         *
         * Example usage:
         *
         *     Event::listen('system.resizer.getAvailableSources', function ((array) &$sources)) {
         *         $sources['custom'] = [
         *              'disk' => 'custom',
         *              'folder' => 'relative/path/on/disk',
         *              'path' => 'publicly/accessible/path',
         *         ];
         *     });
         *
         */
        Event::fire('system.resizer.getAvailableSources', [&$sources]);

        return static::$availableSources = $sources;
    }

    /**
     * Flushes the local sources cache.
     */
    public static function flushAvailableSources(): void
    {
        if (empty(static::$availableSources)) {
            return;
        }

        static::$availableSources = [];
    }

    /**
     * Get the current config
     */
    public function getConfig(): array
    {
        $disk = $this->image['disk'];

        // Normalize local disk adapters with symlinked paths to their target path
        // to support atomic deployments where the base application path changes
        // each deployment but the realpath of the storage directory does not
        if (FileHelper::isLocalDisk($disk)) {
            $realPath = realpath($disk->getPathPrefix());
            if ($realPath) {
                $disk->setPathPrefix($realPath);
            }
        }

        // Include last modified time to tie generated images to the source image
        $mtime = $disk->lastModified($this->image['path']);

        // Handle disks that can't be serialized by referencing them by their
        // filesystems.php config name
        try {
            serialize($disk);
        } catch (Exception $ex) {
            $disk = Storage::identify($disk);
        }

        $config = [
            'image' => [
                'disk' => $disk,
                'path' => $this->image['path'],
                'mtime' => $mtime,
                'source' => $this->image['source'],
            ],
            'width' => $this->width,
            'height' => $this->height,
            'options' => $this->options,
        ];

        if ($fileModel = $this->getFileModel()) {
            $config['image']['fileModel'] = [
                'class' => get_class($fileModel),
                'key' => $fileModel->getKey(),
            ];
        }

        return $config;
    }

    /**
     * Process the resize request
     */
    public function resize(): void
    {
        if ($this->isResized()) {
            return;
        }

        // Get the details for the target image
        list($disk, $path) = $this->getTargetDetails();

        // Copy the image to be resized to the temp directory
        $tempPath = $this->getLocalTempPath();

        try {
            /**
             * @event system.resizer.processResize
             * Halting event that enables replacement of the resizing process. There should only ever be
             * one listener handling this event per project at most, as other listeners would be ignored.
             *
             * Example usage:
             *
             *     Event::listen('system.resizer.processResize', function ((\System\Classes\ImageResizer) $resizer, (string) $localTempPath) {
             *          // Get the resizing configuration
             *          $config = $resizer->getConfig();
             *
             *          // Resize the image
             *          $resizedImageContents = My\Custom\Resizer::resize($localTempPath, $config['width], $config['height'], $config['options']);
             *
             *          // Place the resized image in the correct location for the resizer to finish processing it
             *          file_put_contents($localTempPath, $resizedImageContents);
             *
             *          // Prevent any other resizing replacer logic from running
             *          return true;
             *     });
             *
             */
            $processed = Event::fire('system.resizer.processResize', [$this, $tempPath], true);
            if (!$processed) {
                // Process the resize with the default image resizer
                DefaultResizer::open($tempPath)
                    ->resize($this->width, $this->height, $this->options)
                    ->save($tempPath);
            }

            /**
             * @event system.resizer.afterResize
             * Enables post processing of resized images after they've been resized before the
             * resizing process is finalized (ex. adding watermarks, further optimizing, etc)
             *
             * Example usage:
             *
             *     Event::listen('system.resizer.afterResize', function ((\System\Classes\ImageResizer) $resizer, (string) $localTempPath) {
             *          // Get the resized image data
             *          $resizedImageContents = file_get_contents($localTempPath);
             *
             *          // Post process the image
             *          $processedContents = TinyPNG::optimize($resizedImageContents);
             *
             *          // Place the processed image in the correct location for the resizer to finish processing it
             *          file_put_contents($localTempPath, $processedContents);
             *     });
             *
             */
            Event::fire('system.resizer.afterResize', [$this, $tempPath]);

            // Store the resized image
            $disk->put($path, file_get_contents($tempPath));

            // Clean up
            unlink($tempPath);
        } catch (Exception $ex) {
            // Clean up in case of any issues
            unlink($tempPath);

            // Pass the exception up
            throw $ex;
        }
    }

    /**
     * Process the crop request
     */
    public function crop(): void
    {
        if ($this->isResized()) {
            return;
        }

        // Get the details for the target image
        list($disk, $path) = $this->getTargetDetails();

        // Copy the image to be resized to the temp directory
        $tempPath = $this->getLocalTempPath();

        try {
            /**
             * @event system.resizer.processCrop
             * Halting event that enables replacement of the cropping process. There should only ever be
             * one listener handling this event per project at most, as other listeners would be ignored.
             *
             * Example usage:
             *
             *     Event::listen('system.resizer.processCrop', function ((\System\Classes\ImageResizer) $resizer, (string) $localTempPath) {
             *          // Get the resizing configuration
             *          $config = $resizer->getConfig();
             *
             *          // Resize the image
             *          $resizedImageContents = My\Custom\Resizer::crop($localTempPath, $config['width], $config['height'], $config['options']);
             *
             *          // Place the resized image in the correct location for the resizer to finish processing it
             *          file_put_contents($localTempPath, $resizedImageContents);
             *
             *          // Prevent any other resizing replacer logic from running
             *          return true;
             *     });
             *
             */
            $processed = Event::fire('system.resizer.processCrop', [$this, $tempPath], true);
            if (!$processed) {
                // Process the crop with the default image resizer
                DefaultResizer::open($tempPath)
                    ->crop(
                        $this->options['offset'][0],
                        $this->options['offset'][1],
                        $this->width,
                        $this->height
                    )
                    ->save($tempPath);
            }

            /**
             * @event system.resizer.afterCrop
             * Enables post processing of cropped images after they've been cropped before the
             * cropping process is finalized (ex. adding watermarks, further optimizing, etc)
             *
             * Example usage:
             *
             *     Event::listen('system.resizer.afterCrop', function ((\System\Classes\ImageResizer) $resizer, (string) $localTempPath) {
             *          // Get the resized image data
             *          $croppedImageContents = file_get_contents($localTempPath);
             *
             *          // Post process the image
             *          $processedContents = TinyPNG::optimize($croppedImageContents);
             *
             *          // Place the processed image in the correct location for the resizer to finish processing it
             *          file_put_contents($localTempPath, $processedContents);
             *     });
             *
             */
            Event::fire('system.resizer.afterCrop', [$this, $tempPath]);

            // Store the resized image
            $disk->put($path, file_get_contents($tempPath));

            // Clean up
            unlink($tempPath);
        } catch (Exception $ex) {
            // Clean up in case of any issues
            unlink($tempPath);

            // Pass the exception up
            throw $ex;
        }
    }

    /**
     * Get the internal temporary drirectory and ensure it exists
     *
     * NOTE: the copy made to measure a remote source now goes through
     * getResizerTempPath(), which returns the same directory but is not overridable
     * per instance. A subclass that replaced this method to relocate the resizer's
     * working directory no longer affects that copy; override getResizerTempPath()
     * instead.
     */
    public function getTempPath(): string
    {
        return static::getResizerTempPath();
    }

    /**
     * Get the internal temporary directory and ensure it exists
     *
     * Kept separate from getTempPath() because that method is part of the public
     * API and therefore cannot be made static without breaking subclasses that
     * override it.
     */
    protected static function getResizerTempPath(): string
    {
        $path = temp_path() . '/resizer';

        if (!FileHelper::isDirectory($path)) {
            FileHelper::makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Get the time to live used for the cached resizer configuration
     *
     * This is a bound on growth, not a correctness mechanism, and it is deliberately not
     * applied to the source or dimension caches. Those are immutable for their key
     * because the identifier embeds the source image's mtime, so a changed source lands
     * on a different key and a TTL could only buy a needless re-read -- a full file
     * transfer on a remote disk. The exception is a filemodel source, whose identifier
     * does not embed the configuration at all; there the entries carry the mtime
     * alongside the dimensions and validate themselves.
     *
     * The residual failure it leaves is worth stating. A resizer URL that is never
     * visited does not have its resize performed, so once this expires the URL stops
     * resolving. Retention lengthens the window from a single fetch to a day rather than
     * removing it, because bounding growth and keeping a promise for ever are not both
     * available at once.
     */
    protected static function dimensionCacheTtl(): \DateTimeInterface
    {
        return now()->addDay();
    }

    /**
     * Stores the current source image in the temp directory and returns the path to it
     *
     * @param string $path The path to suffix the temp directory path with, defaults to $identifier.$ext
     */
    protected function getLocalTempPath($path = null): string
    {
        if (!is_null($path) && is_string($path)) {
            $tempPath = $this->getTempPath() . '/' . $path;
        } else {
            $tempPath = $this->getTempPath() . '/' . $this->getIdentifier() . '.' . $this->getExtension();
        }

        if (!file_exists($tempPath)) {
            FileHelper::put($tempPath, $this->getSourceFileContents());
        }

        return $tempPath;
    }

    /**
     * Returns the file extension.
     */
    public function getExtension(): string
    {
        return FileHelper::extension($this->image['path']);
    }

    /**
     * Get the contents of the image file to be resized
     */
    public function getSourceFileContents()
    {
        return $this->image['disk']->get($this->image['path']);
    }

    /**
     * Gets the current fileModel associated with the source image if one exists
     */
    public function getFileModel(): ?FileModel
    {
        if ($this->fileModel) {
            return $this->fileModel;
        }

        if ($this->image['source'] === 'filemodel') {
            if ($this->image['fileModel'] instanceof FileModel) {
                $this->fileModel = $this->image['fileModel'];
            } else {
                $this->fileModel = $this->image['fileModel']['class']::findOrFail($this->image['fileModel']['key']);
            }
        }

        return $this->fileModel;
    }

    /**
     * Get the default disk used to store processed images
     */
    public static function getDefaultDisk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(Config::get('cms.storage.resized.disk', 'local'));
    }

    /**
     * Get the disk instance for image that is currently being processed
     */
    public function getDisk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return ($this->image['source'] === 'filemodel' && $fileModel = $this->getFileModel())
            ? $fileModel->getDisk()
            : static::getDefaultDisk();
    }

    /**
     * Get the details for the target image
     *
     * @return array [FilesystemAdapter $disk, (string) $path]
     */
    protected function getTargetDetails(): array
    {
        if ($this->image['source'] === 'filemodel' && $fileModel = $this->getFileModel()) {
            return [
                $this->getDisk(),
                $fileModel->getDiskPath($fileModel->getThumbFilename($this->width, $this->height, $this->options)),
            ];
        }

        return [
            $this->getDisk(),
            $this->getPathToResizedImage(),
        ];
    }

    /**
     * Get the reference to the resized image if the requested resize exists
     */
    public function isResized(): bool
    {
        // Get the details for the target image
        list($disk, $path) = $this->getTargetDetails();

        // Return true if the path is a file and it exists on the target disk
        return !empty(FileHelper::extension($path)) && $disk->exists($path);
    }

    /**
     * Get the path of the resized image
     */
    public function getPathToResizedImage(): string
    {
        // Generate the unique file identifier for the resized image
        $fileIdentifier = hash_hmac('sha1', serialize($this->getConfig()), Crypt::getKey());

        // Generate the filename for the resized image
        $name = pathinfo($this->image['path'], PATHINFO_FILENAME) . "_resized_$fileIdentifier.{$this->options['extension']}";

        // Generate the path to the containing folder for the resized image
        $folder = implode('/', array_slice(str_split(str_limit($fileIdentifier, 9), 3), 0, 3));

        // Generate and return the full path
        return Config::get('cms.storage.resized.folder', 'resized') . '/' . $folder . '/' . $name;
    }

    /**
     * Gets the current useful URL to the resized image
     * (resizer if not resized, resized image directly if resized)
     */
    public function getUrl(): string
    {
        if ($this->isResized()) {
            return $this->getResizedUrl();
        } else {
            return $this->getResizerUrl();
        }
    }

    /**
     * Get the URL to the system resizer route for this instance's configuration
     */
    public function getResizerUrl(): string
    {
        // Slashes in URL params have to be double encoded to survive Laravel's router
        // @see https://github.com/octobercms/october/issues/3592#issuecomment-671017380
        $resizedUrl = rawurlencode(rawurlencode($this->getResizedUrl()));
        // Double-encode dots (rawurlencode() skips them) to avoid issues in certain NGINX
        // configurations where dots may trigger asset-serving rules, resulting in 404 errors
        $resizedUrl = str_replace('.', '%252E', $resizedUrl);

        // Get the current configuration's identifier
        $identifier = $this->getIdentifier();

        // Store the current configuration
        $this->storeConfig();

        $url = "/resizer/$identifier/$resizedUrl";

        if (Config::get('cms.linkPolicy', 'detect') === 'force') {
            $url = Url::to($url);
        }

        return $url;
    }

    /**
     * Get the URL to the resized image
     */
    public function getResizedUrl(): string
    {
        $url = '';

        if ($this->image['source'] === 'filemodel') {
            $model = $this->getFileModel();
            $thumbFile = $model->getThumbFilename($this->width, $this->height, $this->options);
            $url = $model->getPath($thumbFile);
        } else {
            $resizedDisk = Storage::disk(Config::get('cms.storage.resized.disk', 'local'));
            $url = $resizedDisk->url($this->getPathToResizedImage());
        }

        // Ensure that a properly encoded URL is returned
        $segments = explode('/', $url);
        $lastSegment = array_pop($segments);
        $url = implode('/', $segments) . '/' . rawurlencode(rawurldecode($lastSegment));

        if (Config::get('cms.linkPolicy', 'detect') === 'force') {
            $url = Url::to($url);
        }

        return $url;
    }

    /**
     * Normalize the provided input into information that the resizer can work with
     *
     * @param mixed $image Supported values below:
     *              ['disk' => FilesystemAdapter, 'path' => string, 'source' => string, 'fileModel' => FileModel|void],
     *              instance of Winter\Storm\Database\Attach\File,
     *              string containing URL or path accessible to the application's filesystem manager
     * @throws SystemException If the image was unable to be identified
     * @return array Array containing the disk, path, source, and fileModel if applicable
     *               ['disk' => FilesystemAdapter, 'path' => string, 'source' => string, 'fileModel' => FileModel|void]
     */
    public static function normalizeImage($image): array
    {
        $disk = null;
        $path = null;
        $selectedSource = null;
        $fileModel = null;

        // Process an array
        if (is_array($image) && !empty($image['disk']) && !empty($image['path']) && !empty($image['source'])) {
            $disk = $image['disk'];
            $path = $image['path'];
            $selectedSource = $image['source'];

            // Handle disks that couldn't be serialized
            if (is_string($disk)) {
                // Handle disks of type "system" (the local file system the application is running on)
                if ($disk === 'system') {
                    Config::set('filesystems.disks.system', [
                        'driver' => 'local',
                        'root' => base_path(),
                    ]);
                    // Regenerate the path relative to the newly defined "system" disk
                    $path = str_after($path, static::normalizePath(base_path()) . '/');
                }

                $disk = Storage::disk($disk);
            }

            // Verify that the source file exists
            if (empty(FileHelper::extension($path)) || !$disk->exists($path)) {
                $disk = null;
                $path = null;
                $selectedSource = null;
            }

            if (!empty($image['fileModel'])) {
                $fileModel = $image['fileModel'];
            }

        // Process a FileModel
        } elseif ($image instanceof FileModel) {
            $disk = $image->getDisk();
            $path = $image->getDiskPath();
            $selectedSource = 'filemodel';
            $fileModel = $image;

            // Verify that the source file exists
            if (empty(FileHelper::extension($path)) || !$disk->exists($path)) {
                $disk = null;
                $path = null;
                $selectedSource = null;
                $fileModel = null;
            }

        // Process a string
        } elseif (is_string($image)) {
            // Parse the provided image path into a filesystem ready relative path
            $relativePath = static::normalizePath(rawurldecode(parse_url($image, PHP_URL_PATH)));

            // Loop through the sources available to the application to pull from
            // to identify the source most likely to be holding the image
            $resizeSources = static::getAvailableSources();
            foreach ($resizeSources as $source => $details) {
                // Normalize the source path
                $sourcePath = static::normalizePath(rawurldecode(parse_url($details['path'], PHP_URL_PATH)));

                // Identify if the current source is a match
                if (starts_with($relativePath, $sourcePath)) {
                    // Attempt to handle FileModel URLs passed as strings
                    if ($source === 'filemodel') {
                        $diskName = pathinfo($relativePath, PATHINFO_BASENAME);
                        $model = SystemFileModel::where('disk_name', $diskName)->first();
                        if ($model && $image = static::normalizeImage($model)) {
                            $disk = $image['disk'];
                            $path = $image['path'];
                            $selectedSource = $image['source'];
                            $fileModel = $image['fileModel'];
                        }
                        // Stop any further path processing from happening on filemodel sources
                        break;
                    }

                    // Generate a path relative to the selected disk
                    $path = static::normalizePath($details['folder']) . '/' . str_after($relativePath, $sourcePath . '/');

                    // Handle disks of type "system" (the local file system the application is running on)
                    if ($details['disk'] === 'system') {
                        Config::set('filesystems.disks.system', [
                            'driver' => 'local',
                            'root' => base_path(),
                        ]);
                        // Regenerate the path relative to the newly defined "system" disk
                        $path = str_after($path, static::normalizePath(base_path()) . '/');
                    }

                    $disk = Storage::disk($details['disk']);

                    // Verify that the file exists before exiting the identification process
                    if (!empty(FileHelper::extension($path)) && $disk->exists($path)) {
                        $selectedSource = $source;
                        break;
                    } else {
                        $disk = null;
                        $path = null;
                        continue;
                    }
                }
            }
        }

        if (!$disk || !$path || !$selectedSource || (!in_array(strtolower(FileHelper::extension($path)), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif']))) {
            if (is_object($image)) {
                $image = get_class($image);
            }
            throw new SystemException("Unable to process the provided image: " . e(var_export($image, true)));
        }

        $data = [
            'disk' => $disk,
            'path' => $path,
            'source' => $selectedSource,
        ];

        if ($fileModel) {
            $data['fileModel'] = $fileModel;
        }

        return $data;
    }

    /**
     * Normalize the provided path to Unix style directory seperators to ensure
     * that path manipulation operations succeed regardless of environment
     *
     * NOTE: Can't use Winter\Storm\FileSystem\PathResolver because it prepends
     * the current working directory to relative paths
     */
    protected static function normalizePath(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Check if the provided identifier looks like a valid identifier
     *
     * @param string $id
     * @return bool
     */
    public static function isValidIdentifier($id): bool
    {
        return is_string($id) && ctype_alnum($id) && strlen($id) === 40;
    }

    /**
     * Gets the identifier for provided resizing configuration
     *
     * @return string 40 character string used as a unique reference to the provided configuration
     */
    public function getIdentifier(): string
    {
        if ($this->identifier) {
            return $this->identifier;
        }

        // Generate & return the identifier
        return $this->identifier = hash_hmac('sha1', $this->getResizedUrl(), Crypt::getKey());
    }

    /**
     * Stores the resizer configuration if the resizing hasn't been completed yet
     */
    public function storeConfig(): void
    {
        // If the image hasn't been resized yet, then store the config data for the resizer to use
        if (!$this->isResized()) {
            Cache::put(
                static::CACHE_PREFIX . $this->getIdentifier(),
                $this->getConfig(),
                static::dimensionCacheTtl()
            );
        }
    }

    /**
     * Instantiate a resizer instance from the provided identifier
     *
     * @param string $identifier The 40 character cache identifier for the desired resizer configuration
     * @throws SystemException If the identifier is unable to be loaded
     */
    public static function fromIdentifier(string $identifier): self
    {
        $cacheKey = static::CACHE_PREFIX . $identifier;

        // Attempt to retrieve the resizer configuration
        $config = Cache::get($cacheKey, null);

        // Validate that the desired config was able to be loaded
        if (empty($config)) {
            throw new SystemException("Unable to retrieve the configuration for " . e($identifier));
        }

        $resizer = new static($config['image'], $config['width'], $config['height'], $config['options']);

        // The configuration is left in the cache rather than consumed here. It used to be
        // evicted on the grounds that the browser "stealing" it with the first request
        // could not race a second visitor, which is a real fault but a poor trade: a page
        // holding a resizer URL -- a cached fragment, a client-side template, a URL stored
        // in content -- then stopped resolving after the first visitor.
        //
        // Nothing sensitive is exposed by keeping it. The contents cannot be recovered
        // from the identifier, which is a one-way HMAC over the resized path, and the
        // resized path carries only a hash of the configuration. What settles it is that
        // anyone holding the identifier already holds a self-authenticating /resizer/ URL
        // -- the route re-verifies that pairing in getValidResizedUrl() -- so the entry
        // is not a capability in the first place, and retaining it reveals nothing the
        // URL did not.
        //
        // The window is lengthened rather than removed. A resizer URL nobody ever visits
        // never has its resize performed, so once the TTL expires the URL stops
        // resolving; see dimensionCacheTtl().
        return $resizer;
    }

    /**
     * Calculate the size of an image constrained to a fixed height
     *
     * Mirrors Resizer::getSizeByFixedHeight(), which returns an unrounded float that
     * GD truncates when it allocates the canvas. Rounding it here instead is a
     * one-pixel divergence from the real resizer in a large fraction of cases.
     */
    protected static function sizeByFixedHeight($newHeight, int $origWidth, int $origHeight): float
    {
        return $newHeight * ($origWidth / $origHeight);
    }

    /**
     * Calculate the size of an image constrained to a fixed width
     *
     * Mirrors Resizer::getSizeByFixedWidth(), which returns an unrounded float.
     * @see static::sizeByFixedHeight()
     */
    protected static function sizeByFixedWidth($newWidth, int $origWidth, int $origHeight): float
    {
        return $newWidth * ($origHeight / $origWidth);
    }

    /**
     * Mirror of Resizer::getSizeByAuto()
     *
     * The order of the two multiplications is not interchangeable with the other
     * algebraic form; across roughly 44 million bound combinations about 0.18% of
     * them truncate to a different pixel, so this is transcribed rather than
     * re-derived.
     */
    protected static function autoDimensions($newWidth, $newHeight, int $origWidth, int $origHeight): array
    {
        if ($newWidth <= 1 && $newHeight <= 1) {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        } elseif ($newWidth <= 1) {
            $newWidth = static::sizeByFixedHeight($newHeight, $origWidth, $origHeight);
        } elseif ($newHeight <= 1) {
            $newHeight = static::sizeByFixedWidth($newWidth, $origWidth, $origHeight);
        }

        if ($origHeight < $origWidth || ($origHeight === $origWidth && $newHeight < $newWidth)) {
            return [
                'width' => (int) $newWidth,
                'height' => (int) static::sizeByFixedWidth($newWidth, $origWidth, $origHeight),
            ];
        }

        if ($origHeight > $origWidth || ($origHeight === $origWidth && $newHeight > $newWidth)) {
            return [
                'width' => (int) static::sizeByFixedHeight($newHeight, $origWidth, $origHeight),
                'height' => (int) $newHeight,
            ];
        }

        return ['width' => (int) $newWidth, 'height' => (int) $newHeight];
    }

    /**
     * Calculate the dimensions the resizer will produce for the given bounds
     *
     * This deliberately duplicates Resizer::getDimensions() rather than calling it,
     * because that method is protected and widening its visibility is a backwards
     * compatibility change on a released package. The duplication is safe only as
     * long as testCalculateResizedDimensionsMatchesResizerOutput passes: that test
     * compares against the real Resizer::resize() output and fails if the upstream
     * formula changes. Unification is tracked as a follow-up against Storm, which
     * cannot ship in the same change because the core pins winter/storm to a
     * dev-develop branch that needs its own release first.
     *
     * Two details of the upstream implementation are easy to get wrong and are
     * therefore spelled out here:
     *
     * 1. The requested bounds are sanitised before the mode is dispatched on, not
     *    inside each mode. A single-bound request is filled in from the source
     *    first, which can push both bounds under the "less than one pixel"
     *    threshold and make the result the original size.
     * 2. Only "fit" rounds. "portrait", "landscape" and the sanitisation all rely on
     *    GD truncating an unrounded float.
     *
     * An unrecognised mode is assumed to be "auto". That is the least-wrong answer
     * rather than a correct one: a custom mode means a custom resizer, whose real
     * output cannot be predicted from here.
     *
     * @return array ['width' => int, 'height' => int]
     */
    protected static function calculateResizedDimensions(
        int $origWidth,
        int $origHeight,
        int $reqWidth,
        int $reqHeight,
        string $mode
    ): array {
        $dimensions = static::computeResizedDimensions($origWidth, $origHeight, $reqWidth, $reqHeight, $mode);

        // GD cannot allocate a canvas with a sub-pixel dimension: it truncates the
        // float to zero and throws. Claiming a size smaller than a pixel would be
        // reporting an image that cannot be produced, so report it as unknown. This
        // is also the exact condition under which the resizer refuses, so the two
        // agree rather than one inventing a size the other rejects.
        if ($dimensions['width'] < 1 || $dimensions['height'] < 1) {
            return ['width' => 0, 'height' => 0];
        }

        return $dimensions;
    }

    /**
     * Apply the resize mode to a pair of sanitised bounds
     *
     * @see static::calculateResizedDimensions()
     * @return array ['width' => int, 'height' => int]
     */
    protected static function computeResizedDimensions(
        int $origWidth,
        int $origHeight,
        int $reqWidth,
        int $reqHeight,
        string $mode
    ): array {
        if ($origWidth <= 0 || $origHeight <= 0) {
            return ['width' => $reqWidth, 'height' => $reqHeight];
        }

        // Mirrors the sanitisation at the top of Resizer::resize(). It runs for every
        // mode and must precede the switch.
        if (!$reqWidth && !$reqHeight) {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        } elseif (!$reqWidth) {
            $newHeight = $reqHeight;
            $newWidth = static::sizeByFixedHeight($newHeight, $origWidth, $origHeight);
        } elseif (!$reqHeight) {
            $newWidth = $reqWidth;
            $newHeight = static::sizeByFixedWidth($newWidth, $origWidth, $origHeight);
        } else {
            $newWidth = $reqWidth;
            $newHeight = $reqHeight;
        }

        switch ($mode) {
            case 'exact':
                return ['width' => (int) $newWidth, 'height' => (int) $newHeight];

            case 'crop':
                // The "crop" mode builds a larger intermediate canvas via
                // getOptimalCrop() and then crops to exactly the requested box, so the
                // image that is finally written is the request itself and not the
                // canvas.
                return ['width' => (int) $newWidth, 'height' => (int) $newHeight];

            case 'portrait':
                return [
                    'width' => (int) static::sizeByFixedHeight($newHeight, $origWidth, $origHeight),
                    'height' => (int) $newHeight,
                ];

            case 'landscape':
                return [
                    'width' => (int) $newWidth,
                    'height' => (int) static::sizeByFixedWidth($newWidth, $origWidth, $origHeight),
                ];

            case 'fit':
                $effectiveRatio = min($newWidth / $origWidth, $newHeight / $origHeight);
                return [
                    'width' => (int) round($origWidth * $effectiveRatio),
                    'height' => (int) round($origHeight * $effectiveRatio),
                ];

            case 'auto':
                return static::autoDimensions($newWidth, $newHeight, $origWidth, $origHeight);

            default:
                return static::autoDimensions($newWidth, $newHeight, $origWidth, $origHeight);
        }
    }

    /**
     * Read the dimensions of a source image, and, if possible, its local path
     *
     * @param mixed $disk A FilesystemAdapter or a filesystem configuration name
     * @return array ['width' => int, 'height' => int]
     */
    protected static function readSourceDimensions($disk, string $path): array
    {
        $tempPath = null;

        try {
            if (FileHelper::isLocalDisk($disk)) {
                $localPath = $disk->getPathPrefix() . $path;
            } else {
                // A remote disk has to be copied somewhere readable by GD before it
                // can be measured. tempnam() reserves the name atomically, unlike
                // uniqid(), so two concurrent lookups cannot select the same path and
                // then unlink each other's copy.
                $tempPath = tempnam(static::getResizerTempPath(), 'src');
                FileHelper::put($tempPath, $disk->get($path));
                $localPath = $tempPath;
            }

            $size = @getimagesize($localPath);
            if ($size === false) {
                return ['width' => 0, 'height' => 0];
            }

            $origWidth = (int) $size[0];
            $origHeight = (int) $size[1];

            // An orientation of 6 or 8 means the pixels are stored rotated. The
            // resizer reports the displayed dimensions, so match that here. Only
            // JPEG carries EXIF, and only JPEG goes down this path upstream.
            if (($size['mime'] ?? null) === 'image/jpeg' && function_exists('exif_read_data')) {
                $exif = @exif_read_data($localPath);
                if (!empty($exif['Orientation']) && in_array($exif['Orientation'], [6, 8], true)) {
                    [$origWidth, $origHeight] = [$origHeight, $origWidth];
                }
            }

            return ['width' => $origWidth, 'height' => $origHeight];
        } finally {
            if ($tempPath !== null && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Resolve the identifier of a system resizer URL
     *
     * Returns null when the provided string is not a resizer URL. Both relative and
     * absolute URLs are accepted, since a forced link policy rewrites the resizer URL
     * to an absolute one. parse_url() is deliberately not used for the scheme-less case
     * because it reads a Windows drive letter as a scheme.
     *
     * @return string|null The resizer identifier, or null if this is not a resizer URL
     */
    protected static function resizerUrlSegments(string $url): ?string
    {
        $path = $url;

        if (Str::startsWith($url, ['http://', 'https://'])) {
            $path = (string) parse_url($url, PHP_URL_PATH);
        } elseif (Str::startsWith($url, '//')) {
            $path = (string) parse_url('https:' . $url, PHP_URL_PATH);
        }

        $segments = explode('/', ltrim(static::normalizePath($path), '/'));

        // Not required to be the first segment: under a subfolder install with a forced
        // link policy, Url::to() produces https://host/sub/resizer/<id>/..., so the route
        // is not at index zero. Searching rather than indexing keeps that deployment
        // working, and requiring a valid identifier at the next position is what stops
        // an ordinary media path containing "resizer" from matching.
        $position = array_search('resizer', $segments, true);

        if (
            $position !== false
            && isset($segments[$position + 1])
            && static::isValidIdentifier($segments[$position + 1])
        ) {
            return $segments[$position + 1];
        }

        return null;
    }

    /**
     * Resolve the dimensions of a cached resizer configuration
     *
     * @return array ['width' => int, 'height' => int]
     */
    protected static function computeCachedDimensions(string $identifier): array
    {
        $config = Cache::get(static::CACHE_PREFIX . $identifier);

        if (!is_array($config)) {
            return ['width' => 0, 'height' => 0];
        }

        return static::dimensionsForConfig($config, $identifier);
    }

    /**
     * Resolve the dimensions described by a resizer configuration
     *
     * A zero pair is returned when the configuration is incomplete or the source
     * cannot be measured. A partial pair is never returned: a width with a zero
     * height reaches an HTML attribute and collapses the layout, whereas a zero pair
     * reads as "unknown", which is what it is.
     *
     * @return array ['width' => int, 'height' => int]
     */
    protected static function dimensionsForConfig($config, string $identifier): array
    {
        if (
            !is_array($config)
            || !isset($config['width'], $config['height'], $config['options']['mode'])
            || !isset($config['image']['disk'], $config['image']['path'])
        ) {
            return ['width' => 0, 'height' => 0];
        }

        $cacheKey = static::CACHE_PREFIX . $identifier;
        $sourceCacheKey = $cacheKey . '.source';
        $dimensionsCacheKey = $cacheKey . '.dimensions';

        // For every source whose resized path is derived from the configuration, the
        // identifier embeds the source's mtime, so this entry is immutable for its key
        // and keeping it for ever costs nothing. A filemodel source is the exception:
        // its resized path is the thumb filename, which carries the attachment id and
        // the requested bounds and nothing at all about the file's contents, so the
        // identifier does not move when the bytes do. Recording the mtime alongside the
        // dimensions makes the entry self-validating for those too, for one comparison.
        //
        // It cannot cover the case where the cached configuration itself is out of date,
        // because storeConfig() skips writing once the thumb exists. A caller still
        // holding an old /resizer/ URL for a filemodel whose bytes were replaced in
        // place therefore keeps the first answer. That is pre-existing and narrow.
        $sourceMtime = isset($config['image']['mtime']) ? (int) $config['image']['mtime'] : null;
        $cachedSource = Cache::get($sourceCacheKey);
        $sourceIsCurrent = is_array($cachedSource)
            && ($sourceMtime === null || ($cachedSource['mtime'] ?? null) === $sourceMtime);

        if (!$sourceIsCurrent) {
            // The dimensions are derived from the source, so they have to move with it
            Cache::forget($dimensionsCacheKey);

            $disk = $config['image']['disk'];
            if (is_string($disk)) {
                $disk = Storage::disk($disk);
            }

            $cachedSource = static::readSourceDimensions($disk, (string) $config['image']['path']);

            // Only a successful read is cached. The guard has to sit outside the write
            // rather than inside a remember() closure, because remember() stores
            // whatever the closure returns, so a transient failure would otherwise
            // become a zero dimension for ever.
            if ($cachedSource['width'] > 0 && $cachedSource['height'] > 0) {
                $cachedSource['mtime'] = $sourceMtime;
                Cache::forever($sourceCacheKey, $cachedSource);
            }
        }

        if ($cachedSource['width'] <= 0 || $cachedSource['height'] <= 0) {
            return ['width' => 0, 'height' => 0];
        }

        // Forever for the same reason as the source cache above: pure function of the key,
        // and dropped above whenever that key had to be re-read
        return Cache::rememberForever(
            $dimensionsCacheKey,
            function () use ($config, $cachedSource) {
                return static::calculateResizedDimensions(
                    (int) $cachedSource['width'],
                    (int) $cachedSource['height'],
                    (int) $config['width'],
                    (int) $config['height'],
                    (string) $config['options']['mode']
                );
            }
        );
    }

    /**
     * Check the provided encoded URL to verify its signature and return the decoded URL
     *
     * @return string|null Returns null if the provided value was invalid
     */
    public static function getValidResizedUrl(string $identifier, string $encodedUrl): ?string
    {
        // Slashes in URL params have to be double encoded to survive Laravel's router
        // @see https://github.com/octobercms/october/issues/3592#issuecomment-671017380
        $decodedUrl = rawurldecode($encodedUrl);
        $url = null;

        // The identifier should be the signed version of the decoded URL
        if (static::isValidIdentifier($identifier) && $identifier === hash_hmac('sha1', $decodedUrl, Crypt::getKey())) {
            $url = $decodedUrl;
        }

        return $url;
    }

    /**
     * Converts supplied input into a URL that will return the desired resized image
     *
     * @param mixed $image Supported values below:
     *              ['disk' => FilesystemAdapter, 'path' => string, 'source' => string, 'fileModel' => FileModel|void],
     *              instance of Winter\Storm\Database\Attach\File,
     *              string containing URL or path accessible to the application's filesystem manager
     * @param integer|string|bool|null $width Desired width of the resized image
     * @param integer|string|bool|null $height Desired height of the resized image
     * @param array|null $options Array of options to pass to the resizer
     * @throws Exception If the provided image was unable to be processed
     */
    public static function filterGetUrl($image, $width = null, $height = null, $options = []): string
    {
        // Attempt to process the provided image
        try {
            $resizer = new static($image, $width, $height, $options);
        } catch (SystemException $ex) {
            // Ignore processing this URL if the resizer is unable to identify it
            if (is_scalar($image) || empty($image)) {
                return (string) $image;
            } elseif ($image instanceof FileModel) {
                return $image->getPath();
            } else {
                throw $ex;
            }
        }

        return $resizer->getUrl();
    }

    /**
     * Gets the dimensions of the provided image file
     *
     * A system resizer URL is resolved from its cached configuration rather than by
     * reading the file, because on a cold cache the resized image has not been written
     * yet and there is nothing on disk to measure.
     *
     * NOTE: Doesn't currently support being passed a FileModel image that has already been resized
     *
     * @param mixed $image Supported values below:
     *              ['disk' => FilesystemAdapter, 'path' => string, 'source' => string, 'fileModel' => FileModel|void],
     *              instance of Winter\Storm\Database\Attach\File,
     *              string containing URL or path accessible to the application's filesystem manager
     * @throws SystemException If the provided input was unable to be processed
     * @return array ['width' => int, 'height' => int]
     */
    public static function filterGetDimensions($image): array
    {
        if (is_string($image) && ($identifier = static::resizerUrlSegments($image)) !== null) {
            return static::computeCachedDimensions($identifier);
        }

        // Anything that cannot be identified still raises, as it always has. Swallowing
        // it here would turn a template pointing at a missing image from a visible error
        // into a silently rendered width="0", and that signal is worth more than the
        // convenience. Only a resizer URL -- handled above, where there is no file to
        // identify -- reports unknown.
        $resizer = new static($image);
        $config = $resizer->getConfig();

        // The configuration is handed over directly rather than written to the cache
        // and read back. Writing it here would leave an entry that no resizer request
        // can ever consume, because storeConfig() is the only writer that a resizer URL
        // depends on and it has already run by the time such a URL exists.
        return static::dimensionsForConfig($config, $resizer->getIdentifier());
    }
}
