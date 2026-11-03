<?php namespace System\Console;

use InvalidArgumentException;
use Winter\Storm\Parse\PHP\ArrayFile;
use Winter\Storm\Scaffold\GeneratorCommand;

abstract class BaseScaffoldCommand extends GeneratorCommand
{
    use Traits\HasPluginArgument;

    /**
     * Prepare variables for stubs.
     */
    protected function prepareVars(): array
    {
        /*
         * Extract the author and name from the plugin code
         */
        $pluginCode = $this->getPluginIdentifier();
        $parts = explode('.', $pluginCode);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException("Invalid plugin name, either too many dots or not enough. Example: Author.PluginName");
        }

        $pluginName = array_pop($parts);
        $authorName = array_pop($parts);

        return [
            'name'   => $this->getNameInput(),
            'plugin' => $pluginName,
            'author' => $authorName,
        ];
    }

    /**
     * Converts all variables to available modifier and case formats and adds plugin helpers
     */
    protected function processVars(array $vars): array
    {
        $vars = parent::processVars($vars);

        $vars['plugin_id'] = "{$vars['lower_author']}.{$vars['lower_plugin']}";
        $vars['plugin_code'] = "{$vars['studly_author']}.{$vars['studly_plugin']}";
        $vars['plugin_url'] = "{$vars['lower_author']}/{$vars['lower_plugin']}";
        $vars['plugin_folder'] = "{$vars['lower_author']}/{$vars['lower_plugin']}";
        $vars['plugin_namespace'] = "{$vars['studly_author']}\\{$vars['studly_plugin']}";

        return $vars;
    }

    /**
     * Get the base path to output generated stubs to
     */
    protected function getDestinationPath(): string
    {
        $plugin = $this->getPlugin();
        if ($plugin) {
            return $plugin->getPluginPath();
        }

        $parts = explode('.', $this->getPluginIdentifier());
        $name = array_pop($parts);
        $author = array_pop($parts);

        return plugins_path(strtolower($author) . '/' . strtolower($name));
    }

    /**
     * Make all stubs.
     */
    public function makeStubs(): void
    {
        parent::makeStubs();

        // Get the language keys to be set
        $langKeys = $this->getLangKeys();
        if (empty($langKeys)) {
            return;
        }

        // Generate the path to the localization file to modify
        $langFilePath = plugins_path(
            $this->vars['plugin_folder']
            . DIRECTORY_SEPARATOR
            . 'lang'
            . DIRECTORY_SEPARATOR
            . $this->laravel->getLocale()
            . DIRECTORY_SEPARATOR
            . 'lang.php'
        );

        if (!file_exists($langFilePath)) {
            $this->makeDirectory($langFilePath);
            $comment = '<fg=green>File generated:</> ' . str_replace(base_path(), '', $langFilePath);
        } else {
            $comment = '<fg=yellow>File updated:</> ' . str_replace(base_path(), '', $langFilePath);
        }

        // Store the localization messages to the determined file path
        ArrayFile::open($langFilePath)->set($langKeys)->write();

        // Inform the user
        $this->output->writeLn($comment);
    }

    /**
     * Gets the localization keys and values to be stored in the plugin's localization files
     * Can reference $this->vars and $this->laravel->getLocale() internally
     */
    protected function getLangKeys(): array
    {
        return [];
    }
}
