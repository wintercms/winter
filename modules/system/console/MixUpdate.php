<?php namespace System\Console;

class MixUpdate extends MixInstall
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'mix:update';

    /**
     * @inheritDoc
     */
    protected $description = 'Update Node.js dependencies required for mixed assets';

    /**
     * @inheritDoc
     */
    protected $signature = 'mix:update
        {npmArgs?* : Arguments to pass through to the "npm" binary}
        {--npm= : Defines a custom path to the "npm" binary}
        {--p|package=* : Defines one or more packages to update}';

    /**
     * @inheritDoc
     */
    protected $terms = [
        'complete' => 'update',
        'completed' => 'updated',
    ];

    /**
     * @inheritDoc
     */
    protected $npmCommand = 'update';
}
