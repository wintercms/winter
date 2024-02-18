<?php namespace Cms\Console;

use System\Console\BaseScaffoldCommand;

class CreateComponent extends BaseScaffoldCommand
{
    /**
     * The default command name for lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'create:component';

    /**
     * The name and signature of this command.
     *
     * @var string
     */
    protected $signature = 'create:component
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {component : The name of the component to generate. <info>(eg: Posts)</info>}
        {--force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new plugin component.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Component';

    /**
     * @var string The argument that the generated class name comes from
     */
    protected $nameFrom = 'component';

    /**
     * A mapping of stub to generated file.
     *
     * @var array
     */
    protected $stubs = [
        'scaffold/component/component.stub'  => 'components/{{studly_name}}.php',
        'scaffold/component/default.stub' => 'components/{{lower_name}}/default.htm',
    ];
}
