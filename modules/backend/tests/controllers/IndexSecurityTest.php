<?php

namespace Backend\Tests\Controllers;

use Backend\Classes\WidgetManager;
use Backend\Models\User;
use Backend\Models\UserRole;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;

/**
 * Regression coverage for GHSA-cj62-7mph-rh54.
 *
 * The dashboard's `backend.access_dashboard` check used to live inside index(), but AJAX
 * handlers are dispatched before the page action runs, so index_onInitReportContainer()
 * rendered the report container -- including the System Status widget -- for users who had
 * been denied the dashboard. The check is now bound to page.beforeDisplay, which runs ahead
 * of both dispatch paths.
 */
class IndexSecurityTest extends PluginTestCase
{
    protected User $denied;
    protected User $allowed;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        // registerReportWidgets() is gated behind runningInBackend(), which is false here.
        WidgetManager::instance()->registerReportWidgets(function ($manager) {
            $manager->registerReportWidget(\System\ReportWidgets\Status::class, [
                'label' => 'backend::lang.dashboard.status.widget_title_default',
                'context' => 'dashboard',
            ]);
        });

        Model::unguard();
        $this->denied = $this->makeUser('denied', ['cms.manage_pages' => 1]);
        $this->allowed = $this->makeUser('allowed', ['backend.access_dashboard' => 1]);
        Model::reguard();
    }

    protected function makeUser(string $login, array $permissions): User
    {
        $role = UserRole::create([
            'name' => $login,
            'code' => $login,
            'permissions' => $permissions,
        ]);

        return User::create([
            'first_name' => ucfirst($login),
            'last_name' => 'User',
            'login' => $login,
            'email' => "{$login}@test.test",
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'role_id' => $role->id,
        ]);
    }

    protected function initReportContainer()
    {
        return $this->post('backend/backend/index', [], [
            'X-WINTER-REQUEST-HANDLER' => 'onInitReportContainer',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    /** The reported request. */
    public function testReportContainerHandlerIsDeniedWithoutDashboardAccess(): void
    {
        $this->actingAs($this->denied);

        $response = $this->initReportContainer();

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringNotContainsString('dashReportContainer', $response->getContent());
    }

    /** The page action's soft redirect is unchanged. */
    public function testPageActionStillRedirectsWithoutDashboardAccess(): void
    {
        $this->actingAs($this->denied);

        $response = $this->get('backend/backend');

        $this->assertEquals(302, $response->getStatusCode());
    }

    /** Nothing legitimate regressed: the container still renders for a permitted user. */
    public function testDashboardStillWorksWithDashboardAccess(): void
    {
        $this->actingAs($this->allowed);

        $response = $this->initReportContainer();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('dashReportContainer', $response->getContent());
    }
}
