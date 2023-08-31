<?php namespace System\Console;

use ApplicationException;
use Http;
use ZipArchive;
use System\Classes\FileManifest;
use System\Classes\SourceManifest;

/**
 * Console command to generate a release/tag manifest for Winter CMS version checks.
 *
 * @package winter\wn-system-module
 * @author Ben Thomson
 * @author Winter CMS
 */
class WinterManifest extends \Illuminate\Console\Command
{
    /**
     * @var string The console command description.
     */
    protected $description = 'Generates a build manifest of Winter CMS builds.';

    /**
     * @var string The name and signature of the console command.
     */
    protected $signature = 'winter:manifest
                            {target : Specifies the target file for the build manifest.}
                            {--token= : Specifies a GitHub token, to get around rate limits.}
                            {--minBuild= : Specifies the minimum build number to retrieve from the source.}
                            {--maxBuild= : Specifies the maximum build number to retreive from the source.}';

    /**
     * @var bool Indicates whether the command should be shown in the Artisan command list.
     */
    protected $hidden = true;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October
        $this->setAliases(['october:manifest']);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $minBuild = $this->getVersionInt($this->option('minBuild') ?? '1.0.420');
        $maxBuild = $this->getVersionInt($this->option('maxBuild') ?? '1.999.999');

        $targetFile = (substr($this->argument('target'), 0, 1) === '/')
            ? $this->argument('target')
            : getcwd() . '/' . $this->argument('target');

        if (empty($targetFile)) {
            throw new ApplicationException(
                'A target argument must be specified for the generated manifest file.'
            );
        }

        if ($minBuild > $maxBuild) {
            throw new ApplicationException(
                'Minimum build specified is larger than the maximum build specified.'
            );
        }

        if (file_exists($targetFile)) {
            $manifest = new SourceManifest($targetFile);
        } else {
            $manifest = new SourceManifest('', null, false);
        }

        // Create temporary directory to hold builds
        $buildDir = storage_path('temp/builds/');
        if (!is_dir($buildDir)) {
            mkdir($buildDir, 0775, true);
        }

        // Find all released builds
        $page = 0;
        $sourceBuilds = [];

        while (true) {
            ++$page;

            if ($this->option('token')) {
                $url = 'https://' . $this->option('token') . '@api.github.com/repos/wintercms/winter/tags?per_page=100&page=' . $page;
            } else {
                $url = 'https://api.github.com/repos/wintercms/winter/tags?per_page=100&page=' . $page;
            }

            $builds = Http::get($url, function ($http) {
                $http->header('User-Agent', 'Winter CMS');
                $http->header('Accept', 'application/vnd.github.v3+json');
            });

            if ($builds->code !== 200) {
                break;
            }

            $builds = json_decode($builds->body);

            if (empty($builds)) {
                break;
            }

            foreach ($builds as $build) {
                $version = preg_replace('/[^0-9\.]+/', '', $build->name);
                $versionInt = $this->getVersionInt($version);

                if ($versionInt >= $minBuild && $versionInt <= $maxBuild) {
                    $sourceBuilds[] = [
                        'version' => $version,
                        'download' => $build->zipball_url
                    ];
                }
            }
        }

        // Sort by version number
        $sourceBuilds = array_sort($sourceBuilds, function ($item) {
            return $this->getVersionInt($item['version']);
        });

        foreach ($sourceBuilds as $sourceBuild) {
            $build = $sourceBuild['version'];

            // Download version from GitHub
            $this->comment('Processing build ' . $build);
            $this->line('  - Downloading...');

            if (file_exists($buildDir . 'build-' . $build . '.zip') || is_dir($buildDir . $build . '/')) {
                $this->info('  - Already downloaded.');
            } else {
                Http::get($sourceBuild['download'], function ($http) use ($buildDir, $build) {
                    $http->header('User-Agent', 'Winter CMS');
                    $http->toFile($buildDir . 'build-' . $build . '.zip');
                });

                $zipFile = @file_get_contents($buildDir . 'build-' . $build . '.zip');

                if (empty($zipFile)) {
                    $this->error('  - Not found (' . $sourceBuild['download'] . ').');
                    break;
                }

                $this->info('  - Downloaded.');
            }

            // Extract version
            $this->line('  - Extracting...');
            if (is_dir($buildDir . $build . '/')) {
                $this->info('  - Already extracted.');
            } else {
                $zip = new ZipArchive;
                if ($zip->open($buildDir . 'build-' . $build . '.zip')) {
                    $rootFolder = substr($zip->statIndex(0)['name'], 0, -1);

                    $toExtract = [];
                    $paths = [
                        $rootFolder . '/modules/backend/',
                        $rootFolder . '/modules/cms/',
                        $rootFolder . '/modules/system/',
                    ];

                    // Only get necessary files from the modules directory
                    for ($i = 1; $i < $zip->numFiles; ++$i) {
                        $filename = $zip->statIndex($i)['name'];

                        foreach ($paths as $path) {
                            if (strpos($filename, $path) === 0) {
                                $toExtract[] = $filename;
                                break;
                            }
                        }
                    }

                    if (!count($toExtract)) {
                        $this->error('  - Unable to get valid files for extraction. Cancelled.');
                        exit(1);
                    }

                    $zip->extractTo($buildDir . $build . '/', $toExtract);
                    $zip->close();

                    // Rename root folder
                    rename($buildDir . $build . '/' . $rootFolder, $buildDir . $build . '/winter-' . $build);

                    // Remove ZIP file
                    unlink($buildDir . 'build-' . $build . '.zip');
                } else {
                    $this->error('  - Unable to extract zip file. Cancelled.');
                    exit(1);
                }

                $this->info('  - Extracted.');
            }

            // Add build to manifest
            $this->line('  - Adding to manifest...');
            $buildManifest = new FileManifest($buildDir . $build . '/winter-' . $build);
            $manifest->addBuild($build, $buildManifest);
            $this->info('  - Added.');
        }

        // Generate manifest
        $this->comment('Generating manifest...');
        file_put_contents($targetFile, $manifest->generate());

        $this->comment('Completed.');
    }

    /**
     * Converts a version string into an integer for comparison.
     *
     * @param string $version
     * @throws ApplicationException if a version string does not match the format "major.minor.path"
     * @return int
     */
    protected function getVersionInt(string $version)
    {
        // Get major.minor.patch versions
        if (!preg_match('/^v?([0-9]+)\.([0-9]+)\.([0-9]+)/', $version, $versionParts)) {
            throw new ApplicationException('Invalid version string - must be of the format "major.minor.path"');
        }

        $int = $versionParts[1] * 1000000;
        $int += $versionParts[2] * 1000;
        $int += $versionParts[3];

        return $int;
    }
}
