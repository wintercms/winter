<?php

namespace System\Controllers\Updates\Traits;

use Backend\Widgets\Form;
use Cms\Classes\ThemeManager;
use Exception;
use Illuminate\Console\OutputStyle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use System\Classes\Core\MarketPlaceApi;
use System\Classes\Extensions\PluginManager;
use System\Classes\Extensions\Source\ComposerSource;
use System\Classes\Extensions\Source\ExtensionSource;
use System\Classes\UpdateManager;
use System\Models\PluginVersion;
use Winter\Storm\Database\Model;
use Winter\Storm\Database\Models\DeferredBinding;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\File as FileHelper;
use Winter\Storm\Support\Facades\Flash;
use Winter\Storm\Support\Str;

trait ManagesPlugins
{
    public string $cachePrefix = 'winter-x-install-';

    public function details($urlCode = null, $tab = null): void
    {
        try {
            $this->pageTitle = 'system::lang.updates.details_title';
            $this->addJs('/modules/system/assets/js/updates/details.js', 'core');
            $this->addCss('/modules/system/assets/css/updates/details.css', 'core');

            $readmeFiles = ['README.md', 'readme.md'];
            $upgradeFiles = ['UPGRADE.md', 'upgrade.md'];
            $licenceFiles = ['LICENCE.md', 'licence.md', 'LICENSE.md', 'license.md'];

            $readme = $changelog = $upgrades = $licence = $name = null;
            $code = str_replace('-', '.', $urlCode);

            // Lookup the plugin
            $manager = PluginManager::instance();
            $plugin = $manager->findByIdentifier($code);
            $code = $manager->getIdentifier($plugin);

            if ($plugin) {
                $details = $plugin->pluginDetails();
                $readme = $plugin->getPluginMarkdownFile($readmeFiles);
                $changelog = $plugin->getPluginVersions(false);
                $upgrades = $plugin->getPluginMarkdownFile($upgradeFiles);
                $licence = $plugin->getPluginMarkdownFile($licenceFiles);

                $pluginVersion = PluginVersion::whereCode($code)->first();
                $this->vars['pluginName'] = array_get($details, 'name', 'system::lang.plugin.unnamed');
                $this->vars['pluginVersion'] = $pluginVersion ? $pluginVersion->version : '???';
                $this->vars['pluginAuthor'] = array_get($details, 'author');
                $this->vars['pluginIcon'] = array_get($details, 'icon', 'icon-leaf');
                $this->vars['pluginHomepage'] = array_get($details, 'homepage');
            } else {
                throw new ApplicationException(Lang::get('system::lang.updates.plugin_not_found'));
            }

            // Fetch from the server
            if (get('fetch')) {
                $fetchedContent = MarketPlaceApi::instance()->request(MarketPlaceApi::REQUEST_PLUGIN_CONTENT, $code);
                $upgrades = array_get($fetchedContent, 'upgrade_guide_html');
            }

            $this->vars['activeTab'] = $tab ?: 'readme';
            $this->vars['urlCode'] = $urlCode;
            $this->vars['readme'] = $readme;
            $this->vars['changelog'] = $changelog;
            $this->vars['upgrades'] = $upgrades;
            $this->vars['licence'] = $licence;
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }
    }

    protected function getInstalledPlugins(): array
    {
        $installed = PluginVersion::lists('code');
        return MarketPlaceApi::instance()->requestProductDetails($installed, 'plugin');
    }

    /**
     * Adds require plugin codes to the collection based on a result.
     */
    protected function appendRequiredPlugins(array $plugins, array $result): array
    {
        foreach ((array) array_get($result, 'require') as $plugin) {
            if (
                ($name = array_get($plugin, 'code')) &&
                ($hash = array_get($plugin, 'hash')) &&
                !PluginManager::instance()->hasPlugin($name)
            ) {
                $plugins[$name] = $hash;
            }
        }

        return $plugins;
    }

