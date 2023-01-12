<?php namespace System\Console;

use System\Console\BaseScaffoldCommand;

class CreateTest extends BaseScaffoldCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:test';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:test
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {name : The name of the test class to generate. <info>(eg: BlogTest)</info>}
        {--u|unit : Generate a Unit test (defaults to generating Feature tests).}
        {--p|pest : Generate a Pest PHP test (defaults to generating PHPUnit tests).}
        {--f|force : Overwrite existing files with generated files.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new test class.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'make:test',
    ];

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Test';

    /**
     * @var array A mapping of stubs to generated files.
     */
    protected $stubs = [
        'scaffold/test/test.stub' => 'tests/Feature/{{studly_name}}.php',
    ];

    /**
     * @inheritDoc
     */
    public function processVars(array $vars): array
    {
        $vars = parent::processVars($vars);

        $prefix = $this->option('pest') ? 'pest' : 'test';
        $suffix = '.stub';
        $type = 'Feature';
        $testName = "{{studly_name}}.php";
        if ($this->option('unit')) {
            $suffix = '.unit.stub';
            $type = 'Unit';

            $name = $this->argument('name');
            $class = $vars['plugin_namespace'] . '\\' . $name;
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                $publicMethods = [];
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC) ?: [];
                foreach ($methods as $method) {
                    if ($method->class === $class) {
                        $publicMethods[] = $method->name;
                    }
                }

                $namePieces = explode('\\', $name);

                $vars['tested_class'] = array_pop($namePieces);

                if (count($namePieces)) {
                    $type .= '\\' . implode('\\', $namePieces);
                }

                $vars['public_methods'] = $publicMethods;
                $suffix = '.class.stub';
                $vars['tested_class_full'] = $class;
                $testName = '{{ tested_class }}Test.php';
            }
        }

        $folder = str_replace('\\', '/', $type);
        $this->stubs = [
            "scaffold/test/{$prefix}{$suffix}" => "tests/$folder/$testName",
        ];

        if (!file_exists($this->getDestinationPath() . '/phpunit.xml')) {
            $this->stubs['scaffold/test/phpunit.xml.stub'] = 'phpunit.xml';
        }

        return array_merge($vars, [
            'test_namespace' => $type,
        ]);
    }
}
