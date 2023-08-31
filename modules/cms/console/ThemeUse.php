<?php namespace Cms\Console;

use Cms\Classes\Theme;
use Winter\Storm\Console\Command;

/**
 * Console command to switch themes.
 *
 * This switches the active theme to another one, saved to the database.
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class ThemeUse extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'theme:use';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'theme:use
        {name : The name of the theme. (directory name).}
        {--f|force : Force the operation to run.}
    ';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Switch the active theme.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        if (!$this->confirmToProceed('Change the active theme?')) {
            return;
        }

        $newThemeName = $this->argument('name');
        $newTheme = Theme::load($newThemeName);

        if (!$newTheme->exists($newThemeName)) {
            return $this->error(sprintf('The theme %s does not exist.', $newThemeName));
        }

        if ($newTheme->isActiveTheme()) {
            return $this->error(sprintf('%s is already the active theme.', $newTheme->getId()));
        }

        $activeTheme = Theme::getActiveTheme();
        $from = $activeTheme ? $activeTheme->getId() : 'nothing';

        $this->info(sprintf('Switching theme from %s to %s', $from, $newTheme->getId()));

        Theme::setActiveTheme($newThemeName);
    }
}
