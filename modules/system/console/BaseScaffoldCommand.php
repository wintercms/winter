<?php namespace System\Console;

use Winter\Storm\Console\GeneratorCommand;

abstract class BaseScaffoldCommand extends GeneratorCommand
{
    use Traits\HasPluginArgument;

    /**
     * Get the plugin path from the input.
     *
     * @return string
     */
    protected function getDestinationPath(): string
    {
        $plugin = $this->getPluginIdentifier();

        $parts = explode('.', $plugin);
        $name = array_pop($parts);
        $author = array_pop($parts);

        return plugins_path(strtolower($author) . '/' . strtolower($name));
    }
}
