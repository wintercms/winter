<?php

namespace Backend\Behaviors;

use Backend\Classes\ControllerBehavior;
use Backend\Classes\NavigationManager;
use Backend\Facades\Backend;
use Backend\Widgets\WorkbenchList;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use System\Classes\PluginManager;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\Validator;
use Winter\Storm\Support\Str;

class WorkbenchController extends ControllerBehavior
{
    protected array $state = [];

    /**
     * @var array<string, WorkbenchList> Array of list widgets for each section.
     */
    protected ?array $listWidgets = null;

    /**
     * @var array Configuration values that must exist when applying the primary config file.
     */
    protected $requiredConfig = ['sections'];

    protected string $nonce;

    /**
     * @var mixed Configuration for this behaviour
     */
    public $workbenchConfig = 'config_workbench.yaml';

    public function __construct($controller)
    {
        parent::__construct($controller);

        // Build configuration
        $this->config = $this->validateConfig(
            $this->makeConfig($controller->workbenchConfig ?: $this->workbenchConfig, $this->requiredConfig)
        );

        // Generate nonce
        $this->nonce = 'workbench_' . bin2hex(random_bytes(10));

        // Register custom side panel
        $this->registerSidePanel();

        // Allow behaviour to provide default partials
        $this->controller->addViewPath(base_path('modules/backend/behaviors/workbenchcontroller/partials'));
    }

    public function index()
    {
        $this->controller->bodyClass = 'compact-container';
        if (empty($this->controller->pageTitle)) {
            $this->controller->pageTitle = $this->getConfig('title', 'Workbench');
        }
        $this->controller->pageTitleTemplate = $this->getConfig('titleTemplate', '%s | ' . $this->controller->pageTitle);

        return $this->makeViewContent($this->makePartial('workbench'));
    }

    /**
     * Gets navigation items for all defined sections.
     *
     * Each navigation item is an object with the following properties:
     *
     * - `label` (string): The label for the navigation item.
     * - `icon` (string): The icon for the navigation item.
     * - `iconSvg` (string): An SVG icon for the navigation item, to use alternatively to the `icon`.
     * - `attributes` (array): Additional attributes for the navigation item.
     *
     * @return array<string, object>
     */
    public function getSectionNavigation(): array
    {
        if (!count($this->getConfig('sections', []))) {
            return [];
        }

        // Process relevant navigation item config
        return array_map(function ($section) {
            return (object) [
                'label' => $section['nav']['label'] ?? Str::plural(last(explode('\\', $section['modelClass']))),
                'icon' => $section['nav']['icon'] ?? null,
                'iconSvg' => $section['nav']['iconSvg'] ?? null,
                'attributes' => $section['nav']['attributes'] ?? [],
            ];
        }, $this->getConfig('sections'));
    }

    /**
     * Gets the list of widgets for each section.
     *
     * @return array
     */
    public function getSectionListWidgets(): array
    {
        if (!is_null($this->listWidgets)) {
            return $this->listWidgets;
        }

        $this->listWidgets = [];

        if (!count($this->getConfig('sections', []))) {
            return [];
        }

        foreach ($this->getConfig('sections') as $section => $config) {
            $this->listWidgets[$section] = new WorkbenchList($this->controller, [
                'model' => $config['modelClass'],
                'scope' => $config['list']['scope'] ?? null,
                'title' => $config['list']['title'] ?? null,
                'mode' => $config['list']['mode'] ?? 'list',
                'multiselect' => $config['list']['multiselect'] ?? false,
                'item' => $config['list']['item'],
            ]);
        }

        return $this->listWidgets;
    }

    /**
     * Validates and returns the configuration for this behavior.
     */
    protected function validateConfig(object $config): object
    {
        $validator = Validator::make((array) $config, [
            'sections.*.modelClass' => 'required|string',
            'sections.*.nav' => 'sometimes|array',
            'sections.*.nav.icon' => 'sometimes|string',
            'sections.*.nav.iconSvg' => 'sometimes|string',
            'sections.*.list' => 'required|array',
            'sections.*.list.item' => 'required|array',
        ], [
            'sections.*.modelClass' => 'backend::lang.workbench.validation.section_model_class_required',
            'sections.*.nav' => 'backend::lang.workbench.validation.section_nav_array',
            'sections.*.nav.icon' => 'backend::lang.workbench.validation.section_nav_icon_string',
            'sections.*.nav.iconSvg' => 'backend::lang.workbench.validation.section_nav_icon_svg_string',
            'sections.*.list' => 'backend::lang.workbench.validation.section_list_array',
            'sections.*.list.item' => 'backend::lang.workbench.validation.section_list_item_array',
        ]);

        if ($validator->fails()) {
            // Get first error message for a particular section.
            foreach ($validator->errors()->messages() as $field => $messages) {
                $section = explode('.', $field)[1];
                throw new ApplicationException('Invalid workbench configuration: ' . e(Lang::get($messages[0], ['section' => $section])));
            }
        }

        return (object) $config;
    }

    /**
     * Determines the plugin code from the current URL.
     */
    protected function getPluginCode()
    {
        [$author, $code] = explode('/', str_replace(Backend::url() . '/', '', Request::url()));
        return ucfirst($author) . '.' . ucfirst($code);
    }

    /**
     * Registers the side panel for the workbench controller and effects navigation.
     */
    protected function registerSidePanel(): void
    {
        $pluginCode = $this->getPluginCode();

        try {
            $plugin = PluginManager::instance()->findByIdentifier($pluginCode);

            if (is_null($plugin)) {
                throw new \Exception(
                    sprintf(
                        'Plugin "%s" not found when generated side navigation for workbench controller.',
                        $pluginCode
                    )
                );
            }

            $navigation = $plugin->registerNavigation();
            if (empty($navigation)) {
                throw new \Exception(
                    sprintf(
                        'Navigation not found for plugin "%s" when generated side navigation for workbench controller.'
                        . ' Defaulting to "_workbench" context.',
                        $pluginCode
                    )
                );
            }

            foreach ($navigation as $context => $nav) {
                if (isset($nav['url']) && str_starts_with(Request::url(), $nav['url'])) {
                    NavigationManager::instance()->registerContextSidenavPartial($pluginCode, $context, 'sidenav');
                    NavigationManager::instance()->setContext($pluginCode, $context);
                    return;
                }
            }
        } catch (\Throwable $e) {
            // If we can't find the plugin or determine the navigation item for the current action, we'll log it
            // and use a default navigation context, which will result in no navigation items being highlighted, but
            // should still work.
            Log::warning($e->getMessage());

            NavigationManager::instance()->registerContextSidenavPartial($pluginCode, '_workbench', 'sidenav');
            NavigationManager::instance()->setContext($pluginCode, '_workbench');
        }
    }
}
