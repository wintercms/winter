<?php

namespace Backend;

use Backend\Classes\WidgetManager;
use Backend\Facades\Backend;
use Backend\Facades\BackendAuth;
use Backend\Facades\BackendMenu;
use Backend\Models\AccessLog;
use Backend\Models\UserRole;
use Exception;
use Illuminate\Support\Facades\Event;
use System\Classes\Asset\PackageManager;
use System\Classes\CombineAssets;
use System\Classes\MailManager;
use System\Classes\SettingsManager;
use System\Classes\UpdateManager;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Flash;
use Winter\Storm\Support\ModuleServiceProvider;

class ServiceProvider extends ModuleServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerConsole();
        $this->registerMailer();
        $this->registerBackendPermissions();
        $this->registerBackendUserEvents();

        /*
         * Backend specific
         */
        if ($this->app->runningInBackend()) {
            $this->registerBackendNavigation();
            $this->registerBackendReportWidgets();
            $this->registerBackendWidgets();
            $this->registerBackendSettings();
        }
    }

    /**
     * Bootstrap the module events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerAssetBundles();
        parent::boot('backend');
    }

    /**
     * Register console commands
     */
    protected function registerConsole()
    {
        $this->registerConsoleCommand('create.controller', \Backend\Console\CreateController::class);
        $this->registerConsoleCommand('create.formwidget', \Backend\Console\CreateFormWidget::class);
        $this->registerConsoleCommand('create.reportwidget', \Backend\Console\CreateReportWidget::class);
        $this->registerConsoleCommand('user.create', \Backend\Console\UserCreate::class);
        $this->registerConsoleCommand('winter.passwd', \Backend\Console\WinterPasswd::class);
    }

    /**
     * Register mail templates
     */
    protected function registerMailer()
    {
        MailManager::instance()->registerCallback(function ($manager) {
            $manager->registerMailTemplates([
                'backend::mail.invite',
                'backend::mail.restore',
            ]);
        });
    }

    /**
     * Register asset bundles
     */
    protected function registerAssetBundles()
    {
        CombineAssets::registerCallback(function ($combiner) {
            $combiner->registerBundle('~/modules/backend/assets/less/winter.less');
            $combiner->registerBundle('~/modules/backend/assets/js/winter.js');
            $combiner->registerBundle('~/modules/backend/widgets/table/assets/js/build.js');
            $combiner->registerBundle('~/modules/backend/assets/vendor/ace-codeeditor/build.js');
            $combiner->registerBundle('~/modules/backend/widgets/mediamanager/assets/js/mediamanager-browser.js');
            $combiner->registerBundle('~/modules/backend/widgets/mediamanager/assets/less/mediamanager.less');
            $combiner->registerBundle('~/modules/backend/widgets/reportcontainer/assets/less/reportcontainer.less');
            $combiner->registerBundle('~/modules/backend/widgets/table/assets/less/table.less');
            $combiner->registerBundle('~/modules/backend/formwidgets/repeater/assets/less/repeater.less');
            $combiner->registerBundle('~/modules/backend/formwidgets/fileupload/assets/less/fileupload.less');
            $combiner->registerBundle('~/modules/backend/formwidgets/nestedform/assets/less/nestedform.less');
            $combiner->registerBundle('~/modules/backend/formwidgets/richeditor/assets/js/build-plugins.js');
            $combiner->registerBundle('~/modules/backend/formwidgets/permissioneditor/assets/less/permissioneditor.less');
            $combiner->registerBundle('~/modules/backend/formwidgets/markdowneditor/assets/less/markdowneditor.less');

            /*
             * Rich Editor is protected by DRM
             */
            if (file_exists(base_path('modules/backend/formwidgets/richeditor/assets/vendor/froala_drm'))) {
                $combiner->registerBundle('~/modules/backend/formwidgets/richeditor/assets/less/richeditor.less');
                $combiner->registerBundle('~/modules/backend/formwidgets/richeditor/assets/js/build.js');
            }
        });

        PackageManager::instance()->registerCallback(function ($mix) {
            $mix->registerPackage('module-backend.formwidgets.codeeditor', '~/modules/backend/formwidgets/codeeditor/assets/winter.mix.js');
        });
    }

    /*
     * Register navigation
     */
    protected function registerBackendNavigation()
    {
        BackendMenu::registerCallback(function ($manager) {
            $manager->registerMenuItems('Winter.Backend', [
                'dashboard' => [
                    'label'       => 'backend::lang.dashboard.menu_label',
                    'icon'        => 'icon-dashboard',
                    'iconSvg'     => 'modules/backend/assets/images/dashboard-icon.svg',
                    'url'         => Backend::url('backend'),
                    'permissions' => ['backend.access_dashboard'],
                    'order'       => 10
                ],
                'media' => [
                    'label'       => 'backend::lang.media.menu_label',
                    'icon'        => 'icon-folder',
                    'iconSvg'     => 'modules/backend/assets/images/media-icon.svg',
                    'url'         => Backend::url('backend/media'),
                    'permissions' => ['media.*'],
                    'order'       => 200
                ]
            ]);
            $manager->registerOwnerAlias('Winter.Backend', 'October.Backend');
        });
    }

    /*
     * Register report widgets
     */
    protected function registerBackendReportWidgets()
    {
        WidgetManager::instance()->registerReportWidgets(function ($manager) {
            $manager->registerReportWidget(\Backend\ReportWidgets\Welcome::class, [
                'label'   => 'backend::lang.dashboard.welcome.widget_title_default',
                'context' => 'dashboard'
            ]);
        });
    }

    /*
     * Register permissions
     */
    protected function registerBackendPermissions()
    {
        BackendAuth::registerCallback(function ($manager) {
            $manager->registerPermissions('Winter.Backend', [
                'backend.access_dashboard' => [
                    'label' => 'system::lang.permissions.view_the_dashboard',
                    'tab'   => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
                ],
                'backend.manage_default_dashboard' => [
                    'label' => 'system::lang.permissions.manage_default_dashboard',
                    'tab'   => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                ],
                'backend.manage_users' => [
                    'label' => 'system::lang.permissions.manage_other_administrators',
                    'tab'   => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                ],
                'backend.impersonate_users' => [
                    'label' => 'system::lang.permissions.impersonate_users',
                    'tab'   => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                ],
                'backend.manage_preferences' => [
                    'label' => 'system::lang.permissions.manage_preferences',
                    'tab'   => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
                ],
                'backend.manage_editor' => [
                    'label' => 'system::lang.permissions.manage_editor',
                    'tab'   => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                ],
                'backend.manage_own_editor' => [
                    'label' => 'system::lang.permissions.manage_own_editor',
                    'tab'   => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
                ],
                'backend.manage_branding' => [
                    'label' => 'system::lang.permissions.manage_branding',
                    'tab'   => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                ],
                'media.manage_media' => [
                    'label' => 'backend::lang.permissions.manage_media',
                    'tab' => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
                ],
                'backend.allow_unsafe_markdown' => [
                    'label' => 'backend::lang.permissions.allow_unsafe_markdown',
                    'tab' => 'system::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                ],
            ]);
            $manager->registerPermissionOwnerAlias('Winter.Backend', 'October.Backend');
        });
    }

    /**
     * Register the backend user events
     */
    protected function registerBackendUserEvents()
    {
        Event::listen('backend.user.login', function (\Backend\Models\User $user) {
            // @TODO: Deprecate this, and only run migrations when it makes sense
            $runMigrationsOnLogin = (bool) Config::get('cms.runMigrationsOnLogin', Config::get('app.debug', false));
            if ($runMigrationsOnLogin) {
                try {
                    // Load version updates
                    UpdateManager::instance()->update();
                } catch (Exception $e) {
                    Flash::error($e->getMessage());
                }
            }

            // Log the sign in event
            AccessLog::add($user);
        });
    }

    /*
     * Register widgets
     */
    protected function registerBackendWidgets()
    {
        WidgetManager::instance()->registerFormWidgets(function ($manager) {
            $manager->registerFormWidget(\Backend\FormWidgets\CodeEditor::class, 'codeeditor');
            $manager->registerFormWidget(\Backend\FormWidgets\ColorPicker::class, 'colorpicker');
            $manager->registerFormWidget(\Backend\FormWidgets\DataTable::class, 'datatable');
            $manager->registerFormWidget(\Backend\FormWidgets\DatePicker::class, 'datepicker');
            $manager->registerFormWidget(\Backend\FormWidgets\FileUpload::class, 'fileupload');
            $manager->registerFormWidget(\Backend\FormWidgets\IconPicker::class, 'iconpicker');
            $manager->registerFormWidget(\Backend\FormWidgets\MarkdownEditor::class, 'markdown');
            $manager->registerFormWidget(\Backend\FormWidgets\MediaFinder::class, 'mediafinder');
            $manager->registerFormWidget(\Backend\FormWidgets\NestedForm::class, 'nestedform');
            $manager->registerFormWidget(\Backend\FormWidgets\RecordFinder::class, 'recordfinder');
            $manager->registerFormWidget(\Backend\FormWidgets\Relation::class, 'relation');
            $manager->registerFormWidget(\Backend\FormWidgets\RelationManager::class, 'relationmanager');
            $manager->registerFormWidget(\Backend\FormWidgets\Repeater::class, 'repeater');
            $manager->registerFormWidget(\Backend\FormWidgets\RichEditor::class, 'richeditor');
            $manager->registerFormWidget(\Backend\FormWidgets\Sensitive::class, 'sensitive');
            $manager->registerFormWidget(\Backend\FormWidgets\TagList::class, 'taglist');
        });
    }

    /*
     * Register settings
     */
    protected function registerBackendSettings()
    {
        SettingsManager::instance()->registerCallback(function ($manager) {
            $manager->registerSettingItems('Winter.Backend', [
                'branding' => [
                    'label'       => 'backend::lang.branding.menu_label',
                    'description' => 'backend::lang.branding.menu_description',
                    'category'    => SettingsManager::CATEGORY_SYSTEM,
                    'icon'        => 'icon-paint-brush',
                    'class'       => 'Backend\Models\BrandSetting',
                    'permissions' => ['backend.manage_branding'],
                    'order'       => 500,
                    'keywords'    => 'brand style'
                ],
                'editor' => [
                    'label'       => 'backend::lang.editor.menu_label',
                    'description' => 'backend::lang.editor.menu_description',
                    'category'    => SettingsManager::CATEGORY_SYSTEM,
                    'icon'        => 'icon-code',
                    'class'       => 'Backend\Models\EditorSetting',
                    'permissions' => ['backend.manage_editor'],
                    'order'       => 500,
                    'keywords'    => 'html code class style'
                ],
                'myaccount' => [
                    'label'       => 'backend::lang.myaccount.menu_label',
                    'description' => 'backend::lang.myaccount.menu_description',
                    'category'    => SettingsManager::CATEGORY_MYSETTINGS,
                    'icon'        => 'icon-user',
                    'url'         => Backend::url('backend/users/myaccount'),
                    'order'       => 500,
                    'context'     => 'mysettings',
                    'keywords'    => 'backend::lang.myaccount.menu_keywords'
                ],
                'preferences' => [
                    'label'       => 'backend::lang.backend_preferences.menu_label',
                    'description' => 'backend::lang.backend_preferences.menu_description',
                    'category'    => SettingsManager::CATEGORY_MYSETTINGS,
                    'icon'        => 'icon-laptop',
                    'url'         => Backend::url('backend/preferences'),
                    'permissions' => ['backend.manage_preferences'],
                    'order'       => 510,
                    'context'     => 'mysettings'
                ],
                'access_logs' => [
                    'label'       => 'backend::lang.access_log.menu_label',
                    'description' => 'backend::lang.access_log.menu_description',
                    'category'    => SettingsManager::CATEGORY_LOGS,
                    'icon'        => 'icon-lock',
                    'url'         => Backend::url('backend/accesslogs'),
                    'permissions' => ['system.access_logs'],
                    'order'       => 920
                ]
            ]);
            $manager->registerOwnerAlias('Winter.Backend', 'October.Backend');
        });
    }
}
