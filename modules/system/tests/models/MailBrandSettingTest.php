<?php

namespace System\Tests\Models;

use System\Models\MailBrandSetting;
use System\Tests\Bootstrap\PluginTestCase;

class MailBrandSettingTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        \System\Behaviors\SettingsModel::clearInternalCache();
    }

    public function tearDown(): void
    {
        MailBrandSetting::instance()->resetDefault();
        \System\Behaviors\SettingsModel::clearInternalCache();

        parent::tearDown();
    }

    /**
     * Regression for GHSA-58fp-mcx6-7qf9. MailBrandSetting takes no raw user CSS
     * string but flows user input through Less_Parser::ModifyVars(), whose
     * serializeVars() helper concatenates raw values into LESS source without
     * escaping. A CSS variable value of `red; @import (inline) "/path/to/secret"`
     * therefore reaches the parser as a working @import directive. The
     * SetImportDirs deny-all gate in compileCss() must close this vector
     * regardless of form-field validation strictness.
     */
    public function testCompileCssBlocksModifyVarsImportInjection()
    {
        $tmpSecret = tempnam(sys_get_temp_dir(), 'mailbrandsetting-leak-canary-');
        file_put_contents($tmpSecret, "APP_KEY=do-not-leak-via-modifyvars\n");

        try {
            // Bypass form-field validation by writing the malicious value directly
            // onto the model. This simulates a model-layer bypass or a future
            // weakening of the field validator.
            $setting = MailBrandSetting::instance();
            $setting->body_bg = 'red; @import (inline) "' . $tmpSecret . '"';
            $setting->save();

            \System\Behaviors\SettingsModel::clearInternalCache();

            $css = MailBrandSetting::compileCss();

            $this->assertStringNotContainsString('APP_KEY', $css);
            $this->assertStringNotContainsString('do-not-leak-via-modifyvars', $css);
        } finally {
            @unlink($tmpSecret);
        }
    }
}
