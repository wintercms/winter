<?php

namespace System\Console\Asset\Vite;

use System\Console\Asset\AssetList;

class ViteList extends AssetList
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'vite:list';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'vite:list
        {--json : Output as JSON}';

    /**
     * @var string The console command description.
     */
    protected $description = 'List all registered Vite packages in this project.';

    /**
     * The asset compiler being used
     */
    protected string $assetType = 'vite';
}
