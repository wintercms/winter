<?php namespace Backend\Console;

use System\Console\BaseScaffoldCommand;

class CreateFormWidget extends BaseScaffoldCommand
{
    /**
     * The default command name for lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'create:formwidget';

    /**
     * The name and signature of this command.
     *
     * @var string
     */
    protected $signature = 'create:formwidget
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {widget : The name of the form widget to generate. <info>(eg: PostList)</info>}
        {--force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new form widget.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'FormWidget';

    /**
     * @var string The argument that the generated class name comes from
     */
    protected $nameFrom = 'widget';

    /**
     * A mapping of stub to generated file.
     *
     * @var array
     */
    protected $stubs = [
        'scaffold/formwidget/formwidget.stub'      => 'formwidgets/{{studly_name}}.php',
        'scaffold/formwidget/partial.stub'         => 'formwidgets/{{lower_name}}/partials/_{{lower_name}}.php',
        'scaffold/formwidget/stylesheet.stub'      => 'formwidgets/{{lower_name}}/assets/css/{{lower_name}}.css',
        'scaffold/formwidget/javascript.stub'      => 'formwidgets/{{lower_name}}/assets/js/{{lower_name}}.js',
    ];
}
