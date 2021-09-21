<?php namespace System\Models;

use Str;
use Model;
use System\Classes\MediaLibraryItem;
use Winter\Storm\Database\Builder;
use Winter\Storm\Database\Relations\HasMany;

class MediaItem extends Model
{
    use \Winter\Storm\Database\Traits\NestedTree;

    /**
     * Table to use.
     *
     * @var string
     */
    public $table = 'media_items';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    public $fillable = [
        'type',
        'name',
        'path',
        'extension',
        'size',
        'modified_at',
    ];

    /**
     * Arrayable attributes.
     *
     * @var array
     */
    public $jsonable = [
        'metadata',
    ];

    /**
     * Date attributes.
     *
     * @var array
     */
    public $dates = [
        'modified_at',
    ];

    /**
     * Cache of the root node.
     *
     * @var static
     */
    protected static $rootCache;

    /**
     * Scope to get the root folder of the media library.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeRoot(Builder $query)
    {
        $query->whereNull('parent_id');
    }

    /**
     * Scope to get all but the root folder of the media library.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeNotRoot(Builder $query)
    {
        $query->whereNotNull('parent_id');
    }

    /**
     * Override of base `setAttribute` method.
     *
     * Prevents writing to metadata key directly.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($key === 'metadata') {
            return false;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Finds a folder at the specific path.
     *
     * @param string $path
     * @return static
     */
    public static function folder($path = '/')
    {
        if ($path === '/' || empty($path)) {
            return self::getRoot();
        }

        $query = self::query();

        $query
            ->where('type', MediaLibraryItem::TYPE_FOLDER)
            ->where('path', $path);

        return $query->firstOrFail();
    }

    /**
     * Retrieves the contents of a folder.
     *
     * This includes both files and folders.
     *
     * @param string|array $sortBy
     * @param string|null $filter
     * @param boolean $ignoreFolders
     * @return array
     */
    public function contents($sortBy = 'title', $filter = null, $ignoreFolders = false)
    {
        $query = $this->children();

        $query = $this->applySort($query, $sortBy);
        if (!is_null($filter)) {
            $query = $this->applyFilter($query, $filter);
        }

        if ($ignoreFolders) {
            $query->where('type', MediaLibraryItem::TYPE_FILE);
        }

        return $query
            ->get()
            ->map(function ($item) {
                return $item->toMediaLibraryItem();
            })
            ->toArray();
    }


    /**
     * Retrieves the folders within a folder.
     *
     * You may optionally provide an array of paths to exclude.
     *
     * @param array $exclude
     * @param bool $includeSelf
     * @return array
     */
    public function folders($exclude = [], $includeSelf = true)
    {
        $query = $this->allChildren()->where('type', MediaLibraryItem::TYPE_FOLDER);

        $directories = $query->get();

        if ($includeSelf) {
            $directories->prepend($this);
        }

        return $directories
            ->filter(function ($item) use ($exclude) {
                foreach ($exclude as $path) {
                    if (Str::startsWith($item->path, $exclude)) {
                        return false;
                    }
                }

                return true;
            })
            ->map(function ($item) {
                return $item->toMediaLibraryItem();
            })
            ->toArray();
    }

    /**
     * Searches within a folder recursively for files or folders that match a search query.
     *
     * @param string|array $sortBy
     * @param string|null $filter
     * @param boolean $ignoreFolders
     * @return array
     */
    public function search($searchTerm, $sortBy = 'title', $filter = null)
    {
        // Normalise search term(s)
        $words = explode(' ', Str::lower($searchTerm));

        $query = $this->allChildren();

        // Only return files as results
        $query->where('type', MediaLibraryItem::TYPE_FILE);

        $query = $this->applySort($query, $sortBy);
        if (!is_null($filter)) {
            $query = $this->applyFilter($query, $filter);
        }

        // Add search query
        $query->where(function ($query) use ($words) {
            foreach ($words as $word) {
                $query->orWhere('path', 'like', '%' . $word . '%');
            }
        });

        // Group by type
        return $query
            ->get()
            ->map(function ($item) {
                return $item->toMediaLibraryItem();
            })
            ->toArray();
    }

