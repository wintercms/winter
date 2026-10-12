<?php

namespace System\Console;

use Exception;
use Illuminate\Database\Console\PruneCommand as BasePruneCommand;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Collection;
use Winter\Storm\Support\Facades\Event;
use System\Helpers\ModelFinder;

class PruneCommand extends BasePruneCommand
{
    /**
     * {@inheritDoc}
     */
    protected function models(): Collection
    {
        if (! empty($models = $this->option('model'))) {
            return collect($models)->filter(function ($model) {
                return class_exists($model);
            })->values();
        }

        $except = $this->option('except');

        return collect($this->findModels())
            ->when(! empty($except), function ($models) use ($except) {
                return $models->reject(function ($model) use ($except) {
                    return in_array($model, $except);
                });
            })->filter(function ($model) {
                return class_exists($model) && $this->isPrunable($model);
            })->values();
    }

    /**
     * {@inheritDoc}
     */
    protected function isPrunable($model): bool
    {
        try {
            $uses = class_uses_recursive($model);
        } catch (Exception $e) {
            return false;
        }

        return in_array(Prunable::class, $uses) || in_array(MassPrunable::class, $uses);
    }

    /**
     * Find all models.
     */
    protected function findModels(): array
    {
        /**
         * @event system.console.model.prune.findModels
         * Give the opportunity to return an array of Models to prune.
         *
         * Example usage:
         *
         *     Event::listen('system.console.model.prune.findModels', function () {
         *         return ['example model' => '\System\Models\File'];
         *     });
         *
         */
        $models = Event::fire('system.console.model.prune.findModels', [$this], true);
        if (is_array($models)) {
            return $models;
        }

        return ModelFinder::findModels();
    }
}
