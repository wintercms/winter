<?php

namespace System\Console\Asset\Npm;

use Symfony\Component\Process\Process;
use System\Console\Asset\Npm\NpmCommand;

class NpmVersion extends NpmCommand
{
    const NPM_MINIMUM_SUPPORTED_VERSION = '7.0';

    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'npm:version';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'npm:version
        {--c|compatible : Report compatible version via exit code.}
        {--s|silent : Silent mode.}
        {--disable-tty : Disable tty mode}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Runs a script in a given package.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $process = new Process(
            ['npm', '--version'],
            base_path(),
            ['NODE_ENV' => $this->getNodeEnv()],
            null,
            null
        );

        $output = '';

        $exit = $process->run(function ($status, $stdout) use (&$output) {
            $output .= $stdout;
        });

        $output = trim($output);

        // Npm failed for some reason, report to user
        if ($exit !== 0) {
            $this->error('NPM exited with error: ' . $output);
            return $exit;
        }

        // Report the version to user
        if (!$this->option('silent')) {
            $this->info($output);
        }

        // If the user has not requested a compatibility check, then return 0
        if (!$this->option('compatible')) {
            return 0;
        }

        // If the version of npm is less than the required minimum, then return fail
        if (version_compare($output, static::NPM_MINIMUM_SUPPORTED_VERSION, '<')) {
            return 1;
        }

        return 0;
    }
}
