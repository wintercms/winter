<?php

namespace System\Classes\Extensions;

use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Error;
use Illuminate\Console\View\Components\Info;
use Illuminate\Console\View\Components\Warn;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\App;
use System\Classes\Extensions\Source\ExtensionSource;
use System\Classes\UpdateManager;
use System\Helpers\Cache as CacheHelper;
use System\Models\Parameter;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Foundation\Extension\WinterExtension;
use Winter\Storm\Packager\Composer;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Schema;

class ModuleManager extends ExtensionManager implements ExtensionManagerInterface
{
    protected Migrator $migrator;
    protected DatabaseMigrationRepository $repository;

    protected function init(): void
    {
        $this->migrator = App::make('migrator');

        $this->migrator->setOutput($this->output);

        $this->repository = App::make('migration.repository');
    }

    public function setOutput(OutputStyle $output): static
    {
        $this->output = $output;

        $this->migrator->setOutput($this->output);

        return $this;
    }

    /**
     * @return array<string, WinterExtension>
     */
    public function list(): array
    {
        return array_merge(...array_map(fn($key) => [$key => $this->get($key)], Config::get('cms.loadModules', [])));
    }

    public function create(string $extension): WinterExtension
    {
        throw new ApplicationException('Support for creating modules is not implemented.');
    }

    public function install(WinterExtension|ExtensionSource|string $extension): WinterExtension
    {
        // Get the module code from input and then update the module
        if (!($code = $this->resolveIdentifier($extension))) {
            throw new ApplicationException('Unable to update module: ' . $code);
        }

        // Force a refresh of the module
        $this->refresh($code);

        // Return an instance of the module
        return $this->get($code);
    }

    public function enable(WinterExtension|string $extension, string|bool $flag = self::DISABLED_BY_USER): mixed
    {
        // TODO: Implement enable() method.
        throw new ApplicationException('Support for enabling modules needs implementing');
    }

    public function disable(WinterExtension|string $extension, string|bool $flag = self::DISABLED_BY_USER): mixed
    {
        // TODO: Implement disable() method.
        throw new ApplicationException('Support for disabling modules needs implementing');
    }

    /**
     * @throws ApplicationException
     */
    public function update(WinterExtension|string|null $extension = null, bool $migrationsOnly = false): ?bool
    {
        $modules = $this->getModuleList($extension);

        $firstUp = !UpdateManager::instance()->isSystemSetup();

        if ($firstUp) {
            $this->repository->createRepository();
            $this->output->info('Migration table created');
        }

        if (Config::get('cms.disableCoreUpdates', false)) {
            $this->renderComponent(Warn::class, 'Not checking for core updates as `cms.disableCoreUpdates` is enabled');
        } elseif (!$migrationsOnly) {
            foreach ($modules as $module) {
                $extension = $this->get($module);
                if (
                    ($composerPackage = Composer::getPackageNameByExtension($extension))
                    && Composer::updateAvailable($composerPackage)
                ) {
                    $this->output->info(sprintf(
                        'Performing composer update for %s (%s) module...',
                        $module,
                        $composerPackage
                    ));

                    Preserver::instance()->store($extension);
                    // @TODO: Make this not dry run
                    $update = Composer::update(dryRun: true, package: $composerPackage, withAllDependencies: true);

                    $versions = $update->getUpgraded()[$composerPackage] ?? null;

                    $versions
                        ? $this->renderComponent(Info::class, sprintf('Updated module %s (%s) from v%s => v%s', $module, $composerPackage, $versions[0], $versions[1]))
                        : $this->renderComponent(Error::class, sprintf('Failed to update module %s (%s)', $module, $composerPackage));
                } elseif (false /* Detect if market */) {
                    Preserver::instance()->store($extension);
                    // @TODO: Update files from market
                }
            }
        }

        foreach ($modules as $module => $extension) {
            $this->output->info(sprintf('Migrating %s module...', $module));
            $this->migrator->run(base_path() . '/modules/' . strtolower($module) . '/database/migrations');

            if ($firstUp) {
                $className = '\\' . $module . '\Database\Seeds\DatabaseSeeder';
                if (class_exists($className)) {
                    $this->output->info(sprintf('Seeding %s module...', $module));

                    $seeder = App::make($className);
                    $return = $seeder->run();

                    if ($return && (is_string($return) || is_array($return))) {
                        $return = is_string($return) ? [$return] : $return;
                        foreach ($return as $item) {
                            $this->output->info(sprintf('[%s]: %s', $className, $item));
                        }
                    }

                    $this->output->info(sprintf('Seeded %s', $module));
                }
            }
        }

        Parameter::set('system::update.count', 0);
        CacheHelper::clear();

        return true;
    }

