<?php

namespace System\Console\Asset\Mix;

use System\Console\Asset\AssetList;

class MixList extends AssetList
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:list';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mix:list
        {--json : Output as JSON}';

    /**
     * @var string The console command description.
     */
    protected $description = 'List all registered Mix packages in this project.';

    /**
     * The asset compiler being used
     */
    protected string $assetType = 'mix';
}