    public function onGetMarketplaceListings(): array
    {
        return [
            'result' => MarketPlaceApi::instance()->getListing()
        ];
    }

    protected ?Form $packageUploadWidget = null;

    /**
     * Get the form widget for the import popup.
     */
    protected function getPackageUploadWidget(string $type = 'plugin'): Form
    {
        $type = post('type', $type);

        if (!in_array($type, ['plugin', 'theme'])) {
            throw new ApplicationException('Invalid package type');
        }

        if ($this->packageUploadWidget !== null) {
            return $this->packageUploadWidget;
        }

        $config = $this->makeConfig("form.{$type}_upload.yaml");
        $config->model = new class extends Model {
            public $attachOne = [
                'uploaded_package' => [\System\Models\File::class, 'public' => false],
            ];
        };
        $widget = $this->makeWidget(Form::class, $config);
        $widget->bindToController();

        return $this->packageUploadWidget = $widget;
    }

    /**
     * Displays the plugin uploader form
     */
    public function onLoadPluginUploader(): string
    {
        $this->vars['packageUploadWidget'] = $this->getPackageUploadWidget('plugin');
        return $this->makePartial('popup_upload_plugin');
    }

    /**
     * Installs an uploaded plugin
     */
    public function onInstallUploadedPlugin(): string
    {
        try {
            // Get the deferred binding record for the uploaded file
            $widget = $this->getPackageUploadWidget();
            $class = str_contains($class = Str::before(get_class($widget->model), chr(0)), '\\\\')
                ? str_replace('\\\\', '\\', $class)
                : $class;

            $deferred = DeferredBinding::query()
                ->where('master_type', 'LIKE', $class . '%')
                ->where('master_field', 'uploaded_package')
                ->where('session_key', $widget->getSessionKey())
                ->first();

            // Attempt to get the file from the deferred binding
            if (!$deferred || !$deferred->slave) {
                throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
            }

            $file = $deferred->slave;
            $localPath = $file->disk_name;
            if (!FileHelper::copyBetweenDisks($file->getDisk(), 'temp', $file->getDiskPath(), $localPath)) {
                throw new ApplicationException(Lang::get('system::lang.server.shit_gone_fucky'));
            }

            /**
             * @TODO:
             * - Process the uploaded file to identify the plugins to install
             * - (optional) require confirmation to install each detected plugin
             * - Install the identified plugins
             * - Ensure that deferred binding records and uploaded files are removed post processing or on failure
             */

            $manager = UpdateManager::instance();

            // @TODO: Implement
            // $result = $manager->installUploadedPlugin(Storage::disk('temp')->path($localPath));

            if (!isset($result['code']) || !isset($result['hash'])) {
                throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
            }

            $name = $result['code'];
            $hash = $result['hash'];
            $plugins = [$name => $hash];
            $plugins = $this->appendRequiredPlugins($plugins, $result);

            /*
             * Update steps
             */
            $updateSteps = $this->buildUpdateSteps(null, $plugins, [], true);

            /*
             * Finish up
             */
            $updateSteps[] = [
                'code'  => 'completeInstall',
                'label' => Lang::get('system::lang.install.install_completing'),
            ];

            $this->vars['updateSteps'] = $updateSteps;

            return $this->makePartial('execute');
        }
        catch (Exception $ex) {
            // @TODO: Remove this, temporary debugging
            throw $ex;
            $this->handleError($ex);
            return $this->makePartial('plugin_uploader');
        }
    }

