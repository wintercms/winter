<?php namespace Cms;

use Backend;
use Backend\Classes\WidgetManager;
use Backend\Facades\BackendAuth;
use Backend\Facades\BackendMenu;
use Backend\Models\UserRole;
use Cms\Classes\CmsController;
use Cms\Classes\CmsObject;
use Cms\Classes\ComponentManager;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Router;
use Cms\Classes\Theme;
use Cms\Models\ThemeData;
use Cms\Models\ThemeLog;
use Cms\Twig\DebugExtension;
use Cms\Twig\Extension as CmsTwigExtension;
use Cms\Twig\Loader as CmsTwigLoader;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use System\Classes\CombineAssets;
use System\Classes\MarkupManager;
use System\Classes\SettingsManager;
use Twig\Cache\FilesystemCache as TwigCacheFilesystem;
use Winter\Storm\Support\Facades\Event;
use Winter\Storm\Support\Facades\Url;
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
        $this->registerErrorHandler();
        $this->registerTwigParser();
        $this->registerComponents();
        $this->registerThemeLogging();
        $this->registerCombinerEvents();
        $this->registerHalcyonModels();
        $this->registerBackendPermissions();

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

        parent::boot('cms');

        $this->bootMenuItemEvents();
        $this->bootRichEditorEvents();
    }

    /**
     * Register command line specifics
     */
    protected function registerConsole()
    {
        $this->registerConsoleCommand('create.component', \Cms\Console\CreateComponent::class);
        $this->registerConsoleCommand('create.theme', \Cms\Console\CreateTheme::class);

        $this->registerConsoleCommand('theme.install', \Cms\Console\ThemeInstall::class);
        $this->registerConsoleCommand('theme.remove', \Cms\Console\ThemeRemove::class);
        $this->registerConsoleCommand('theme.list', \Cms\Console\ThemeList::class);
        $this->registerConsoleCommand('theme.use', \Cms\Console\ThemeUse::class);
        $this->registerConsoleCommand('theme.sync', \Cms\Console\ThemeSync::class);
    }

    /**
     * Error handling for abort() errors
     */
    protected function registerErrorHandler()
    {
        $this->app->error(function (HttpExceptionInterface $exception, $code, $fromConsole) {
            if ($this->app->runningInBackend() && BackendAuth::check()) {
                return;
            }

            $theme = Theme::getActiveTheme();
            $controller = new CmsController($theme);
            if ($code === 404) {
                return Response::make($controller->run('/404')->original, 404, []);
            }

            if (!Config::get('app.debug', false)) {
                $router = new Router($theme);
                // Use the default view if no "/error" URL is found.
                if (!$router->findByUrl('/error')) {
                    $result = View::make('cms::error');
                } else {
                    // Route to the CMS error page.
                    $controller = new CmsController($theme);
                    $result = $controller->run('/error')->original;
                }

                return Response::make($result, $code, []);
            }
        });
    }

    /*
     * Register Twig Environments and other Twig modifications provided by the module
     */
    protected function registerTwigParser()
    {
        // Register CMS Twig environment
        $this->app->bind('twig.environment.cms', function ($app) {
            // Load Twig options
            $useCache = !Config::get('cms.twigNoCache');
            $isDebugMode = Config::get('app.debug', false);
            $strictVariables = Config::get('cms.enableTwigStrictVariables', false);
            $strictVariables = $strictVariables ?? $isDebugMode;
            $forceBytecode = Config::get('cms.forceBytecodeInvalidation', false);

            $options = [
                'auto_reload' => true,
                'debug' => $isDebugMode,
                'strict_variables' => $strictVariables,
            ];

            if ($useCache) {
                $theme = Theme::getActiveTheme();
                $themeDir = $theme->getDirName();
                if ($parent = $theme->getConfig()['parent'] ?? false) {
                    $themeDir .= '-' . $parent;
                }

                $options['cache'] = new TwigCacheFilesystem(
                    storage_path(implode(DIRECTORY_SEPARATOR, [
                        'cms',
                        'twig',
                        $themeDir,
                    ])) . DIRECTORY_SEPARATOR,
                    $forceBytecode ? TwigCacheFilesystem::FORCE_BYTECODE_INVALIDATION : 0
                );
            }

            $twig = MarkupManager::makeBaseTwigEnvironment(new CmsTwigLoader, $options);
            $twig->addExtension(new CmsTwigExtension);
            if ($isDebugMode) {
                $twig->addExtension(new DebugExtension);
            }

            return $twig;
        });
    }

    /**
     * Register asset bundles
     */
    protected function registerAssetBundles()
    {
        CombineAssets::registerCallback(function ($combiner) {
            $combiner->registerBundle('~/modules/cms/assets/less/winter.components.less');
            $combiner->registerBundle('~/modules/cms/assets/less/winter.theme-selector.less');
            $combiner->registerBundle('~/modules/cms/widgets/assetlist/assets/less/assetlist.less');
        });
    }

    /**
     * Register components.
     */
    protected function registerComponents()
    {
        ComponentManager::instance()->registerComponents(function ($manager) {
            $manager->registerComponent(\Cms\Components\ViewBag::class, 'viewBag');
            $manager->registerComponent(\Cms\Components\Resources::class, 'resources');
        });
    }

    /**
     * Registers theme logging on templates.
     */
    protected function registerThemeLogging()
    {
        CmsObject::extend(function ($model) {
            ThemeLog::bindEventsToModel($model);
        });
    }

    /**
     * Registers events for the asset combiner.
     */
    protected function registerCombinerEvents()
    {
        if ($this->app->runningInBackend() || $this->app->runningInConsole()) {
            return;
        }

        Event::listen('cms.combiner.beforePrepare', function ($combiner, $assets) {
            $filters = array_flatten($combiner->getFilters());
            ThemeData::applyAssetVariablesToCombinerFilters($filters);
        });

        Event::listen('cms.combiner.getCacheKey', function ($combiner, $holder) {
            $holder->key = $holder->key . ThemeData::getCombinerCacheKey();
        });
    }

    /*
     * Register navigation
     */
    protected function registerBackendNavigation()
    {
        BackendMenu::registerCallback(function ($manager) {
            $manager->registerMenuItems('Winter.Cms', [
                'cms' => [
                    'label'       => 'cms::lang.cms.menu_label',
                    'icon'        => 'icon-magic',
                    'iconSvg'     => 'modules/cms/assets/images/cms-icon.svg',
                    'url'         => Backend::url('cms'),
                    'order'       => 100,
                    'permissions' => [
                        'cms.manage_content',
                        'cms.manage_assets',
                        'cms.manage_pages',
                        'cms.manage_layouts',
                        'cms.manage_partials'
                    ],
                    'sideMenu' => [
                        'pages' => [
                            'label'        => 'cms::lang.page.menu_label',
                            'icon'         => 'icon-copy',
                            'url'          => 'javascript:;',
                            'attributes'   => ['data-menu-item' => 'pages'],
                            'permissions'  => ['cms.manage_pages'],
                            'counterLabel' => 'cms::lang.page.unsaved_label'
                        ],
                        'partials' => [
                            'label'        => 'cms::lang.partial.menu_label',
                            'icon'         => 'icon-tags',
                            'url'          => 'javascript:;',
                            'attributes'   => ['data-menu-item' => 'partials'],
                            'permissions'  => ['cms.manage_partials'],
                            'counterLabel' => 'cms::lang.partial.unsaved_label'
                        ],
                        'layouts' => [
                            'label'        => 'cms::lang.layout.menu_label',
                            'icon'         => 'icon-th-large',
                            'url'          => 'javascript:;',
                            'attributes'   => ['data-menu-item' => 'layouts'],
                            'permissions'  => ['cms.manage_layouts'],
                            'counterLabel' => 'cms::lang.layout.unsaved_label'
                        ],
                        'content' => [
                            'label'        => 'cms::lang.content.menu_label',
                            'icon'         => 'icon-file-text-o',
                            'url'          => 'javascript:;',
                            'attributes'   => ['data-menu-item' => 'content'],
                            'permissions'  => ['cms.manage_content'],
                            'counterLabel' => 'cms::lang.content.unsaved_label'
                        ],
                        'assets' => [
                            'label'        => 'cms::lang.asset.menu_label',
                            'icon'         => 'icon-picture-o',
                            'url'          => 'javascript:;',
                            'attributes'   => ['data-menu-item' => 'assets'],
                            'permissions'  => ['cms.manage_assets'],
                            'counterLabel' => 'cms::lang.asset.unsaved_label'
                        ],
                        'components' => [
                            'label'       => 'cms::lang.component.menu_label',
                            'icon'        => 'icon-puzzle-piece',
                            'url'         => 'javascript:;',
                            'attributes'  => ['data-menu-item' => 'components'],
                            'permissions' => ['cms.manage_pages', 'cms.manage_layouts', 'cms.manage_partials']
                        ]
                    ]
                ]
            ]);
            $manager->registerQuickActions('Winter.Cms', [
                'preview' => [
                    'label'      => 'backend::lang.tooltips.preview_website',
                    'icon'       => 'icon-crosshairs',
                    'url'        => Url::to('/'),
                    'order'      => 10,
                    'attributes' => [
                        'target' => '_blank',
                        'rel'    => 'noopener noreferrer',
                    ],
                ],
            ]);
            $manager->registerOwnerAlias('Winter.Cms', 'October.Cms');
        });
    }

    /*
     * Register report widgets
     */
    protected function registerBackendReportWidgets()
    {
        WidgetManager::instance()->registerReportWidgets(function ($manager) {
            $manager->registerReportWidget(\Cms\ReportWidgets\ActiveTheme::class, [
                'label'   => 'cms::lang.dashboard.active_theme.widget_title_default',
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
            $manager->registerPermissions('Winter.Cms', [
                'cms.manage_content' => [
                    'label' => 'cms::lang.permissions.manage_content',
                    'tab' => 'cms::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                    'order' => 100
                ],
                'cms.manage_assets' => [
                    'label' => 'cms::lang.permissions.manage_assets',
                    'tab' => 'cms::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                    'order' => 100
                ],
                'cms.manage_pages' => [
                    'label' => 'cms::lang.permissions.manage_pages',
                    'tab' => 'cms::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                    'order' => 100
                ],
                'cms.manage_layouts' => [
                    'label' => 'cms::lang.permissions.manage_layouts',
                    'tab' => 'cms::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                    'order' => 100
                ],
                'cms.manage_partials' => [
                    'label' => 'cms::lang.permissions.manage_partials',
                    'tab' => 'cms::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                    'order' => 100
                ],
                'cms.manage_themes' => [
                    'label' => 'cms::lang.permissions.manage_themes',
                    'tab' => 'cms::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER],
                    'order' => 100
                ],
                'cms.manage_theme_options' => [
                    'label' => 'cms::lang.permissions.manage_theme_options',
                    'tab' => 'cms::lang.permissions.name',
                    'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
                    'order' => 100
                ],
            ]);
            $manager->registerPermissionOwnerAlias('Winter.Cms', 'October.Cms');
        });
    }

    /*
     * Register widgets
     */
    protected function registerBackendWidgets()
    {
        WidgetManager::instance()->registerFormWidgets(function ($manager) {
            $manager->registerFormWidget(FormWidgets\Components::class);
        });
    }

    /*
     * Register settings
     */
    protected function registerBackendSettings()
    {
        SettingsManager::instance()->registerCallback(function ($manager) {
            $manager->registerSettingItems('Winter.Cms', [
                'theme' => [
                    'label'       => 'cms::lang.theme.settings_menu',
                    'description' => 'cms::lang.theme.settings_menu_description',
                    'category'    => SettingsManager::CATEGORY_CMS,
                    'icon'        => 'icon-picture-o',
                    'url'         => Backend::url('cms/themes'),
                    'permissions' => ['cms.manage_themes', 'cms.manage_theme_options'],
                    'order'       => 200
                ],
                'maintenance_settings' => [
                    'label'       => 'cms::lang.maintenance.settings_menu',
                    'description' => 'cms::lang.maintenance.settings_menu_description',
                    'category'    => SettingsManager::CATEGORY_CMS,
                    'icon'        => 'icon-plug',
                    'class'       => Models\MaintenanceSetting::class,
                    'permissions' => ['cms.manage_themes'],
                    'order'       => 300
                ],
                'theme_logs' => [
                    'label'       => 'cms::lang.theme_log.menu_label',
                    'description' => 'cms::lang.theme_log.menu_description',
                    'category'    => SettingsManager::CATEGORY_LOGS,
                    'icon'        => 'icon-magic',
                    'url'         => Backend::url('cms/themelogs'),
                    'permissions' => ['system.access_logs'],
                    'order'       => 910,
                    'keywords'    => 'theme change log'
                ]
            ]);
            $manager->registerOwnerAlias('Winter.Cms', 'October.Cms');
        });
    }

    /**
     * Registers events for menu items.
     */
    protected function bootMenuItemEvents()
    {
        Event::listen('pages.menuitem.listTypes', function () {
            return [
                'cms-page' => 'cms::lang.page.cms_page'
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function ($type) {
            if ($type === 'cms-page') {
                return CmsPage::getMenuTypeInfo($type);
            }
        });

        Event::listen('pages.menuitem.resolveItem', function ($type, $item, $url, $theme) {
            if ($type === 'cms-page') {
                return CmsPage::resolveMenuItem($item, $url, $theme);
            }
        });
    }

    /**
     * Registers events for rich editor page links.
     */
    protected function bootRichEditorEvents()
    {
        Event::listen('backend.richeditor.listTypes', function () {
            return [
                'cms-page' => 'cms::lang.page.cms_page'
            ];
        });

        Event::listen('backend.richeditor.getTypeInfo', function ($type) {
            if ($type === 'cms-page') {
                return CmsPage::getRichEditorTypeInfo($type);
            }
        });
    }

    /**
     * Registers the models to be made available to the theme database layer
     */
    protected function registerHalcyonModels()
    {
        Event::listen('system.console.theme.sync.getAvailableModelClasses', function () {
            return [
                Classes\Theme::class,
                Classes\Meta::class,
                Classes\Page::class,
                Classes\Layout::class,
                Classes\Content::class,
                Classes\Partial::class
            ];
        });
    }
}