    public function refresh(WinterExtension|string|null $extension = null): mixed
    {
        $this->rollback($extension);
        return $this->update($extension, migrationsOnly: true);
    }

    /**
     * @throws ApplicationException
     */
    public function rollback(WinterExtension|string|null $extension = null, ?string $targetVersion = null): mixed
    {
        $modules = $this->getModuleList($extension);

        $paths = [];
        foreach ($modules as $module => $extension) {
            $paths[] = base_path() . '/modules/' . strtolower($module) . '/database/migrations';
        }

        while (true) {
            $rolledBack = $this->migrator->rollback($paths, ['pretend' => false]);

            if (count($rolledBack) == 0) {
                break;
            }
        }

        return true;
    }

    public function uninstall(WinterExtension|string $extension, bool $noRollback = false, bool $preserveFiles = false): mixed
    {
        if (!($module = $this->resolve($extension))) {
            throw new ApplicationException(sprintf(
                'Unable to uninstall module: %s',
                is_string($extension) ? $extension : $extension->getIdentifier()
            ));
        }

        if (!$noRollback) {
            $this->rollback($module);
        }

        if (!$preserveFiles) {
            // Modules probably should not be removed
            // File::deleteDirectory($module->getPath());
        }

        return true;
    }

    public function tearDown(): static
    {
        foreach ($this->list() as $module) {
            $this->uninstall($module);
        }

        Schema::dropIfExists(UpdateManager::instance()->getMigrationTableName());

        return $this;
    }

    public function isInstalled(WinterExtension|ExtensionSource|string $extension): bool
    {
        return !!$this->get($extension);
    }

    public function get(WinterExtension|ExtensionSource|string $extension): ?WinterExtension
    {
        if ($extension instanceof WinterExtension) {
            return $extension;
        }

        // @TODO: improve
        try {
            if (is_string($extension) && ($resolved = App::get($extension . '\\ServiceProvider'))) {
                return $resolved;
            }
        } catch (\Throwable $e) {
//            $this->output->error($e->getMessage());
        }

        try {
            return App::get(ucfirst($extension) . '\\ServiceProvider');
        } catch (\Throwable $e) {
            $this->output->error($e->getMessage());
            return null;
        }
    }

    public function availableUpdates(WinterExtension|string|null $extension = null): ?array
    {
        $toCheck = $extension ? [$this->get($extension)] : $this->list();

        $composerUpdates = Composer::getAvailableUpdates();

        $updates = [];
        foreach ($toCheck as $module) {
            if (!$module->getComposerPackageName() || !isset($composerUpdates[$module->getComposerPackageName()])) {
                continue;
            }

            $updates[$module->getIdentifier()] = [
                'package_name' => $module->getComposerPackageName(),
                ...$composerUpdates[$module->getComposerPackageName()],
            ];
        }

        return $updates;
    }

    /**
     * @param WinterExtension|string|null $extension
     * @return array<string, WinterExtension>
     * @throws ApplicationException
     */
    protected function getModuleList(WinterExtension|string|null $extension = null): array
    {
        if (!$extension) {
            return $this->list();
        }

        if (!($resolved = $this->resolve($extension))) {
            throw new ApplicationException('Unable to locate extension');
        }

        return [$resolved->getIdentifier() => $resolved];
    }
}
