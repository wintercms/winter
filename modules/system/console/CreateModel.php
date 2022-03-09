<?php namespace System\Console;

use Str;
use System\Console\BaseScaffoldCommand;

class CreateModel extends BaseScaffoldCommand
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
        {--f|force : Overwrite existing files with generated files.}

        {--a|all : Generate a controller, migration, & seeder for the model}
        {--c|controller : Create a new controller for the model}
        {--s|seed : Create a new seeder for the model}
        {--p|pivot : Indicates if the generated model should be a custom intermediate table model}
        {--no-migration : Don\'t create a migration file for the model}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new model.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'make:model',
    ];

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Model';

    /**
     * @var string The argument that the generated class name comes from
     */
    protected $nameFrom = 'model';

    /**
     * @var array A mapping of stubs to generated files.
     */
    protected $stubs = [
        'scaffold/model/model.stub'        => 'models/{{studly_name}}.php',
        'scaffold/model/fields.stub'       => 'models/{{lower_name}}/fields.yaml',
        'scaffold/model/columns.stub'      => 'models/{{lower_name}}/columns.yaml',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() === false && !$this->option('force')) {
            return false;
        }

        if ($this->option('all')) {
            $this->input->setOption('controller', true);
            $this->input->setOption('seed', true);
        }

        if ($this->option('controller')) {
            $this->createController();
        }

        if ($this->option('seed')) {
            $this->createSeeder();
        }

        if ($this->option('no-migration') !== false) {
            $this->createMigration();
        }
    }

    /**
     * Prepare variables for stubs.
     *
     * @return array
     */
    protected function prepareVars()
    {
        $parts = explode('.', $this->getPluginIdentifier());
        $plugin = array_pop($parts);
        $author = array_pop($parts);
        $name = $this->getNameInput();

        return [
            'name' => $name,
            'author' => $author,
            'plugin' => $plugin,
        ];
    }

    /**
     * Create a migration for the model.
     */
    public function createMigration()
    {
        $this->call('create:migration', [
            'plugin'  => $this->getPluginIdentifier(),
            '--model' => $this->getNameInput(),
        ]);
    }

    /**
     * Create a seeder for the model.
     */
    public function createSeeder()
    {
        $this->call('create:seeder', [
            'plugin'  => $this->getPluginIdentifier(),
            'model' => $this->getNameInput(),
        ]);
    }

    /**
     * Create a controller for the model.
     */
    public function createController()
    {
        $this->call('create:controller', [
            'plugin'  => $this->getPluginIdentifier(),
            'controller' => Str::pluralize($this->argument('model')),
            '--model' => $this->getNameInput(),
        ]);
    }
}
