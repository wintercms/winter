<?php namespace System\Console;

use Winter\Storm\Scaffold\GeneratorCommand;

class CreateCommand extends GeneratorCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:command';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:command
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {name : The name of the command to generate. <info>(eg: create)</info>}
        {--f|force : Overwrite existing files with generated files.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new console command.';

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Command';

    /**
     * @var array A mapping of stubs to generated files.
     */
    protected $stubs = [
        'scaffold/command/command.stub' => 'console/{{studly_name}}.php',
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
        $command = $this->argument('name');

        return [
            'name' => $command,
            'author' => $author,
            'plugin' => $plugin
        ];
    }
}
