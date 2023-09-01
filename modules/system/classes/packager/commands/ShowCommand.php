<?php

namespace System\Classes\Packager\Commands;

use Winter\Packager\Commands\Show;
use Winter\Packager\Exceptions\CommandException;

class ShowCommand extends Show
{
    protected bool $path = false;

    /**
     * Command handler.
     *
     * The mode can be one of the following:
     *  - `installed`: Show installed packages
     *  - `locked`: Show locked packages
     *  - `platform`: Show platform requirements
     *  - `available`: Show all available packages
     *  - `self`: Show the current package
     *  - `path`: Show the package path
     *  - `tree`: Show packages in a dependency tree
     *  - `outdated`: Show only outdated packages
     *  - `direct`: Show only direct dependencies
     *
     * @param string|null $mode
     * @param string|null $package
     * @param boolean $noDev
     * @param boolean $path
     * @return void
     */
    public function handle(?string $mode = 'installed', string $package = null, bool $noDev = false, bool $path = false): void
    {
        parent::handle($mode, $package, $noDev);

        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function arguments(): array
    {
        $arguments = [];

        if (!empty($this->package)) {
            $arguments['package'] = $this->package;
        }

        if ($this->mode !== 'installed') {
            $arguments['--' . $this->mode] = true;
        }

        if ($this->noDev) {
            $arguments['--no-dev'] = true;
        }

        if ($this->path) {
            $arguments['--path'] = true;
        }

        $arguments['--format'] = 'json';

        return $arguments;
    }
}