    /**
     * Converts a media item model instance into a MediaLibraryItem instance.
     *
     * @return MediaLibraryItem
     */
    protected function toMediaLibraryItem()
    {
        return new MediaLibraryItem(
            $this->path,
            $this->size,
            (isset($this->modified_at)) ? $this->modified_at->getTimestamp() : null,
            $this->type,
            ''
        );
    }

    /**
     * Sets metadata for this media item.
     *
     * @param string|array $key
     * @param mixed $value
     * @param string|null $label
     * @param integer $order
     * @param string $group
     * @return void
     */
    public function setMetadata($key, $value = null, $label = null, $order = 100, $group = 'Metadata')
    {
        if (is_array($key)) {
            foreach ($key as $name => $options) {
                $this->setMetadata(
                    $name,
                    $options['value'] ?? null,
                    $options['label'] ?? $name,
                    $options['order'] ?? 100,
                    $options['group'] ?? 'Metadata'
                );
            }
            return;
        }

        $meta = array_replace($this->metadata ?? [], [
            $key => [
                'label' => $label ?? $key,
                'order' => $order,
                'group' => $group,
                'value' => $value,
            ]
        ]);

        parent::setAttribute('metadata', $meta);
    }

    /**
     * Applies sorting to the contents of a folder.
     *
     * @param HasMany|Builder $query
     * @param string|array $sortBy Accepts either a string value of "title", "size", or "lastModified", or an array
     *   with two keys: "by" which represents the sort type (same 3 options as above), and "direction" - either "asc"
     *   or "desc".
     * @return HasMany|Builder
     */
    protected function applySort($query, $sortBy)
    {
        // Force ordering of folders first
        $query->orderBy('type', 'DESC');

        if (is_array($sortBy)) {
            $sortDir = $sortBy['direction'] ?? 'asc';
            $sortBy = $sortBy['by'];
        } else {
            $sortDir = 'asc';
        }

        if (!in_array($sortBy, ['title', 'size', 'lastModified'])) {
            return $query;
        }

        switch ($sortBy) {
            case 'title':
                $query->orderBy('name', $sortDir);
                break;
            case 'size':
                $query->orderBy('size', $sortDir);
                break;
            case 'lastModified':
                $query->orderBy('modified_at', $sortDir);
                break;
        }

        return $query;
    }

    /**
     * Applies a filter to the contents of the directory. This only applies to files, not folders.
     *
     * @param HasMany|Builder $query
     * @param string $filter Accepts one of the following: "audio", "document", "image", "video"
     * @return HasMany|Builder
     */
    protected function applyFilter($query, $filter)
    {
        if (!in_array($filter, [
            MediaLibraryItem::FILE_TYPE_AUDIO,
            MediaLibraryItem::FILE_TYPE_DOCUMENT,
            MediaLibraryItem::FILE_TYPE_IMAGE,
            MediaLibraryItem::FILE_TYPE_VIDEO,
        ])) {
            return $query;
        }

        return $query->where(function ($query) use ($filter) {
            $query->where('file_type', $filter)
                ->orWhere('type', MediaLibraryItem::TYPE_FOLDER);
        });
    }

    /**
     * Creates an empty root node to represent the root folder of the media library.
     *
     * @return static
     */
    protected static function makeRoot()
    {
        $root = new static;
        $root->parent_id = null;
        $root->type = MediaLibraryItem::TYPE_FOLDER;
        $root->name = 'Root';
        $root->path = '/';
        $root->size = 0;
        $root->save();

        return $root;
    }

    /**
     * Gets the root folder, or creates one if it does not exist.
     *
     * @return static
     */
    public static function getRoot()
    {
        if (self::$rootCache) {
            return self::$rootCache;
        }

        $query = self::query();

        // Find the root folder node
        $query
            ->whereNull('parent_id');

        if (!$query->count()) {
            // Create a root node if one does not already exist
            return self::$rootCache = self::makeRoot();
        }

        return self::$rootCache = $query->first();
    }
}
