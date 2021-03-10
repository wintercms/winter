<?php namespace System\Models;

use Model;

/**
 * System log settings
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class LogSetting extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    /**
     * @var array Behaviors implemented by this model.
     */
    public $implement = [
        \System\Behaviors\SettingsModel::class
    ];

    /**
     * @var string Unique code
     */
    public $settingsCode = 'system_log_settings';

    /**
     * @var mixed Settings form field defitions
     */
    public $settingsFields = 'fields.yaml';

    /**
     * Validation rules
     */
    public $rules = [];

    public static function filterSettingItems($manager)
    {
        if (!self::isConfigured()) {
            $manager->removeSettingItem('Winter.System', 'request_logs');
            $manager->removeSettingItem('Winter.Cms', 'theme_logs');
            return;
        }

        if (!self::get('log_events')) {
            $manager->removeSettingItem('Winter.System', 'event_logs');
        }

        if (!self::get('log_requests')) {
            $manager->removeSettingItem('Winter.System', 'request_logs');
        }

        if (!self::get('log_theme')) {
            $manager->removeSettingItem('Winter.Cms', 'theme_logs');
        }
    }

    /**
     * Initialize the seed data for this model. This only executes when the
     * model is first created or reset to default.
     * @return void
     */
    public function initSettingsData()
    {
        $this->log_events = true;
        $this->log_requests = false;
        $this->log_theme = false;
    }
}
