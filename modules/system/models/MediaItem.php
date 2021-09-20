<?php namespace System\Models;

use Model;
use System\Classes\MediaLibraryItem;

class MediaItem extends Model
{
    use \Winter\Storm\Database\Traits\NestedTree;

    /**
     * Table to use.
     *
     * @var string
     */
    public $table = 'media_items';
}
