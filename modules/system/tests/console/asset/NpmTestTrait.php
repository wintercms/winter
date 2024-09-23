<?php

namespace System\Tests\Console\Asset;

trait NpmTestTrait
{
    protected string $jsonPath;
    protected string $backupPath;

    /**
     * Helper to run test logic and handle restoring package.json file after
     */
    protected function withPackageJsonRestore(callable $callback): void
    {
        copy($this->jsonPath, $this->backupPath);
        $callback();
        rename($this->backupPath, $this->jsonPath);
    }
}
