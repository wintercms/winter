<?php namespace System\Classes;

use Http;
use Config;
use ApplicationException;
use Winter\Storm\Argon\Argon;

/**
 * Reads and stores the Winter CMS source manifest information.
 *
 * The source manifest is a meta JSON file, stored on GitHub, that contains the hashsums of all module files across all
 * builds of Winter CMS. This allows us to compare the Winter CMS installation against the expected file checksums and
 * determine the installed build and whether it has been modified.
 *
 * Since Winter CMS v1.1.1, a forks manifest is also used to determine at which point we forked a branch off to a new
 * major release. This allows us to track concurrent histories - ie. the 1.0.x history vs. the 1.1.x history.
 *
 * @package winter\wn-system-module
 * @author Ben Thomson
 */
class SourceManifest
{
    /**
     * @var string The URL to the source manifest
     */
    protected $source;

    /**
     * @var array Array of builds, keyed by build number, with files for keys and hashes for values.
     */
    protected $builds = [];

    /**
     * @var array The version map where forks occurred.
     */
    protected $forks;

    /**
     * @var string The URL to the forked version manifest
     */
    protected $forksUrl;

    /**
     * Constructor
     */
    public function __construct(string $source = null, string $forks = null, bool $autoload = true)
    {
        $this->setSource($source ?? Config::get(
            'cms.sourceManifestUrl',
            'https://raw.githubusercontent.com/wintercms/meta/master/manifest/builds.json'
        ));

        $this->setForksSource($forks ?? Config::get(
            'cms.forkManifestUrl',
            'https://raw.githubusercontent.com/wintercms/meta/master/manifest/forks.json'
        ));

        if ($autoload) {
            $this->loadSource();
            $this->loadForks();
        }
    }

    /**
     * Sets the source manifest URL.
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * Sets the forked version manifest URL.
     */
    public function setForksSource(string $forks): void
    {
        $this->forksUrl = $forks;
    }

    /**
     * Loads the manifest file.
     *
     * @throws ApplicationException If the manifest is invalid, or cannot be parsed.
     */
    public function loadSource(): static
    {
        if (file_exists($this->source)) {
            $source = file_get_contents($this->source);
        } else {
            $source = Http::get($this->source)->body;
        }

        if (empty($source)) {
            throw new ApplicationException(
                'Source manifest not found'
            );
        }

        $data = json_decode($source, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApplicationException(
                'Unable to decode source manifest JSON data. JSON Error: ' . json_last_error_msg()
            );
        }
        if (!isset($data['manifest']) || !is_array($data['manifest'])) {
            throw new ApplicationException(
                'The source manifest at "' . $this->source . '" does not appear to be a valid source manifest file.'
            );
        }

        foreach ($data['manifest'] as $build) {
            $this->builds[$this->getVersionInt($build['build'])] = [
                'version' => $build['build'],
                'parent' => $build['parent'],
                'modules' => $build['modules'],
                'files' => $build['files'],
            ];
        }

        return $this;
    }

    /**
     * Loads the forked version manifest file.
     *
     * @throws ApplicationException If the manifest is invalid, or cannot be parsed.
     */
    public function loadForks(): static
    {
        if (file_exists($this->forksUrl)) {
            $forks = file_get_contents($this->forksUrl);
        } else {
            $forks = Http::get($this->forksUrl)->body;
        }

        if (empty($forks)) {
            throw new ApplicationException(
                'Forked version manifest not found'
            );
        }

        $data = json_decode($forks, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApplicationException(
                'Unable to decode forked version manifest JSON data. JSON Error: ' . json_last_error_msg()
            );
        }
        if (!isset($data['forks']) || !is_array($data['forks'])) {
            throw new ApplicationException(
                'The forked version manifest at "' . $this->forksUrl . '" does not appear to be a valid forked version
                manifest file.'
            );
        }

        // Map forks to int values
        foreach ($data['forks'] as $child => $parent) {
            $this->forks[$this->getVersionInt($child)] = $this->getVersionInt($parent);
        }

        return $this;
    }

