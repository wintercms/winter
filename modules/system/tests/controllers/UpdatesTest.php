<?php

namespace System\Tests\Controllers;

use Backend\Classes\WidgetManager;
use Backend\Models\User;
use Backend\Models\UserRole;
use Carbon\Carbon;
use System\Controllers\Updates;
use System\Models\Parameter;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;

/**
 * Covers the core build identifier that the Updates controller's step runner stores, and the
 * two pages that display it: the Updates page itself and the System Status dashboard widget.
 */
class UpdatesTest extends PluginTestCase
{
    protected User $updater;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        // Keep the update check offline: the retry window has not elapsed.
        Parameter::set(['system::update.retry' => Carbon::now()->addDay()->timestamp]);

        // registerReportWidgets() is gated behind runningInBackend(), which is false here.
        WidgetManager::instance()->registerReportWidgets(function ($manager) {
            $manager->registerReportWidget(\System\ReportWidgets\Status::class, [
                'label' => 'backend::lang.dashboard.status.widget_title_default',
                'context' => 'dashboard',
            ]);
        });

        Model::unguard();
        $role = UserRole::create([
            'name' => 'updater',
            'code' => 'updater',
            'permissions' => [
                'system.manage_updates' => 1,
                'backend.access_dashboard' => 1,
            ],
        ]);
        $this->updater = User::create([
            'first_name' => 'Updater',
            'last_name' => 'User',
            'login' => 'updater',
            'email' => 'updater@test.test',
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'role_id' => $role->id,
        ]);
        Model::reguard();

        $this->actingAs($this->updater);
    }

    protected function executeStep(array $post)
    {
        return $this->post('backend/system/updates', $post, [
            'X-WINTER-REQUEST-HANDLER' => 'onExecuteStep',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    protected function storedBuild(): ?string
    {
        self::setProtectedProperty(new Parameter(), 'cache', []);

        return Parameter::get('system::core.build');
    }

    public function testSetBuildStepStoresAVersionIdentifier(): void
    {
        $response = $this->executeStep(['code' => 'setBuild', 'build' => '1.2.14', 'hash' => 'abc123']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('1.2.14', $this->storedBuild());
    }

    public function testSetBuildStepRefusesAMalformedIdentifier(): void
    {
        $response = $this->executeStep(['code' => 'setBuild', 'build' => "1'.phpversion().'", 'hash' => 'abc123']);

        $this->assertNotEquals(200, $response->getStatusCode());
        $this->assertNull($this->storedBuild());
        $this->assertNull(Parameter::get('system::core.hash'));
    }

    /**
     * Renders the Updates page scoreboard. The full page cannot be requested over HTTP here --
     * the backend main menu needs settings items that are only registered when running in the
     * backend -- so the view file is rendered through the controller directly.
     */
    protected function renderUpdatesScoreboard(): string
    {
        $controller = new Updates();
        $controller->asExtension('ListController')->makeLists();

        return self::callProtectedMethod($controller, 'makeFileContents', [
            base_path('modules/system/controllers/updates/index.php'),
            [
                'projectId' => Parameter::get('system::project.id'),
                'projectName' => Parameter::get('system::project.name'),
                'projectOwner' => Parameter::get('system::project.owner'),
                'coreBuild' => Parameter::get('system::core.build'),
                'coreBuildModified' => Parameter::get('system::core.modified', false),
                'pluginsCount' => 0,
                'pluginsActiveCount' => 0,
                'warnings' => [],
            ],
        ]);
    }

    public function testUpdatesPageEscapesTheStoredParameters(): void
    {
        // Written straight to the parameter store, which is free-form, so the page cannot rely
        // on the step runner's check.
        Parameter::set([
            'system::core.build' => '1<b>build</b>',
            'system::project.id' => 'abc',
            'system::project.name' => '<b>name</b>',
            'system::project.owner' => '<b>owner</b>',
        ]);

        $content = $this->renderUpdatesScoreboard();

        $this->assertStringContainsString('1&lt;b&gt;build&lt;/b&gt;', $content);
        $this->assertStringContainsString('&lt;b&gt;name&lt;/b&gt;', $content);
        $this->assertStringContainsString('&lt;b&gt;owner&lt;/b&gt;', $content);
        $this->assertStringNotContainsString('<b>build</b>', $content);
        $this->assertStringNotContainsString('<b>name</b>', $content);
        $this->assertStringNotContainsString('<b>owner</b>', $content);
    }

    public function testUpdatesPageStillShowsAVersionBuild(): void
    {
        Parameter::set(['system::core.build' => '1.2.14']);

        $content = $this->renderUpdatesScoreboard();

        $this->assertStringContainsString('<p>1.2.14</p>', $content);
    }

    /**
     * The update step list is written into a `<script>` block as JSON, and it carries the
     * posted build identifier along with plugin and theme names supplied by the gateway.
     * Nothing in that block may be able to close the element.
     */
    protected function applyUpdates(string $build): string
    {
        $response = $this->post('backend/system/updates', [
            'hash' => 'abc123',
            'build' => $build,
        ], [
            'X-WINTER-REQUEST-HANDLER' => 'onApplyUpdates',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true)['result'];

        $this->assertSame(
            1,
            preg_match('/\$\.wn\.updateProcess\.execute\((\[.*\])\)/', $content, $matches),
            'the execute() call was not found in the rendered partial'
        );

        return $matches[1];
    }

    public function testExecutePartialCannotCloseTheScriptBlock(): void
    {
        $build = '1</script><script>alert(1)</script>';
        $json = $this->applyUpdates($build);

        // Nothing an HTML parser would read as a tag survives into the script block, and the
        // value is still carried intact for the step runner.
        $this->assertDoesNotMatchRegularExpression('/[<>]/', $json);
        $this->assertSame($build, collect(json_decode($json, true))->firstWhere('code', 'setBuild')['build']);
    }

    public function testExecutePartialStillCarriesAVersionBuild(): void
    {
        $json = $this->applyUpdates('1.2.14');

        $this->assertStringContainsString('"code":"setBuild"', $json);
        $this->assertSame('1.2.14', collect(json_decode($json, true))->firstWhere('code', 'setBuild')['build']);
    }

    /**
     * The System Status widget is on the default dashboard, which any backend user with
     * dashboard access loads.
     */
    public function testStatusWidgetEscapesTheStoredBuildIdentifier(): void
    {
        Parameter::set(['system::core.build' => '1<b>build</b>']);

        $response = $this->post('backend/backend/index', [], [
            'X-WINTER-REQUEST-HANDLER' => 'onInitReportContainer',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $widget = json_decode($response->getContent(), true)['#dashReportContainer'];

        $this->assertStringContainsString('1&lt;b&gt;build&lt;/b&gt;', $widget);
        $this->assertStringNotContainsString('<b>build</b>', $widget);
    }

    public function testStatusWidgetStillShowsAVersionBuild(): void
    {
        Parameter::set(['system::core.build' => '1.2.14']);

        $response = $this->post('backend/backend/index', [], [
            'X-WINTER-REQUEST-HANDLER' => 'onInitReportContainer',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $widget = json_decode($response->getContent(), true)['#dashReportContainer'];

        $this->assertStringContainsString('class="status-label primary">1.2.14</a>', $widget);
    }
}
