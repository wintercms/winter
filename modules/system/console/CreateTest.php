<?php

namespace System\Console;

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
        {test : The name of the test to generate. <info>(eg: Post)</info>}
        {--f|force : Overwrite existing files with generated files.}

        {--u|unit : Creates the test in the tests/Unit directory (default tests/Feature)}
        {--p|pest : Creates the test as a PestPHP test}
        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new test file.';

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
     * @var string The argument that the generated class name comes from
     */
    protected $nameFrom = 'test';

    /**
     * @var array Stub files to make a plugin testable
     */
    protected $pluginStubs = [
        'scaffold/test/test.plugin.stub' => 'tests/Unit/PluginTest.php',
        'scaffold/test/phpunit.stub' => 'phpunit.xml',
    ];

    /**
     * Prepare variables for stubs.
     */
    protected function prepareVars(): array
    {
        // Enable testing on the plugin if it isn't already
        if (!$this->files->exists($this->getDestinationPath() . '/phpunit.xml')) {
            $this->stubs = array_merge($this->pluginStubs, $this->stubs);
        }

        $test = $this->option('pest') ? 'pest' : 'test';
        $type = $this->option('unit') ? 'Unit' : 'Feature';
        $name = $this->argument('test') . 'Test';
        $suffix = $this->option('unit') ? '.unit.stub' : '.stub';

        $this->stubs['scaffold/test/' . $test . $suffix] = "tests/$type/$name.php";

        return array_merge(parent::prepareVars(), [
            'test_type' => $type,
            'class' => $name,
        ]);
    }

    /**
     * Adds controller & model lang helpers to the vars
     */
    protected function processVars($vars): array
    {
        $vars = parent::processVars($vars);

        $vars['test_namespace'] = "{$vars['plugin_namespace']}\\Tests\\{$vars['test_type']}";

        return $vars;
    }
}
