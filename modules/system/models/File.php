<?php

namespace System\Models;

use Backend\Controllers\Files;
use Winter\Storm\Database\Attach\File as FileBase;

/**
 * File attachment model
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class File extends FileBase
{
    /**
     * The name of the storage disk used by this class.
     */
    public const DISK = 'uploads';

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'system_files';

    /**
     * {@inheritDoc}
     */
    public function getThumb($width, $height, $options = [])
    {
        $url = '';
        $width = !empty($width) ? $width : 0;
        $height = !empty($height) ? $height : 0;

        if (!$this->isPublic() && class_exists(Files::class)) {
            $options = $this->getDefaultThumbOptions($options);
            // Ensure that the thumb exists first
            parent::getThumb($width, $height, $options);

            // Return the Files controller handler for the URL
            $url = Files::getThumbUrl($this, $width, $height, $options);
        } else {
            $url = parent::getThumb($width, $height, $options);
        }

        return $url;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath(?string $fileName = null): string
    {
        $url = '';
        if (!$this->isPublic() && class_exists(Files::class)) {
            $url = Files::getDownloadUrl($this);
        } else {
            $url = parent::getPath($fileName);
        }

        return $url;
    }
}
