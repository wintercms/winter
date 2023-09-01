<?php

namespace System\Classes\Packager\Commands;

use Cache;
use System\Classes\Packager\ComposerFactory;
use Winter\Packager\Commands\Update;

class UpdateCommand extends Update
{
    protected ?string $package = null;

    /**
     * Handle options before execution.
     *
     * @param boolean $includeDev Include "require-dev" dependencies in the update.
     * @param boolean $lockFileOnly Do a lockfile update only, do not install dependencies.
     * @param boolean $ignorePlatformReqs Ignore platform reqs when running the update.
     * @param string $installPreference Set an install preference - must be one of "none", "dist", "source"
     * @param boolean $ignoreScripts Ignores scripts that run after Composer events.
     * @return void
     */
    public function handle(
        bool $includeDev = true,
        bool $lockFileOnly = false,
        bool $ignorePlatformReqs = false,
        string $installPreference = 'none',
        bool $ignoreScripts = false,
        bool $dryRun = false,
        ?string $package = null
    ) {
        if ($this->executed) {
            return;
        }

        $this->includeDev = $includeDev;
        $this->lockFileOnly = $lockFileOnly;
        $this->ignorePlatformReqs = $ignorePlatformReqs;
        $this->ignoreScripts = $ignoreScripts;
        $this->dryRun = $dryRun;
        $this->package = $package;

        if (in_array($installPreference, [self::PREFER_NONE, self::PREFER_DIST, self::PREFER_SOURCE])) {
            $this->installPreference = $installPreference;
        }
    }

    /**
     * @inheritDoc
     */
    public function arguments(): array
    {
        $arguments = [];

        if ($this->dryRun) {
            $arguments['--dry-run'] = true;
        }

        if ($this->lockFileOnly) {
            $arguments['--no-install'] = true;
        }

        if ($this->ignorePlatformReqs) {
            $arguments['--ignore-platform-reqs'] = true;
        }

        if ($this->ignoreScripts) {
            $arguments['--no-scripts'] = true;
        }

        if (in_array($this->installPreference, [self::PREFER_DIST, self::PREFER_SOURCE])) {
            $arguments['--prefer-' . $this->installPreference] = true;
        }

        if ($this->package) {
            $arguments['--'] = true;
            $arguments[$this->package] = true;
        }

        return $arguments;
    }

    public function execute()
    {
        Cache::forget(ComposerFactory::COMPOSER_CACHE_KEY);
        return parent::execute();
    }
}
