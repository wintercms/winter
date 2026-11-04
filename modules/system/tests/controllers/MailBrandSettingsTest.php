<?php

namespace System\Tests\Controllers;

use Backend\Models\User;
use Backend\Models\UserRole;
use System\Models\MailBrandSetting;
use System\Models\MailPartial;
use System\Tests\Bootstrap\PluginTestCase;
use System\Tests\Concerns\MigratesDatabaseOnce;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;

/**
 * MailBrandSetting declares no $rules; what validates a posted colour is ColorPicker::getSaveValue(). Both the save
 * and the live preview handler reach it through Form::getSaveData(), and renderCss()'s strip_tags() is only a backstop.
 */
class MailBrandSettingsTest extends PluginTestCase
{
    use MigratesDatabaseOnce;

    const BREAKOUT_VALUE = 'red; .xss::before { content: "</style><img src=x onerror=alert(1)>"; }';

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        MailBrandSetting::clearInternalCache();
        \Cache::flush();

        // Both are gated behind runningInBackend(), false under PHPUnit. Without the first the `colorpicker` alias
        // never resolves and getSaveValue() is never reached, which would make these tests pass for the wrong reason.
        $this->invokeProviderMethod(new \Backend\ServiceProvider($this->app), 'registerBackendWidgets');
        $this->invokeProviderMethod(new \System\ServiceProvider($this->app), 'registerBackendSettings');
    }

    public function tearDown(): void
    {
        MailBrandSetting::instance()->resetDefault();
        MailBrandSetting::clearInternalCache();
        \Cache::flush();

        parent::tearDown();
    }

    protected function invokeProviderMethod($provider, string $method): void
    {
        $reflection = new \ReflectionMethod($provider, $method);
        $reflection->setAccessible(true);
        $reflection->invoke($provider);
    }

    protected function actingAsMailManager(string $login): void
    {
        Model::unguard();
        $role = UserRole::create([
            'name' => $login,
            'code' => $login,
            'permissions' => ['system.manage_mail_templates' => 1],
        ]);
        $user = User::create([
            'first_name' => ucfirst($login),
            'last_name' => 'User',
            'login' => $login,
            'email' => "{$login}@test.test",
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'role_id' => $role->id,
        ]);
        Model::reguard();

        $this->actingAs($user);
    }

    protected function post_(string $handler, string $bodyBg)
    {
        return $this->post('backend/system/mailbrandsettings', [
            'MailBrandSetting' => ['body_bg' => $bodyBg],
        ], [
            'X-WINTER-REQUEST-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    public function testSaveRejectsAColourValueCarryingMarkup()
    {
        $this->actingAsMailManager('mailbrand_save');

        $this->post_('onSave', self::BREAKOUT_VALUE);

        MailBrandSetting::clearInternalCache();
        $this->assertEquals(MailBrandSetting::BODY_BG, MailBrandSetting::get('body_bg'));
    }

    public function testPreviewRejectsAColourValueCarryingMarkup()
    {
        $this->actingAsMailManager('mailbrand_preview');

        $response = $this->post_('onUpdateSampleMessage', self::BREAKOUT_VALUE);

        // Asserted on the outcome, not on the payload's absence from the body: the debug error page echoes its source.
        $this->assertNotEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('color value supplied is invalid', $response->getContent());

        MailBrandSetting::clearInternalCache();
        $this->assertEquals(MailBrandSetting::BODY_BG, MailBrandSetting::get('body_bg'));
    }

    public function testALegitimateColourStillSavesAndPreviews()
    {
        $this->actingAsMailManager('mailbrand_legit');

        $this->post_('onSave', '#abcdef');

        MailBrandSetting::clearInternalCache();
        $this->assertEquals('#abcdef', MailBrandSetting::get('body_bg'));

        $response = $this->post_('onUpdateSampleMessage', '#abcdef');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(
            'background-color: #abcdef',
            json_decode($response->getContent(), true)['previewHtml']
        );
    }

    /**
     * A mail partial is free-form HTML and may legitimately contain a complete script element, so the preview cannot
     * be carried to the browser in a raw-text context that a closing tag inside it would end.
     */
    public function testAMailPartialCannotBreakOutOfThePreviewScriptBlock()
    {
        $this->actingAsMailManager('mailbrand_partial');

        $this->storePartial('button', '<script>/* mail */</script><img src=x onerror=alert(document.domain)>');

        $body = $this->get('backend/system/mailbrandsettings')->getContent();

        $dom = new \DOMDocument();
        @$dom->loadHTML($body);

        $live = [];
        foreach ($dom->getElementsByTagName('img') as $img) {
            if ($img->hasAttribute('onerror')) {
                $live[] = $dom->saveHTML($img);
            }
        }

        $this->assertSame([], $live, 'A mail partial produced an element of its own on the backend page.');
    }

    /**
     * Invalidation case: the partial still reaches the preview, carried as data rather than as markup.
     */
    public function testAMailPartialStillReachesThePreview()
    {
        $this->actingAsMailManager('mailbrand_partial_legit');

        $this->storePartial('button', '<span class="canary-partial">Rendered</span>');

        $body = $this->get('backend/system/mailbrandsettings')->getContent();

        $this->assertStringContainsString('canary-partial', $body);
    }

    protected function storePartial(string $code, string $html): void
    {
        Model::unguard();
        MailPartial::create([
            'name' => $code,
            'code' => $code,
            'content_html' => $html,
            'content_text' => $html,
            'is_custom' => true,
        ]);
        Model::reguard();
    }
}
