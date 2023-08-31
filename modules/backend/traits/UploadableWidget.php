<?php namespace Backend\Traits;

use ApplicationException;
use Event;
use File;
use Illuminate\Filesystem\FilesystemAdapter;
use Lang;
use Request;
use Response;
use Str;
use System\Classes\MediaLibrary;
use Winter\Storm\Filesystem\Definitions as FileDefinitions;
use Winter\Storm\Support\Svg;

/**
 * Uploadable Widget Trait
 * Adds media library upload features to back-end widgets
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
trait UploadableWidget
{
    // /**
    //  * @var string Path in the Media Library where uploaded files should be stored. If empty it will be pulled from Request::input('path');
    //  */
    // public $uploadPath;

    /**
     * Returns the disk that will be used to store the uploaded file
     */
    public function uploadableGetDisk(): FilesystemAdapter
    {
        return MediaLibrary::instance()->getStorageDisk();
    }

    /**
     * Returns the path on the disk to store the uploaded file
     */
    public function uploadableGetUploadPath(string $fileName): string
    {
        // Use the configured upload path unless it's null, in which case use the user-provided path
        $path = !empty($this->uploadPath) ? $this->uploadPath : Request::input('path');
        $path = MediaLibrary::validatePath($path);
        $path = MediaLibrary::instance()->getMediaPath($path);
        $filePath = rtrim($path, '/') . '/' . $fileName;
        return $filePath;
    }

    /**
     * Returns the URL to the uploaded file
     *
     * @TODO: Replace cms.storage system with real disks
     */
    public function uploadableGetUploadUrl(string $diskPath): string
    {
        // Get the media folder
        $storageFolder = MediaLibrary::instance()->getMediaPath('');

        // Remove the media folder from the provided disk path since it already has it
        $url = MediaLibrary::url(Str::after($diskPath, $storageFolder));

        return $url;
    }

    /**
     * Process file uploads submitted via AJAX
     *
     * @throws ApplicationException If the file "file_data" wasn't detected in the request or if the file failed to pass validation / security checks
     */
    public function onUpload(): ?\Illuminate\Http\Response
    {
        if ($this->readOnly) {
            return null;
        }

        /**
         * @event backend.widgets.uploadable.onUpload
         * Provides an opportunity to process the file upload using custom logic.
         *
         * Example usage ()
         */
        if ($result = Event::fire('backend.widgets.uploadable.onUpload', [$this], true)) {
            return $result;
        }

        return $this->onUploadDirect();
    }

    protected function onUploadDirect(): \Illuminate\Http\Response
    {
        if (!Request::hasFile('file_data')) {
            throw new ApplicationException('File missing from request');
        }

        try {
            $uploadedFile = Request::file('file_data');

            $fileName = $this->validateMediaFileName(
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientOriginalExtension()
            );

            /*
             * See mime type handling in the asset manager
             */
            if (!$uploadedFile->isValid()) {
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $message = "The file \"{$uploadedFile->getClientOriginalName()}\" uploaded successfully but wasn't "
                        . "available at {$uploadedFile->getPathName()}. Check to make sure that nothing moved it away.";
                } else {
                    $message = $uploadedFile->getErrorMessage();
                }
                throw new ApplicationException($message);
            }

            /*
             * getRealPath() can be empty for some environments (IIS)
             */
            $sourcePath = empty(trim($uploadedFile->getRealPath()))
                ? $uploadedFile->getPath() . DIRECTORY_SEPARATOR . $uploadedFile->getFileName()
                : $uploadedFile->getRealPath();

            $filePath = $this->uploadableGetUploadPath($fileName);

            // Filter SVG files
            if (pathinfo($filePath, PATHINFO_EXTENSION) === 'svg') {
                file_put_contents($sourcePath, Svg::extract($sourcePath));
            }

            $this->uploadableGetDisk()->put($filePath, File::get($sourcePath));

            /**
             * @event media.file.upload
             * Called after a file is uploaded
             *
             * Example usage:
             *
             *     Event::listen('media.file.upload', function ((\Backend\Widgets\MediaManager) $mediaWidget, (string) &$path, (\Symfony\Component\HttpFoundation\File\UploadedFile) $uploadedFile) {
             *         \Log::info($path . " was upoaded.");
             *     });
             *
             * Or
             *
             *     $mediaWidget->bindEvent('file.upload', function ((string) &$path, (\Symfony\Component\HttpFoundation\File\UploadedFile) $uploadedFile) {
             *         \Log::info($path . " was uploaded");
             *     });
             *
             */
            $this->fireSystemEvent('media.file.upload', [&$filePath, $uploadedFile]);

            $response = Response::make([
                'link' => $this->uploadableGetUploadUrl($filePath),
                'result' => 'success'
            ]);
        } catch (\Exception $ex) {
            throw new ApplicationException($ex->getMessage());
        }

        return $response;
    }

    public function validateMediaFileName(string $fileName, string $extension): string
    {
        /*
         * Convert uppcare case file extensions to lower case
         */
        $extension = strtolower($extension);
        $fileName = File::name($fileName).'.'.$extension;

        /*
         * File name contains non-latin characters, attempt to slug the value
         */
        if (!$this->validateFileName($fileName)) {
            $fileName = $this->cleanFileName(File::name($fileName)) . '.' . $extension;
        }

        /*
         * Check for unsafe file extensions
         */
        if (!$this->validateFileType($fileName)) {
            throw new ApplicationException(Lang::get('backend::lang.media.type_blocked'));
        }

        return $fileName;
    }

    /**
     * Validate a proposed media item file name.
     *
     * @param string
     * @return bool
     */
    protected function validateFileName($name): bool
    {
        if (!preg_match('/^[\w@\.\s_\-]+$/iu', $name)) {
            return false;
        }

        if (strpos($name, '..') !== false) {
            return false;
        }

        return true;
    }

    /**
     * Check for blocked / unsafe file extensions
     *
     * @param string
     * @return bool
     */
    protected function validateFileType($name): bool
    {
        $extension = strtolower(File::extension($name));

        $allowedFileTypes = FileDefinitions::get('defaultExtensions');

        if (!in_array($extension, $allowedFileTypes)) {
            return false;
        }

        return true;
    }

    /**
     * Creates a slug form the string. A modified version of Str::slug
     * with the main difference that it accepts @-signs
     *
     * @param string $name
     * @return string
     */
    protected function cleanFileName($name)
    {
        $title = Str::ascii($name);

        // Convert all dashes/underscores into separator
        $flip = $separator = '-';
        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, whitespace or @.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s@]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}
