<?php namespace System\Console;

use Winter\Storm\Scaffold\GeneratorCommand;

class CreateModel extends GeneratorCommand
{
    /**
     * The default command name for lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'create:model';

    /**
     * The name and signature of this command.
     *
     * @var string
     */
    protected $signature = 'create:model
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {model : The name of the model to generate. <info>(eg: Post)</info>}
        {--force : Overwrite existing files with generated files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new model.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * A mapping of stub to generated file.
     *
     * @var array
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
     * return @array
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
