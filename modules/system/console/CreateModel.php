<?php namespace System\Console;

use Winter\Storm\Scaffold\GeneratorCommand;

class CreateModel extends GeneratorCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:model';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:model
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {model : The name of the model to generate. <info>(eg: Post)</info>}
        {--f|force : Overwrite existing files with generated files.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new model.';

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Model';

    /**
     * @var array A mapping of stubs to generated files.
     */
    protected $stubs = [
        'scaffold/model/model.stub'        => 'models/{{studly_name}}.php',
        'scaffold/model/fields.stub'       => 'models/{{lower_name}}/fields.yaml',
        'scaffold/model/columns.stub'      => 'models/{{lower_name}}/columns.yaml',
        'scaffold/model/create_table.stub' => 'updates/create_{{snake_plural_name}}_table.php',
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

        $model = $this->argument('model');

        return [
            'name' => $model,
            'author' => $author,
            'plugin' => $plugin
        ];
    }
}
