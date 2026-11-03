<?php namespace System\Models;

use App;
use Model;

/**
 * Mail settings
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MailSetting extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    const MODE_FAILOVER  = 'failover';
    const MODE_LOG       = 'log';
    const MODE_MAIL      = 'mail';
    const MODE_SENDMAIL  = 'sendmail';
    const MODE_SMTP      = 'smtp';

    /**
     * @var array Behaviors implemented by this model.
     */
    public $implement = [
        \System\Behaviors\SettingsModel::class
    ];

    /**
     * @var string Unique code
     */
    public $settingsCode = 'system_mail_settings';

    /**
     * @var mixed Settings form field defitions
     */
    public $settingsFields = 'fields.yaml';

    /*
     * Validation rules
     */
    public $rules = [
        'failover_mailers' => 'required_if:send_mode,'.self::MODE_FAILOVER,
        'sender_name'  => 'required',
        'sender_email' => 'required|email'
    ];

    /**
     * Initialize the seed data for this model. This only executes when the
     * model is first created or reset to default.
     * @return void
     */
    public function initSettingsData()
    {
        $config = App::make('config');
        $mailers = $config->get('mail.mailers', [
            'sendmail' => ['path' => $config->get('mail.sendmail', '/usr/sbin/sendmail')],
            'smtp' => [
                'host' => $config->get('mail.host'),
                'port' => $config->get('mail.port', 587),
                'username' => $config->get('mail.username'),
                'password' => $config->get('mail.password'),
            ],
        ]);

        $this->send_mode = $config->get('mail.default', static::MODE_MAIL);
        $this->sender_name = $config->get('mail.from.name', 'Your Site');
        $this->sender_email = $config->get('mail.from.address', 'admin@example.com');
        $this->sendmail_path = array_get($mailers['sendmail'], 'path', '/usr/sbin/sendmail');
        $this->smtp_address = array_get($mailers['smtp'], 'host');
        $this->smtp_port = array_get($mailers['smtp'], 'port', 587);
        $this->smtp_user = array_get($mailers['smtp'], 'username');
        $this->smtp_password = array_get($mailers['smtp'], 'password');
        $this->smtp_authorization = !!strlen($this->smtp_user);
        $this->failover_mailers = implode(',', $config->get('mail.mailers.failover.mailers', []));
    }

    public function getFailoverMailersOptions()
    {
        return collect(App::make('config')->get('mail.mailers'))->except('failover')->keys()->all();
    }

    public function getSendModeOptions()
    {
        return [
            static::MODE_FAILOVER => 'system::lang.mail.failover',
            static::MODE_LOG      => 'system::lang.mail.log_file',
            static::MODE_MAIL     => 'system::lang.mail.php_mail',
            static::MODE_SENDMAIL => 'system::lang.mail.sendmail',
            static::MODE_SMTP     => 'system::lang.mail.smtp',
        ];
    }

    public static function applyConfigValues()
    {
        $config = App::make('config');
        $settings = self::instance();
        $config->set('mail.default', $settings->send_mode);
        $config->set('mail.from.name', $settings->sender_name);
        $config->set('mail.from.address', $settings->sender_email);

        switch ($settings->send_mode) {
            case self::MODE_FAILOVER:
                $config->set('mail.mailers.failover.mailers', explode(',', $settings->failover_mailers));
                break;

            case self::MODE_SMTP:
                $config->set('mail.mailers.smtp.host', $settings->smtp_address);
                $config->set('mail.mailers.smtp.port', $settings->smtp_port);
                $config->set('mail.mailers.smtp.encryption', $settings->smtp_port === 465 ? 'tls' : null);
                if ($settings->smtp_authorization) {
                    $config->set('mail.mailers.smtp.username', $settings->smtp_user);
                    $config->set('mail.mailers.smtp.password', $settings->smtp_password);
                }
                else {
                    $config->set('mail.mailers.smtp.username', null);
                    $config->set('mail.mailers.smtp.password', null);
                }
                break;

            case self::MODE_SENDMAIL:
                $config->set('mail.mailers.sendmail.path', $settings->sendmail_path);
                break;
        }
    }

    /**
     * Filter fields callback.
     *
     * We use this to show smtp credential fields only for smtp mode and when smtp authorization is required.
     *
     * @param array $fields
     * @param string|null $context
     * @return void
     */
    public function filterFields($fields, $context = null)
    {
        $hideAuth = $fields->send_mode->value !== 'smtp' || !$fields->smtp_authorization->value;

        if (isset($fields->smtp_user)) {
            $fields->smtp_user->hidden = $hideAuth;
        }
        if (isset($fields->smtp_password)) {
            $fields->smtp_password->hidden = $hideAuth;
        }
    }
}
