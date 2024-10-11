<?php

namespace System\Classes\Packager;

use System\Classes\Packager\Commands\RemoveCommand;
use System\Classes\Packager\Commands\SearchCommand;
use System\Classes\Packager\Commands\ShowCommand;
use System\Classes\Packager\Commands\UpdateCommand;
use System\Classes\Packager\Commands\RequireCommand;
use Winter\Packager\Composer as PackagerComposer;

/**
 * @class Composer
 * @method static i(): array
 * @method static install(): array
 * @method static search(string $query, ?string $type = null, bool $onlyNames = false, bool $onlyVendors = false): \Winter\Packager\Commands\Search
 * @method static show(?string $mode = 'installed', string $package = null, bool $noDev = false, bool $path = false): object
 * @method static update(bool $includeDev = true, bool $lockFileOnly = false, bool $ignorePlatformReqs = false, string $installPreference = 'none', bool $ignoreScripts = false, bool $dryRun = false, ?string $package = null): \Winter\Packager\Commands\Update
 * @method static remove(?string $package = null, bool $dryRun = false): array
 * @method static require(?string $package = null, bool $dryRun = false, bool $dev = false): array
 * @method static version(string $detail = 'version'): array<string, string>|string
 */
class Composer
{
    public const COMPOSER_CACHE_KEY = 'winter.system.composer';

    protected static PackagerComposer $composer;

    public static function make(bool $fresh = false): PackagerComposer
    {
        if (!$fresh && isset(static::$composer)) {
            return static::$composer;
        }

        static::$composer = new PackagerComposer();
        static::$composer->setWorkDir(base_path());

        static::$composer->setCommand('remove', new RemoveCommand(static::$composer));
        static::$composer->setCommand('require', new RequireCommand(static::$composer));
        static::$composer->setCommand('search', new SearchCommand(static::$composer));
        static::$composer->setCommand('show', new ShowCommand(static::$composer));
        static::$composer->setCommand('update', new UpdateCommand(static::$composer));

        return static::$composer;
    }

    public static function __callStatic(string $name, array $args = []): mixed
    {
        if (!isset(static::$composer)) {
            static::make();
        }

        return static::$composer->{$name}(...$args);
    }
}