    /**
     * Validate the plugin code and execute the plugin installation
     *
     * @throws ApplicationException If validation fails or the plugin cannot be installed
     */
    public function onInstallPackage(): array
    {
        if (!$code = trim(post('package'))) {
            throw new ApplicationException(Lang::get('system::lang.install.missing_package_name'));
        }

        if (!($type = trim(post('type'))) || !in_array($type, ['plugin', 'theme', 'module'])) {
            throw new ApplicationException(Lang::get('system::lang.install.invalid_package_type'));
        }

        $key = base64_encode($this->cachePrefix . Session::getId() . md5(time() . $code));

        App::terminating(function () use ($code, $type, $key) {
            $output = new class extends BufferedOutput {
                protected string $key;

                protected function doWrite(string $message, bool $newline): void
                {
                    Cache::put($this->key, Cache::get($this->key, '') . trim($message) . ($newline ? "\n" : ''));
                }

                public function setKey(string $key): void
                {
                    $this->key = $key;
                }
            };

            $output->setKey($key);

            switch ($type) {
                case 'plugin':
                    PluginManager::instance()->setOutput(new OutputStyle(new ArrayInput([]), $output));
                    break;
                case 'theme':
                    ThemeManager::instance()->setOutput(new OutputStyle(new ArrayInput([]), $output));
                    break;
            }

            try {
                $response = (new ComposerSource(match ($type) {
                    'plugin' => ExtensionSource::TYPE_PLUGIN,
                    'theme' => ExtensionSource::TYPE_THEME,
                    'module' => ExtensionSource::TYPE_MODULE,
                }, composerPackage: $code))->install();
            } catch (\Throwable $e) {
                $response = null;
                Cache::put($key, Cache::get($key, '') . 'ERROR: ' . implode(PHP_EOL, [
                    $e->getMessage(),
                    $e->getFile() . '@' . $e->getLine(),
                    $e->getTraceAsString(),
                ]));
            } finally {
                Cache::put($key, Cache::get($key, '') . 'FINISHED:' . ($response ? 'SUCCESS' : 'FAILED'));
            }
        });

        return [
            'install_key' => $key
        ];
    }

    public function onInstallProductStatus(): array
    {
        if (!$key = trim(post('install_key'))) {
            throw new ApplicationException(Lang::get('system::lang.install.missing_plugin_name'));
        }

        if (!str_starts_with(base64_decode($key), $this->cachePrefix . Session::getId())) {
            throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
        }

        $data = Cache::get($key, '');

        return [
            'done' => !$data || str_contains($data, 'FINISHED:SUCCESS') || str_contains($data, 'FINISHED:FAILED'),
            'success' => str_contains($data, 'FINISHED:SUCCESS'),
            'data' => $data
        ];
    }

    /**
     * Rollback and remove a single plugin from the system.
     */
    public function onRemovePlugin(): RedirectResponse
    {
        if ($pluginCode = post('code')) {
            PluginManager::instance()->uninstall($pluginCode);
            Flash::success(Lang::get('system::lang.plugins.remove_success'));
        }

        return Redirect::refresh();
    }

    /**
     * Perform a bulk action on the provided plugins
     */
    public function onBulkAction(): array
    {
        if (($bulkAction = post('action')) &&
            ($checkedIds = post('checked')) &&
            is_array($checkedIds) &&
            count($checkedIds)
        ) {
            $manager = PluginManager::instance();
            $codes = PluginVersion::lists('code', 'id');

            foreach ($checkedIds as $id) {
                $code = $codes[$id] ?? null;
                if (!$code) {
                    continue;
                }

                switch ($bulkAction) {
                    // Enables plugin's updates.
                    case 'freeze':
                        $manager->freezePlugin($code);
                        break;

                    // Disables plugin's updates.
                    case 'unfreeze':
                        $manager->unfreezePlugin($code);
                        break;

                    // Disables plugin on the system.
                    case 'disable':
                        $manager->disablePlugin($code);
                        break;

                    // Enables plugin on the system.
                    case 'enable':
                        $manager->enablePlugin($code);
                        break;

                    // Rebuilds plugin database migrations.
                    case 'refresh':
                        $manager->refreshPlugin($code);
                        break;

                    // Rollback and remove plugins from the system.
                    case 'remove':
                        $manager->deletePlugin($code);
                        break;
                }
            }
        }

        Flash::success(Lang::get("system::lang.plugins.{$bulkAction}_success"));
        return $this->listRefresh();
    }
}
