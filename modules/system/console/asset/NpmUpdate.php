<?php

namespace System\Console\Asset;

class NpmUpdate extends AssetInstall
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'npm:update';

    /**
     * @inheritDoc
     */
    protected $description = 'Update Node.js dependencies required for mixed assets';

    /**
     * @inheritDoc
     */
    protected $signature = 'npm:update
        {npmArgs?* : Arguments to pass through to the "npm" binary}
        {--npm= : Defines a custom path to the "npm" binary}
        {--p|package=* : Defines one or more packages to update}';

    /**
     * @inheritDoc
     */
    protected array $terms = [
        'complete' => 'update',
        'completed' => 'updated',
    ];

    /**
     * @inheritDoc
     */
    protected string $npmCommand = 'update';

    /**
     * @inheritDoc
     */
    public $replaces = [
        'mix:update',
    ];
}
