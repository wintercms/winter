<?php namespace System\ReportWidgets;

use Lang;
use Config;
use BackendAuth;
use System\Models\Parameter;
use System\Models\LogSetting;
use System\Classes\UpdateManager;
use System\Classes\PluginManager;
use Backend\Classes\ReportWidgetBase;
use Backend\Models\User;
use System\Models\EventLog;
use System\Models\RequestLog;
use Exception;

/**
 * System status report widget.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class Status extends ReportWidgetBase
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'status';

    /**
     * Renders the widget.
     */
    public function render()
    {
        try {
            $this->loadData();
        }
        catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
        }

        return $this->makePartial('widget');
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title'             => 'backend::lang.dashboard.widget_title_label',
                'default'           => 'backend::lang.dashboard.status.widget_title_default',
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error',
            ]
        ];
    }

    protected function loadData()
    {
        $manager = UpdateManager::instance();

        $this->vars['canUpdate'] = BackendAuth::getUser()->hasAccess('system.manage_updates');
        $this->vars['updates']   = $manager->check();
        $this->vars['warnings']  = $this->getSystemWarnings();
        $this->vars['coreBuild'] = Parameter::get('system::core.build');

        $this->vars['eventLog']      = EventLog::count();
        $this->vars['eventLogMsg']   = LogSetting::get('log_events', false) ? false : true;
        $this->vars['requestLog']    = RequestLog::count();
        $this->vars['requestLogMsg'] = LogSetting::get('log_requests', false) ? false : true;

        $this->vars['appBirthday'] = Parameter::get('system::app.birthday');
    }

    public function onLoadWarningsForm()
    {
        $this->vars['warnings'] = $this->getSystemWarnings();
        return $this->makePartial('warnings_form');
    }

    protected function getSystemWarnings()
    {
        $warnings = [];

        $missingDependencies = PluginManager::instance()->findMissingDependencies();

        $writablePaths = [
            temp_path(),
            storage_path(),
            storage_path('app'),
            storage_path('logs'),
            storage_path('framework'),
            storage_path('cms'),
            storage_path('cms/cache'),
            storage_path('cms/twig'),
            storage_path('cms/combiner'),
        ];

        if (in_array('Cms', Config::get('cms.loadModules', []))) {
            $writablePaths[] = themes_path();
        }

        // Warn if debug mode is enabled - this is a security risk
        if (Config::get('app.debug', true)) {
            $warnings[] = [
                'message' => Lang::get('backend::lang.warnings.debug'),
                'fixUrl' => 'https://wintercms.com/docs/v1.2/docs/setup/configuration#debug-mode',
            ];
        }
        // Warn if CSRF protection is disabled - this is a security risk
        if (Config::get('cms.enableCsrfProtection', true) === false) {
            $warnings[] = [
                'message' => Lang::get('backend::lang.warnings.csrf'),
                'fixUrl' => 'https://wintercms.com/docs/v1.2/docs/setup/configuration#csrf-protection',
            ];
        }
        // Warn if backend auth throttling is disabled - this is a security risk
        if (Config::get('auth.throttle.enabled', true) === false) {
            $warnings[] = [
                'message' => Lang::get('backend::lang.warnings.auth_throttle_disabled'),
            ];
        }
        // Warn if the user has disabled base directory restriction - this is a security risk
        if (Config::get('cms.restrictBaseDir', true) === false) {
            $warnings[] = [
                'message' => Lang::get('backend::lang.warnings.restrict_base_dir'),
            ];
        }
        // Warn if the default backend user is using the default username or email, and has access to manage users
        if (
            BackendAuth::getUser()->hasAccess('backend.manage_users')
            && User::where('login', 'admin')->orWhere('email', 'admin@domain.tld')->count()
        ) {
            $warnings[] = [
                'message' => Lang::get('backend::lang.warnings.default_backend_user'),
            ];
        }
        // Warn if backend assets are being decompiled
        if (Config::get('develop.decompileBackendAssets', false)) {
            $warnings[] = [
                'message' => Lang::get('backend::lang.warnings.decompileBackendAssets'),
            ];
        }

        $requiredExtensions = [
            'GD'       => extension_loaded('gd'),
            'fileinfo' => extension_loaded('fileinfo'),
            'Zip'      => class_exists('ZipArchive'),
            'cURL'     => function_exists('curl_init') && defined('CURLOPT_FOLLOWLOCATION'),
            'OpenSSL'  => function_exists('openssl_random_pseudo_bytes'),
        ];

        foreach ($writablePaths as $path) {
            if (!is_writable($path)) {
                $warnings[] = [
                    'message' => Lang::get('backend::lang.warnings.permissions', ['name' => '<strong>'.$path.'</strong>'])
                ];
            }
        }

        foreach ($requiredExtensions as $extension => $installed) {
            if (!$installed) {
                $warnings[] = [
                    'message' => Lang::get('backend::lang.warnings.extension', ['name' => '<strong>'.$extension.'</strong>']),
                    'fixUrl' => 'https://wintercms.com/docs/v1.2/docs/setup/installation#minimum-system-requirements',
                ];
            }
        }

        foreach ($missingDependencies as $pluginCode => $plugin) {
            foreach ($plugin as $missingPluginCode) {
                $warnings[] = [
                    'message' => Lang::get('system::lang.updates.update_warnings_plugin_missing', [
                        'code' => '<strong>' . $missingPluginCode . '</strong>',
                        'parent_code' => '<strong>' . $pluginCode . '</strong>'
                    ]),
                ];
            }
        }

        return $warnings;
    }
}
