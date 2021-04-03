<?php namespace System\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Winter\Storm\Exception\ApplicationException;

/**
 * Console command to run a subsplit of the modules within Winter CMS.
 *
 * @package winter\wn-system-module
 * @author Ben Thomson
 * @author Winter CMS
 */
class WinterSplit extends \Illuminate\Console\Command
{
    /**
     * @var string The console command description.
     */
    protected $description = 'Runs a subsplit to publish the Winter CMS modules in their own repositories.';

    /**
     * @var string The name and signature of the console command.
     */
    protected $signature = 'winter:split {token : GitHub token for writing to the repositories.}
                            {--b|branch= : Publishes a branch in the subsplit repositories. }
                            {--g|git= : The path to the "git" binary. }
                            {--remove-branch= : Removes a branch in the subsplit repositories. }
                            {--remove-tag= : Removes a tag in the subsplit repositories. }
                            {--s|sync : Fully synchronises branches and tags with the subsplit repositories. }
                            {--a|tag= : Publishes a tag in the subsplit repositories. }
                            {--w|work-repo= : Defines the location of the working repository. Defaults to "/storage/temp/split-repo" within the base path. }';

    /**
     * @var bool Indicates whether the command should be shown in the Artisan command list.
     */
    protected $hidden = true;

    /**
     * @var string The path to the "git" binary.
     */
    protected $gitPath;

    /**
     * @var string GitHub token
     */
    protected $token;

    /**
     * @var string The path to the working Git repository.
     */
    protected $workRepoPath;

    /**
     * @var string Origin repository. Requires one string placeholder in the URL to insert a token.
     */
    protected $origin = 'https://%s@github.com/wintercms/winter.git';

    /**
     * @var array Remote subsplit repositories. Each remote requires one string placeholder in the URL to insert a token.
     */
    protected $remotes = [
        'system' => [
            'prefix' => 'modules/system',
            'url' => 'https://%s@github.com/wintercms/wn-system-module-test.git',
        ],
        'backend' => [
            'prefix' => 'modules/backend',
            'url' => 'https://%s@github.com/wintercms/wn-backend-module-test.git',
        ],
        'cms' => [
            'prefix' => 'modules/cms',
            'url' => 'https://%s@github.com/wintercms/wn-cms-module-test.git',
        ],
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->error('Windows is not supported for subsplitting, as no Windows binary for the "splitsh-lite" utility exists.');
            return;
        }

        $this->token = $this->argument('token');

        $this->workRepoPath = (!empty($this->option('work-repo')))
            ? rtrim($this->option('work-repo'), DIRECTORY_SEPARATOR)
            : storage_path('temp/split-repo');
        $this->gitPath = (!empty($this->option('git')))
            ? trim($this->option('git'))
            : $this->getGitPath();

        $action = null;

        foreach (['branch', 'remove-branch', 'remove-tag', 'tag', 'sync'] as $option) {
            if ($this->option($option)) {
                $action = $option;
                break;
            }
        }

        if (is_null($action)) {
            $this->error('You must specify an action to take: one of --branch, --remove-branch, --remove-tag, --tag or --sync');
            return;
        }

        $this->comment('Setting up work repository...');

        if (!$this->workRepoExists()) {
            $this->line(' - Creating work repository.');
            $this->createWorkRepo();
        } else {
            $this->line(' - Work repository already exists.');
            $this->line(' - Updating work repository.');
            $this->updateWorkRepo();
        }

        switch ($action) {
            case 'sync':
            default:
                $this->doSync();
                break;
            case 'branch':
                $this->doBranchSync();
                break;
        }

