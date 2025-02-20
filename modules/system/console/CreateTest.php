<?php

namespace System\Console;

use System\Console\BaseScaffoldCommand;
use Winter\Storm\Support\Str;

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
        {name : The name of the test class to generate. Test will be automatically added to the end. Can also be a relative path to a class to generate a test for. <info>(eg: Components\Posts)</info>}
        {--u|unit : Generate a Unit test (defaults to generating Feature tests).}
        {--p|pest : Generate a Pest PHP test (defaults to generating PHPUnit tests).}
        {--f|force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

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
     * @var array Stub files to make a plugin testable
     */
    protected $pluginStubs = [
        'scaffold/test/test.plugin.stub' => 'tests/Unit/PluginTest.php',
        'scaffold/test/phpunit.stub' => 'phpunit.xml',
    ];

    /**
     * Adds controller & model lang helpers to the vars
     */
    protected function processVars($vars): array
    {
        $vars = parent::processVars($vars);

        // Enable testing on the plugin if it isn't already
        if (!$this->files->exists($this->getDestinationPath() . '/phpunit.xml')) {
            $this->stubs = array_merge($this->pluginStubs, $this->stubs);
        }

        // Populate Pest.php if it doesn't exist
        $isPest = $this->option('pest');
        if ($isPest && !$this->files->exists($this->getDestinationPath() . '/tests/Pest.php')) {
            $this->stubs = array_merge($this->stubs, [
                'scaffold/test/pest.init.stub' => 'tests/Pest.php',
            ]);
        }

        $prefix = $isPest ? 'pest' : 'test';
        $type = $this->option('unit') ? 'Unit' : 'Feature';
        $suffix = $this->option('unit') ? '.unit.stub' : '.stub';

        $name = $this->argument('name');
        $class = $vars['plugin_namespace'] . '\\' . $name;

        // provided name is a class in the plugin
        if (class_exists($class)) {
            // Get the public methods to stub out tests for
            $reflection = new \ReflectionClass($class);
            $publicMethods = [];
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC) ?: [];
            foreach ($methods as $method) {
                if ($method->class === $class) {
                    $publicMethods[] = $method->name;
                }
            }
            $vars['public_methods'] = $publicMethods;

            // Generate the necessary stub variables
            $namePieces = explode('\\', $name);
            $vars['tested_class_full'] = $class;
            $vars['tested_class'] = array_pop($namePieces);
            $testClass = $vars['tested_class'] . 'Test';
            $suffix = '.class.stub';
            if (count($namePieces)) {
                $type .= '\\' . implode('\\', $namePieces);
            }
        // sometimes a name is just a name. Move on.
        } else {
            $testClass = $name . 'Test';
        }

        // Just in case :)
        $testClass = Str::replace('TestTest', 'Test', $testClass);

        $folder = str_replace('\\', '/', $type);
        $vars['test_class'] = $testClass;
        $vars['test_namespace'] = "{$vars['plugin_namespace']}\\Tests\\$type";

        $this->stubs["scaffold/test/{$prefix}{$suffix}"] = "tests/$folder/$testClass.php";

        return array_merge($vars, [
            'test_namespace' => "{$vars['plugin_namespace']}\\Tests\\$type",
        ]);
    }
}
