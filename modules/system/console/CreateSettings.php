<?php namespace System\Console;

use Winter\Storm\Scaffold\GeneratorCommand;

class CreateSettings extends GeneratorCommand
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
        {settings : The name of the settings model to generate. <info>(eg: BlogSettings)</info>}
        {--f|force : Overwrite existing files with generated files.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new settings model.';

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Settings Model';

    /**
     * @var array A mapping of stubs to generated files.
     */
    protected $stubs = [
        'scaffold/settings/model.stub' => 'models/{{studly_name}}.php',
        'scaffold/settings/fields.stub'   => 'models/{{lower_name}}/fields.yaml'
    ];

    /**
     * Prepare variables for stubs.
     *
     * @return array
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
