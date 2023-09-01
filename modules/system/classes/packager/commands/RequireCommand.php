<?php

namespace System\Classes\Packager\Commands;

use Cache;
use System\Classes\Packager\ComposerFactory;
use Winter\Packager\Commands\BaseCommand;
use Winter\Packager\Exceptions\CommandException;

class RequireCommand extends BaseCommand
{
    protected ?string $package = null;
    protected bool $dryRun = false;
    protected bool $dev = false;

    /**
     * Command handler.
     *
     * @param string|null $package
     * @param boolean $dryRun
     * @param boolean $dev
     * @return void
     * @throws CommandException
     */
    public function handle(?string $package = null, bool $dryRun = false, bool $dev = false): void
    {
        if (!$package) {
            throw new CommandException('Must provide a package');
        }

        $this->package = $package;
        $this->dryRun = $dryRun;
        $this->dev = $dev;
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

        if ($this->dev) {
            $arguments['--dev'] = true;
        }

        $arguments['packages'] = [$this->package];

        return $arguments;
    }

    public function execute()
    {
        $output = $this->runComposerCommand();
        $message = implode(PHP_EOL, $output['output']);

        if ($output['code'] !== 0) {
            throw new CommandException($message);
        }

        Cache::forget(ComposerFactory::COMPOSER_CACHE_KEY);

        return $message;
    }

    /**
     * @inheritDoc
     */
    public function getCommandName(): string
    {
        return 'require';
    }
}
