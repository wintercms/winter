<?php namespace System\Console;

use System\Classes\UpdateManager;

/**
 * Detects the version of Winter CMS installed.
 *
 * This checks against a central manifest on Winter CMS's GitHub account to determine the version. If any files have
 * been modified, this will be indicated when detecting the version.
 *
 * To get a list of modified files, simply add the "--changes" parameter.
 *
 * @package winter\wn-system-module
 * @author Ben Thomson
 * @author Winter CMS
 */
class WinterVersion extends \Winter\Storm\Console\Command
{
    /**
     * @var string The console command description.
     */
    protected $description = 'Detects the build number (version) of this Winter CMS instance.';

    /**
     * @var string The name and signature of the console command.
     */
    protected $signature = 'winter:version
        {--changes : Include the list of changes between this install and the expected files for the detected build.}
        {--o|only-version : Return only the build version number.}
    ';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October
        $this->setAliases(['october:version']);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->option('only-version')) {
            $this->comment('*** Detecting Winter CMS build...');
        }

        if (!$this->laravel->hasDatabase()) {
            $build = UpdateManager::instance()->getBuildNumberManually($this->option('changes'));

            // Skip setting the build number if no database is detected to set it within
            if (!$this->option('only-version')) {
                $this->comment('*** No database detected - skipping setting the build number.');
            }
        } else {
            $build = UpdateManager::instance()->setBuildNumberManually($this->option('changes'));
        }

        if ($this->option('only-version')) {
            $this->line($build['build']);
            return 0;
        }

        if (!$build['confident']) {
            $this->warn('*** We could not accurately determine your Winter CMS build due to the number of modifications. The closest detected build is Winter CMS build ' . $build['build'] . '.');
        } elseif ($build['modified']) {
            $this->info('*** Detected a modified version of Winter CMS build ' . $build['build'] . '.');
        } else {
            $this->info('*** Detected Winter CMS build ' . $build['build'] . '.');
        }

        if (!empty($build['changes']) && $this->option('changes')) {
            $this->line('');
            $this->comment('We have detected the following modifications:');

            if (count($build['changes']['added'] ?? [])) {
                $this->line('');
                $this->info('Files added:');

                foreach (array_keys($build['changes']['added']) as $file) {
                    $this->line(' - ' . $file);
                }
            }

            if (count($build['changes']['modified'] ?? [])) {
                $this->line('');
                $this->info('Files modified:');

                foreach (array_keys($build['changes']['modified']) as $file) {
                    $this->line(' - ' . $file);
                }
            }

            if (count($build['changes']['removed'] ?? [])) {
                $this->line('');
                $this->info('Files removed:');

                foreach ($build['changes']['removed'] as $file) {
                    $this->line(' - ' . $file);
                }
            }
        }
    }
}
