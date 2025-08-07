<?php

namespace System\Classes;

use Carbon\Carbon;
use Cms\Classes\ThemeManager;
use Exception;
use Illuminate\Support\Facades\App;
use System\Classes\Extensions\ModuleManager;
use System\Classes\Extensions\PluginManager;
use System\Models\Parameter;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Exception\SystemException;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Schema;

/**
 * Update manager
 *
 * Handles the CMS install and update process.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class UpdateManager
{
    use \Winter\Storm\Support\Traits\Singleton;

    /**
     * If set to true, core updates will not be downloaded or extracted.
     */
    protected bool $disableCoreUpdates = false;

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->disableCoreUpdates = Config::get('cms.disableCoreUpdates', false);
    }

    public function isSystemSetup(): bool
    {
        return Schema::hasTable($this->getMigrationTableName());
    }

    public function getMigrationTableName(): string
    {
        return Config::get('database.migrations', 'migrations');
    }

    /**
     * Checks for new updates and returns the amount of unapplied updates.
     * Only requests from the server at a set interval (retry timer).
     * @param bool $force Ignore the retry timer.
     */
    public function check(bool $force = false): int
    {
        $updateCount = Parameter::get('system::update.count');

        // Retry period not passed, skipping.
        if (
            !$force
            && ($retryTimestamp = Parameter::get('system::update.retry'))
            && Carbon::createFromTimeStamp($retryTimestamp)->isFuture()
            && $updateCount > 0
        ) {
            return $updateCount;
        }

        try {
            $updateCount = array_reduce(array_values($this->availableUpdates()), function (int $carry, array $updates) {
                return $carry + count($updates);
            }, 0);
        } catch (Exception $ex) {
            $updateCount = 0;
        }

        /*
         * Remember update count, set retry date
         */
        Parameter::set('system::update.count', $updateCount);
        Parameter::set('system::update.retry', Carbon::now()->addHours(24)->timestamp);

        return $updateCount;
    }

    public function availableUpdates(): array
    {
        return [
            'modules' => ModuleManager::instance()->availableUpdates(),
            'plugins' => PluginManager::instance()->availableUpdates(),
            'themes' => ThemeManager::instance()->availableUpdates(),
        ];
    }

    /**
     * @throws ApplicationException
     * @throws SystemException
     */
    public function update(bool $migrationsOnly = true): static
    {
        ModuleManager::instance()->update(migrationsOnly: $migrationsOnly);
        PluginManager::instance()->update(migrationsOnly: $migrationsOnly);
        ThemeManager::instance()->update(migrationsOnly: $migrationsOnly);

        return $this;
    }

    /**
     * Roll back all modules and plugins.
     * @throws ApplicationException
     */
    public function tearDownTheSystem(): static
    {
        ThemeManager::instance()->tearDown();
        PluginManager::instance()->tearDown();
        ModuleManager::instance()->tearDown();

        return $this;
    }

    /**
     * Determines build number from source manifest.
     *
     * This will return an array with the following information:
     *  - `build`: The build number we determined was most likely the build installed.
     *  - `modified`: Whether we detected any modifications between the installed build and the manifest.
     *  - `confident`: Whether we are at least 60% sure that this is the installed build. More modifications to
     *                  to the code = less confidence.
     *  - `changes`: If $detailed is true, this will include the list of files modified, created and deleted.
     *
     * @param bool $detailed If true, the list of files modified, added and deleted will be included in the result.
     */
    public function getBuildNumberManually(bool $detailed = false): array
    {
        // Find build by comparing with source manifest
        return App::make(SourceManifest::class)->compare(App::make(FileManifest::class), $detailed);
    }

    /**
     * Sets the build number in the database.
     *
     * @param bool $detailed If true, the list of files modified, added and deleted will be included in the result.
     */
    public function setBuildNumberManually(bool $detailed = false): array
    {
        $build = $this->getBuildNumberManually($detailed);

        if ($build['confident']) {
            $this->setBuild($build['build'], null, $build['modified']);
        }

        return $build;
    }

    /**
     * Returns the currently installed system hash.
     */
    public function getHash(): string
    {
        return Parameter::get('system::core.hash', md5('NULL'));
    }

    /**
     * Sets the build number and hash
     */
    public function setBuild(string $build, ?string $hash = null, bool $modified = false): void
    {
        $params = [
            'system::core.build' => $build,
            'system::core.modified' => $modified,
        ];

        if ($hash) {
            $params['system::core.hash'] = $hash;
        }

        Parameter::set($params);
    }
}
