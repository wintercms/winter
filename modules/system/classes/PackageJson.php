<?php namespace System\Classes;

use File;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Support\Arr;

/**
 * PHP-based "package.json" handler.
 *
 * Allows for the management of "package.json" files through PHP, handling creation and modification of the file and
 * dependency management.
 *
 * @author Winter CMS
 */
class PackageJson
{
    /**
     * Path to the "package.json" file.
     *
     * @var string|null
     */
    protected $path;

    /**
     * Structure of the "package.json" file.
     *
     * @var array
     */
    protected $structure = [];

    /**
     * Standard structure for new "package.json" files.
     *
     * @var array
     */
    protected $defaultStructure = [
        'private' => true,
        'dependencies' => [],
        'devDependencies' => [],
        'engines' => [
            'node' => '>= 14',
            'npm' => '>= 7',
        ],
    ];

    /**
     * Constructor.
     *
     * @param string|null $path
     */
    public function __construct($path = null)
    {
        $this->path = $path;

        if ($this->exists()) {
            $this->parseStructure();
        }
    }

    /**
     * Gets a value from the structure using dot notation.
     *
     * @param string|null $keyPath
     * @param mixed|null $default Default value
     * @return mixed|null
     */
    public function get($keyPath = null, $default = null)
    {
        if (is_null($keyPath)) {
            return $this->structure;
        }

        return Arr::get($this->structure, $keyPath, $default);
    }

    /**
     * Sets a value in the structure using dot notation.
     *
     * @param string $keyPath
     * @param mixed $value
     * @return void
     */
    public function set($keyPath, $value)
    {
        Arr::set($this->structure, $keyPath, $value);
    }

    /**
     * Sets the path of the "package.json" file.
     *
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;

        if ($this->exists()) {
            $this->parseStructure();
        }
    }

    /**
     * Sets the default structure for newly created "package.json" files.
     *
     * @param array $structure
     * @return void
     */
    public function setDefaultStructure(array $structure)
    {
        $this->defaultStructure = $structure;
    }

    /**
     * Determines if the "package.json" file eixsts.
     *
     * @return bool
     */
    public function exists()
    {
        if (is_null($this->path)) {
            return false;
        }

        return File::exists($this->path);
    }

    /**
     * Creates a new "package.json" file in the path.
     *
     * @param bool $mergeDefaults If `true`, the given structure will be merged with the default structure.
     * @return void
     */
    public function create($structure = [], $mergeDefaults = true)
    {
        $newStructure = ($mergeDefaults) ? $this->defaultStructure : [];
        $newStructure = array_replace($newStructure, $structure);

        if ($this->exists()) {
            File::delete($this->path);
        }

        // Set structure
        $this->structure = $newStructure;

        if (!File::put($this->path, $this->buildFile())) {
            throw new SystemException(sprintf(
                'Unable to write to file "%s". Please check your permissions on this path.',
                $this->path
            ));
        }
    }

    /**
     * Writes the "package.json" file.
     *
     * @return void
     */
    public function write()
    {
        if (!$this->exists()) {
            throw new SystemException(sprintf(
                'Package file "%s" does not exist. Please create it before writing.',
                $this->path
            ));
        }

        if (!File::put($this->path, $this->buildFile())) {
            throw new SystemException(sprintf(
                'Unable to write to file "%s". Please check your permissions on this path.',
                $this->path
            ));
        }
    }

    /**
     * Renders the contents of the "package.json" file.
     *
     * @return string
     */
    protected function buildFile()
    {
        return json_encode($this->structure, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Parses the structure of an existing "package.json" file.
     *
     * @return void
     */
    protected function parseStructure()
    {
        $this->structure = json_decode(File::get($this->path));
    }
}
