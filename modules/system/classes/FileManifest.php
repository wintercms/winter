<?php namespace System\Classes;

use ApplicationException;
use Config;
use Winter\Storm\Filesystem\Filesystem;
use Winter\Storm\Halcyon\Datasource\FileDatasource;

/**
 * Stores the file manifest for this Winter CMS installation.
 *
 * This manifest is a file checksum of all files within this Winter CMS installation. When compared to the source
 * manifest, this allows us to determine the current installation's build number.
 *
 * @package winter\wn-system-module
 * @author Ben Thomson
 */
class FileManifest
{
    /**
     * @var string Root folder of this installation.
     */
    protected $root;

    /**
     * @var array Modules to store in manifest.
     */
    protected $modules = ['system', 'backend', 'cms'];

    /**
     * @var array Files cache.
     */
    protected $files = [];

    /**
     * @var array File extensions to normalize newlines for
     */
    protected $normalizeExtensions = [
        'css',
        'htm',
        'html',
        'js',
        'json',
        'less',
        'md',
        'php',
        'sass',
        'scss',
        'svg',
        'txt',
        'xml',
        'yaml',
    ];

    /**
     * Constructor.
     */
    public function __construct(string $root = null, array $modules = null)
    {
        $this->setRoot($root ?? base_path());
        $this->setModules($modules ?? Config::get('cms.loadModules', ['System', 'Backend', 'Cms']));
    }

    /**
     * Sets the root folder.
     *
     * @throws ApplicationException If the specified root does not exist.
     */
    public function setRoot(string $root): static
    {
        if (is_string($root)) {
            $this->root = realpath($root);

            if ($this->root === false || !is_dir($this->root)) {
                throw new ApplicationException(
                    'Invalid root specified for the file manifest.'
                );
            }
        }

        return $this;
    }

    /**
     * Sets the modules.
     */
    public function setModules(array $modules): static
    {
        $this->modules = array_map(function ($module) {
            return strtolower($module);
        }, $modules);

        return $this;
    }

    /**
     * Gets a list of files and their corresponding hashsums.
     */
    public function getFiles(): array
    {
        if (count($this->files)) {
            return $this->files;
        }

        $files = [];

        foreach ($this->modules as $module) {
            $path = $this->root . '/modules/' . $module;

            if (!is_dir($path)) {
                continue;
            }

            foreach ($this->findFiles($path) as $file) {
                $files[$this->getFilename($file)] = hash('sha3-256', $this->normalizeFileContents($file));
            }
        }

        return $this->files = $files;
    }

    /**
     * Gets the checksum of a specific install.
     */
    public function getModuleChecksums(): array
    {
        if (!count($this->files)) {
            $this->getFiles();
        }

        $modules = [];
        foreach ($this->modules as $module) {
            $modules[$module] = '';
        }

        foreach ($this->files as $path => $hash) {
            // Determine module
            $module = explode('/', $path)[2];

            $modules[$module] .= $hash;
        }

        return array_map(function ($moduleSum) {
            return hash('sha3-256', $moduleSum);
        }, $modules);
    }

    /**
     * Finds all files within the path.
     */
    protected function findFiles(string $basePath): array
    {
        $datasource = new FileDatasource($basePath, new Filesystem);

        $files = array_map(function ($path) use ($basePath) {
            return $basePath . '/' . $path;
        }, array_keys($datasource->getAvailablePaths()));

        // Ensure files are sorted so they are in a consistent order, no matter the way the OS returns the file list.
        sort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Returns the filename without the root.
     */
    protected function getFilename(string $file): string
    {
        return substr($file, strlen($this->root));
    }

    /**
     * Normalises the file contents, irrespective of OS.
     */
    protected function normalizeFileContents(string $file): string
    {
        if (!is_file($file)) {
            return '';
        }

        $contents = file_get_contents($file);

        // Replace Windows newlines in text files with Unix newlines
        if (
            PHP_EOL === "\r\n"
            && in_array(pathinfo($file, PATHINFO_EXTENSION), $this->normalizeExtensions)
        ) {
            $contents = str_replace(PHP_EOL, "\n", $contents);
        }

        return $contents;
    }
}
