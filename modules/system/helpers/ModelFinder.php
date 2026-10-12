<?php

namespace System\Helpers;

use Winter\Storm\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class ModelFinder
{
    /**
     * Find all models in core and active plugins.
     *
     * @return Collection
     */
    public static function findModels(): array
    {
        $models = [];
        $models[] = static::findModuleModels();
        $models[] = static::findActivePluginsModels();

        return collect($models)->flatten()->all();
    }

    public static function findModuleModels(): array
    {
        $modulesPath = base_path() . '/modules';

        $models = collect(Finder::create()->in($modulesPath)->path('/models/')->notPath('/tests/')->files()->name('/^[A-Z]{1}.+\.php$/'))
            ->map(function ($model) use ($modulesPath) {
                $modelPath = str_replace(['/', '.php'], ['\\', ''], Str::after($model->getRealPath(), realpath($modulesPath).DIRECTORY_SEPARATOR));
                return ucwords($modelPath, '\\');
            });

        return $models->values()->all();
    }

    public static function findActivePluginsModels(): array
    {
        $models = [];
        $pm = \System\Classes\PluginManager::instance();

        $pluginsPaths = collect($pm->getPlugins())->map(function ($plugin) use ($pm) {
            return $pm->getPluginPath($plugin);
        })->filter(function ($path) {
            return File::exists($path . '/models');
        })->each(function ($path) use (&$models) {
            $modelPaths = Finder::create()->in($path . '/models')->files()->name('/^[A-Z]{1}.+\.php$/');
            $models[] = collect($modelPaths)->map(function ($model) {
                $modelPath = str_replace(['/', '.php'], ['\\', ''], Str::after($model->getRealPath(), plugins_path().DIRECTORY_SEPARATOR));
                return ucwords($modelPath, '\\');
            })->all();
        });

        return collect($models)->flatten()->all();
    }
}
