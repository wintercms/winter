<?php namespace Cms\Console;

use Cms\Classes\Theme;
use Cms\Classes\ThemeManager;
use Exception;
use Winter\Storm\Console\Command;

/**
 * Console command to remove a theme.
 *
 * This completely deletes an existing theme, including all files and directories.
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class ThemeRemove extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'theme:remove';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'theme:remove
        {name : The name of the theme to delete. <info>(eg: mytheme)</info>}
        {--f|force : Force the operation to run.}
    ';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Delete an existing theme.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $themeManager = ThemeManager::instance();
        $themeName = $this->argument('name');
        $themeExists = Theme::exists($themeName);

        if (!$themeExists) {
            $themeName = strtolower(str_replace('.', '-', $themeName));
            $themeExists = Theme::exists($themeName);
        }

        if (!$themeExists) {
            return $this->error(sprintf('The theme %s does not exist.', $themeName));
        }

        if (!$this->confirmToProceed(sprintf('This will DELETE theme "%s" from the filesystem and database.', $themeName))) {
            return;
        }

        try {
            $themeManager->deleteTheme($themeName);
            $this->info(sprintf('The theme %s has been deleted.', $themeName));
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
        }
    }
}
