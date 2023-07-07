<?php namespace System\Console;

use System\Console\BaseScaffoldCommand;

class CreatePlugin extends BaseScaffoldCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:plugin';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:plugin
        {plugin : The name of the plugin to create. <info>(eg: Winter.Blog)</info>}
        {--f|force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new plugin.';

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Plugin';

    /**
     * @var string The argument that the generated class name comes from
     */
    protected $nameFrom = 'plugin';

    /**
     * @var array A mapping of stubs to generated files.
     */
    protected $stubs = [
        'scaffold/plugin/plugin.stub'  => 'Plugin.php',
        'scaffold/plugin/version.stub' => 'updates/version.yaml',
    ];

    /**
     * @var bool Validate the provided plugin input against the PluginManager, default true.
     */
    protected $validatePluginInput = false;

    /**
     * Get the desired class name from the input.
     */
    protected function getNameInput(): string
    {
        return explode('.', $this->getPluginIdentifier())[1];
    }

    /**
     * Gets the localization keys and values to be stored in the plugin's localization files
     * Can reference $this->vars and $this->laravel->getLocale() internally
     */
    protected function getLangKeys(): array
    {
        return [
            'plugin.name' => $this->vars['name'],
            'plugin.description' => 'No description provided yet...',
            'permissions.some_permission' => 'Some permission',
        ];
    }
}
