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
                'encryption' => $config->get('mail.encryption'),
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
        $this->smtp_encryption = array_get($mailers['smtp'], 'encryption');
    }

    public function getSendModeOptions()
    {
        return [
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
            case self::MODE_SMTP:
                $config->set('mail.mailers.smtp.host', $settings->smtp_address);
                $config->set('mail.mailers.smtp.port', $settings->smtp_port);
                if ($settings->smtp_authorization) {
                    $config->set('mail.mailers.smtp.username', $settings->smtp_user);
                    $config->set('mail.mailers.smtp.password', $settings->smtp_password);
                }
                else {
                    $config->set('mail.mailers.smtp.username', null);
                    $config->set('mail.mailers.smtp.password', null);
                }
                if ($settings->smtp_encryption) {
                    $config->set('mail.mailers.smtp.encryption', $settings->smtp_encryption);
                }
                else {
                    $config->set('mail.mailers.smtp.encryption', null);
                }
                break;

            case self::MODE_SENDMAIL:
                $config->set('mail.mailers.sendmail.path', $settings->sendmail_path);
                break;
        }
    }

    /**
     * @return array smtp_encryption options values
     */
    public function getSmtpEncryptionOptions()
    {
        return [
            '' => 'system::lang.mail.smtp_encryption_none',
            'tls' => 'system::lang.mail.smtp_encryption_tls',
            'ssl' => 'system::lang.mail.smtp_encryption_ssl',
        ];
    }

    /**
     * Filter fields callback.
     *
     * We use this to automatically set the SMTP port to the encryption type's corresponding port, if it was originally
     * using a default port.
     *
     * @param array $fields
     * @param string|null $context
     * @return void
     */
    public function filterFields($fields, $context = null)
    {
        if (in_array($fields->smtp_port->value ?? 25, [25, 465, 587])) {
            switch ($fields->smtp_encryption->value ?? '') {
                case 'tls':
                    $fields->smtp_port->value = 587;
                    break;
                case 'ssl':
                    $fields->smtp_port->value = 465;
                    break;
                case '':
                default:
                    $fields->smtp_port->value = 25;
                    break;
            }
        }
    }
}
