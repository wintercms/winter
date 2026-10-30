<?php

namespace System\Tests\Console;

use File;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use System\Tests\Bootstrap\TestCase;

class WinterUtilGitPullTest extends TestCase
{
    /** Throwaway root holding the themes, plugins and upstream fixtures for one test. */
    protected string $sandbox;

    protected string $originalCwd;

    protected function setUp(): void
    {
        parent::setUp();

        if (!(new ExecutableFinder())->find('git')) {
            $this->markTestSkipped('This test requires the git binary to be installed.');
        }

        $this->originalCwd = getcwd();

        // Kept outside the project so git never resolves a parent repository and so a
        // relative side effect of a directory name cannot land in the working tree.
        $this->sandbox = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'wn-gitpull-' . bin2hex(random_bytes(6));

        File::makeDirectory($this->sandbox . '/themes', 0777, true);
        File::makeDirectory($this->sandbox . '/plugins', 0777, true);

        $this->app->setThemesPath($this->sandbox . '/themes');
        $this->app->setPluginsPath($this->sandbox . '/plugins');
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        File::deleteDirectory($this->sandbox);

        parent::tearDown();
    }

    /**
     * Directories whose names carry shell syntax must be handed to git as a path, not
     * parsed. The marker file is only ever created if the name is evaluated.
     */
    public function testThemeDirectoryNamesAreNotEvaluated()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('The directory name used here is POSIX shell syntax.');
        }

        $marker = 'theme-name-was-evaluated';
        $path = $this->sandbox . '/themes/' . 'a-theme;touch${IFS}' . $marker . ';#';

        File::makeDirectory($path, 0777, true);
        File::put($path . '/.git', "gitdir: nowhere\n");

        $this->runGitPull();

        $this->assertFileDoesNotExist($this->sandbox . '/' . $marker);
    }

    public function testPluginDirectoryNamesAreNotEvaluated()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('The directory name used here is POSIX shell syntax.');
        }

        $marker = 'plugin-name-was-evaluated';
        $path = $this->sandbox . '/plugins/acme/' . 'a-plugin;touch${IFS}' . $marker . ';#';

        File::makeDirectory($path, 0777, true);
        File::put($path . '/.git', "gitdir: nowhere\n");

        $this->runGitPull();

        $this->assertFileDoesNotExist($this->sandbox . '/' . $marker);
    }

    public function testAuthorDirectoryNamesAreNotEvaluated()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('The directory name used here is POSIX shell syntax.');
        }

        $marker = 'author-name-was-evaluated';
        $path = $this->sandbox . '/plugins/' . 'acme;touch${IFS}' . $marker . ';#' . '/a-plugin';

        File::makeDirectory($path, 0777, true);
        File::put($path . '/.git', "gitdir: nowhere\n");

        $this->runGitPull();

        $this->assertFileDoesNotExist($this->sandbox . '/' . $marker);
    }

    /**
     * Repositories are pulled, each one is announced, and directories without a `.git`
     * entry are left alone. The theme directory deliberately carries characters that are
     * legal in a directory name but meaningful to a shell, to prove the path still
     * reaches git intact.
     */
    public function testRepositoriesArePulledAndOtherDirectoriesSkipped()
    {
        $themeDir = 'a-theme; $with {braces}';

        $this->makeClone($this->sandbox . '/themes/' . $themeDir);
        $this->makeClone($this->sandbox . '/plugins/acme/widgets');

        File::makeDirectory($this->sandbox . '/themes/no-repo-theme', 0777, true);
        File::makeDirectory($this->sandbox . '/plugins/acme/no-repo-plugin', 0777, true);

        $output = $this->runGitPull();

        // Each repository is announced, and only the repositories.
        $this->assertStringContainsString('Updating theme: ' . $themeDir, $output);
        $this->assertStringContainsString('Updating plugin: acme.widgets', $output);
        $this->assertStringNotContainsString('no-repo-theme', $output);
        $this->assertStringNotContainsString('no-repo-plugin', $output);

        // The pull actually ran in each repository and brought the upstream commit down.
        $this->assertFileExists($this->sandbox . '/themes/' . $themeDir . '/pulled.txt');
        $this->assertFileExists($this->sandbox . '/plugins/acme/widgets/pulled.txt');

        // The directories that were skipped were not touched.
        $this->assertFileDoesNotExist($this->sandbox . '/themes/no-repo-theme/pulled.txt');
    }

    /**
     * Creates an upstream repository holding one commit, clones it to the given path so
     * the clone has a tracking branch, then adds `pulled.txt` upstream. A successful
     * `git pull` in the clone is therefore observable as that file appearing.
     */
    protected function makeClone(string $path): void
    {
        $upstream = $this->sandbox . '/upstream-' . bin2hex(random_bytes(4));

        File::makeDirectory($upstream, 0777, true);

        $this->git($upstream, ['init', '-q']);
        $this->git($upstream, ['config', 'user.email', 'test@example.com']);
        $this->git($upstream, ['config', 'user.name', 'Winter Test']);
        $this->git($upstream, ['commit', '-q', '--allow-empty', '-m', 'first']);

        File::makeDirectory(dirname($path), 0777, true, true);
        $this->git($this->sandbox, ['clone', '-q', $upstream, $path]);

        File::put($upstream . '/pulled.txt', "pulled\n");
        $this->git($upstream, ['add', 'pulled.txt']);
        $this->git($upstream, ['commit', '-q', '-m', 'second']);
    }

    protected function git(string $cwd, array $arguments): void
    {
        (new Process(
            command: ['git', ...$arguments],
            cwd: $cwd,
            timeout: null
        ))->mustRun();
    }

    /**
     * Runs the utility with the process working directory inside the sandbox and returns
     * everything it printed.
     */
    protected function runGitPull(): string
    {
        chdir($this->sandbox);
        ob_start();

        try {
            $this->artisan('winter:util git pull')->execute();
        } finally {
            $output = ob_get_clean();
            chdir($this->originalCwd);
        }

        return $output;
    }
}
