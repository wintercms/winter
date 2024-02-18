<?php namespace Backend\Widgets;

use Str;
use Lang;
use Input;
use Event;
use Config;
use Backend;
use ApplicationException;
use Backend\Classes\WidgetBase;
use System\Classes\ImageResizer;
use System\Classes\MediaLibrary;
use System\Classes\MediaLibraryItem;

/**
 * Media Manager widget.
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MediaManager extends WidgetBase
{
    use \Backend\Traits\UploadableWidget;
    use \Backend\Traits\PreferenceMaker;

    const FOLDER_ROOT = '/';

    const VIEW_MODE_GRID = 'grid';
    const VIEW_MODE_LIST = 'list';
    const VIEW_MODE_TILES = 'tiles';

    const SELECTION_MODE_NORMAL = 'normal';
    const SELECTION_MODE_FIXED_RATIO = 'fixed-ratio';
    const SELECTION_MODE_FIXED_SIZE = 'fixed-size';

    const FILTER_ALL = 'all';

    /**
     * @var boolean Determines whether the widget is in readonly mode or not.
     */
    public $readOnly = false;

    /**
     * @var boolean Determines whether the bottom toolbar is visible.
     */
    public $bottomToolbar = false;

    /**
     * @var boolean Determines whether the Crop & Insert button is visible.
     */
    public $cropAndInsertButton = false;

    /**
     * @var boolean Determines whether the Display filters are visible.
     */
    public bool $filterDisplay = true;

    /**
     * Constructor.
     */
    public function __construct($controller, $alias, $readOnly = false)
    {
        $this->alias = $alias;
        $this->readOnly = $readOnly;

        parent::__construct($controller, []);
    }

    /**
     * Adds widget specific asset files. Use $this->addJs() and $this->addCss()
     * to register new assets to include on the page.
     * @return void
     */
    protected function loadAssets()
    {
        $this->addCss('css/mediamanager.css', 'core');

        if (Config::get('develop.decompileBackendAssets', false)) {
            $scripts = Backend::decompileAsset($this->getAssetPath('js/mediamanager-browser.js'));
            foreach ($scripts as $script) {
                $this->addJs($script, 'core');
            }
        } else {
            $this->addJs('js/mediamanager-browser-min.js', 'core');
        }
    }

    /**
     * Abort the request with an access-denied code if readOnly mode is active
     */
    protected function abortIfReadOnly(): void
    {
        if ($this->readOnly) {
            abort(403);
        }
    }

    /**
     * Renders the widget.
     */
    public function render(): string
    {
        $this->prepareVars();

        return $this->makePartial('body');
    }

    //
    // AJAX handlers
    //

    /**
     * Perform a search with the query specified in the request ("search")
     */
    public function onSearch(): array
    {
        $this->setSearchTerm(Input::get('search'));

        $this->prepareVars();

        return [
            '#' . $this->getId('item-list') => $this->makePartial('item-list'),
            '#' . $this->getId('folder-path') => $this->makePartial('folder-path')
        ];
    }

    /**
     * Go to the path specified in the request ("path")
     */
    public function onGoToFolder(): array
    {
        $path = Input::get('path');

        if (Input::get('clearCache')) {
            MediaLibrary::instance()->resetCache();
        }

        if (Input::get('resetSearch')) {
            $this->setSearchTerm(null);
        }

        $this->setCurrentFolder($path);
        $this->prepareVars();

        return [
            '#' . $this->getId('item-list') => $this->makePartial('item-list'),
            '#' . $this->getId('folder-path') => $this->makePartial('folder-path')
        ];
    }

    /**
     * Generate thumbnails for the provided array of thumbnail info ("batch")
     */
    public function onGenerateThumbnails(): array
    {
        $batch = Input::get('batch');
        if (!is_array($batch)) {
            return [];
        }

        $result = [];
        foreach ($batch as $thumbnailInfo) {
            $result[] = $this->generateThumbnail($thumbnailInfo);
        }

        return [
            'generatedThumbnails' => $result
        ];
    }

    /**
     * Get the thumbnail for the provided path ("path") and lastModified date ("lastModified")
     *
     * @throws ApplicationException if the lastModified date is invalid
     */
    public function onGetSidebarThumbnail(): array
    {
        $path = MediaLibrary::validatePath(Input::get('path'));
        $lastModified = Input::get('lastModified');

        if (!is_numeric($lastModified)) {
            throw new ApplicationException('Invalid input data');
        }

        $thumbnailParams = $this->getThumbnailParams();
        $thumbnailParams['width'] = 300;
        $thumbnailParams['height'] = 255;
        $thumbnailParams['mode'] = 'auto';

        $thumbnailInfo = $thumbnailParams;
        $thumbnailInfo['path'] = $path;
        $thumbnailInfo['lastModified'] = $lastModified;
        $thumbnailInfo['id'] = 'sidebar-thumbnail';

        return $this->generateThumbnail($thumbnailInfo, $thumbnailParams);
    }

    /**
     * Render the view for the provided "path" and "view" mode from the request
     */
    public function onChangeView(): array
    {
        $viewMode = Input::get('view');
        $path = Input::get('path');

        $this->setViewMode($viewMode);
        $this->setCurrentFolder($path);

        $this->prepareVars();

        return [
            '#' . $this->getId('item-list') => $this->makePartial('item-list'),
            '#' . $this->getId('folder-path') => $this->makePartial('folder-path'),
            '#' . $this->getId('view-mode-buttons') => $this->makePartial('view-mode-buttons')
        ];
    }

    /**
     * Set the current filter from the request ("filter")
     */
    public function onSetFilter(): array
    {
        $filter = Input::get('filter');
        $path = Input::get('path');

        $this->setFilter($filter);
        $this->setCurrentFolder($path);

        $this->prepareVars();

        return [
            '#' . $this->getId('item-list') => $this->makePartial('item-list'),
            '#' . $this->getId('folder-path') => $this->makePartial('folder-path'),
            '#' . $this->getId('filters') => $this->makePartial('filters')
        ];
    }

    /**
     * Set the current sorting configuration from the request ("sortBy", "sortDirection")
     */
    public function onSetSorting(): array
    {
        $sortBy = Input::get('sortBy', $this->getSortBy());
        $sortDirection = Input::get('sortDirection', $this->getSortDirection());
        $path = Input::get('path');

        $this->setSortBy($sortBy);
        $this->setSortDirection($sortDirection);
        $this->setCurrentFolder($path);

        $this->prepareVars();

        return [
            '#' . $this->getId('item-list') => $this->makePartial('item-list'),
            '#' . $this->getId('folder-path') => $this->makePartial('folder-path')
        ];
    }

    /**
     * Deletes the provided paths from the request ("paths")
     *
     * @throws ApplicationException if the paths input is invalid
     * @todo Move media events to the MediaLibary class instead.
     */
    public function onDeleteItem(): array
    {
        $this->abortIfReadOnly();

        $paths = Input::get('paths');

        if (!is_array($paths)) {
            throw new ApplicationException('Invalid input data');
        }

        $library = MediaLibrary::instance();

        $filesToDelete = [];
        foreach ($paths as $pathInfo) {
            $path = array_get($pathInfo, 'path');
            $type = array_get($pathInfo, 'type');

            if (!$path || !$type) {
                throw new ApplicationException('Invalid input data');
            }

            if ($type === MediaLibraryItem::TYPE_FILE) {
                /*
                 * Add to bulk collection
                 */
                $filesToDelete[] = $path;
            } elseif ($type === MediaLibraryItem::TYPE_FOLDER) {
                /*
                 * Delete single folder
                 */
                $library->deleteFolder($path);

                /**
                 * @event media.folder.delete
                 * Called after a folder is deleted
                 *
                 * Example usage:
                 *
                 *     Event::listen('media.folder.delete', function ((\Backend\Widgets\MediaManager) $mediaWidget, (string) $path) {
                 *         \Log::info($path . " was deleted");
                 *     });
                 *
                 * Or
                 *
                 *     $mediaWidget->bindEvent('folder.delete', function ((string) $path) {
                 *         \Log::info($path . " was deleted");
                 *     });
                 *
                 */
                $this->fireSystemEvent('media.folder.delete', [$path]);
            }
        }

        if (count($filesToDelete) > 0) {
            /*
             * Delete collection of files
             */
            $library->deleteFiles($filesToDelete);

            /*
             * Extensibility
             */
            foreach ($filesToDelete as $path) {
                /**
                 * @event media.file.delete
                 * Called after a file is deleted
                 *
                 * Example usage:
                 *
                 *     Event::listen('media.file.delete', function ((\Backend\Widgets\MediaManager) $mediaWidget, (string) $path) {
                 *         \Log::info($path . " was deleted");
                 *     });
                 *
                 * Or
                 *
                 *     $mediaWidget->bindEvent('file.delete', function ((string) $path) {
                 *         \Log::info($path . " was deleted");
                 *     });
                 *
                 */
                $this->fireSystemEvent('media.file.delete', [$path]);
            }
        }

        $library->resetCache();
        $this->prepareVars();

        return [
            '#' . $this->getId('item-list') => $this->makePartial('item-list')
        ];
    }

    /**
     * Render the rename popup for the provided "path" from the request
     */
    public function onLoadRenamePopup(): string
    {
        $this->abortIfReadOnly();

        $path = Input::get('path');
        $path = MediaLibrary::validatePath($path);

        $this->vars['originalPath'] = $path;
        $this->vars['name'] = basename($path);
        $this->vars['listId'] = Input::get('listId');
        $this->vars['type'] = Input::get('type');

        return $this->makePartial('rename-form');
    }

    /**
     * Rename the provided path from the request ("originalPath") to the new name ("name")
     *
     * @throws ApplicationException if the new name is invalid
     * @todo Move media events to the MediaLibary class instead.
     */
    public function onApplyName(): void
    {
        $this->abortIfReadOnly();

        $newName = trim(Input::get('name'));
        if (!strlen($newName)) {
            throw new ApplicationException(Lang::get('cms::lang.asset.name_cant_be_empty'));
        }

        if (!$this->validateFileName($newName)) {
            throw new ApplicationException(Lang::get('cms::lang.asset.invalid_name'));
        }

        $originalPath = Input::get('originalPath');
        $originalPath = MediaLibrary::validatePath($originalPath);
        $newPath = dirname($originalPath) . '/' . $newName;
        $type = Input::get('type');

        if ($type == MediaLibraryItem::TYPE_FILE) {
            /*
             * Validate extension
             */
            if (!$this->validateFileType($newName)) {
                throw new ApplicationException(Lang::get('backend::lang.media.type_blocked'));
            }

            /*
             * Move single file
             */
            MediaLibrary::instance()->moveFile($originalPath, $newPath);

            /**
             * @event media.file.rename
             * Called after a file is renamed / moved
             *
             * Example usage:
             *
             *     Event::listen('media.file.rename', function ((\Backend\Widgets\MediaManager) $mediaWidget, (string) $originalPath, (string) $newPath) {
             *         \Log::info($originalPath . " was moved to " . $path);
             *     });
             *
             * Or
             *
             *     $mediaWidget->bindEvent('file.rename', function ((string) $originalPath, (string) $newPath) {
             *         \Log::info($originalPath . " was moved to " . $path);
             *     });
             *
             */
            $this->fireSystemEvent('media.file.rename', [$originalPath, $newPath]);
        } else {
            /*
             * Move single folder
             */
            MediaLibrary::instance()->moveFolder($originalPath, $newPath);

            /**
             * @event media.folder.rename
             * Called after a folder is renamed / moved
             *
             * Example usage:
             *
             *     Event::listen('media.folder.rename', function ((\Backend\Widgets\MediaManager) $mediaWidget, (string) $originalPath, (string) $newPath) {
             *         \Log::info($originalPath . " was moved to " . $path);
             *     });
             *
             * Or
             *
             *     $mediaWidget->bindEvent('folder.rename', function ((string) $originalPath, (string) $newPath) {
             *         \Log::info($originalPath . " was moved to " . $path);
             *     });
             *
             */
            $this->fireSystemEvent('media.folder.rename', [$originalPath, $newPath]);
        }

        MediaLibrary::instance()->resetCache();
    }

    /**
     * Create a new folder ("name") in the provided "path" from the request
     *
     * @throws ApplicationException If the requested folder already exists or is otherwise invalid
     */
    public function onCreateFolder(): array
    {
        $this->abortIfReadOnly();

        $name = trim(Input::get('name'));
        if (!strlen($name)) {
            throw new ApplicationException(Lang::get('cms::lang.asset.name_cant_be_empty'));
        }

        if (!$this->validateFileName($name)) {
            throw new ApplicationException(Lang::get('cms::lang.asset.invalid_name'));
        }

        $path = Input::get('path');
        $path = MediaLibrary::validatePath($path);

        $newFolderPath = $path . '/' . $name;

        $library = MediaLibrary::instance();

        if ($library->folderExists($newFolderPath)) {
            throw new ApplicationException(Lang::get('backend::lang.media.folder_or_file_exist'));
        }

        /*
         * Create the new folder
         */
        if (!$library->makeFolder($newFolderPath)) {
            throw new ApplicationException(Lang::get('backend::lang.media.error_creating_folder'));
        }

        /**
         * @event media.folder.create
         * Called after a folder is created
         *
         * Example usage:
         *
         *     Event::listen('media.folder.create', function ((\Backend\Widgets\MediaManager) $mediaWidget, (string) $newFolderPath) {
         *         \Log::info($newFolderPath . " was created");
         *     });
         *
         * Or
         *
         *     $mediaWidget->bindEvent('folder.create', function ((string) $newFolderPath) {
         *         \Log::info($newFolderPath . " was created");
         *     });
         *
         */
        $this->fireSystemEvent('media.folder.create', [$newFolderPath]);

        $library->resetCache();

        $this->prepareVars();

        return [
            '#' . $this->getId('item-list') => $this->makePartial('item-list')
        ];
    }

    /**
     * Render the move popup with a list of folders to move the selected items to excluding the provided paths in the request ("exclude")
     *
     * @throws ApplicationException If the exclude input data is not an array
     */
    public function onLoadMovePopup(): string
    {
        $this->abortIfReadOnly();

        $exclude = Input::get('exclude', []);
        if (!is_array($exclude)) {
            throw new ApplicationException('Invalid input data');
        }

        $folders = MediaLibrary::instance()->listAllDirectories($exclude);

        $folderList = [];
        foreach ($folders as $folder) {
            $path = $folder;

            if ($folder == '/') {
                $name = Lang::get('backend::lang.media.library');
            } else {
                $segments = explode('/', $folder);
                $name = str_repeat('&nbsp;', (count($segments) - 1) * 4) . basename($folder);
            }

            $folderList[$path] = $name;
        }

        $this->vars['folders'] = $folderList;
        $this->vars['originalPath'] = Input::get('path');

        return $this->makePartial('move-form');
    }

    /**
     * Move the selected items ("files", "folders") to the provided destination path from the request ("dest")
     *
     * @throws ApplicationException if the input data is invalid
     */
    public function onMoveItems(): array
    {
        $this->abortIfReadOnly();

        $dest = trim(Input::get('dest'));
        if (!strlen($dest)) {
            throw new ApplicationException(Lang::get('backend::lang.media.please_select_move_dest'));
        }

        $dest = MediaLibrary::validatePath($dest);
        if ($dest == Input::get('originalPath')) {
            throw new ApplicationException(Lang::get('backend::lang.media.move_dest_src_match'));
        }

        $files = Input::get('files', []);
        if (!is_array($files)) {
            throw new ApplicationException('Invalid input data');
        }

        $folders = Input::get('folders', []);
        if (!is_array($folders)) {
            throw new ApplicationException('Invalid input data');
        }

        $library = MediaLibrary::instance();

        foreach ($files as $path) {
            /*
             * Move a single file
             */
            $library->moveFile($path, $dest . '/' . basename($path));

            /**
             * @event media.file.move
             * Called after a file is moved
             *
             * Example usage:
             *
             *     Event::listen('media.file.move', function ((\Backend\Widgets\MediaManager) $mediaWidget, (string) $path, (string) $dest) {
             *         \Log::info($path . " was moved to " . $dest);
             *     });
             *
             * Or
             *
             *     $mediaWidget->bindEvent('file.rename', function ((string) $path, (string) $dest) {
             *         \Log::info($path . " was moved to " . $dest);
             *     });
             *
             */
            $this->fireSystemEvent('media.file.move', [$path, $dest]);
        }

        foreach ($folders as $path) {
            /*
             * Move a single folder
             */
            $library->moveFolder($path, $dest . '/' . basename($path));

            /**
             * @event media.folder.move
             * Called after a folder is moved
             *
             * Example usage:
             *
             *     Event::listen('media.folder.move', function ((\Backend\Widgets\MediaManager) $mediaWidget, (string) $path, (string) $dest) {
             *         \Log::info($path . " was moved to " . $dest);
             *     });
             *
             * Or
             *
             *     $mediaWidget->bindEvent('folder.rename', function ((string) $path, (string) $dest) {
             *         \Log::info($path . " was moved to " . $dest);
             *     });
             *
             */
            $this->fireSystemEvent('media.folder.move', [$path, $dest]);
        }

        $library->resetCache();

        $this->prepareVars();

        return [
            '#' . $this->getId('item-list') => $this->makePartial('item-list')
        ];
    }

    /**
     * Sets the sidebar visibility state from the request ("visible")
     */
    public function onSetSidebarVisible(): void
    {
        $visible = (bool) Input::get('visible');

        $this->setSidebarVisible($visible);
    }

    /**
     * Renders the widget in a popup body (options include "bottomToolbar" and "cropAndInsertButton")
     */
    public function onLoadPopup(): string
    {
        $this->bottomToolbar = Input::get('bottomToolbar', $this->bottomToolbar);

        $this->cropAndInsertButton = Input::get('cropAndInsertButton', $this->cropAndInsertButton);

        if ($mode = Input::get('mode')) {
            $this->setFilter($mode);
            if ($mode !== static::FILTER_ALL) {
                $this->setFilterDisplay(false);
            }
        }

        return $this->makePartial('popup-body');
    }

    /**
     * Prepares & renders the image crop popup body
     */
    public function onLoadImageCropPopup(): string
    {
        $this->abortIfReadOnly();

        $path = Input::get('path');
        $path = MediaLibrary::validatePath($path);
        $selectionParams = $this->getSelectionParams();
        $url = MediaLibrary::url($path);

        // @TODO: Improve support non-local disks
        if (Str::startsWith($url, '/')) {
            $localPath = base_path(implode("/", array_map("rawurldecode", explode("/", $url))));
            $dimensions = getimagesize($localPath);
        } else {
            $dimensions = getimagesize($url);
        }

        $width = $dimensions[0];
        $height = $dimensions[1] ?: 1;

        $this->vars['currentSelectionMode'] = $selectionParams['mode'];
        $this->vars['currentSelectionWidth'] = $selectionParams['width'];
        $this->vars['currentSelectionHeight'] = $selectionParams['height'];
        $this->vars['imageUrl'] = $url;
        $this->vars['dimensions'] = $dimensions;
        $this->vars['originalRatio'] = round($width / $height, 5);
        $this->vars['path'] = $path;

        return $this->makePartial('image-crop-popup-body');
    }

    /**
     * Crop image AJAX handler
     *
     * @throws ApplicationException if the input data is invalid
     */
    public function onCropImage(): array
    {
        $this->abortIfReadOnly();

        $selectionData = Input::get('selection');
        $sourceImageUrl = Input::get('img');
        $mediaItemPath = Input::get('path');

        if (!is_array($selectionData)) {
            throw new ApplicationException('Invalid input data');
        }

        foreach (['x', 'y', 'w', 'h'] as $key) {
            if (!isset($selectionData[$key]) || !is_numeric($selectionData[$key])) {
                throw new ApplicationException('Invalid selection data.');
            }

            $selectionData[$key] = (int) $selectionData[$key];
        }

        if ($selectionData['h'] === 0 || $selectionData['w'] === 0) {
            throw new ApplicationException('You must define a crop size before inserting');
        }

        // Initialize the ImageResizer
        $resizer = new ImageResizer(
            $sourceImageUrl,
            $selectionData['w'],
            $selectionData['h'],
            [
                'mode' => 'exact',
                'offset' => [
                    $selectionData['x'],
                    $selectionData['y'],
                ],
            ],
        );

        // Crop the image
        $resizer->crop();

        // Get the path to the cropped image
        $croppedPath = $resizer->getPathToResizedImage();

        // Generate the target path for the cropped image
        $targetPath = $this->deduplicatePath($mediaItemPath, '_cropped');

        // Move the cropped image to the target path
        MediaLibrary::instance()->put(
            $targetPath,
            ImageResizer::getDefaultDisk()->get($croppedPath)
        );

        $result = [
            'publicUrl' => MediaLibrary::url($targetPath),
            'documentType' => MediaLibraryItem::FILE_TYPE_IMAGE,
            'itemType' => MediaLibraryItem::TYPE_FILE,
            'path' => $targetPath,
            'title' => basename($targetPath),
            'folder' => dirname($targetPath),
        ];

        $selectionMode = Input::get('selectionMode');
        $selectionWidth = Input::get('selectionWidth');
        $selectionHeight = Input::get('selectionHeight');

        $this->setSelectionParams($selectionMode, $selectionWidth, $selectionHeight);

        return $result;
    }

    /**
     * Handles resizing the provided image and returns the URL to the resized image
     * Used by the Crop & Insert popup to resize the image being cropped on the canvas
     * before cropping it.
     *
     * @throws ApplicationException if the provided input data is invalid
     */
    public function onResizeImage(): array
    {
        $this->abortIfReadOnly();

        $width = trim(Input::get('width'));
        if (!strlen($width) || !ctype_digit($width)) {
            throw new ApplicationException('Invalid input data');
        }

        $height = trim(Input::get('height'));
        if (!strlen($height) || !ctype_digit($height)) {
            throw new ApplicationException('Invalid input data');
        }

        $path = Input::get('path');
        $path = MediaLibrary::validatePath($path);

        // Initialize the ImageResizer
        $resizer = new ImageResizer(
            MediaLibrary::url($path),
            $width,
            $height,
            [
                'mode' => 'exact',
            ],
        );

        // Process the resize
        $resizer->resize();

        // Get the URL to the resized image
        $resizedUrl = $resizer->getResizedUrl();

        return [
            'url' => $resizedUrl,
            'dimensions' => [$width, $height]
        ];
    }

    //
    // Methods for internal use
    //

    /**
     * Internal method to prepare view variables.
     */
    protected function prepareVars()
    {
        clearstatcache();

        $folder = $this->getCurrentFolder();
        $viewMode = $this->getViewMode();
        $filter = $this->getFilter();
        $sortBy = $this->getSortBy();
        $sortDirection = $this->getSortDirection();
        $searchTerm = $this->getSearchTerm();
        $searchMode = strlen($searchTerm) > 0;

        if (!$searchMode) {
            $this->vars['items'] = $this->listFolderItems($folder, $filter, ['by' => $sortBy, 'direction' => $sortDirection]);
        }
        else {
            $this->vars['items'] = $this->findFiles($searchTerm, $filter, ['by' => $sortBy, 'direction' => $sortDirection]);
        }

        $this->vars['currentFolder'] = $folder;
        $this->vars['isRootFolder'] = $folder == self::FOLDER_ROOT;
        $this->vars['pathSegments'] = $this->splitPathToSegments($folder);
        $this->vars['viewMode'] = $viewMode;
        $this->vars['thumbnailParams'] = $this->getThumbnailParams($viewMode);
        $this->vars['currentFilter'] = $filter;
        $this->vars['sortBy'] = $sortBy;
        $this->vars['sortDirection'] = $sortDirection;
        $this->vars['searchMode'] = $searchMode;
        $this->vars['searchTerm'] = $searchTerm;
        $this->vars['sidebarVisible'] = $this->getSidebarVisible();
    }

    /**
     * Returns a list of folders and files in a Library folder.
     *
     * @param string $searchTerm
     * @param string $filter
     * @param string $sortBy
     * @return array[System\Classes\MediaLibraryItem]
     */
    protected function listFolderItems($folder, $filter, $sortBy)
    {
        $filter = $filter !== self::FILTER_ALL ? $filter : null;

        return MediaLibrary::instance()->listFolderContents($folder, $sortBy, $filter);
    }

    /**
     * Finds files from within the media library based on supplied criteria,
     * returns an array of MediaLibraryItem objects.
     *
     * @param string $searchTerm
     * @param string $filter
     * @param string $sortBy
     * @return array[System\Classes\MediaLibraryItem]
     */
    protected function findFiles($searchTerm, $filter, $sortBy)
    {
        $filter = $filter !== self::FILTER_ALL ? $filter : null;

        return MediaLibrary::instance()->findFiles($searchTerm, $sortBy, $filter);
    }

    /**
     * Sets the provided path as the current folder in the session
     */
    protected function setCurrentFolder(string $path): void
    {
        $path = MediaLibrary::validatePath($path);

        $this->putSession('media_folder', $path);
    }

    /**
     * Gets the user's current folder from the session
     */
    protected function getCurrentFolder(): string
    {
        return $this->getSession('media_folder', self::FOLDER_ROOT);
    }

    /**
     * Sets the user filter from the session
     */
    protected function setFilter(string $filter): void
    {
        if (!in_array($filter, [
            self::FILTER_ALL,
            MediaLibraryItem::FILE_TYPE_IMAGE,
            MediaLibraryItem::FILE_TYPE_AUDIO,
            MediaLibraryItem::FILE_TYPE_DOCUMENT,
            MediaLibraryItem::FILE_TYPE_VIDEO
        ])) {
            throw new ApplicationException('Invalid input data');
        }

        $this->putSession('media_filter', $filter);
    }

    /**
     * Sets the filter display option for the request
     */
    protected function setFilterDisplay(bool $status): void
    {
        $this->filterDisplay = $status;
    }

    /**
     * Gets the filter display option for the request
     */
    protected function getFilterDisplay(): bool
    {
        return $this->filterDisplay;
    }

    /**
     * Gets the user filter from the session state
     *
     * @return string
     */
    protected function getFilter()
    {
        return $this->getSession('media_filter', self::FILTER_ALL);
    }

    /**
     * Sets the user search term from the session state
     *
     * @param string $searchTerm
     */
    protected function setSearchTerm($searchTerm): void
    {
        $this->putSession('media_search', trim($searchTerm));
    }

    /**
     * Gets the user search term from the session state
     */
    protected function getSearchTerm(): ?string
    {
        return $this->getSession('media_search', null);
    }

    /**
     * Sets the sort column
     */
    protected function setSortBy(string $sortBy): void
    {
        if (!in_array($sortBy, [
            MediaLibrary::SORT_BY_TITLE,
            MediaLibrary::SORT_BY_SIZE,
            MediaLibrary::SORT_BY_MODIFIED
        ])) {
            throw new ApplicationException('Invalid input data');
        }

        $key = 'media_sort_by';
        $this->putUserPreference($key, $sortBy);
        $this->putSession($key, $sortBy);
    }

    /**
     * Gets the current column to sort by
     */
    protected function getSortBy(): string
    {
        $key = 'media_sort_by';
        return $this->getSession($key, $this->getUserPreference($key, MediaLibrary::SORT_BY_TITLE));
    }

    /**
     * Sets the sort direction from the session state
     *
     * @param string $sortDirection
     */
    protected function setSortDirection($sortDirection): void
    {
        if (!in_array($sortDirection, [
            MediaLibrary::SORT_DIRECTION_ASC,
            MediaLibrary::SORT_DIRECTION_DESC
        ])) {
            throw new ApplicationException('Invalid input data');
        }

        $key = 'media_sort_direction';
        $this->putUserPreference($key, $sortDirection);
        $this->putSession($key, $sortDirection);
    }

    /**
     * Gets the user sort direction from the session state
     */
    protected function getSortDirection(): string
    {
        $key = 'media_sort_direction';
        return $this->getSession($key, $this->getUserPreference($key, MediaLibrary::SORT_DIRECTION_ASC));
    }

    /**
     * Gets the user selection parameters from the session state
     */
    protected function getSelectionParams(): array
    {
        $result = $this->getSession('media_crop_selection_params');

        if ($result) {
            if (!isset($result['mode'])) {
                $result['mode'] = self::SELECTION_MODE_NORMAL;
            }

            if (!isset($result['width'])) {
                $result['width'] = null;
            }

            if (!isset($result['height'])) {
                $result['height'] = null;
            }

            return $result;
        }

        return [
            'mode'   => self::SELECTION_MODE_NORMAL,
            'width'  => null,
            'height' => null
        ];
    }

    /**
     * Stores the user selection parameters in the session state
     *
     * @param string $selectionMode
     * @param int $selectionWidth
     * @param int $selectionHeight
     */
    protected function setSelectionParams($selectionMode, $selectionWidth, $selectionHeight): void
    {
        if (!in_array($selectionMode, [
            self::SELECTION_MODE_NORMAL,
            self::SELECTION_MODE_FIXED_RATIO,
            self::SELECTION_MODE_FIXED_SIZE
        ])) {
            throw new ApplicationException('Invalid input data');
        }

        if (strlen($selectionWidth) && !ctype_digit($selectionWidth)) {
            throw new ApplicationException('Invalid input data');
        }

        if (strlen($selectionHeight) && !ctype_digit($selectionHeight)) {
            throw new ApplicationException('Invalid input data');
        }

        $this->putSession('media_crop_selection_params', [
            'mode'   => $selectionMode,
            'width'  => $selectionWidth,
            'height' => $selectionHeight
        ]);
    }

    /**
     * Sets the sidebar visible state
     */
    protected function setSidebarVisible(bool $visible): void
    {
        $this->putSession('sidebar_visible', $visible);
    }

    /**
     * Checks if the sidebar is visible
     */
    protected function getSidebarVisible(): bool
    {
        return $this->getSession('sidebar_visible', true);
    }

    /**
     * Returns an icon for the item type
     */
    protected function itemTypeToIconClass(?MediaLibraryItem $item, ?string $itemType): string
    {
        if ($item->type == MediaLibraryItem::TYPE_FOLDER) {
            return 'icon-folder';
        }

        switch ($itemType) {
            case MediaLibraryItem::FILE_TYPE_IMAGE:
                return "icon-picture-o";
            case MediaLibraryItem::FILE_TYPE_VIDEO:
                return "icon-video-camera";
            case MediaLibraryItem::FILE_TYPE_AUDIO:
                return "icon-volume-up";
            default:
                return "icon-file";
        }
    }

    /**
     * Splits a path in to segments
     *
     * @param string $path
     */
    protected function splitPathToSegments($path): array
    {
        $path = MediaLibrary::validatePath($path, true);
        $path = explode('/', ltrim($path, '/'));

        $result = [];
        while (count($path) > 0) {
            $folder = array_pop($path);

            $result[$folder] = implode('/', $path).'/'.$folder;
            if (substr($result[$folder], 0, 1) != '/') {
                $result[$folder] = '/'.$result[$folder];
            }
        }

        return array_reverse($result, true);
    }

    /**
     * Stores a view mode in the session
     *
     * @throws ApplicationException if $viewMode is not a valid VIEW_MODE_* constant
     */
    protected function setViewMode(string $viewMode): void
    {
        if (!in_array($viewMode, [
            self::VIEW_MODE_GRID,
            self::VIEW_MODE_LIST,
            self::VIEW_MODE_TILES
        ])) {
            throw new ApplicationException('Invalid input data');
        }

        $key = 'view_mode';
        $this->putUserPreference($key, $viewMode);
        $this->putSession($key, $viewMode);
    }

    /**
     * Returns the current view mode stored in the session
     */
    protected function getViewMode(): string
    {
        $key = 'view_mode';
        return $this->getSession($key, $this->getUserPreference($key, self::VIEW_MODE_GRID));
    }

    /**
     * Returns thumbnail parameters
     */
    protected function getThumbnailParams(string $viewMode = null): array
    {
        $result = [
            'mode' => 'crop'
        ];

        if (!$viewMode) {
            return $result;
        }

        if ($viewMode === self::VIEW_MODE_LIST) {
            return array_merge($result, [
                'width' => 75,
                'height' => 75
            ]);
        }

        return array_merge($result, [
            'width' => 165,
            'height' => 165
        ]);
    }

    /**
     * Get placeholder identifier
     *
     * @param System\Classes\MediaLibraryItem $item
     * @return string
     */
    protected function getPlaceholderId($item)
    {
        return 'placeholder'.md5($item->path.'-'.$item->lastModified.uniqid(microtime()));
    }

    /**
     * Generate thumbnail
     *
     * @param array $thumbnailInfo
     * @param array|null $thumbnailParams
     */
    protected function generateThumbnail($thumbnailInfo, $thumbnailParams = null): array
    {
        $markup = null;

        $path = $thumbnailInfo['path'];

        if ($this->isVector($path) && ($id = $thumbnailInfo['id'])) {
            return [
                'id' => $id,
                'markup' => $this->makePartial('thumbnail-image', [
                    'imageUrl' => MediaLibrary::url($thumbnailInfo['path']),
                ]),
            ];
        }

        try {
            /*
             * Get and validate input data
             */
            $width = $thumbnailInfo['width'];
            $height = $thumbnailInfo['height'];
            $lastModified = $thumbnailInfo['lastModified'];

            if (!is_numeric($width) || !is_numeric($height) || !is_numeric($lastModified)) {
                throw new ApplicationException('Invalid input data');
            }

            if (!$thumbnailParams) {
                $thumbnailParams = $this->getThumbnailParams();
                $thumbnailParams['width'] = $width;
                $thumbnailParams['height'] = $height;
            }

            /*
             * Resize the thumbnail and save to the thumbnails directory
             */
            $thumbnailUrl = $this->getResizedImageUrl($path, $thumbnailParams);

            /*
             * Delete the temporary file
             */
            $markup = $this->makePartial('thumbnail-image', [
                'imageUrl' => $thumbnailUrl,
            ]);
        } catch (\Throwable $ex) {
            $markup = $this->makePartial('thumbnail-image', [
                'imageUrl' => false,
            ]);

            traceLog($ex->getMessage());
        }

        if ($markup && ($id = $thumbnailInfo['id'])) {
            return [
                'id' => $id,
                'markup' => $markup,
            ];
        }

        return [];
    }

    /**
     * Get the URL to the resized image based on the provided path and parameters
     */
    protected function getResizedImageUrl(string $path, array $params): string
    {
        return ImageResizer::filterGetUrl(
            MediaLibrary::url($path),
            $params['width'],
            $params['height'],
            array_merge(
                ['mode' => 'exact'],
                $params
            )
        );
    }

    /**
     * Process the provided path and add a suffix of _$int to prevent conflicts
     * with existing paths
     *
     * @todo Consider moving this into the File helper and accepting a $disk instance
     */
    protected function deduplicatePath(string $path, string $suffix = null): string
    {
        $parts = pathinfo($path);
        $i = 1;

        // Path generation adds a DIRECTORY_SEPARATOR between the dirname and
        // the filename so ensure that the dirname doesn't already end with one
        $parts['dirname'] = rtrim($parts['dirname'], DIRECTORY_SEPARATOR);

        // Apply the requested suffix to the path
        if (!empty($suffix)) {
            $parts['filename'] = preg_replace(
                // Remove the suffix if it's already there before re-adding it
                '/' . preg_quote($suffix, '/') . '(_\d)?/',
                '',
                $parts['filename']
            ) . $suffix;

            // Regenerate the path so that it can be checked for existance
            $path = sprintf(
                '%s%s%s.%s',
                $parts['dirname'],
                DIRECTORY_SEPARATOR,
                $parts['filename'],
                $parts['extension']
            );
        }

        while (MediaLibrary::instance()->exists($path)) {
            $path = sprintf(
                '%s%s%s_%d.%s',
                $parts['dirname'],
                DIRECTORY_SEPARATOR,
                $parts['filename'],
                $i++,
                $parts['extension']
            );
        }

        return $path;
    }

    /**
     * Detect if image is vector graphic (SVG)
     */
    protected function isVector(string $path): bool
    {
        return (pathinfo($path, PATHINFO_EXTENSION) == 'svg');
    }

    /**
     * Returns a unique identifier for this widget and controller action for preference storage.
     *
     * @return string
     */
    protected function getPreferenceKey()
    {
        // User preferences should persist across controller usages for the MediaManager
        return "backend::widgets.media_manager." . strtolower($this->getId());
    }
}
