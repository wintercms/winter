<?php namespace System\Console;

use Winter\Storm\Scaffold\GeneratorCommand;

class CreateCommand extends GeneratorCommand
{
    /**
     * The default command name for lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'create:command';

    /**
     * The name and signature of this command.
     *
     * @var string
     */
    protected $signature = 'create:command
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {name : The name of the command to generate. <info>(eg: create)</info>}
        {--force : Overwrite existing files with generated files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new console command.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Command';

    /**
     * A mapping of stub to generated file.
     *
     * @var array
     */
    protected $stubs = [
        'scaffold/command/command.stub' => 'console/{{studly_name}}.php',
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
        $command = $this->argument('name');

        return [
            'name' => $command,
            'author' => $author,
            'plugin' => $plugin
        ];
    }
}
