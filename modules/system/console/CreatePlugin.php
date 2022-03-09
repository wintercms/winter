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
        {--f|force : Overwrite existing files with generated files.}';

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
     * Prepare variables for stubs.
     *
     * @return array
     */
    protected function prepareVars()
    {
        /*
         * Extract the author and name from the plugin code
         */
        $parts = explode('.', $this->getNameInput());

        if (count($parts) !== 2) {
            $this->error('Invalid plugin name, either too many dots or not enough.');
            $this->error('Example name: AuthorName.PluginName');
            return;
        }

        $plugin = array_pop($parts);
        $author = array_pop($parts);

        return [
            'name'   => $plugin,
            'author' => $author,
        ];
    }

    /**
     * Don't suggest any values, this command is for creating plugins
     */
    public function suggestPluginValues(): array
    {
        return [];
    }
}
