<?php

namespace Backend\Tests\Behaviors;

use Backend\Classes\Controller;
use Backend\Models\ExportModel;
use Backend\Models\User;
use Cms\Classes\Theme;
use Cms\Models\ThemeExport;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\Event;
use Winter\Storm\Support\Facades\File;

/**
 * GHSA-fm29-4mq3-phg6 gave ImportExportController's download action the export[permissions] gate,
 * so a user without export access on a controller is refused there. It says nothing about whose
 * artefact a name belongs to: the files produced by every controller using the behaviour, and by
 * the theme archive flows, sat in one directory and were addressed by name alone. A user with
 * export access anywhere could therefore read any of them through their own controller.
 *
 * Artefacts now live in a directory belonging to the backend user that produced them, and each
 * reader resolves a name only inside its own.
 */
class OwnedExportModel extends ExportModel
{
    public $table = 'backend_users';

    public static string $marker = 'victim-secret-row';

    public function exportData($columns, $sessionKey = null)
    {
        return [['secret' => static::$marker]];
    }
}

class VictimExportController extends Controller
{
    public $implement = [\Backend\Behaviors\ImportExportController::class];

    public $requiredPermissions = ['acme.view_secret'];

    public $importExportConfig = [
        'export' => [
            'title' => 'Export secret records',
            'modelClass' => OwnedExportModel::class,
            'permissions' => ['acme.export_secret'],
            'list' => ['columns' => ['secret' => 'Secret']],
        ],
    ];
}

class AttackerExportController extends Controller
{
    public $implement = [\Backend\Behaviors\ImportExportController::class];

    public $requiredPermissions = ['acme.view_public'];

    public $importExportConfig = [
        'export' => [
            'title' => 'Export public records',
            'modelClass' => OwnedExportModel::class,
            'permissions' => ['acme.export_public'],
            'list' => ['columns' => ['secret' => 'Secret']],
        ],
    ];
}

class ImportExportControllerOwnershipTest extends PluginTestCase
{
    protected User $victim;
    protected User $attacker;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('app.debug', true);
        Config::set('cms.themesPath', '/modules/cms/tests/fixtures/themes');

        Event::flush('cms.theme.getActiveTheme');
        Theme::resetCache();

        Model::unguard();
        $this->victim = $this->makeUser('victim', [
            'acme.view_secret' => 1,
            'acme.export_secret' => 1,
        ]);
        $this->attacker = $this->makeUser('attacker', [
            'acme.view_public' => 1,
            'acme.export_public' => 1,
            'acme.view_secret' => -1,
            'acme.export_secret' => -1,
        ]);
        Model::reguard();
    }

    public function tearDown(): void
    {
        File::deleteDirectory(temp_path('exports'));

        parent::tearDown();
    }

    protected function makeUser(string $login, array $permissions): User
    {
        return User::create([
            'first_name' => ucfirst($login),
            'last_name' => 'User',
            'login' => $login,
            'email' => "{$login}@test.test",
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Handlers read their input with post(), which returns defaults unless the request method is
     * genuinely POST.
     */
    protected function makeController(string $class, array $params = []): Controller
    {
        $request = Request::createFromBase(
            \Symfony\Component\HttpFoundation\Request::create('http://localhost/backend/acme/records', 'POST', $params)
        );
        $this->app->instance('request', $request);
        \Illuminate\Support\Facades\Request::swap($request);

        return new $class;
    }

    /**
     * Produces an artefact through the real export flow, as the victim, and returns its reference.
     */
    protected function exportAsVictim(): string
    {
        $this->actingAs($this->victim);

        $controller = $this->makeController(VictimExportController::class, [
            'export_columns' => ['secret'],
            'visible_columns' => ['secret' => 1],
        ]);
        $controller->onExport();

        $fileUrl = $controller->vars['fileUrl'] ?? null;
        $this->assertNotNull($fileUrl, 'Precondition: the victim\'s export produced a download reference');

        $reference = basename(dirname($fileUrl));
        $this->assertMatchesRegularExpression('/^oc[0-9a-z]+$/i', $reference);

        return $reference;
    }

    protected function contentsOf($response): string
    {
        return file_get_contents($response->getFile()->getPathname());
    }

    /**
     * The validity control: the attacker genuinely has no access to the victim's area, so the gate
     * GHSA-fm29 added is doing its job and this is not a regression of it.
     */
    public function testTheAttackerIsRefusedOnTheVictimsController(): void
    {
        $reference = $this->exportAsVictim();

        $this->actingAs($this->attacker);
        $controller = $this->makeController(VictimExportController::class);

        try {
            $controller->download($reference, 'export.csv');
            $this->fail('download() must abort(403) on a controller the attacker has no export access to');
        } catch (HttpException $ex) {
            $this->assertEquals(403, $ex->getStatusCode());
        }
    }

    /** The reported attack. */
    public function testTheAttackerCannotReadTheVictimsExportThroughTheirOwnController(): void
    {
        $reference = $this->exportAsVictim();

        $this->actingAs($this->attacker);
        $controller = $this->makeController(AttackerExportController::class);

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('File not found');

        $controller->download($reference, 'export.csv');
    }

    /** Nothing legitimate regressed: the owner still gets their own file, with its contents. */
    public function testTheVictimCanStillDownloadTheirOwnExport(): void
    {
        $reference = $this->exportAsVictim();

        $controller = $this->makeController(VictimExportController::class);
        $response = $controller->download($reference, 'export.csv');

        $this->assertStringContainsString(OwnedExportModel::$marker, $this->contentsOf($response));
    }

    /**
     * The cross-subsystem arm: a theme archive is written to the same directory, and was readable
     * through an unrelated plugin's export controller by a user holding no cms.* permission at all.
     */
    public function testAThemeArchiveCannotBeReadThroughAnExportController(): void
    {
        $reference = $this->exportThemeAsVictim();

        $this->actingAs($this->attacker);
        $controller = $this->makeController(AttackerExportController::class);

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('File not found');

        $controller->download($reference, 'theme.zip');
    }

    public function testAThemeArchiveCannotBeReadByAnotherUser(): void
    {
        $reference = $this->exportThemeAsVictim();

        $this->actingAs($this->attacker);

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('File not found');

        ThemeExport::download($reference, 'theme.zip');
    }

    public function testTheThemeArchiveOwnerCanStillDownloadIt(): void
    {
        $reference = $this->exportThemeAsVictim();

        $response = ThemeExport::download($reference, 'theme.zip');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringStartsWith("PK\x03\x04", $response->getContent());
    }

    protected function exportThemeAsVictim(): string
    {
        $this->actingAs($this->victim);

        $reference = (new ThemeExport)->export(Theme::load('themedata'), ['folders' => []]);

        $this->assertMatchesRegularExpression('/^oc[0-9a-z]+$/i', $reference);

        return $reference;
    }
}
