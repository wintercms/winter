<?php namespace System\Console;

use InvalidArgumentException;
use System\Console\BaseScaffoldCommand;

class CreateCommand extends BaseScaffoldCommand
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
        {name : The name of the command to generate. <info>(eg: ImportPosts)</info>}
        {--command= : The terminal command that should be assigned. <info>(eg: blog:importposts)</info>}
        {--description= : The command description displayed in help.}
        {--f|force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new console command.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'make:command',
    ];

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
     */
    protected function prepareVars(): array
    {
        $parts = explode('.', $this->getPluginIdentifier());
        $plugin = array_pop($parts);
        $author = array_pop($parts);
        $name = $this->getNameInput();
        $command = trim($this->option('command') ?? strtolower("{$plugin}:{$name}"));
        $description = trim($this->option('description') ?? 'No description provided yet...');

        // More strict than the base Symfony validateName()
        // method, make a PR if it's a problem for you
        //  - Plugin and command names can contain a number, but they can't start with it.
        //  - Command name can contain a dash (-), but it can't start with it.
        if (preg_match('/^[a-z][\w]++(:[a-z][\w\-]++)*$/', $command) !== 1) {
            throw new InvalidArgumentException(sprintf('Command name "%s" is invalid.', $command));
        }

        return [
            'name' => $name,
            'command' => $command,
            'author' => $author,
            'plugin' => $plugin,
            'description' => $description,
        ];
    }
}
