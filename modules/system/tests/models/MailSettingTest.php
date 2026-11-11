<?php

namespace System\Tests\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use System\Behaviors\SettingsModel;
use System\Models\MailSetting;
use System\Tests\Bootstrap\PluginTestCase;
use System\Tests\Concerns\MigratesDatabaseOnce;

class MailSettingTest extends PluginTestCase
{
    use MigratesDatabaseOnce;

    public function setUp(): void
    {
        parent::setUp();

        SettingsModel::clearInternalCache();
    }

    public function tearDown(): void
    {
        Cache::forget(MailSetting::instance()->cacheKey);
        MailSetting::instance()->resetDefault();
        SettingsModel::clearInternalCache();

        parent::tearDown();
    }

    /**
     * Reads the stored settings the way they sit in the database, bypassing the model.
     */
    protected function getStoredValues(): array
    {
        return json_decode(DB::table('system_settings')->where('item', 'system_mail_settings')->value('value'), true);
    }

    /**
     * Drops every cached copy of the settings so that the next read comes from the database.
     */
    protected function reloadSettings(): void
    {
        Cache::forget(MailSetting::instance()->cacheKey);
        SettingsModel::clearInternalCache();
    }

    public function testEncryptedSettingIsEncryptedInTheDatabase()
    {
        MailSetting::set('smtp_password', 'hunter2');

        $stored = $this->getStoredValues();

        $this->assertNotSame('hunter2', $stored['smtp_password']);
        $this->assertStringNotContainsString('hunter2', json_encode($stored));
        $this->assertSame('hunter2', Crypt::decrypt($stored['smtp_password']));
    }

    public function testEncryptedSettingIsReadAsPlainValue()
    {
        MailSetting::set('smtp_password', 'hunter2');

        $this->assertSame('hunter2', MailSetting::get('smtp_password'));

        $this->reloadSettings();

        $this->assertSame('hunter2', MailSetting::get('smtp_password'));
        $this->assertSame('hunter2', MailSetting::instance()->smtp_password);
    }

    public function testOtherSettingsAreNotEncrypted()
    {
        MailSetting::set([
            'smtp_user' => 'mailer',
            'smtp_password' => 'hunter2',
        ]);

        $this->assertSame('mailer', $this->getStoredValues()['smtp_user']);
    }

    public function testSavingAgainDoesNotEncryptTwice()
    {
        MailSetting::set('smtp_password', 'hunter2');

        $this->reloadSettings();
        MailSetting::set('smtp_user', 'mailer');

        $this->reloadSettings();
        MailSetting::set('smtp_port', 2525);

        $this->assertSame('hunter2', Crypt::decrypt($this->getStoredValues()['smtp_password']));

        $this->reloadSettings();

        $this->assertSame('hunter2', MailSetting::get('smtp_password'));
    }

    public function testEmptyValueIsNotEncrypted()
    {
        MailSetting::set('smtp_password', '');

        $this->assertSame('', $this->getStoredValues()['smtp_password']);

        $this->reloadSettings();

        $this->assertSame('', MailSetting::get('smtp_password'));
    }

    public function testPlainTextValueStoredBeforeEncryptionIsStillReadable()
    {
        MailSetting::set('smtp_user', 'mailer');

        $values = $this->getStoredValues();
        $values['smtp_password'] = 'legacy-secret';
        DB::table('system_settings')
            ->where('item', 'system_mail_settings')
            ->update(['value' => json_encode($values)]);

        $this->reloadSettings();

        $this->assertSame('legacy-secret', MailSetting::get('smtp_password'));

        // It gets encrypted the next time the settings are saved
        MailSetting::set('smtp_user', 'other');

        $this->assertSame('legacy-secret', Crypt::decrypt($this->getStoredValues()['smtp_password']));
    }
}
