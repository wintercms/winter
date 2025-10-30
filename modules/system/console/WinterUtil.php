<?php namespace System\Console;

use Lang;
use File;
use Config;
use DirectoryIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File as Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use System\Classes\UpdateManager;
use System\Classes\CombineAssets;
use System\Models\Parameter;
use System\Models\File as FileModel;
use Winter\Storm\Filesystem\Zip;
use Winter\Storm\Network\Http as NetworkHttp;
use Winter\Storm\Support\Facades\Http;

/**
 * Console command for other utility commands.
 *
 * This provides functionality that doesn't quite deserve its own dedicated
 * console class. It is used mostly developer tools and maintenance tasks.
 *
 * Currently supported commands:
 *
 * - purge resized: Deletes all files in the resized directory.
 * - purge thumbs: Deletes all thumbnail files in the uploads directory.
 * - purge uploads: Deletes files in the uploads directory that do not exist in the "system_files" table.
 * - git pull: Perform "git pull" on all plugins and themes.
 * - compile assets: Compile registered Language, LESS and JS files.
 * - compile js: Compile registered JS files only.
 * - compile less: Compile registered LESS files only.
 * - compile scss: Compile registered SCSS files only.
 * - compile lang: Compile registered Language files only.
 * - set project --projectId=<id>: Set the projectId for this winter instance.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class WinterUtil extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * The console command name.
     */
    protected $name = 'winter:util';

    /**
     * The console command description.
     */
    protected $description = 'Utility commands for Winter';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October
        $this->setAliases(['october:util']);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $command = implode(' ', (array) $this->argument('name'));
        $method = 'util'.studly_case($command);

        $methods = preg_grep('/^util/', get_class_methods(get_called_class()));
        $list = array_map(function ($item) {
            return "winter:".snake_case($item, " ");
        }, $methods);

        if (!$this->argument('name')) {
            $message = 'There are no commands defined in the "util" namespace.';
            if (1 == count($list)) {
                $message .= "\n\nDid you mean this?\n    ";
            } else {
                $message .= "\n\nDid you mean one of these?\n    ";
            }

            $message .= implode("\n    ", $list);
            throw new \InvalidArgumentException($message);
        }

        if (!method_exists($this, $method)) {
            $this->error(sprintf('Utility command "%s" does not exist!', $command));
            return;
        }

        $this->$method();
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::IS_ARRAY, 'The utility command to perform, For more info, see "https://wintercms.com/docs/v1.2/docs/console/utilities#utility-runner".'],
        ];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['debug', null, InputOption::VALUE_NONE, 'Run the operation in debug / development mode.'],
            ['projectId', null, InputOption::VALUE_REQUIRED, 'Specify a projectId for set project'],
            ['missing-files', null, InputOption::VALUE_NONE, 'Purge system_files records for missing storage files'],
        ];
    }

    //
    // Utilties
    //

    protected function utilSetBuild()
    {
        $this->comment('NOTE: This command is now deprecated. Please use "php artisan winter:version" instead.');
        $this->comment('');

        return $this->call('winter:version');
    }

    protected function utilCompileJs()
    {
        $this->utilCompileAssets('js');
    }

    protected function utilCompileLess()
    {
        $this->utilCompileAssets('less');
    }

    protected function utilCompileScss()
    {
        $this->utilCompileAssets('scss');
    }

    protected function utilCompileAssets($type = null)
    {
        // Download Font Awesome icons if they are missing and LESS files are being compiled
        if (
            (
                !is_dir(base_path('node_modules/@fortawesome/fontawesome-free'))
                || !is_file(base_path('node_modules/@fortawesome/fontawesome-free/less/_variables.less'))
            )
            && ($type === 'less' || $type === null)
        ) {
            $this->comment('Downloading Font Awesome icons...');

            $releases = Http::get('https://api.github.com/repos/FortAwesome/Font-Awesome/releases/191042913', function (NetworkHttp $http) {
                $http->header('Accept', 'application/json');
                $http->header('User-Agent', 'Winter CMS');
            });

            if (!$releases->ok) {
                $this->error('Failed to download Font Awesome icons');
                return;
            }

            $releases = json_decode($releases->body, true);
            $releaseName = null;
            $zipUrl = null;

            foreach ($releases['assets'] as $asset) {
                if (
                    str_starts_with($asset['name'], 'fontawesome-free-')
                    && str_ends_with($asset['name'], 'web.zip')
                ) {
                    $zipUrl = $asset['browser_download_url'];
                    $releaseName = pathinfo($asset['name'], PATHINFO_FILENAME);
                }
            }

            if (is_null($zipUrl)) {
                $this->error('Failed to find Font Awesome icons download URL');
                return;
            }

            Http::get($zipUrl, function (NetworkHttp $http) {
                $http->header('User-Agent', 'Winter CMS');
                $http->toFile(storage_path('temp/fontawesome.zip'));
            });

            // Extract Font Awesome files
            if (is_dir(storage_path('temp/fontawesome'))) {
                $this->rimraf(storage_path('temp/fontawesome'));
            }
            
            Zip::extract(storage_path('temp/fontawesome.zip'), storage_path('temp/fontawesome'));
            Filesystem::delete(storage_path('temp/fontawesome.zip'));

            // Move Font Awesome LESS and font files into place
            Filesystem::makeDirectory(base_path('node_modules/@fortawesome/fontawesome-free/less'), 0755, true);
            Filesystem::moveDirectory(storage_path('temp/fontawesome/' . $releaseName . '/less'), base_path('node_modules/@fortawesome/fontawesome-free/less'));
            Filesystem::copyDirectory(storage_path('temp/fontawesome/' . $releaseName . '/webfonts'), base_path('modules/system/assets/ui/font'));

            // Remove remaining files
            $this->rimraf(storage_path('temp/fontawesome'));
        }

        $this->comment('Compiling registered asset bundles...');

        Config::set('cms.enableAssetMinify', !$this->option('debug'));
        $combiner = CombineAssets::instance();
        $bundles = $combiner->getBundles($type);

        if (!$bundles) {
            $this->comment('Nothing to compile!');
            return;
        }

        if ($type) {
            $bundles = [$bundles];
        }

        foreach ($bundles as $bundleType) {
            foreach ($bundleType as $destination => $assets) {
                $destination = File::symbolizePath($destination);
                $publicDest = File::localToPublic(realpath(dirname($destination))) . '/' . basename($destination);

                $combiner->combineToFile($assets, $destination);
                $shortAssets = implode(', ', array_map('basename', $assets));
                $this->comment($shortAssets);
                $this->comment(sprintf(' -> %s', $publicDest));
            }
        }

        if ($type === null) {
            $this->utilCompileLang();
        }
    }

    protected function utilCompileLang()
    {
        if (!$locales = Lang::get('system::lang.locale')) {
            return;
        }

        $this->comment('Compiling client-side language files...');

        $locales = array_keys($locales);
        $stub = base_path() . '/modules/system/assets/js/lang/lang.stub';

        foreach ($locales as $locale) {
            /*
             * Generate messages
             */
            $fallbackPath = base_path() . '/modules/system/lang/en/client.php';
            $srcPath = base_path() . '/modules/system/lang/'.$locale.'/client.php';

            $messages = require $fallbackPath;

            if (File::isFile($srcPath) && $fallbackPath != $srcPath) {
                $messages = array_replace_recursive($messages, require $srcPath);
            }

            /*
             * Load possible replacements from /lang
             */
            $overrides = [];
            $parentOverrides = [];

            $overridePath = base_path() . '/lang/'.$locale.'/system/client.php';
            if (File::isFile($overridePath)) {
                $overrides = require $overridePath;
            }

            if (str_contains($locale, '-')) {
                list($parentLocale, $country) = explode('-', $locale);

                $parentOverridePath = base_path() . '/lang/'.$parentLocale.'/system/client.php';
                if (File::isFile($parentOverridePath)) {
                    $parentOverrides = require $parentOverridePath;
                }
            }

            $messages = array_replace_recursive($messages, $parentOverrides, $overrides);

            /*
             * Compile from stub and save file
             */
            $destPath = base_path() . '/modules/system/assets/js/lang/lang.'.$locale.'.js';

            $contents = str_replace(
                ['{{locale}}', '{{messages}}'],
                [$locale, json_encode($messages)],
                File::get($stub)
            );

            /*
             * Include the moment localization data
             */
            $momentPath = base_path() . '/modules/system/assets/ui/vendor/moment/locale/'.$locale.'.js';
            if (File::exists($momentPath)) {
                $contents .= PHP_EOL.PHP_EOL.File::get($momentPath).PHP_EOL;
            }

            File::put($destPath, $contents);

            /*
             * Output notes
             */
            $publicDest = File::localToPublic(realpath(dirname($destPath))) . '/' . basename($destPath);

            $this->comment($locale.'/'.basename($srcPath));
            $this->comment(sprintf(' -> %s', $publicDest));
        }
    }

    protected function utilPurgeResized()
    {
        if (!$this->confirmToProceed('This will PERMANENTLY DELETE all files in the resized directory.')) {
            return;
        }

        $resizedDisk = Config::get('cms.storage.resized.disk', 'local');
        $resizedFolder = Config::get('cms.storage.resized.folder', 'resized');

        $totalCount = count(Storage::disk($resizedDisk)->allFiles($resizedFolder));

        foreach (Storage::disk($resizedDisk)->directories($resizedFolder, false) as $directory) {
            Storage::disk($resizedDisk)->deleteDirectory($directory);
        }

        if ($totalCount > 0) {
            $this->comment(sprintf('Successfully deleted %d file(s)', $totalCount));
        } else {
            $this->comment('No files found to purge.');
        }
    }

    protected function utilPurgeThumbs()
    {
        if (!$this->confirmToProceed('This will PERMANENTLY DELETE all thumbs in the uploads directory.')) {
            return;
        }

        $totalCount = 0;
        $uploadsPath = Config::get('filesystems.disks.local.root', storage_path('app'));
        $uploadsPath .= '/uploads';

        /*
         * Recursive function to scan the directory for files beginning
         * with "thumb_" and repeat itself on directories.
         */
        $purgeFunc = function ($targetDir) use (&$purgeFunc, &$totalCount) {
            if ($files = File::glob($targetDir.'/thumb_*')) {
                foreach ($files as $file) {
                    $this->info('Purged: '. basename($file));
                    $totalCount++;
                    @unlink($file);
                }
            }

            if ($dirs = File::directories($targetDir)) {
                foreach ($dirs as $dir) {
                    $purgeFunc($dir);
                }
            }
        };

        $purgeFunc($uploadsPath);

        if ($totalCount > 0) {
            $this->comment(sprintf('Successfully deleted %s thumbs', $totalCount));
        }
        else {
            $this->comment('No thumbs found to delete');
        }
    }

    protected function utilPurgeUploads()
    {
        if (!$this->confirmToProceed('This will PERMANENTLY DELETE files in the uploads directory that do not exist in the "system_files" table.')) {
            return;
        }

        $uploadsDisk = Config::get('cms.storage.uploads.disk', 'local');

        $uploadsFolder = Config::get('cms.storage.uploads.folder', 'uploads');

        $totalCount = 0;

        $validFiles = FileModel::pluck('disk_name')->all();

        foreach (Storage::disk($uploadsDisk)->allFiles($uploadsFolder) as $filePath) {
            $fileName = basename($filePath);

            // Skip .gitignore files
            if ($fileName === '.gitignore') {
                continue;
            }
            // Purge invalid files
            if (!in_array($fileName, $validFiles)) {
                // Purge invalid upload file
                Storage::disk($uploadsDisk)->delete($filePath);
                $this->info('Purged: ' . $filePath);
                // Purge parent directories
                $currentDir = dirname($filePath);
                while ($currentDir !== $uploadsFolder) {
                    // Get parent directory children
                    $children = Storage::disk($uploadsDisk)->allFiles($currentDir);
                    // Parent directory is empty
                    if (count($children) === 0) {
                        Storage::disk($uploadsDisk)->deleteDirectory($currentDir);
                        $this->info('Removed folder: ' . $currentDir);
                    } else {
                        // Parent directory is not empty
                        // stop the iteration
                        break;
                    }
                    $currentDir = dirname($currentDir);
                }
                $totalCount++;
            }
        }

        if ($totalCount > 0) {
            $this->comment(sprintf('Successfully deleted %d invalid file(s), leaving %d valid files', $totalCount, count($validFiles)));
        } else {
            $this->comment('No files found to purge.');
        }
    }

    protected function utilPurgeOrphans()
    {
        if (!$this->confirmToProceed('This will PERMANENTLY DELETE files in "system_files" that do not belong to any other model.')) {
            return;
        }

        $isDebug = $this->option('debug');
        $orphanedFiles = 0;
        $isLocalStorage = Config::get('cms.storage.uploads.disk', 'local') === 'local';

        $files = FileModel::whereDoesntHaveMorph('attachment', '*')
                    ->orWhereNull('attachment_id')
                    ->orWhereNull('attachment_type')
                    ->get();

        foreach ($files as $file) {
            if (!$isDebug) {
                $file->delete();
            }
            $orphanedFiles += 1;
        }

        if ($this->option('missing-files') && $isLocalStorage) {
            foreach (FileModel::all() as $file) {
                if (!File::exists($file->getLocalPath())) {
                    if (!$isDebug) {
                        $file->delete();
                    }
                    $orphanedFiles += 1;
                }
            }
        }

        if ($orphanedFiles > 0) {
            $this->comment(sprintf('Successfully deleted %d orphaned record(s).', $orphanedFiles));
        } else {
            $this->comment('No records to purge.');
        }
    }

    /**
     * This command requires the git binary to be installed.
     */
    protected function utilGitPull()
    {
        foreach (File::directories(plugins_path()) as $authorDir) {
            foreach (File::directories($authorDir) as $pluginDir) {
                if (!File::exists($pluginDir.'/.git')) {
                    continue;
                }

                $exec = 'cd ' . $pluginDir . ' && ';
                $exec .= 'git pull 2>&1';
                echo 'Updating plugin: '. basename(dirname($pluginDir)) .'.'. basename($pluginDir) . PHP_EOL;
                echo shell_exec($exec);
            }
        }

        foreach (File::directories(themes_path()) as $themeDir) {
            if (!File::exists($themeDir.'/.git')) {
                continue;
            }

            $exec = 'cd ' . $themeDir . ' && ';
            $exec .= 'git pull 2>&1';
            echo 'Updating theme: '. basename($themeDir) . PHP_EOL;
            echo shell_exec($exec);
        }
    }

    protected function utilSetProject()
    {
        $projectId = $this->option('projectId');

        if (empty($projectId)) {
            $this->error("No projectId defined, use --projectId=<id> to set a projectId");
            return;
        }

        $manager = UpdateManager::instance();
        $result = $manager->requestProjectDetails($projectId);

        Parameter::set([
            'system::project.id'    => $projectId,
            'system::project.name'  => $result['name'],
            'system::project.owner' => $result['owner'],
        ]);
    }

    /**
     * PHP-based "rm -rf" command.
     *
     * Recursively removes a directory and all files and subdirectories within.
     */
    protected function rimraf(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        if (is_file($path)) {
            @unlink($path);
            return;
        }

        $dir = new DirectoryIterator($path);

        foreach ($dir as $item) {
            if ($item->isDot()) {
                continue;
            }

            if ($item->isDir()) {
                $this->rimraf($item->getPathname());
            }

            @unlink($item->getPathname());
        }

        @rmdir($path);
    }
}
