<?php namespace System\Console;

use System\Console\BaseScaffoldCommand;

class CreateSettings extends BaseScaffoldCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:settings';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:settings
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {settings? : The name of the settings model to generate. <info>(eg: BlogSettings)</info>}
        {--f|force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new settings model.';

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Settings Model';

    /**
     * @var string The argument that the generated class name comes from
     */
    protected $nameFrom = 'settings';

    /**
     * @var array A mapping of stubs to generated files.
     */
    protected $stubs = [
        'scaffold/settings/model.stub' => 'models/{{studly_name}}.php',
        'scaffold/settings/fields.stub'   => 'models/{{lower_name}}/fields.yaml'
    ];

    /**
     * Get the desired class name from the input.
     */
    protected function getNameInput(): string
    {
        return parent::getNameInput() ?: 'Settings';
    }
}
