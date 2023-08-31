<?php namespace Cms\Console;

use Cms\Classes\Theme;
use Cms\Classes\ThemeManager;
use System\Classes\UpdateManager;
use Winter\Storm\Console\Command;

/**
 * Console command to list themes.
 *
 * This lists all the available themes in the system. It also shows the active theme.
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class ThemeList extends Command
{
    /**
     * The console command name.
     */
    protected $name = 'theme:list';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'theme:list
        {--m|include-marketplace : Include downloadable themes from the Winter marketplace.}
    ';

    /**
     * The console command description.
     */
    protected $description = 'List available themes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $themeManager = ThemeManager::instance();
        $updateManager = UpdateManager::instance();
        $results = [];

        foreach (Theme::all() as $theme) {
            $results[] = [
                'code' => $theme->getId(),
                'is_active' => $theme->isActiveTheme() ? '<info>Yes</info>': '<fg=red>No</>',
                'is_installed' => '<info>Yes</info>',
            ];
        }

        if ($this->option('include-marketplace')) {
            // @TODO List everything in the marketplace - not just popular.
            $popularThemes = $updateManager->requestPopularProducts('theme');
            foreach ($popularThemes as $popularTheme) {
                $results[] = [
                    'code' => $popularTheme['code'],
                    'is_active' => '<fg=red>No</>',
                    'is_installed' => $themeManager->isInstalled($popularTheme['code']) ? '<info>Yes</info>': '<fg=red>No</>',
                ];
            }
        }

        $this->table(['Theme', 'Active', 'Installed'], $results);
    }
}
