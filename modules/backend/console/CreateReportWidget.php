<?php namespace Backend\Console;

use System\Console\BaseScaffoldCommand;

class CreateReportWidget extends BaseScaffoldCommand
{
    /**
     * The default command name for lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'create:reportwidget';

    /**
     * The name and signature of this command.
     *
     * @var string
     */
    protected $signature = 'create:reportwidget
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {widget : The name of the report widget to generate. <info>(eg: PostViews)</info>}
        {--force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new report widget.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'ReportWidget';

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
        'scaffold/reportwidget/reportwidget.stub' => 'reportwidgets/{{studly_name}}.php',
        'scaffold/reportwidget/widget.stub'       => 'reportwidgets/{{lower_name}}/partials/_{{lower_name}}.php',
    ];
}
