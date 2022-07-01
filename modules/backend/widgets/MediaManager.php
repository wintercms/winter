<?php namespace Backend\Widgets;

use Url;
use Str;
use Lang;
use File;
use Input;
use Config;
use Backend;
use Storage;
use Request;
use Response;
use Exception;
use SystemException;
use ApplicationException;
use Backend\Classes\WidgetBase;
use System\Classes\ImageResizer;
use System\Classes\MediaLibrary;
use System\Classes\MediaLibraryItem;
use Winter\Storm\Database\Attach\Resizer;
use Winter\Storm\Filesystem\Definitions as FileDefinitions;
use Form as FormHelper;

/**
 * Media Manager widget.
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MediaManager extends WidgetBase
{
    use \Backend\Traits\UploadableWidget;

    const FOLDER_ROOT = '/';

    const VIEW_MODE_GRID = 'grid';
    const VIEW_MODE_LIST = 'list';
    const VIEW_MODE_TILES = 'tiles';

    const SELECTION_MODE_NORMAL = 'normal';
    const SELECTION_MODE_FIXED_RATIO = 'fixed-ratio';
    const SELECTION_MODE_FIXED_SIZE = 'fixed-size';

    const FILTER_EVERYTHING = 'everything';

    /**
     * @var string Hash string for the broken image graphic.
     */
    protected $brokenImageHash;

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
     * @return void
     */
    protected function abortIfReadOnly()
    {
        if ($this->readOnly) {
            abort(403);
        }
    }

    /**
     * Renders the widget.
     * @return string
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('body');
    }

    //
    // AJAX handlers
    //

    /**
     * Perform search AJAX handler
     * @return array
     */
    public function onSearch()
    {
        $this->setSearchTerm(Input::get('search'));

        $this->prepareVars();

        return [
            '#' . $this->getId('item-list') => $this->makePartial('item-list'),
            '#' . $this->getId('folder-path') => $this->makePartial('folder-path')
        ];
    }

    /**
     * Change view AJAX handler
     * @return array
     */
    public function onGoToFolder()
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
     * Generate thumbnail AJAX handler
     * @return array
     */
    public function onGenerateThumbnails()
    {
        $batch = Input::get('batch');
        if (!is_array($batch)) {
            return;
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
     * Get thumbnail AJAX handler
     * @return array
     */
    public function onGetSidebarThumbnail()
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
     * Set view preference AJAX handler
     * @return array
     */
    public function onChangeView()
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
     * Set filter preference AJAX handler
     * @return array
     */
    public function onSetFilter()
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
     * Set sorting preference AJAX handler
     * @return array
     */
    public function onSetSorting()
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
     * Delete library item AJAX handler
     * @return array
     */
    public function onDeleteItem()
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
     * Show rename item popup AJAX handler
     * @return array
     */
    public function onLoadRenamePopup()
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
     * Reanem library item AJAX handler
     * @return array
     */
    public function onApplyName()
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
     * Create library folder AJAX handler
     * @return array
     */
    public function onCreateFolder()
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
     * Show move item popup AJAX handler
     * @return array
     */
    public function onLoadMovePopup()
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
     * Move library item AJAX handler
     * @return array
     */
    public function onMoveItems()
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
     * Sidebar visibility AJAX handler
     * @return array
     */
    public function onSetSidebarVisible()
    {
        $visible = Input::get('visible');

        $this->setSidebarVisible($visible);
    }

    /**
     * Start image cropping session AJAX handler
     * @return array
     */
    public function onLoadPopup()
    {
        $this->bottomToolbar = Input::get('bottomToolbar', $this->bottomToolbar);

        $this->cropAndInsertButton = Input::get('cropAndInsertButton', $this->cropAndInsertButton);

        return $this->makePartial('popup-body');
    }

    /**
     * Load image for cropping AJAX handler
     * @return string
     */
    public function onLoadImageCropPopup(): string
    {
        $this->abortIfReadOnly();

        $path = Input::get('path');
        $path = MediaLibrary::validatePath($path);
        $selectionParams = $this->getSelectionParams();

        $urlAndSize = $this->getCropEditImageUrlAndSize($path);
        $width = $urlAndSize['dimensions'][0];
        $height = $urlAndSize['dimensions'][1] ?: 1;

        $this->vars['currentSelectionMode'] = $selectionParams['mode'];
        $this->vars['currentSelectionWidth'] = $selectionParams['width'];
        $this->vars['currentSelectionHeight'] = $selectionParams['height'];
        $this->vars['imageUrl'] = $urlAndSize['url'];
        $this->vars['dimensions'] = $urlAndSize['dimensions'];
        $this->vars['originalRatio'] = round($width / $height, 5);
        $this->vars['path'] = $path;

        return $this->makePartial('image-crop-popup-body');
    }

    /**
     * End crop session AJAX handler
     * @return array
     */
    public function onEndCroppingSession()
    {
        $this->abortIfReadOnly();
    }

    /**
     * Crop image AJAX handler
     * @return array
     */
    public function onCropImage()
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
                throw new SystemException('Invalid selection data.');
            }

            $selectionData[$key] = (int) $selectionData[$key];
        }

        if ($selectionData['h'] === 0 || $selectionData['w'] === 0) {
            throw new ApplicationException('You must define a crop size before inserting');
        }

        $croppedPath = $this->cropImage($sourceImageUrl, [
            'height' => $selectionData['h'],
            'width' => $selectionData['w'],
            'offset' => [
                $selectionData['x'],
                $selectionData['y'],
            ],
        ]);

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
     * Resize image AJAX handler
     * @return array
     */
    public function onResizeImage()
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

        $croppedPath = $this->resizeImage(MediaLibrary::url($path), [
            'mode' => 'exact',
            'width' => $width,
            'height' => $height
        ]);

        return [
            'url' => $this->getThumbnailImageUrl($croppedPath),
            'dimensions' => [$width, $height]
        ];
    }

    //
    // Methods for internal use
    //

    /**
     * Internal method to prepare view variables.
     * @return array
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
     * @param string $searchTerm
     * @param string $filter
     * @param string $sortBy
     * @param array[System\Classes\MediaLibraryItem]
     */
    protected function listFolderItems($folder, $filter, $sortBy)
    {
        $filter = $filter !== self::FILTER_EVERYTHING ? $filter : null;

        return MediaLibrary::instance()->listFolderContents($folder, $sortBy, $filter);
    }

    /**
     * Finds files from within the media library based on supplied criteria,
     * returns an array of MediaLibraryItem objects.
     * @param string $searchTerm
     * @param string $filter
     * @param string $sortBy
     * @param array[System\Classes\MediaLibraryItem]
     */
    protected function findFiles($searchTerm, $filter, $sortBy)
    {
        $filter = $filter !== self::FILTER_EVERYTHING ? $filter : null;

        return MediaLibrary::instance()->findFiles($searchTerm, $sortBy, $filter);
    }

    /**
     * Sets the user current folder from the session state
     * @param string $path
     * @return void
     */
    protected function setCurrentFolder($path)
    {
        $path = MediaLibrary::validatePath($path);

        $this->putSession('media_folder', $path);
    }

    /**
     * Gets the user current folder from the session state
     * @return string
     */
    protected function getCurrentFolder()
    {
        return $this->getSession('media_folder', self::FOLDER_ROOT);
    }

    /**
     * Sets the user filter from the session state
     * @param string $filter
     * @return void
     */
    protected function setFilter($filter)
    {
        if (!in_array($filter, [
            self::FILTER_EVERYTHING,
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
     * Gets the user filter from the session state
     * @return string
     */
    protected function getFilter()
    {
        return $this->getSession('media_filter', self::FILTER_EVERYTHING);
    }

    /**
     * Sets the user search term from the session state
     * @param string $searchTerm
     * @return void
     */
    protected function setSearchTerm($searchTerm)
    {
        $this->putSession('media_search', trim($searchTerm));
    }

    /**
     * Gets the user search term from the session state
     * @return string
     */
    protected function getSearchTerm()
    {
        return $this->getSession('media_search', null);
    }

    /**
     * Sets the user sort column from the session state
     * @param string $sortBy
     * @return void
     */
    protected function setSortBy($sortBy)
    {
        if (!in_array($sortBy, [
            MediaLibrary::SORT_BY_TITLE,
            MediaLibrary::SORT_BY_SIZE,
            MediaLibrary::SORT_BY_MODIFIED
        ])) {
            throw new ApplicationException('Invalid input data');
        }

        $this->putSession('media_sort_by', $sortBy);
    }

    /**
     * Gets the user sort column from the session state
     * @return string
     */
    protected function getSortBy()
    {
        return $this->getSession('media_sort_by', MediaLibrary::SORT_BY_TITLE);
    }

    /**
     * Sets the user sort direction from the session state
     * @param string $sortDirection
     * @return void
     */
    protected function setSortDirection($sortDirection)
    {
        if (!in_array($sortDirection, [
            MediaLibrary::SORT_DIRECTION_ASC,
            MediaLibrary::SORT_DIRECTION_DESC
        ])) {
            throw new ApplicationException('Invalid input data');
        }

        $this->putSession('media_sort_direction', $sortDirection);
    }

    /**
     * Gets the user sort direction from the session state
     * @return string
     */
    protected function getSortDirection()
    {
        return $this->getSession('media_sort_direction', MediaLibrary::SORT_DIRECTION_ASC);
    }

    /**
     * Gets the user selection parameters from the session state
     * @return array
     */
    protected function getSelectionParams()
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
     * @param string $selectionMode
     * @param int $selectionWidth
     * @param int $selectionHeight
     * @return void
     */
    protected function setSelectionParams($selectionMode, $selectionWidth, $selectionHeight)
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
     * @param bool $visible
     * @return void
     */
    protected function setSidebarVisible($visible)
    {
        $this->putSession('sidebar_visible', !!$visible);
    }

    /**
     * Checks if the sidebar is visible
     * @return bool
     */
    protected function getSidebarVisible()
    {
        return $this->getSession('sidebar_visible', true);
    }

    /**
     * Returns an icon for the item type
     * @param System\Classes\MediaLibraryItem $item
     * @param string $itemType
     * @return string
     */
    protected function itemTypeToIconClass($item, $itemType)
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
     * @param string $path
     * @return array
     */
    protected function splitPathToSegments($path)
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
     * @param string $viewMode
     * @return void
     */
    protected function setViewMode($viewMode)
    {
        if (!in_array($viewMode, [
            self::VIEW_MODE_GRID,
            self::VIEW_MODE_LIST,
            self::VIEW_MODE_TILES
        ])) {
            throw new ApplicationException('Invalid input data');
        }

        $this->putSession('view_mode', $viewMode);
    }

    /**
     * Returns the current view mode stored in the session
     * @return string
     */
    protected function getViewMode()
    {
        return $this->getSession('view_mode', self::VIEW_MODE_GRID);
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
     * Generates a thumbnail image path
     * @param array|null $thumbnailParams
     * @param string $itemPath
     * @param int $lastModified
     * @return string
     */
    protected function getThumbnailImagePath($thumbnailParams, $itemPath, $lastModified)
    {
        $itemSignature = md5($itemPath).$lastModified;

        $thumbFile = 'thumb_' .
            $itemSignature . '_' .
            $thumbnailParams['width'] . 'x' .
            $thumbnailParams['height'] . '_' .
            $thumbnailParams['mode'] . '.' .
            $this->getThumbnailImageExtension($itemPath);

        $partition = implode('/', array_slice(str_split($itemSignature, 3), 0, 3)) . '/';

        return $this->getThumbnailDirectory().$partition.$thumbFile;
    }

    /**
     * Preferred thumbnail image extension
     * @param string $itemPath
     * @return string
     */
    protected function getThumbnailImageExtension($itemPath)
    {
        $extension = pathinfo($itemPath, PATHINFO_EXTENSION);

        if (in_array($extension, ['png', 'gif', 'webp'])) {
            return $extension;
        }

        return 'jpg';
    }

    /**
     * Returns the URL to a thumbnail
     */
    protected function getThumbnailImageUrl(string $imagePath): string
    {
        return Url::to(
            rtrim(
                Config::get('cms.storage.resized.path', ''),
                Config::get('cms.storage.resized.folder', '')
            ) . $imagePath
        );
    }

    /**
     * Get temporary local file path
     * @param string $fileName
     * @return string
     */
    protected function getLocalTempFilePath($fileName)
    {
        $fileName = md5($fileName.uniqid().microtime());

        $mediaFolder = Config::get('cms.storage.media.folder', 'media');

        $path = temp_path() . MediaLibrary::validatePath($mediaFolder, true);

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        return $path.'/'.$fileName;
    }

    /**
     * Get thumbnail directory
     * @return string
     */
    protected function getThumbnailDirectory()
    {
        /*
         * NOTE: Custom routing for /storage/temp/$thumbnailDirectory must be setup
         * to return the thumbnail if not using default 'public' directory
         */
        return MediaLibrary::validatePath(Config::get('cms.storage.media.thumbFolder', 'public'), true) . '/';
    }

    /**
     * Get placeholder identifier
     * @param System\Classes\MediaLibraryItem $item
     * @return string
     */
    protected function getPlaceholderId($item)
    {
        return 'placeholder'.md5($item->path.'-'.$item->lastModified.uniqid(microtime()));
    }

    /**
     * Generate thumbnail
     * @param array $thumbnailInfo
     * @param array|null $thumbnailParams
     * @return array
     */
    protected function generateThumbnail($thumbnailInfo, $thumbnailParams = null): array
    {
        $markup = null;

        $path = $thumbnailInfo['path'];

        if ($this->isVector($path) && ($id = $thumbnailInfo['id'])) {
            return [
                'id' => $id,
                'markup' => $this->makePartial('thumbnail-image', [
                    'isError' => false,
                    'imageUrl' => Url::to(config('cms.storage.media.path') . $thumbnailInfo['path'])
                ])
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
            $fullThumbnailPath = $this->resizeImage(MediaLibrary::url($path), $thumbnailParams);

            /*
             * Delete the temporary file
             */
            $markup = $this->makePartial('thumbnail-image', [
                'isError' => false,
                'imageUrl' => $this->getThumbnailImageUrl($fullThumbnailPath)
            ]);
        } catch (\Throwable $ex) {
            $markup = $this->makePartial('thumbnail-image', ['isError' => true]);

            traceLog($ex->getMessage());
        }

        if ($markup && ($id = $thumbnailInfo['id'])) {
            return [
                'id' => $id,
                'markup' => $markup
            ];
        }

        return [];
    }

    /**
     * Resize an image
     */
    protected function resizeImage(string $image, array $params): string
    {
        return ImageResizer::processImage(
            $image,
            $params['width'],
            $params['height'],
            array_merge(
                ['mode' => 'exact'],
                $params
            ),
            ImageResizer::METHOD_RESIZE
        );
    }

    /**
     * Crop an image
     */
    protected function cropImage(string $image, array $params): string
    {
        return ImageResizer::processImage(
            $image,
            $params['width'],
            $params['height'],
            array_merge(
                ['mode' => 'exact'],
                $params
            ),
            ImageResizer::METHOD_CROP
        );
    }

    /**
     * Returns the path for the broken image graphic
     * @return string
     */
    protected function getBrokenImagePath()
    {
        return __DIR__ . '/mediamanager/assets/images/broken-thumbnail.gif';
    }

    /**
     * Returns a CRC32 hash for a broken image
     * @return string
     */
    protected function getBrokenImageHash()
    {
        if ($this->brokenImageHash) {
            return $this->brokenImageHash;
        }

        $fullPath = $this->getBrokenImagePath();

        return $this->brokenImageHash = hash_file('crc32', $fullPath);
    }

    //
    // Cropping
    //

    /**
     * Prepares an image for cropping and returns payload containing a URL
     * @param string $path
     * @param array $params
     * @return array
     */
    protected function getCropEditImageUrlAndSize($path, $params = null)
    {
        $url = MediaLibrary::url($path);

        if ($params) {
            $url = $this->resizeImage($url, [
                'mode' => 'exact',
                'width' => $params['width'],
                'height' => $params['height'],
            ]);
        }

        return [
            'url' => $url,
            'dimensions' => Str::startsWith($url, '/')
                ? getimagesize(base_path($url))
                : getimagesize($url)
        ];
    }

    /**
     * Process the provided path and add a suffix of _$int to prevent conflicts
     * with existing paths
     * @TODO: Consider moving this into the File helper and accepting a $disk instance
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
}
