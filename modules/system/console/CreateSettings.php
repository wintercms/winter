<?php namespace System\Console;

use Winter\Storm\Scaffold\GeneratorCommand;

class CreateSettings extends GeneratorCommand
{
    /**
     * The default command name for lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'create:settings';

    /**
     * The name and signature of this command.
     *
     * @var string
     */
    protected $signature = 'create:settings
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {settings : The name of the settings model to generate. <info>(eg: BlogSettings)</info>}
        {--force : Overwrite existing files with generated files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new settings model.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Settings Model';

    /**
     * A mapping of stubs to generated files.
     *
     * @var array
     */
    protected $stubs = [
        'scaffold/settings/model.stub' => 'models/{{studly_name}}.php',
        'scaffold/settings/fields.stub'   => 'models/{{lower_name}}/fields.yaml'
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
        $settings = $this->argument('settings') ?? 'Settings';

        return [
            'name' => $settings,
            'author' => $author,
            'plugin' => $plugin
        ];
    }
}