        $this->comment('Complete');
        $this->line('');
    }

    /**
     * Executes a full synchronisation (all branches and tags) of the subplit repositories.
     *
     * @return void
     */
    protected function doSync()
    {
        $this->comment('Performing full sync of subsplits...');

        $this->line(' - Finding branches.');

        $branches = $this->getBranches();

        // Create progress bar
        $progress = new ProgressBar($this->output);

        foreach ($progress->iterate($branches) as $branch) {
            $progress->clear();
            $this->line('   - Syncing "' . $branch . '" branch.');
            $progress->display();

            $this->syncBranch($branch);
        }
    }

    /**
     * Executes a synchronisation of a branch to the subplit repositories.
     *
     * @return void
     */
    protected function doBranchSync()
    {
        $branch = $this->option('branch');

        $this->comment('Performing sync of "' . $branch . '" to subsplits...');

        $this->line(' - Syncing "' . $branch . '" branch.');

        $this->syncBranch($branch);
    }

    /**
     * Determines if the work repo exists
     *
     * @return void
     */
    protected function workRepoExists()
    {
        return is_dir($this->workRepoPath)
            && file_exists($this->workRepoPath . '/HEAD')
            && is_dir($this->workRepoPath . '/refs');
    }

    /**
     * Creates a working bare Git repository for subsplitting the modules and determining commits.
     *
     * By default, this will be stored in the "storage/temp/split-repo" directory relative to the base path, but it can
     * be modified by the "--work-repo" option in the command line.
     *
     * @return void
     */
    protected function createWorkRepo()
    {
        $this->clearPath($this->workRepoPath);

        if (!mkdir($this->workRepoPath, 0755, true)) {
            throw new ApplicationException('Unable to create a work repository in path "' . $this->workRepoPath . '". Please check your permissions.');
        }

        $command = [
            'clone',
            '--bare',
            sprintf($this->origin, $this->token),
            $this->workRepoPath
        ];

        $process = $this->runGitCommand($command, false);
        if (!$process->isSuccessful()) {
            $this->error('Unable to create work repository in path "' . $this->workRepoPath . '". ' . $process->getErrorOutput());
        } else {
            $this->line(' - Created and checked out bare repository.');
        }

        $this->updateWorkRepo();
    }

    /**
     * Fetches all recent changes to origin in the working bare repository.
     *
     * @return void
     */
    protected function updateWorkRepo()
    {
        $process = $this->runGitCommand([
            'fetch',
            'origin',
            '*:*'
        ]);

        if (!$process->isSuccessful()) {
            $this->error('Unable to update work repository in path "' . $this->workRepoPath . '". ' . $process->getErrorOutput());
        } else {
            $this->line(' - Updated work repository.');
        }

        $this->setRemotes();
    }

    /**
     * Sets the remotes for the working repository to point to subsplit repositories.
     *
     * @return void
     */
    protected function setRemotes()
    {
        $process = $this->runGitCommand(['remote']);
        $remotes = preg_split('/[\n\r]+/', trim($process->getOutput()), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($this->remotes as $remote => $split) {
            $process = $this->runGitCommand([
                'remote',
                (in_array($remote, $remotes)) ? 'set-url' : 'add',
                $remote,
                sprintf($split['url'], $this->token)
            ]);
            if (!$process->isSuccessful()) {
                $this->error(' - Unable to set remote repository for "' . $remote . '" module. ' . $process->getErrorOutput());
            } else {
                $this->line(' - Set remote repository for "' . $remote . '" module.');
            }

            $process = $this->runGitCommand([
                'fetch',
                $remote
            ]);
            if (!$process->isSuccessful()) {
                $this->error(' - Unable to fetch repository for "' . $remote . '" module. ' . $process->getErrorOutput());
            } else {
                $this->line(' - Fetched repository for "' . $remote . '" module.');
            }
        }
    }

    /**
     * Get branches from a repository.
     *
     * By default, this will get the origin branches, but you may specify an optional remote to get branches from the
     * remote.
     *
     * @return array
     */
    protected function getBranches($remote = null)
    {
        if (is_null($remote)) {
            $command = [
                'branch',
                '-l'
            ];
        } elseif (in_array($remote, array_keys($this->remotes))) {
            $command = [
                'branch',
                '-lr',
                '"' . $remote . '/*"'
            ];
        } else {
            throw new ApplicationException('Invalid remote "' . $remote . '" specified.');
        }

        $process = $this->runGitCommand($command);

        if (!$process->isSuccessful()) {
            $this->error('Unable to determine available branches.');
            return;
        }

        $this->line(' - Synchronising branches.');

        $branches = array_map(function ($item) use ($remote) {
            $branch = trim(str_replace('* ', '', $item));

            if (!is_null($remote)) {
                $branch = str_ireplace($remote . '/', '', $branch);
            }

            return $branch;
        }, preg_split('/[\n\r]+/', trim($process->getOutput()), -1, PREG_SPLIT_NO_EMPTY));

        return $branches;
    }

    /**
     * Synchronises a branch with all subsplits.
     *
     * @param string $branch
     * @return void
     */
    protected function syncBranch($branch)
    {
        foreach ($this->remotes as $remote => $split) {
            // Process subsplit through "splitsh" utility for given remote and module
            $process = new Process([
                $this->getSplitshPath(),
                '--origin=heads/' . $branch,
                '--target=heads/' . $remote . '-' . $branch,
                '--prefix=' . $split['prefix'],
                '--path=' . $this->workRepoPath
            ]);
            $this->line('Running command: ' . $process->getCommandLine(), null, OutputInterface::VERBOSITY_DEBUG);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ApplicationException(
                    'Unable to create a subsplit of the ' . $remote . ' module from "' . $split['prefix'] . '". '
                    . $process->getErrorOutput()
                );
            }

            // Push to the remote
            $process = $this->runGitCommand([
                'push',
                '-f',
                $remote,
                'heads/' . $remote . '-' . $branch . ':refs/heads/' . $branch
            ]);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ApplicationException(
                    'Unable to push a subsplit of the ' . $remote . ' module from "' . $split['prefix'] . '". '
                    . $process->getErrorOutput()
                );
            }
        }
    }

    /**
     * Runs a Git command.
     *
     * @param array $command
     * @param boolean $includeWorkDir If true, the working directory will be prepended to the command.
     * @return Process
     */
    protected function runGitCommand($command, $includeWorkDir = true)
    {
        if (empty($this->getGitPath())) {
            return;
        }

        if ($includeWorkDir) {
            array_unshift($command, '--git-dir=' . $this->workRepoPath . '');
        }
        array_unshift($command, $this->getGitPath());

        $process = new Process($command);
        $this->line('Running Git command: ' . implode(' ', $command), null, OutputInterface::VERBOSITY_DEBUG);
        $process->run();

        return $process;
    }

    /**
     * Determines the path to the "git" binary.
     *
     * @return string
     */
    protected function getGitPath()
    {
        if (!empty($this->gitPath)) {
            return $this->gitPath;
        }

        if (PHP_OS_FAMILY == 'Windows') {
            $command = ['where.exe', 'git.exe'];
        } else {
            $command = ['which', 'git'];
        }

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Unable to determine the correct path for the "git" binary. Please make sure it is installed.');
            return;
        }

        $path = $process->getOutput();

        if (empty($path)) {
            $this->error('Unable to determine the correct path for the "git" binary. Please make sure it is installed.');
            return;
        }

        return $this->gitPath = trim($path);
    }

    /**
     * Get path to the "splitsh" utility for the current OS, bundled with Winter CMS.
     *
     * @return string
     */
    protected function getSplitshPath()
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            return base_path('.github/workflows/utilities/vendor/splitsh-lite-mac');
        }

        return base_path('.github/workflows/utilities/vendor/splitsh-lite-unix');
    }

    /**
     * Clears a path, and all its files and subfolders.
     *
     * @param string $path
     * @return void
     * @throws ApplicationException If path is a file
     */
    protected function clearPath($path)
    {
        if (!file_exists($path)) {
            return;
        }
        if (is_file($path)) {
            throw new ApplicationException('Path "' . $path . '" is a file.');
        }

        $process = new Process(['rm', '-rf', $path]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ApplicationException('Unable to clear path "' . $path . '"');
        }
    }
}
