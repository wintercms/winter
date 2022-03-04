<?php namespace Backend\Console;

use Winter\Storm\Scaffold\GeneratorCommand;

class CreateReportWidget extends GeneratorCommand
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
        {--force : Overwrite existing files with generated files.}';

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
     * A mapping of stub to generated file.
     *
     * @var array
     */
    protected $stubs = [
        'scaffold/reportwidget/reportwidget.stub' => 'reportwidgets/{{studly_name}}.php',
        'scaffold/reportwidget/widget.stub'       => 'reportwidgets/{{lower_name}}/partials/_{{lower_name}}.htm',
    ];

    /**
     * Prepare variables for stubs.
     *
     * return @array
     */
    protected function prepareVars()
    {
        $pluginCode = $this->argument('plugin');

        $parts = explode('.', $pluginCode);
        $plugin = array_pop($parts);
        $author = array_pop($parts);

        $widget = $this->argument('widget');

        return [
            'name' => $widget,
            'author' => $author,
            'plugin' => $plugin
        ];
    }
}
