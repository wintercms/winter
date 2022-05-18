<?php namespace System\Console;

use File;
use Event;
use JetBrains\PhpStorm\ArrayShape;
use StdClass;
use Winter\Storm\Console\Command;

/**
 * Console command to implement a "public" folder.
 *
 * This command will create symbolic links to files and directories
 * that are commonly required to be publicly available.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class WinterMirror extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'winter:mirror';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'winter:mirror
        {destination : The destination path relative to the current directory. Eg: public/}
        {--r|relative : Create symlinks relative to the public directory.}
        {--c|copy : Copies files instead of creating symlinks.}
        {--i|ignore=* : Regex patterns of paths to ignore.}
    ';

    /**
     * The console command description.
     */
    protected $description = 'Generates a mirrored public folder using symbolic links.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'october:mirror',
    ];

    /**
     * @var array Files that should be mirrored
     */
    protected $files = [
        '.htaccess',
        'index.php',
        'favicon.ico',
        'robots.txt',
        'humans.txt',
        'sitemap.xml',
    ];

    /**
     * @var array Directories that should be mirrored
     */
    protected $directories = [
        'storage/app/uploads/public',
        'storage/app/media',
        'storage/app/resized',
        'storage/temp/public',
    ];

    /**
     * @var array Wildcard paths that should be mirrored
     */
    protected $wildcards = [
        'modules/*/assets',
        'modules/*/resources',
        'modules/*/behaviors/*/assets',
        'modules/*/behaviors/*/resources',
        'modules/*/widgets/*/assets',
        'modules/*/widgets/*/resources',
        'modules/*/formwidgets/*/assets',
        'modules/*/formwidgets/*/resources',
        'modules/*/reportwidgets/*/assets',
        'modules/*/reportwidgets/*/resources',

        'plugins/*/*/assets',
        'plugins/*/*/resources',
        'plugins/*/*/behaviors/*/assets',
        'plugins/*/*/behaviors/*/resources',
        'plugins/*/*/reportwidgets/*/assets',
        'plugins/*/*/reportwidgets/*/resources',
        'plugins/*/*/formwidgets/*/assets',
        'plugins/*/*/formwidgets/*/resources',
        'plugins/*/*/widgets/*/assets',
        'plugins/*/*/widgets/*/resources',

        'themes/*/assets',
        'themes/*/resources',
    ];

    /**
     * @var string|null Local cache of the mirror destination path
     */
    protected string $destinationPath;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getDestinationPath();

        $paths = new StdClass();
        $paths->files = $this->files;
        $paths->directories = $this->directories;
        $paths->wildcards = $this->wildcards;

        /**
         * @event system.console.mirror.extendPaths
         * Enables extending the `php artisan winter:mirror` command
         *
         * You will have access to a $paths stdClass with `files`, `directories`, `wildcards` properties available for modifying.
         *
         * Example usage:
         *
         *     Event::listen('system.console.mirror.extendPaths', function ($paths) {
         *          $paths->directories = array_merge($paths->directories, ['plugins/myauthor/myplugin/public']);
         *     });
         *
         */
        Event::fire('system.console.mirror.extendPaths', [$paths]);

        foreach ($paths->files as $file) {
            $this->mirrorFile($file);
        }

        foreach ($paths->directories as $directory) {
            $this->mirrorDirectory($directory);
        }

        foreach ($paths->wildcards as $wildcard) {
            $this->mirrorWildcard($wildcard);
        }

        $index = $this->getDestinationPath() . '/index.php';
        if ($this->option('copy') && File::isFile($index)) {
            $this->updateIndexPaths($index);
        }

        $this->output->writeln('<info>Mirror complete!</info>');
    }

    protected function mirrorFile(string $file): bool
    {
        $src = base_path().'/'.$file;

        $dest = $this->getDestinationPath().'/'.$file;

        if (!File::isFile($src) || File::isFile($dest)) {
            return false;
        }

        return $this->mirror($src, $dest);
    }

    protected function mirrorDirectory(string $directory): bool
    {
        $src = base_path().'/'.$directory;

        $dest = $this->getDestinationPath().'/'.$directory;

        if (!File::isDirectory($src) || (File::isDirectory($dest) && !$this->option('copy'))) {
            return false;
        }

        if (!File::isDirectory(dirname($dest))) {
            File::makeDirectory(dirname($dest), 0755, true);
        }

        return $this->mirror($src, $dest);
    }

    protected function mirrorWildcard(string $wildcard): bool
    {
        if (strpos($wildcard, '*') === false) {
            return $this->mirrorDirectory($wildcard);
        }

        list($start, $end) = explode('*', $wildcard, 2);

        $startDir = base_path().'/'.$start;

        if (!File::isDirectory($startDir)) {
            return false;
        }

        foreach (File::directories($startDir) as $directory) {
            $this->mirrorWildcard($start.basename($directory).$end);
        }

        return true;
    }

    protected function mirror(string $src, string $dest): bool
    {
        if ($this->option('relative')) {
            $src = $this->getRelativePath($dest, $src);

            if (strpos($src, '../') === 0) {
                $src = rtrim(substr($src, 3), '/');
            }
        }

        foreach ($this->option('ignore') as $ignore) {
            if (preg_match($ignore, $src)) {
                $this->warn('ignoring: ' . $src);
                return false;
            }
        }

        if (!$this->option('copy')) {
            File::link($src, $dest);
            $this->info('Linked: ' . $dest);
            return true;
        }

        if (File::isDirectory($src) && !File::isDirectory($dest)) {
            File::makeDirectory($dest, 0755, true);
        }

        if (File::isFile($src)) {
            if (!File::isDirectory(dirname($dest))) {
                File::makeDirectory(dirname($dest), 0755, true);
            }

            File::copy($src, $dest);
            $this->info('Copied: ' . $dest);
            return true;
        }

        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $src,
                    \RecursiveDirectoryIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::SELF_FIRST
            ) as $item
        ) {
            if ($item->isDir()) {
                if (!File::isDirectory($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathname())) {
                    File::makeDirectory($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathname());
                }
                continue;
            }
            File::copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathname());
        }

        $this->info('Copied: ' . $dest);

        return true;
    }

    protected function getDestinationPath(): ?string
    {
        if (isset($this->destinationPath)) {
            return $this->destinationPath;
        }

        $destPath = $this->argument('destination');
        if (realpath($destPath) === false) {
            $destPath = base_path() . '/' . $destPath;
        }

        if (!File::isDirectory($destPath)) {
            File::makeDirectory($destPath, 0755, true);
        }

        $destPath = realpath($destPath);

        $this->output->writeln(sprintf('<info>Destination: %s</info>', $destPath));

        return $this->destinationPath = $destPath;
    }

    protected function getRelativePath($from, $to)
    {
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);

        $dir = explode('/', File::isFile($from) ? dirname($from) : rtrim($from, '/'));
        $file = explode('/', $to);

        while ($dir && $file && ($dir[0] == $file[0])) {
            array_shift($dir);
            array_shift($file);
        }

        return str_repeat('../', count($dir)) . implode('/', $file);
    }

    protected function updateIndexPaths(string $index): void
    {
        $contents = File::get($index);

        /**
         * @event system.console.mirror.updateIndexPaths
         * Enables extending the `php artisan winter:mirror` command
         *
         * You will have access to the new index path, and the contents of the file.
         *
         * Example usage:
         *
         *     Event::listen('system.console.mirror.updateIndexPaths', function ($indexPath, $contents) {
         *          return str_replace('a', 'b', $contents);
         *     });
         *
         */
        $result = Event::fire('system.console.mirror.updateIndexPaths', [$index, $contents]);

        if ($result) {
            File::put($index, $result);
            return;
        }

        File::put($index, preg_replace(
            '/\/bootstrap\/(.*?).php\'/',
            str_repeat('/..', count(explode('/', $this->getRelativePath(base_path(), dirname($index))))) . '$0',
            $contents
        ));
    }
}
