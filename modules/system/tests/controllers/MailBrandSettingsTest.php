<?php

namespace System\Tests\Controllers;

use Backend\Models\User;
use Backend\Models\UserRole;
use System\Models\MailBrandSetting;
use System\Models\MailPartial;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;

/**
 * MailBrandSetting declares no $rules, so nothing at the model layer stops a CSS variable
 * value from carrying markup into the compiled stylesheet. What stops it on the backend
 * form is Backend\FormWidgets\ColorPicker::getSaveValue(), which validates the posted
 * value against its hex pattern. Both the save handler and the live preview handler go
 * through Backend\Widgets\Form::getSaveData(), so both are covered -- and both need to
 * stay that way, because the strip_tags() in MailBrandSetting::renderCss() is the
 * backstop, not the primary control.
 */
class MailBrandSettingsTest extends PluginTestCase
{
    const BREAKOUT_VALUE = 'red; .xss::before { content: "</style><img src=x onerror=alert(1)>"; }';

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        MailBrandSetting::clearInternalCache();
        \Cache::flush();

        // registerBackendWidgets() and registerBackendSettings() are gated behind
        // runningInBackend(), which is false when the providers register under PHPUnit.
        // Without the first of these the `colorpicker` alias never resolves to a form
        // widget and getSaveValue() is never reached, which would make these tests pass
        // for the wrong reason.
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

        // The colorpicker's own rejection, not some unrelated failure. The debug
        // error page echoes the offending source, so assert on the outcome rather
        // than on the absence of the payload string in the response body.
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
     * The preview is assembled from the mail partials, which are stored in
     * system_mail_partials and edited from Settings > Mail > Mail partials. A partial is
     * free-form HTML and may legitimately contain a complete script element, so the
     * message cannot be carried to the browser in an HTML raw-text context: a closing tag
     * inside it would end that context and the remainder would be parsed as part of the
     * backend page. It is passed as a JSON string literal instead.
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
     * Invalidation counterpart: the partial still reaches the preview, it is just
     * carried as data rather than as markup on the backend page.
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