    /**
     * Adds a FileManifest instance as a build to this source manifest.
     *
     * Changes between builds are calculated and stored with the build. Builds are stored in order of semantic
     * versioning: ie. 1.1.1 > 1.1.0 > 1.0.468
     *
     * @param integer $build Build number.
     * @param FileManifest $manifest The file manifest to add as a build.
     */
    public function addBuild($build, FileManifest $manifest): void
    {
        $parent = $this->determineParent($build);

        if (!is_null($parent)) {
            $parent = $parent['version'];
        }

        $this->builds[$this->getVersionInt($build)] = [
            'version' => $build,
            'modules' => $manifest->getModuleChecksums(),
            'parent' => $parent,
            'files' => $this->processChanges($manifest, $parent),
        ];

        // Sort builds numerically in ascending order.
        ksort($this->builds, SORT_NUMERIC);
    }

    /**
     * Gets all builds.
     */
    public function getBuilds(): array
    {
        return array_values(array_map(function ($build) {
            return $build['version'];
        }, $this->builds));
    }

    /**
     * Generates the JSON data to be stored with the source manifest.
     *
     * @throws ApplicationException If no builds have been added to this source manifest.
     */
    public function generate(): string
    {
        if (!count($this->builds)) {
            throw new ApplicationException(
                'No builds have been added to the manifest.'
            );
        }

        $json = [
            '_description' => 'This is the source manifest of changes to Winter CMS for each version. This is used to'
                . ' determine which version of Winter CMS is in use, via the "winter:version" Artisan command.',
            '_created' => Argon::now()->toIso8601String(),
            'manifest' => [],
        ];

        foreach (array_values($this->builds) as $details) {
            $json['manifest'][] = [
                'build' => $details['version'],
                'parent' => $details['parent'] ?? null,
                'modules' => $details['modules'],
                'files' => $details['files'],
            ];
        }

        return json_encode($json, JSON_PRETTY_PRINT);
    }

    /**
     * Gets the filelist state at a selected build.
     *
     * This method will list all expected files and hashsums at the specified build number. It does this by following
     * the history, switching branches as necessary.
     *
     * @param string|integer $build Build version to get the filelist state for.
     * @throws ApplicationException If the specified build has not been added to the source manifest.
     */
    public function getState(mixed $build): array
    {
        if (is_string($build)) {
            $build = $this->getVersionInt($build);
        }

        if (!isset($this->builds[$build])) {
            throw new \Exception('The specified build has not been added.');
        }

        $state = [];

        foreach ($this->builds as $number => $details) {
            // Follow fork if necessary
            if (isset($this->forks) && array_key_exists($build, $this->forks)) {
                $state = $this->getState($this->forks[$build]);
            }

            if (isset($details['files']['added'])) {
                foreach ($details['files']['added'] as $filename => $sum) {
                    $state[$filename] = $sum;
                }
            }
            if (isset($details['files']['modified'])) {
                foreach ($details['files']['modified'] as $filename => $sum) {
                    $state[$filename] = $sum;
                }
            }
            if (isset($details['files']['removed'])) {
                foreach ($details['files']['removed'] as $filename) {
                    unset($state[$filename]);
                }
            }

            if ($number === $build) {
                break;
            }
        }

        return $state;
    }

