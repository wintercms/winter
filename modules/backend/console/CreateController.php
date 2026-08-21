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
        {--stubs : Create view files for local overwrites.}
        {--force : Overwrite existing files with generated files.}
        {--model= : Defines the model name to use. If not provided, the singular name of the controller is used.}
        {--l|layout=standard : Set the formLayout to use (standard, sidebar, fancy)}
        {--uninspiring : Disable inspirational quotes}
    ';

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
     * @var bool Allows the process to continue if an existing file is detected
     */
    protected bool $throwOverwriteException = false;

    /**
     * @var array A mapping of stub to generated file.
     */
    protected $stubs = [
        'scaffold/controller/config_form.stub'   => 'controllers/{{lower_name}}/config_form.yaml',
        'scaffold/controller/config_list.stub'   => 'controllers/{{lower_name}}/config_list.yaml',
        'scaffold/controller/controller.stub'    => 'controllers/{{studly_name}}.php',
    ];

    /**
     * Prepare variables for stubs.
     */
    protected function prepareVars(): array
    {
        $vars = parent::prepareVars();
        $layout = $this->option('layout');
        /*
         * Determine the model name to use,
         * either supplied or singular from the controller name.
         */
        $model = $this->option('model');
        if (!$model) {
            $model = Str::singular($vars['name']);
        }
        $vars['model'] = $model;
        $vars['sidebar'] = $layout === 'sidebar';
        $vars['fancy'] = $layout === 'fancy';
        $vars['stubs'] = $this->option('stubs');

        if ($this->option('stubs')) {
            $this->stubs['scaffold/controller/index.stub'] = 'controllers/{{lower_name}}/index.php';
            $this->stubs['scaffold/controller/_list_toolbar.stub'] = 'controllers/{{lower_name}}/_list_toolbar.php';
            $this->stubs["scaffold/controller/{$layout}/create.stub"] = 'controllers/{{lower_name}}/create.php';
            $this->stubs["scaffold/controller/{$layout}/update.stub"] = 'controllers/{{lower_name}}/update.php';
            $this->stubs["scaffold/controller/{$layout}/preview.stub"] = 'controllers/{{lower_name}}/preview.php';

            if ($layout === 'fancy') {
                $this->stubs['scaffold/controller/fancy/_toolbar.stub'] = 'controllers/{{lower_name}}/_toolbar.php';
            }
        }

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
