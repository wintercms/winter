<?php namespace System\Helpers;

use App;
use File;
use Cache as CacheFacade;
use Config;

class Cache
{
    use \Winter\Storm\Support\Traits\Singleton;

    /**
     * Execute the console command.
     */
    public static function clear()
    {
        CacheFacade::flush();
        self::clearInternal();
    }

    public static function clearInternal()
    {
        $instance = self::instance();
        $instance->clearCombiner();
        $instance->clearCache();

        if (!Config::get('cms.twigNoCache')) {
            $instance->clearTwig();
        }

        $instance->clearMeta();
    }

    /*
     * Combiner
     */
    public function clearCombiner()
    {
        $path = storage_path().'/cms/combiner';
        if (!is_dir($path)) {
            return;
        }
        foreach (File::directories($path) as $directory) {
            File::deleteDirectory($directory);
        }
    }

    /*
     * Cache
     */
    public function clearCache()
    {
        $path = storage_path().'/cms/cache';
        if (!is_dir($path)) {
            return;
        }
        foreach (File::directories(storage_path().'/cms/cache') as $directory) {
            File::deleteDirectory($directory);
        }
    }

    /*
     * Twig
     */
    public function clearTwig()
    {
        $path = storage_path().'/cms/twig';
        if (!is_dir($path)) {
            return;
        }
        foreach (File::directories(storage_path().'/cms/twig') as $directory) {
            File::deleteDirectory($directory);
        }
    }

    /*
     * Meta
     */
    public function clearMeta()
    {
        File::delete(App::getCachedCompilePath());
        File::delete(App::getCachedServicesPath());
    }
}
