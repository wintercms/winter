<?php

namespace Cms\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use Backend\Widgets\Form;
use Cms\Classes\Asset;
use Cms\Classes\CmsCompoundObject;
use Cms\Classes\CmsObject;
use Cms\Classes\ComponentManager;
use Cms\Classes\ComponentPartial;
use Cms\Classes\Content;
use Cms\Classes\Layout;
use Cms\Classes\Page;
use Cms\Classes\Partial;
use Cms\Classes\Router;
use Cms\Classes\Theme;
use Cms\Helpers\Cms as CmsHelpers;
use Cms\Widgets\AssetList;
use Cms\Widgets\ComponentList;
use Cms\Widgets\TemplateList;
use Exception;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;
use System\Helpers\DateTime;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Halcyon\Datasource\DatasourceInterface;
use Winter\Storm\Router\Router as StormRouter;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Event;
use Winter\Storm\Support\Facades\Flash;
use Winter\Storm\Support\Facades\Url;

/**
 * CMS index
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class Index extends Controller
{
    use \Backend\Traits\InspectableContainer;

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = [
        'cms.manage_content',
        'cms.manage_assets',
        'cms.manage_pages',
        'cms.manage_layouts',
        'cms.manage_partials'
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        Event::listen('backend.form.extendFieldsBefore', function ($widget) {
            if (!$widget->getController() instanceof Index) {
                return;
            }
            if (!$widget->model instanceof CmsCompoundObject) {
                return;
            }

            if (empty($widget->secondaryTabs['fields'])) {
                return;
            }

            if (array_key_exists('code', $widget->secondaryTabs['fields']) && CmsHelpers::safeModeEnabled()) {
                $widget->secondaryTabs['fields']['safemode_notice']['hidden'] = false;
                $widget->secondaryTabs['fields']['code']['readOnly'] = true;
            };
        });

        BackendMenu::setContext('Winter.Cms', 'cms', true);

        try {
            if (!($theme = Theme::getEditTheme())) {
                throw new ApplicationException(Lang::get('cms::lang.theme.edit.not_found'));
            }

            $this->theme = $theme;

            new TemplateList($this, 'pageList', function () use ($theme) {
                return Page::listInTheme($theme, true);
            });

            new TemplateList($this, 'partialList', function () use ($theme) {
                return Partial::listInTheme($theme, true);
            });

            new TemplateList($this, 'layoutList', function () use ($theme) {
                return Layout::listInTheme($theme, true);
            });

            new TemplateList($this, 'contentList', function () use ($theme) {
                return Content::listInTheme($theme, true);
            });

            new ComponentList($this, 'componentList');

            new AssetList($this, 'assetList');
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }
    }

    //
    // Pages
    //

    /**
     * Index page action
     */
    public function index(): void
    {
        $this->addJs('/modules/cms/assets/js/winter.cmspage.js', 'core');
        $this->addJs('/modules/cms/assets/js/winter.dragcomponents.js', 'core');
        $this->addJs('/modules/cms/assets/js/winter.tokenexpander.js', 'core');
        $this->addCss('/modules/cms/assets/css/winter.components.css', 'core');

        $this->bodyClass = 'compact-container';
        $this->pageTitle = 'cms::lang.cms.menu_label';
        $this->pageTitleTemplate = '%s '.Lang::get($this->pageTitle);

        if (Request::ajax() && Request::input('formWidgetAlias')) {
            $this->bindFormWidgetToController();
        }
    }

    /**
     * Opens an existing template from the index page
     */
    public function index_onOpenTemplate(): array
    {
        $this->validateRequestTheme();

        $type = Request::input('type');
        $template = $this->loadTemplate($type, Request::input('path'));
        $widget = $this->makeTemplateFormWidget($type, $template);

        $this->vars['templatePath'] = Request::input('path');
        $this->vars['lastModified'] = DateTime::makeCarbon($template->mtime);
        $this->vars['canCommit'] = $this->canCommitTemplate($template);
        $this->vars['canReset'] = $this->canResetTemplate($template);

        if ($type === 'page') {
            $router = new StormRouter;
            $this->vars['pageUrl'] = $router->urlFromPattern($template->url);
        }

        return [
            'tabTitle' => $this->getTabTitle($type, $template),
            'tab'      => $this->makePartial('form_page', [
                'form'          => $widget,
                'templateType'  => $type,
                'templateTheme' => $this->theme->getDirName(),
                'templateMtime' => $template->mtime
            ])
        ];
    }

    /**
     * Saves the template currently open
     * @throws ApplicationException if the file in the datasource has been modified since the file was loaded in the browser
     */
    public function onSave(): array
    {
        $this->validateRequestTheme();
        $type = Request::input('templateType');
        $templatePath = trim(Request::input('templatePath'));
        $template = $templatePath ? $this->loadTemplate($type, $templatePath) : $this->createTemplate($type);
        $formWidget = $this->makeTemplateFormWidget($type, $template);

        $saveData = $formWidget->getSaveData();
        $postData = post();
        $templateData = [];

        $settings = array_get($saveData, 'settings', []) + Request::input('settings', []);
        $settings = $this->upgradeSettings($settings, $template->settings);

        if ($settings) {
            $templateData['settings'] = $settings;
        }

        $fields = ['markup', 'code', 'fileName', 'content'];

        foreach ($fields as $field) {
            if (array_key_exists($field, $saveData)) {
                $templateData[$field] = $saveData[$field];
            }
            elseif (array_key_exists($field, $postData)) {
                $templateData[$field] = $postData[$field];
            }
        }

        if (!empty($templateData['markup']) && Config::get('cms.convertLineEndings', false) === true) {
            $templateData['markup'] = $this->convertLineEndings($templateData['markup']);
        }

        if (!empty($templateData['code']) && Config::get('cms.convertLineEndings', false) === true) {
            $templateData['code'] = $this->convertLineEndings($templateData['code']);
        }

        if (
            !Request::input('templateForceSave') && $template->mtime
            && Request::input('templateMtime') != $template->mtime
        ) {
            throw new ApplicationException('mtime-mismatch');
        }

        $template->attributes = [];
        $template->fill($templateData);
        $template->save();

        /**
         * @event cms.template.save
         * Fires after a CMS template (page|partial|layout|content|asset) has been saved.
         *
         * Example usage:
         *
         *     Event::listen('cms.template.save', function ((\Cms\Controllers\Index) $controller, (mixed) $templateObject, (string) $type) {
         *         \Log::info("A $type has been saved");
         *     });
         *
         * Or
         *
         *     $CmsIndexController->bindEvent('template.save', function ((mixed) $templateObject, (string) $type) {
         *         \Log::info("A $type has been saved");
         *     });
         *
         */
        $this->fireSystemEvent('cms.template.save', [$template, $type]);

        Flash::success(Lang::get('cms::lang.template.saved'));

        return $this->getUpdateResponse($template, $type);
    }

    /**
     * Displays a form that suggests the template has been edited elsewhere
     */
    public function onOpenConcurrencyResolveForm(): string
    {
        return $this->makePartial('concurrency_resolve_form');
    }

    /**
     * Create a new template
     */
    public function onCreateTemplate(): array
    {
        $type = Request::input('type');
        $template = $this->createTemplate($type);

        if ($type === 'asset') {
            $template->fileName = $this->widget->assetList->getCurrentRelativePath();
        }

        $widget = $this->makeTemplateFormWidget($type, $template);

        $this->vars['templatePath'] = '';
        $this->vars['canCommit'] = $this->canCommitTemplate($template);
        $this->vars['canReset'] = $this->canResetTemplate($template);

        return [
            'tabTitle' => $this->getTabTitle($type, $template),
            'tab'      => $this->makePartial('form_page', [
                'form'          => $widget,
                'templateType'  => $type,
                'templateTheme' => $this->theme->getDirName(),
                'templateMtime' => null
            ])
        ];
    }

    /**
     * Deletes multiple templates at the same time
     */
    public function onDeleteTemplates(): array
    {
        $this->validateRequestTheme();

        $type = Request::input('type');
        $templates = Request::input('template');
        $error = null;
        $deleted = [];

        try {
            foreach ($templates as $path => $selected) {
                if ($selected) {
                    $this->loadTemplate($type, $path)->delete();
                    $deleted[] = $path;
                }
            }
        }
        catch (Exception $ex) {
            $error = $ex->getMessage();
        }

        /**
         * @event cms.template.delete
         * Fires after a CMS template (page|partial|layout|content|asset) has been deleted.
         *
         * Example usage:
         *
         *     Event::listen('cms.template.delete', function ((\Cms\Controllers\Index) $controller, (string) $type) {
         *         \Log::info("A $type has been deleted");
         *     });
         *
         * Or
         *
         *     $CmsIndexController->bindEvent('template.delete', function ((string) $type) {
         *         \Log::info("A $type has been deleted");
         *     });
         *
         */
        $this->fireSystemEvent('cms.template.delete', [$type]);

        return [
            'deleted' => $deleted,
            'error'   => $error,
            'theme'   => Request::input('theme')
        ];
    }

    /**
     * Deletes a template
     */
    public function onDelete(): void
    {
        $this->validateRequestTheme();

        $type = Request::input('templateType');

        $this->loadTemplate($type, trim(Request::input('templatePath')))->delete();

        /*
         * Extensibility - documented above
         */
        $this->fireSystemEvent('cms.template.delete', [$type]);
    }

    /**
     * Returns list of available templates
     */
    public function onGetTemplateList(): array
    {
        $this->validateRequestTheme();

        $page = Page::inTheme($this->theme);
        return [
            'layouts' => $page->getLayoutOptions()
        ];
    }

    /**
     * Remembers an open or closed state for a supplied token, for example, component folders.
     */
    public function onExpandMarkupToken(): string
    {
        if (!$alias = post('tokenName')) {
            throw new ApplicationException(Lang::get('cms::lang.component.no_records'));
        }

        // Can only expand components at this stage
        if ((!$type = post('tokenType')) && $type !== 'component') {
            throw new ApplicationException("Unsupported token type: $type");
        }

        if (!($names = (array) post('component_names')) || !($aliases = (array) post('component_aliases'))) {
            throw new ApplicationException(Lang::get('cms::lang.component.not_found', ['name' => $alias]));
        }

        if (($index = array_get(array_flip($aliases), $alias, false)) === false) {
            throw new ApplicationException(Lang::get('cms::lang.component.not_found', ['name' => $alias]));
        }

        if (!$componentName = array_get($names, $index)) {
            throw new ApplicationException(Lang::get('cms::lang.component.not_found', ['name' => $alias]));
        }

        $manager = ComponentManager::instance();
        $componentObj = $manager->makeComponent($componentName);
        /**
         * @var ?ComponentPartial
         */
        $partial = ComponentPartial::load($componentObj, 'default');

        if (!$partial) {
            throw new ApplicationException(Lang::get('cms::lang.component.no_default_partial'));
        }

        $content = $partial->getContent();
        $content = str_replace('__SELF__', $alias, $content);

        return $content;
    }

    /**
     * Commits the DB changes of a template to the filesystem
     */
    public function onCommit(): array
    {
        $this->validateRequestTheme();
        $type = Request::input('templateType');
        $template = $this->loadTemplate($type, trim(Request::input('templatePath')));

        if ($this->canCommitTemplate($template)) {
            // Populate the filesystem with the template and then remove it from the db
            /**
             * @var AutoDatasource
             */
            $datasource = $this->getThemeDatasource();
            $datasource->pushToSource($template, 'filesystem');
            $datasource->removeFromSource($template, 'database');

            Flash::success(Lang::get('cms::lang.editor.commit_success', ['type' => $type]));
        }

        return array_merge($this->getUpdateResponse($template, $type), ['forceReload' => true]);
    }

    /**
     * Resets a template to the version on the filesystem
     */
    public function onReset(): array
    {
        $this->validateRequestTheme();
        $type = Request::input('templateType');
        $template = $this->loadTemplate($type, trim(Request::input('templatePath')));

        if ($this->canResetTemplate($template)) {
            // Remove the template from the DB
            /**
             * @var AutoDatasource
             */
            $datasource = $this->getThemeDatasource();
            $datasource->removeFromSource($template, 'database');

            Flash::success(Lang::get('cms::lang.editor.reset_success', ['type' => $type]));
        }

        return array_merge($this->getUpdateResponse($template, $type), ['forceReload' => true]);
    }

    //
    // Methods for internal use
    //

    /**
     * Get the response to return in an AJAX request that updates a template
     *
     * @param object $template The template that has been affected
     * @param string $type The type of template being affected
     */
    protected function getUpdateResponse($template, string $type): array
    {
        $result = [
            'templatePath'  => $template->fileName,
            'templateMtime' => $template->mtime,
            'tabTitle'      => $this->getTabTitle($type, $template)
        ];

        if ($type === 'page') {
            $result['pageUrl'] = Url::to($template->url);
            $router = new Router($this->theme);
            $router->clearCache();
            CmsCompoundObject::clearCache($this->theme);
        }

        $result['canCommit'] = $this->canCommitTemplate($template);
        $result['canReset'] = $this->canResetTemplate($template);

        return $result;
    }

    /**
     * Get the active theme's datasource
     */
    protected function getThemeDatasource(): DatasourceInterface
    {
        return $this->theme->getDatasource();
    }

    /**
     * Check to see if the provided template can be committed
     * Only available in debug mode, the DB layer must be enabled, and the template must exist in the database
     */
    protected function canCommitTemplate(CmsObject|Asset $template): bool
    {
        if (
            $template instanceof CmsObject === false
            || Config::get('app.debug', false)
        ) {
            return false;
        }

        $result = false;

        if (Theme::databaseLayerEnabled()) {
            /**
             * @var AutoDatasource
             */
            $datasource = $this->getThemeDatasource();
            $result = $datasource->sourceHasModel('database', $template);
        }

        return $result;
    }

    /**
     * Check to see if the provided template can be reset
     * Only available when the DB layer is enabled and the template exists in both the DB & Filesystem
     */
    protected function canResetTemplate(CmsObject|Asset $template): bool
    {
        if ($template instanceof CmsObject === false) {
            return false;
        }

        $result = false;

        if (Theme::databaseLayerEnabled()) {
            /**
             * @var AutoDatasource
             */
            $datasource = $this->getThemeDatasource();
            $result = $datasource->sourceHasModel('database', $template) && $datasource->sourceHasModel('filesystem', $template);
        }

        return $result;
    }

    /**
     * Validate that the current request is within the active theme
     * @throws ApplicationException if the requested theme does not match the currently loaded theme
     */
    protected function validateRequestTheme(): void
    {
        if ($this->theme->getDirName() != Request::input('theme')) {
            throw new ApplicationException(Lang::get('cms::lang.theme.edit.not_match'));
        }
    }

    /**
     * Resolves a template type to its class name
     */
    protected function resolveTypeClassName(string $type): string
    {
        $types = [
            'page'    => Page::class,
            'partial' => Partial::class,
            'layout'  => Layout::class,
            'content' => Content::class,
            'asset'   => Asset::class
        ];

        if (!array_key_exists($type, $types)) {
            throw new ApplicationException(Lang::get('cms::lang.template.invalid_type'));
        }

        return $types[$type];
    }

    /**
     * Returns an existing template of a given type
     */
    protected function loadTemplate(string $type, string $path): CmsObject|Asset
    {
        $class = $this->resolveTypeClassName($type);

        if (!($template = call_user_func([$class, 'load'], $this->theme, $path))) {
            throw new ApplicationException(Lang::get('cms::lang.template.not_found'));
        }

        /**
         * @event cms.template.processSettingsAfterLoad
         * Fires immediately after a CMS template (page|partial|layout|content|asset) has been loaded and provides an opportunity to interact with it.
         *
         * Example usage:
         *
         *     Event::listen('cms.template.processSettingsAfterLoad', function ((\Cms\Controllers\Index) $controller, (mixed) $templateObject) {
         *         // Make some modifications to the $template object
         *     });
         *
         * Or
         *
         *     $CmsIndexController->bindEvent('template.processSettingsAfterLoad', function ((mixed) $templateObject) {
         *         // Make some modifications to the $template object
         *     });
         *
         */
        $this->fireSystemEvent('cms.template.processSettingsAfterLoad', [$template]);

        return $template;
    }

    /**
     * Creates a new template of a given type
     * @throws ApplicationException if the requested type can't be initialized with the current theme
     */
    protected function createTemplate(string $type): CmsObject|Asset
    {
        $class = $this->resolveTypeClassName($type);

        if (!($template = $class::inTheme($this->theme))) {
            throw new ApplicationException(Lang::get('cms::lang.template.not_found'));
        }

        return $template;
    }

    /**
     * Returns the text for a template tab
     */
    protected function getTabTitle(string $type, CmsObject|Asset $template): string
    {
        if ($type === 'page') {
            $result = $template->title ?: $template->getFileName();
            if (!$result) {
                $result = Lang::get('cms::lang.page.new');
            }

            return $result;
        }

        if ($type === 'partial' || $type === 'layout' || $type === 'content' || $type === 'asset') {
            $result = in_array($type, ['asset', 'content']) ? $template->getFileName() : $template->getBaseFileName();
            if (!$result) {
                $result = Lang::get('cms::lang.'.$type.'.new');
            }

            return $result;
        }

        return $template->getFileName();
    }

    /**
     * Returns a form widget for a specified template type.
     */
    protected function makeTemplateFormWidget(string $type, CmsObject|Asset $template, ?string $alias = null): Form
    {
        $formConfigs = [
            'page'    => '~/modules/cms/classes/page/fields.yaml',
            'partial' => '~/modules/cms/classes/partial/fields.yaml',
            'layout'  => '~/modules/cms/classes/layout/fields.yaml',
            'content' => '~/modules/cms/classes/content/fields.yaml',
            'asset'   => '~/modules/cms/classes/asset/fields.yaml'
        ];

        if (!array_key_exists($type, $formConfigs)) {
            throw new ApplicationException(Lang::get('cms::lang.template.not_found'));
        }

        $widgetConfig = $this->makeConfig($formConfigs[$type]);

        $ext = pathinfo($template->fileName, PATHINFO_EXTENSION);
        if ($type === 'content') {
            switch ($ext) {
                case 'htm':
                    $type = 'richeditor';
                    break;
                case 'md':
                    $type = 'markdown';
                    break;
                default:
                    $type = 'codeeditor';
                    break;
            }
            array_set($widgetConfig->secondaryTabs, 'fields.markup.type', $type);
        }

        $codeField = ($template instanceof Asset) ? 'content' : 'markup';

        $lang = match ($ext) {
            'htm' => 'twig',
            'html' => 'html',
            'css' => 'css',
            'js', 'json' => 'javascript',
            'txt' => 'txt',
            default => 'php',
        };

        if (array_get($widgetConfig->secondaryTabs, "fields.$codeField.type") === 'codeeditor') {
            array_set($widgetConfig->secondaryTabs, "fields.$codeField.language", $lang);
        }

        $widgetConfig->model = $template;
        $widgetConfig->alias = $alias ?: 'form'.studly_case($type).md5($template->exists ? $template->getFileName() : uniqid());

        return $this->makeWidget(Form::class, $widgetConfig);
    }

    /**
     * Processes the component settings so they are ready to be saved.
     * @param array $settings The new settings for this template.
     * @param array $prevSettings The previous settings for this template.
     */
    protected function upgradeSettings($settings, $prevSettings): array
    {
        /*
         * Handle component usage
         */
        $componentProperties = post('component_properties');
        $componentNames = post('component_names');
        $componentAliases = post('component_aliases');

        if ($componentProperties !== null) {
            if ($componentNames === null || $componentAliases === null) {
                throw new ApplicationException(Lang::get('cms::lang.component.invalid_request'));
            }

            $count = count($componentProperties);
            if (count($componentNames) != $count || count($componentAliases) != $count) {
                throw new ApplicationException(Lang::get('cms::lang.component.invalid_request'));
            }

            for ($index = 0; $index < $count; $index++) {
                $componentName = $componentNames[$index];
                $componentAlias = $componentAliases[$index];

                $isSoftComponent = (substr($componentAlias, 0, 1) === '@');
                $componentName = ltrim($componentName, '@');
                $componentAlias = ltrim($componentAlias, '@');

                if ($componentAlias !== $componentName) {
                    $section = $componentName . ' ' . $componentAlias;
                } else {
                    $section = $componentName;
                }
                if ($isSoftComponent) {
                    $section = '@' . $section;
                }

                $properties = json_decode($componentProperties[$index], true);
                unset($properties['oc.alias'], $properties['inspectorProperty'], $properties['inspectorClassName']);

                if (!$properties) {
                    $oldComponentSettings = array_key_exists($section, $prevSettings['components'])
                        ? $prevSettings['components'][$section]
                        : null;
                    if ($isSoftComponent && $oldComponentSettings) {
                        $settings[$section] = $oldComponentSettings;
                    } else {
                        $settings[$section] = $properties;
                    }
                } else {
                    $settings[$section] = $properties;
                }
            }
        }

        /*
         * Handle view bag
         */
        $viewBag = post('viewBag');
        if ($viewBag !== null) {
            $settings['viewBag'] = $viewBag;
        }

        /**
         * @event cms.template.processSettingsBeforeSave
         * Fires before a CMS template (page|partial|layout|content|asset) is saved and provides an opportunity to interact with the settings data. `$dataHolder` = {settings: []}
         *
         * Example usage:
         *
         *     Event::listen('cms.template.processSettingsBeforeSave', function ((\Cms\Controllers\Index) $controller, (object) $dataHolder) {
         *         // Make some modifications to the $dataHolder object
         *     });
         *
         * Or
         *
         *     $CmsIndexController->bindEvent('template.processSettingsBeforeSave', function ((object) $dataHolder) {
         *         // Make some modifications to the $dataHolder object
         *     });
         *
         */
        $dataHolder = (object) ['settings' => $settings];
        $this->fireSystemEvent('cms.template.processSettingsBeforeSave', [$dataHolder]);

        return $dataHolder->settings;
    }

    /**
     * Finds a given component by its alias.
     *
     * If found, this will return the component's name, alias and properties.
     *
     * @param string $aliasQuery The alias to search for
     * @param array $components The array of components to look within.
     */
    protected function findComponentByAlias(string $aliasQuery, array $components = []): ?array
    {
        $found = null;

        foreach ($components as $name => $properties) {
            list($name, $alias) = strpos($name, ' ') ? explode(' ', $name) : [$name, $name];

            if (ltrim($alias, '@') === ltrim($aliasQuery, '@')) {
                $found = [
                    'name' => ltrim($name, '@'),
                    'alias' => $alias,
                    'properties' => $properties
                ];
                break;
            }
        }

        return $found;
    }

    /**
     * Binds the active form widget to the controller
     */
    protected function bindFormWidgetToController(): void
    {
        $alias = Request::input('formWidgetAlias');
        $type = Request::input('templateType');
        if (!empty(Request::input('templatePath'))) {
            $object = $this->loadTemplate($type, Request::input('templatePath'));
        } else {
            $object = $this->createTemplate($type);
        }
        $widget = $this->makeTemplateFormWidget($type, $object, $alias);

        $widget->bindToController();
    }

    /**
     * Replaces Windows style (/r/n) line endings with unix style (/n)
     * line endings.
     */
    protected function convertLineEndings(string $markup): string
    {
        return str_replace(["\r\n", "\r"], "\n", $markup);
    }
}
