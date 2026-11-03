<?php namespace System\Console;

use System\Console\BaseScaffoldCommand;

class CreateFactory extends BaseScaffoldCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:factory';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:factory
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {factory : The name of the factory to generate. <info>(eg: PostFactory)</info>}
        {--m|model= : The name of the model. <info>(eg: Post)</info>}
        {--f|force : Overwrite existing files with generated files.}

        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new factory.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'make:factory',
    ];

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Factory';

    /**
     * @var string The argument that the generated class name comes from
     */
    protected $nameFrom = 'factory';

    /**
     * @var array A mapping of stubs to generated files.
     */
    protected $stubs = [
        'scaffold/factory/factory.stub' => 'database/factories/{{studly_name}}.php',
    ];

    protected function processVars($vars): array
    {
        $vars = parent::processVars($vars);

        $vars['model'] = $this->option('model');

        return $vars;
    }
}