    /**
     * Compares a file manifest with the source manifest.
     *
     * This will determine the build of the Winter CMS installation.
     *
     * This will return an array with the following information:
     *  - `build`: The build number we determined was most likely the build installed.
     *  - `modified`: Whether we detected any modifications between the installed build and the manifest.
     *  - `confident`: Whether we are at least 60% sure that this is the installed build. More modifications to
     *                  to the code = less confidence.
     *  - `changes`: If $detailed is true, this will include the list of files modified, created and deleted.
     *
     * @param FileManifest $manifest The file manifest to compare against the source.
     * @param bool $detailed If true, the list of files modified, added and deleted will be included in the result.
     */
    public function compare(FileManifest $manifest, bool $detailed = false): array
    {
        $modules = $manifest->getModuleChecksums();

        // Look for an unmodified version
        foreach ($this->getBuilds() as $buildString) {
            $build = $this->builds[$this->getVersionInt($buildString)];

            $matched = array_intersect_assoc($build['modules'], $modules);

            if (count($matched) === count($build['modules'])) {
                $details = [
                    'build' => $buildString,
                    'modified' => false,
                    'confident' => true,
                ];

                if ($detailed) {
                    $details['changes'] = [];
                }

                return $details;
            }
        }

        // If we could not find an unmodified version, try to find the closest version and assume this is a modified
        // install.
        $buildMatch = [];

        foreach ($this->getBuilds() as $buildString) {
            $build = $this->builds[$this->getVersionInt($buildString)];

            $state = $this->getState($buildString);

            // Include only the files that match the modules being loaded in this file manifest
            $availableModules = array_keys($modules);

            foreach ($state as $file => $sum) {
                // Determine module
                $module = explode('/', $file)[2];

                if (!in_array($module, $availableModules)) {
                    unset($state[$file]);
                }
            }

            $filesExpected = count($state);
            $filesFound = [];
            $filesChanged = [];

            foreach ($manifest->getFiles() as $file => $sum) {
                // Unknown new file
                if (!isset($state[$file])) {
                    $filesChanged[] = $file;
                    continue;
                }

                // Modified file
                if ($state[$file] !== $sum) {
                    $filesFound[] = $file;
                    $filesChanged[] = $file;
                    continue;
                }

                // Pristine file
                $filesFound[] = $file;
            }

            $foundPercent = count($filesFound) / $filesExpected;
            $changedPercent = count($filesChanged) / $filesExpected;

            $score = ((1 * $foundPercent) - $changedPercent);
            $buildMatch[$buildString] = round($score * 100, 2);
        }

        // Find likely version
        $likelyBuild = array_search(max($buildMatch), $buildMatch);

        $details = [
            'build' => $likelyBuild,
            'modified' => true,
            'confident' => ($buildMatch[$likelyBuild] >= 60)
        ];

        if ($detailed) {
            $details['changes'] = $this->processChanges($manifest, $likelyBuild);
        }

        return $details;
    }

    /**
     * Determines file changes between the specified build and the previous build.
     *
     * Will return an array of added, modified and removed files.
     *
     * @param FileManifest $manifest The current build's file manifest.
     * @param FileManifest|string|integer $previous Either a previous manifest, or the previous build number as an int
     *  or string, used to determine changes with this build.
     */
    protected function processChanges(FileManifest $manifest, mixed $previous = null): array
    {
        // If no previous build has been provided, all files are added
        if (is_null($previous)) {
            return [
                'added' => $manifest->getFiles(),
            ];
        }

        // Only save files if they are changing the "state" of the manifest (ie. the file is modified, added or removed)
        if (is_int($previous) || is_string($previous)) {
            $state = $this->getState($previous);
        } else {
            $state = $previous->getFiles();
        }
        $added = [];
        $modified = [];

        foreach ($manifest->getFiles() as $file => $sum) {
            if (!isset($state[$file])) {
                $added[$file] = $sum;
                continue;
            } else {
                if ($state[$file] !== $sum) {
                    $modified[$file] = $sum;
                }
                unset($state[$file]);
            }
        }

        // Any files still left in state have been removed
        $removed = array_keys($state);

        $changes = [];
        if (count($added)) {
            $changes['added'] = $added;
        }
        if (count($modified)) {
            $changes['modified'] = $modified;
        }
        if (count($removed)) {
            $changes['removed'] = $removed;
        }

        return $changes;
    }

    /**
     * Determine the parent of the provided build number
     */
    protected function determineParent(string $build): ?array
    {
        $buildInt = $this->getVersionInt($build);

        // First, we'll check for a fork - if so, the source version for the fork is a parent
        if (isset($this->forks) && array_key_exists($buildInt, $this->forks)) {
            return $this->builds[$this->forks[$buildInt]];
        }

        // If not a fork, then determine the parent by finding the nearest minor version to the build
        $parent = null;

        for ($i = 1; $i <= 999; ++$i) {
            if (array_key_exists($buildInt - $i, $this->builds)) {
                $parent = $this->builds[$buildInt - $i];
                break;
            }
        }

        return $parent;
    }

    /**
     * Converts a version string into an integer for comparison.
     *
     * @throws ApplicationException if a version string does not match the format "major.minor.path"
     */
    protected function getVersionInt(string $version): int
    {
        // Get major.minor.patch versions
        if (!preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/', $version, $versionParts)) {
            throw new ApplicationException('Invalid version string - must be of the format "major.minor.path"');
        }

        $int = $versionParts[1] * 1000000;
        $int += $versionParts[2] * 1000;
        $int += $versionParts[3];

        return $int;
    }
}
