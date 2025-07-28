<?php

namespace System\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\Backend;
use Backend\Facades\BackendMenu;
use Cms\Classes\Theme;
use Cms\Classes\ThemeManager;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use System\Classes\Core\MarketPlaceApi;
use System\Classes\Extensions\PluginManager;
use System\Classes\SettingsManager;
use System\Classes\UpdateManager;
use System\Models\Parameter;
use System\Models\PluginVersion;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\Flash;

/**
 * Updates controller
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class Updates extends Controller
{
    use Updates\Traits\ManagesMarketplaceProject;
    use Updates\Traits\ManagesPlugins;
    use Updates\Traits\ManagesThemes;

    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\ListController::class,
    ];

    /**
     * @var array `ListController` configuration.
     */
    public $listConfig = [
        'list' => 'config_list.yaml',
        'manage' => 'config_manage_list.yaml'
    ];

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['system.manage_updates'];

    public function __construct()
    {
        parent::__construct();

        // New
        $this->addVite([
            'controllers/updates/assets/src/updates.js',
            'controllers/updates/assets/src/updates.css'
        ], 'module-system');
        // Old
        $this->addJs('/modules/system/assets/js/updates/updates.js', 'core');
        $this->addCss('/modules/system/assets/css/updates/updates.css', 'core');

        BackendMenu::setContext('Winter.System', 'system', 'updates');
        SettingsManager::setContext('Winter.System', 'updates');


        $this->vars['warnings'] = PluginManager::instance()->getWarnings();
    }

    /**
     * Main landing page for managing installed plugins, installing new plugins and themes
     */
    public function index(): void
    {
        $this->vars['coreBuild'] = Parameter::get('system::core.build');
        $this->vars['coreBuildModified'] = Parameter::get('system::core.modified', false);
        $this->vars['projectId'] = Parameter::get('system::project.id');
        $this->vars['projectName'] = Parameter::get('system::project.name');
        $this->vars['projectOwner'] = Parameter::get('system::project.owner');
        $this->vars['pluginsActiveCount'] = PluginVersion::applyEnabled()->count();
        $this->vars['pluginsCount'] = PluginVersion::count();
        $this->asExtension('ListController')->index();
    }


    /**
     * Install new plugins / themes
     */
    public function install($tab = null): ?HttpResponse
    {
        if (get('search')) {
            return Response::make($this->onSearchProducts());
        }

        try {
            $this->bodyClass = 'compact-container breadcrumb-flush';
            $this->pageTitle = 'system::lang.plugins.install_products';

//            $this->addJs('/modules/system/assets/js/updates/install.js', 'core');
            $this->addCss('/modules/system/assets/css/updates/install.css', 'core');

            $this->vars['activeTab'] = $tab ?: 'plugins';

            $this->vars['packageUploadWidget'] = $this->getPackageUploadWidget($tab === 'themes' ? 'theme' : 'plugin');
        } catch (Exception $ex) {
            $this->handleError($ex);
        }

        return null;
    }

    /**
     * Override for ListController behavior.
     * Modifies the CSS class for each row in the list to
     *
     * - hidden - Disabled by configuration
     * - safe disabled - Orphaned or disabled
     * - negative - Disabled by system
     * - frozen - Frozen by the user
     * - positive - Default CSS class
     *
     * @see \Backend\Behaviors\ListController
     */
    public function listInjectRowClass($record, $definition = null): string
    {
        if ($record->disabledByConfig) {
            return 'hidden';
        }

        if ($record->orphaned || $record->is_disabled) {
            return 'safe disabled';
        }

        if ($definition != 'manage') {
            return '';
        }

        if ($record->disabledBySystem) {
            return 'negative';
        }

        if ($record->is_frozen) {
            return 'frozen';
        }

        return 'positive';
    }

    /**
     * Runs a specific update step.
     */
    public function onExecuteStep(): ?RedirectResponse
    {
        /*
         * Address timeout limits
         */
        @set_time_limit(3600);

        $manager = UpdateManager::instance();
        $stepCode = post('code');

        switch ($stepCode) {
            case 'downloadCore':
                $manager->downloadCore(post('hash'));
                break;

            case 'extractCore':
                $manager->extractCore();
                break;

            case 'setBuild':
                $manager->setBuild(post('build'), post('hash'));
                break;

            case 'downloadPlugin':
                $manager->downloadPlugin(post('name'), post('hash'), post('install'));
                break;

            case 'downloadTheme':
                $manager->downloadTheme(post('name'), post('hash'));
                break;

            case 'extractPlugin':
                $manager->extractPlugin(post('name'), post('hash'));
                break;

            case 'extractTheme':
                $manager->extractTheme(post('name'), post('hash'));
                break;

            case 'completeUpdate':
                $manager->update();
                Flash::success(Lang::get('system::lang.updates.update_success'));
                return Redirect::refresh();

            case 'completeInstall':
                $manager->update();
                Flash::success(Lang::get('system::lang.install.install_success'));
                return Redirect::refresh();
        }

        return null;
    }

    //
    // Updates
    //

    /**
     * Spawns the update checker popup.
     */
    public function onLoadUpdates(): string
    {
        return $this->makePartial('update_form');
    }

    /**
     * Contacts the update server for a list of necessary updates.
     */
    public function onCheckForUpdates(): array
    {
        try {
            $updates = UpdateManager::instance()->availableUpdates();

            $this->vars['core'] = $updates['modules'] ? [
                'updates' => $updates['modules'],
                'isImportant' => true
            ] : false;

            $this->vars['pluginList'] = $updates['plugins']
                ? array_reduce(array_keys($updates['plugins']), function (array $carry, string $code) use ($updates) {
                    $carry[$code] = array_merge(PluginManager::instance()->get($code)->pluginDetails(), [
                        'isImportant' => false,
                        'old_version' => $updates['plugins'][$code]['from'],
                        'new_version' => $updates['plugins'][$code]['to'],
                    ]);
                    return $carry;
                }, [])
                : false;

            $this->vars['themeList'] = $updates['themes']
                ? array_reduce(array_keys($updates['themes']), function (array $carry, string $code) use ($updates) {
                    $theme = ThemeManager::instance()->get($code);
                    $carry[$code] = [
                        'name' => $theme['name'],
                        'isImportant' => false,
                        'old_version' => $updates['themes'][$code]['from'],
                        'new_version' => $updates['themes'][$code]['to'],
                    ];
                    return $carry;
                }, [])
                : false;

            $this->vars['hasImportantUpdates'] = !!count($updates['modules']);

            $this->vars['hasUpdates'] = $this->vars['core'] || $this->vars['pluginList'] || $this->vars['themeList'];
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }

        return ['#updateContainer' => $this->makePartial('update_list')];
    }

    /**
     * Loops the update list and checks for actionable updates.
     */
    protected function processImportantUpdates(array $result): array
    {
        $hasImportantUpdates = false;

        // Core
        if (isset($result['core'])) {
            $coreImportant = false;

            foreach (array_get($result, 'core.updates', []) as $build => $description) {
                if (strpos($description, '!!!') === false) {
                    continue;
                }

                $detailsUrl = '//wintercms.com/support/articles/release-notes';
                $description = str_replace('!!!', '', $description);
                $result['core']['updates'][$build] = [$description, $detailsUrl];
                $coreImportant = $hasImportantUpdates = true;
            }

            $result['core']['isImportant'] = $coreImportant ? '1' : '0';
        }

        // Plugins
        foreach (array_get($result, 'plugins', []) as $code => $plugin) {
            $isImportant = false;

            foreach (array_get($plugin, 'updates', []) as $version => $description) {
                if (strpos($description, '!!!') === false) {
                    continue;
                }

                $isImportant = $hasImportantUpdates = true;
                $detailsUrl = Backend::url('system/updates/details/'.PluginVersion::makeSlug($code).'/upgrades').'?fetch=1';
                $description = str_replace('!!!', '', $description);
                $result['plugins'][$code]['updates'][$version] = [$description, $detailsUrl];
            }

            $result['plugins'][$code]['isImportant'] = $isImportant ? '1' : '0';
        }

        $result['hasImportantUpdates'] = $hasImportantUpdates;

        return $result;
    }

    /**
     * Reverses the update lists for the core and all plugins.
     */
    protected function processUpdateLists(array $result): array
    {
        if ($core = array_get($result, 'core')) {
            $result['core']['updates'] = array_reverse(array_get($core, 'updates', []), true);
        }

        foreach (array_get($result, 'plugins', []) as $code => $plugin) {
            $result['plugins'][$code]['updates'] = array_reverse(array_get($plugin, 'updates', []), true);
        }

        return $result;
    }

    /**
     * Contacts the update server for a list of necessary updates.
     *
     * @param $force Whether or not to force the redownload of existing tools
     */
    public function onForceUpdate(bool $force = true): string
    {
        try {
            $manager = UpdateManager::instance();
            $result = $manager->requestUpdateList($force);

            $coreHash = array_get($result, 'core.hash', false);
            $coreBuild = array_get($result, 'core.build', false);
            $core = [$coreHash, $coreBuild];

            $plugins = [];
            $pluginList = array_get($result, 'plugins', []);
            foreach ($pluginList as $code => $plugin) {
                $plugins[$code] = array_get($plugin, 'hash', null);
            }

            $themes = [];
            $themeList = array_get($result, 'themes', []);
            foreach ($themeList as $code => $theme) {
                $themes[$code] = array_get($theme, 'hash', null);
            }

            /*
             * Update steps
             */
            $updateSteps = $this->buildUpdateSteps($core, $plugins, $themes, false);

            /*
             * Finish up
             */
            $updateSteps[] = [
                'code' => 'completeUpdate',
                'label' => Lang::get('system::lang.updates.update_completing'),
            ];

            $this->vars['updateSteps'] = $updateSteps;
        } catch (Exception $ex) {
            $this->handleError($ex);
        }

        return $this->makePartial('execute');
    }

    /**
     * Converts the update data to an actionable array of steps.
     */
    public function onApplyUpdates(): string
    {
        try {
            /*
             * Process core
             */
            $coreHash = post('hash');
            $coreBuild = post('build');
            $core = [$coreHash, $coreBuild];

            /*
             * Process plugins
             */
            $plugins = post('plugins');
            if (is_array($plugins)) {
                $pluginCodes = [];
                foreach ($plugins as $code => $hash) {
                    $pluginCodes[] = $this->decodeCode($code);
                }

                $plugins = array_combine($pluginCodes, $plugins);
            } else {
                $plugins = [];
            }

            /*
             * Process themes
             */
            $themes = post('themes');
            if (is_array($themes)) {
                $themeCodes = [];
                foreach ($themes as $code => $hash) {
                    $themeCodes[] = $this->decodeCode($code);
                }

                $themes = array_combine($themeCodes, $themes);
            } else {
                $themes = [];
            }

            /*
             * Process important update actions
             */
            $pluginActions = (array) post('plugin_actions');
            foreach ($plugins as $code => $hash) {
                $_code = $this->encodeCode($code);

                if (!array_key_exists($_code, $pluginActions)) {
                    continue;
                }

                $pluginAction = $pluginActions[$_code];

                if (!$pluginAction) {
                    throw new ApplicationException('Please select an action for plugin '. $code);
                }

                if ($pluginAction != 'confirm') {
                    unset($plugins[$code]);
                }

                if ($pluginAction == 'ignore') {
                    PluginVersion::whereCode($code)->update([
                        'is_frozen' => true
                    ]);
                }
            }

            /*
             * Update steps
             */
            $updateSteps = $this->buildUpdateSteps($core, $plugins, $themes, false);

            /*
             * Finish up
             */
            $updateSteps[] = [
                'code' => 'completeUpdate',
                'label' => Lang::get('system::lang.updates.update_completing'),
            ];

            $this->vars['updateSteps'] = $updateSteps;
        } catch (Exception $ex) {
            $this->handleError($ex);
        }

        return $this->makePartial('execute');
    }

    protected function buildUpdateSteps($core, $plugins, $themes, $isInstallationRequest): array
    {
        if (!is_array($core)) {
            $core = [null, null];
        }

        if (!is_array($themes)) {
            $themes = [];
        }

        if (!is_array($plugins)) {
            $plugins = [];
        }

        $updateSteps = [];
        list($coreHash, $coreBuild) = $core;

        /*
         * Download
         */
        if ($coreHash) {
            $updateSteps[] = [
                'code'  => 'downloadCore',
                'label' => Lang::get('system::lang.updates.core_downloading'),
                'hash'  => $coreHash
            ];
        }

        foreach ($themes as $name => $hash) {
            $updateSteps[] = [
                'code'  => 'downloadTheme',
                'label' => Lang::get('system::lang.updates.theme_downloading', compact('name')),
                'name'  => $name,
                'hash'  => $hash
            ];
        }

        foreach ($plugins as $name => $hash) {
            $updateSteps[] = [
                'code'  => 'downloadPlugin',
                'label' => Lang::get('system::lang.updates.plugin_downloading', compact('name')),
                'name'  => $name,
                'hash'  => $hash,
                'install' => $isInstallationRequest ? 1 : 0
            ];
        }

        /*
         * Extract
         */
        if ($coreHash) {
            $updateSteps[] = [
                'code'  => 'extractCore',
                'label' => Lang::get('system::lang.updates.core_extracting')
            ];

            $updateSteps[] = [
                'code'  => 'setBuild',
                'label' => Lang::get('system::lang.updates.core_set_build'),
                'hash'  => $coreHash,
                'build' => $coreBuild
            ];
        }

        foreach ($themes as $name => $hash) {
            $updateSteps[] = [
                'code' => 'extractTheme',
                'label' => Lang::get('system::lang.updates.theme_extracting', compact('name')),
                'name' => $name,
                'hash' => $hash
            ];
        }

        foreach ($plugins as $name => $hash) {
            $updateSteps[] = [
                'code' => 'extractPlugin',
                'label' => Lang::get('system::lang.updates.plugin_extracting', compact('name')),
                'name' => $name,
                'hash' => $hash
            ];
        }

        return $updateSteps;
    }

    //
    // View Changelog
    //

    /**
     * Displays changelog information
     *
     * @throws ApplicationException if the changelog could not be fetched from the server
     */
    public function onLoadChangelog(): string
    {
        try {
            $fetchedContent = MarketPlaceApi::instance()->requestChangelog();

            $changelog = array_get($fetchedContent, 'history');

            if (!$changelog || !is_array($changelog)) {
                throw new ApplicationException(Lang::get('system::lang.server.response_empty'));
            }

            $this->vars['changelog'] = $changelog;
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }

        return $this->makePartial('changelog_list');
    }

    //
    // Product install
    //

    /**
     * @return array
     * @throws ApplicationException
     */
    public function onSearchProducts(): array
    {
        $searchType = get('search', 'plugins') === 'plugins' ? 'plugin' : 'theme';

        return MarketPlaceApi::instance()->search(get('query'), $searchType);
    }

    /*
     * Remove installed products from the collection
     */
    protected function filterPopularProducts($popular, $installed): array
    {
        $installedArray = [];
        foreach ($installed as $product) {
            $installedArray[] = array_get($product, 'code', -1);
        }

        foreach ($popular as $key => $product) {
            $code = array_get($product, 'code');
            if (in_array($code, $installedArray)) {
                unset($popular[$key]);
            }
        }

        return array_values($popular);
    }

    //
    // Helpers
    //

    /**
     * Encode HTML safe product code, this is to prevent issues with array_get().
     */
    protected function encodeCode(string $code): string
    {
        return str_replace('.', ':', $code);
    }

    /**
     * Decode HTML safe product code.
     */
    protected function decodeCode(string $code): string
    {
        return str_replace(':', '.', $code);
    }
}
