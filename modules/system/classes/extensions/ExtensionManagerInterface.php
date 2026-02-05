<?php

namespace System\Classes\Extensions;

use System\Classes\Extensions\Source\ExtensionSource;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Foundation\Extension\WinterExtension;

/**
 * @TODO: Move this into the ExtensionManager abstract class instead and make the specific
 * extension managers use more specific typehints than the WinterExtension interface
 */
interface ExtensionManagerInterface
{
    /**
     * Disabled by system
     */
    public const DISABLED_MISSING = 'disabled-missing';
    public const DISABLED_REPLACED = 'disabled-replaced';
    public const DISABLED_REPLACEMENT_FAILED = 'disabled-replacement-failed';
    public const DISABLED_MISSING_DEPENDENCIES = 'disabled-dependencies';

    /**
     * Explicitly disabled for a reason
     */
    public const DISABLED_REQUEST = 'disabled-request';
    public const DISABLED_BY_USER = 'disabled-user';
    public const DISABLED_BY_CONFIG = 'disabled-config';

    /**
     * Returns installed extensions by key
     *
     * @return array<string, WinterExtension>
     */
    public function list(): array;

    /**
     * Creates a new extension with the code provided
     *
     * @param string $extension
     * @return WinterExtension
     */
    public function create(string $extension): WinterExtension;

    /**
     * Installs an ExtensionSource or Extension, if extension is registered but not installed then installation steps
     * are ran
     *
     * @throws ApplicationException If the installation fails
     */
    public function install(ExtensionSource|WinterExtension|string $extension): WinterExtension;

    /**
     * Validates if an extension is installed or not
     *
     * @param ExtensionSource|WinterExtension|string $extension
     * @return bool
     */
    public function isInstalled(ExtensionSource|WinterExtension|string $extension): bool;

    /**
     * Returns an extension
     *
     * @param ExtensionSource|WinterExtension|string $extension
     * @return WinterExtension|null
     */
    public function get(ExtensionSource|WinterExtension|string $extension): ?WinterExtension;

    /**
     * Clears flag passed, if all flags are removed the extension will be enabled
     *
     * @param WinterExtension|string $extension
     * @param string|bool $flag
     * @return mixed
     */
    public function enable(WinterExtension|string $extension, string|bool $flag = self::DISABLED_BY_USER): mixed;

    /**
     * Disables the extension using the flag provided
     *
     * @param WinterExtension|string $extension
     * @param string|bool $flag
     * @return mixed
     */
    public function disable(WinterExtension|string $extension, string|bool $flag = self::DISABLED_BY_USER): mixed;

    /**
     * Updates the extension, by default fetching any remote updates prior to running migrations
     *
     * @param WinterExtension|string|null $extension
     * @param bool $migrationsOnly
     * @return mixed
     */
    public function update(WinterExtension|string|null $extension = null, bool $migrationsOnly = false): mixed;

    /**
     * Fetches updates available for extension, if null is passed then returns all updates for registered extensions
     *
     * @param WinterExtension|string|null $extension
     * @return array|null
     */
    public function availableUpdates(WinterExtension|string|null $extension = null): ?array;

    /**
     * Rollback and re-apply any migrations provided by the extension
     *
     * @param WinterExtension|string|null $extension
     * @return mixed
     */
    public function refresh(WinterExtension|string|null $extension = null): mixed;

    /**
     * Rollback an extension to a specific version
     *
     * @param WinterExtension|string|null $extension
     * @param string|null $targetVersion
     * @return mixed
     */
    public function rollback(WinterExtension|string|null $extension = null, ?string $targetVersion = null): mixed;

    /**
     * Remove a single extension
     *
     * @param WinterExtension|string $extension
     * @return mixed
     */
    public function uninstall(WinterExtension|string $extension, bool $noRollback = false, bool $preserveFiles = false): mixed;

    /**
     * Completely uninstall all extensions managed by this manager
     *
     * @return static
     */
    public function tearDown(): static;
}
