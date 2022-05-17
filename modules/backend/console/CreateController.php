<?php namespace Backend\Console;

use System\Console\BaseScaffoldCommand;
use Winter\Storm\Support\Str;

/**
 * @TODO:
 * - Support creating related permissions and navigation items and injecting them into the plugin
 */
class CreateController extends BaseScaffoldCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:controller';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:controller
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {controller : The name of the controller to generate. <info>(eg: Posts)</info>}
        {--force : Overwrite existing files with generated files.}
        {--model= : Defines the model name to use. If not provided, the singular name of the controller is used.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new controller.';

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Controller';

    /**
     * @var string The argument that the generated class name comes from
     */
    protected $nameFrom = 'controller';

    /**
     * @var array A mapping of stub to generated file.
     */
    protected $stubs = [
        'scaffold/controller/_list_toolbar.stub' => 'controllers/{{lower_name}}/_list_toolbar.php',
        'scaffold/controller/config_form.stub'   => 'controllers/{{lower_name}}/config_form.yaml',
        'scaffold/controller/config_list.stub'   => 'controllers/{{lower_name}}/config_list.yaml',
        'scaffold/controller/create.stub'        => 'controllers/{{lower_name}}/create.php',
        'scaffold/controller/index.stub'         => 'controllers/{{lower_name}}/index.php',
        'scaffold/controller/preview.stub'       => 'controllers/{{lower_name}}/preview.php',
        'scaffold/controller/update.stub'        => 'controllers/{{lower_name}}/update.php',
        'scaffold/controller/controller.stub'    => 'controllers/{{studly_name}}.php',
    ];

    /**
     * Prepare variables for stubs.
     */
    protected function prepareVars(): array
    {
        $vars = parent::prepareVars();
        /*
         * Determine the model name to use,
         * either supplied or singular from the controller name.
         */
        $model = $this->option('model');
        if (!$model) {
            $model = Str::singular($vars['name']);
        }
        $vars['model'] = $model;

        return $vars;
    }

    /**
     * Adds controller & model lang helpers to the vars
     */
    protected function processVars($vars): array
    {
        $vars = parent::processVars($vars);

        $vars['controller_url'] = "{$vars['plugin_url']}/{$vars['lower_name']}";
        $vars['model_lang_key_short'] = "models.{$vars['lower_model']}";
        $vars['model_lang_key'] = "{$vars['plugin_id']}::lang.{$vars['model_lang_key_short']}";

        return $vars;
    }

    /**
     * Gets the localization keys and values to be stored in the plugin's localization files
     * Can reference $this->vars and $this->laravel->getLocale() internally
     */
    protected function getLangKeys(): array
    {
        return [
            "{$this->vars['model_lang_key_short']}.label" => $this->vars['title_singular_name'],
            "{$this->vars['model_lang_key_short']}.label_plural" => $this->vars['title_plural_name'],
        ];
    }
}
