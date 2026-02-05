<?php

namespace System\Tests\Console\Asset\Vite;

use Symfony\Component\Process\Process;
use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Support\Facades\File;

class BaseViteTestCase extends TestCase
{
    protected string $viteVersion;

    public function setUp(): void
    {
        parent::setUp();

        if (!File::exists(base_path('node_modules'))) {
            $this->markTestSkipped('This test requires node_modules to be installed');
        }

        if (!File::exists(base_path('node_modules/.bin/vite'))) {
            $this->markTestSkipped('This test requires the vite package to be installed');
        }

        if (!isset($this->viteVersion)) {
            $this->viteVersion = $this->getViteVersion();
        }
    }

    protected function getViteVersion(): string
    {
        $process = Process::fromShellCommandline('npx vite --version', base_path());
        $process->run();
        $out = trim($process->getOutput());

        preg_match('/^vite\/([\d.]*) /', $out, $matches);

        return $matches[1] ?? '6.0.0';
    }
}
