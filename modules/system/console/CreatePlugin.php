<?php namespace System\Console;

use Winter\Storm\Scaffold\GeneratorCommand;

class CreatePlugin extends GeneratorCommand
{
    /**
     * The default command name for lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'create:plugin';

    /**
     * The name and signature of this command.
     *
     * @var string
     */
    protected $signature = 'create:plugin
        {plugin : The name of the plugin to create. <info>(eg: Winter.Blog)</info>}
        {--force : Overwrite existing files with generated files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new plugin.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Plugin';

    /**
     * A mapping of stub to generated file.
     *
     * @var array
     */
    protected $stubs = [
        'scaffold/plugin/plugin.stub'  => 'Plugin.php',
        'scaffold/plugin/version.stub' => 'updates/version.yaml',
    ];

    /**
     * Prepare variables for stubs.
     *
     * return @array
     */
    protected function prepareVars()
    {
        /*
         * Extract the author and name from the plugin code
         */
        $pluginCode = $this->argument('plugin');
        $parts = explode('.', $pluginCode);

        if (count($parts) != 2) {
            $this->error('Invalid plugin name, either too many dots or not enough.');
            $this->error('Example name: AuthorName.PluginName');
            return;
        }


        $pluginName = array_pop($parts);
        $authorName = array_pop($parts);

        return [
            'name'   => $pluginName,
            'author' => $authorName,
        ];
    }
}
