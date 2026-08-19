<?php

namespace Backend\Tests\Behaviors;

use Backend\Classes\Controller;
use Backend\Models\ExportModel;
use Backend\Models\ImportModel;
use Backend\Tests\Fixtures\Models\UserFixture;
use Symfony\Component\HttpKernel\Exception\HttpException;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Exception\ApplicationException;

/**
 * Regression coverage for GHSA-fm29-4mq3-phg6.
 *
 * `ImportExportController` offers granular per-operation access control through the
 * `import[permissions]` / `export[permissions]` config keys, enforced by
 * `userHasAccess()`. That gate was previously applied to the `import()` / `export()`
 * page actions only. Because `Controller::execAjaxHandlers()` dispatches and returns
 * before `execPageAction()`, and the behavior binds its widgets in the constructor,
 * every handler that performed the actual privileged work was reachable without it.
 *
 * The gate must therefore be enforced on each handler and action that performs, or
 * exposes, an import or export.
 *
 * @see modules/backend/behaviors/ImportExportController.php
 */
class GatedExportModel extends ExportModel
{
    public $table = 'backend_users';

    public static array $exportCalls = [];

    public function export($columns, $options = [])
    {
        static::$exportCalls[] = $columns;

        return parent::export($columns, $options);
    }

    public function exportData($columns, $sessionKey = null)
    {
        return [
            ['secret' => 'protected-record-1'],
            ['secret' => 'protected-record-2'],
        ];
    }
}

class GatedImportModel extends ImportModel
{
    public $table = 'backend_users';
    public $rules = [];

    public static array $importCalls = [];

    public function import($matches, $options = [])
    {
        static::$importCalls[] = $matches;
    }

    public function importData($results, $sessionKey = null)
    {
    }
}

/**
 * Declares granular permissions finer than its own `$requiredPermissions` -- the only
 * configuration in which this gate does anything.
 */
class GatedImportExportController extends Controller
{
    public $implement = [\Backend\Behaviors\ImportExportController::class];

    public $requiredPermissions = ['acme.view_records'];

    public $importExportConfig = [
        'import' => [
            'title' => 'Import records',
            'modelClass' => GatedImportModel::class,
            'permissions' => ['acme.manage_imports'],
            'list' => ['columns' => ['secret' => 'Secret']],
        ],
        'export' => [
            'title' => 'Export records',
            'modelClass' => GatedExportModel::class,
            'permissions' => ['acme.manage_exports'],
            'list' => ['columns' => ['secret' => 'Secret']],
        ],
    ];
}

/**
 * Declares no granular permissions. `userHasAccess()` is default-permissive, so this
 * controller must keep working for any user who can reach it -- the guards must not
 * become a breaking change for the majority of consumers.
 */
class UngatedImportExportController extends Controller
{
    public $implement = [\Backend\Behaviors\ImportExportController::class];

    public $requiredPermissions = ['acme.view_records'];

    public $importExportConfig = [
        'import' => [
            'title' => 'Import records',
            'modelClass' => GatedImportModel::class,
            'list' => ['columns' => ['secret' => 'Secret']],
        ],
        'export' => [
            'title' => 'Export records',
            'modelClass' => GatedExportModel::class,
            'list' => ['columns' => ['secret' => 'Secret']],
        ],
    ];
}

class ImportExportControllerPermissionsTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        GatedImportModel::$importCalls = [];
        GatedExportModel::$exportCalls = [];
    }

    /**
     * Acts as a user holding the coarse controller permission but explicitly denied the
     * granular import/export permissions -- the attacker in the advisory.
     */
    protected function actAsDeniedUser(): void
    {
        $this->actingAs(
            (new UserFixture)
                ->withPermission('acme.view_records', true)
                ->withPermission('acme.manage_imports', false)
                ->withPermission('acme.manage_exports', false)
        );
    }

    protected function actAsPermittedUser(): void
    {
        $this->actingAs(
            (new UserFixture)
                ->withPermission('acme.view_records', true)
                ->withPermission('acme.manage_imports', true)
                ->withPermission('acme.manage_exports', true)
        );
    }

    /**
     * Handlers read their input with post(), which returns defaults unless the request
     * method is genuinely POST -- so a real POST request must be in play.
     */
    protected function makeController(string $class, array $params = []): Controller
    {
        $request = \Illuminate\Http\Request::createFromBase(
            \Symfony\Component\HttpFoundation\Request::create(
                'http://localhost/backend/acme/records',
                'POST',
                $params
            )
        );
        $this->app->instance('request', $request);
        \Illuminate\Support\Facades\Request::swap($request);

        return new $class;
    }

    protected function statusOf(callable $fn): ?int
    {
        try {
            $fn();
        } catch (HttpException $ex) {
            return $ex->getStatusCode();
        } catch (\Throwable $ex) {
            // Anything else means the guard was passed and the method got on with its
            // work; surface it as "not blocked".
            return null;
        }

        return null;
    }

    public static function guardedCallProvider(): array
    {
        return [
            'onImport' => ['onImport', ['column_match' => [0 => ['file_column' => 'secret', 'db_column' => 'secret']]]],
            'onImportLoadForm' => ['onImportLoadForm', []],
            'onImportLoadColumnSampleForm' => ['onImportLoadColumnSampleForm', ['file_column_id' => 0]],
            'onExport' => ['onExport', ['export_columns' => ['secret'], 'visible_columns' => ['secret' => 1]]],
            'onExportLoadForm' => ['onExportLoadForm', []],
        ];
    }

    /**
     * @dataProvider guardedCallProvider
     */
    public function testHandlersAreDeniedWithoutTheGranularPermission(string $method, array $params)
    {
        $this->actAsDeniedUser();
        $controller = $this->makeController(GatedImportExportController::class, $params);

        $status = $this->statusOf(fn () => $controller->$method());

        $this->assertSame(403, $status, "{$method}() must abort(403) for a user without the granular permission");
    }

    public function testDownloadIsDeniedWithoutTheExportPermission()
    {
        $this->actAsDeniedUser();
        $controller = $this->makeController(GatedImportExportController::class);

        $status = $this->statusOf(fn () => $controller->download('oc0000000000000', 'export.csv'));

        $this->assertSame(403, $status, 'download() must abort(403) without the export permission');
    }

    public function testPageActionsRemainDenied()
    {
        $this->actAsDeniedUser();
        $controller = $this->makeController(GatedImportExportController::class);

        $this->assertSame(403, $this->statusOf(fn () => $controller->import()));
        $this->assertSame(403, $this->statusOf(fn () => $controller->export()));
    }

    /**
     * The point of the fix: the privileged sinks are never reached.
     */
    public function testPrivilegedSinksAreNeverReachedByADeniedUser()
    {
        $this->actAsDeniedUser();

        $importer = $this->makeController(GatedImportExportController::class, [
            'column_match' => [0 => ['file_column' => 'secret', 'db_column' => 'secret']],
            'first_row_titles' => 1,
        ]);
        $this->statusOf(fn () => $importer->onImport());

        $exporter = $this->makeController(GatedImportExportController::class, [
            'export_columns' => ['secret'],
            'visible_columns' => ['secret' => 1],
        ]);
        $this->statusOf(fn () => $exporter->onExport());

        $this->assertSame([], GatedImportModel::$importCalls, 'import() sink was reached despite the gate');
        $this->assertSame([], GatedExportModel::$exportCalls, 'export() sink was reached despite the gate');
        $this->assertNull($exporter->vars['fileUrl'] ?? null, 'A download reference was produced despite the gate');
    }

    //
    // Positive controls -- the guards must not deny users who DO hold the permission
    //

    public function testPermittedUserCanStillImport()
    {
        $this->actAsPermittedUser();
        $controller = $this->makeController(GatedImportExportController::class, [
            'column_match' => [0 => ['file_column' => 'secret', 'db_column' => 'secret']],
            'first_row_titles' => 1,
        ]);

        $controller->onImport();

        $this->assertCount(1, GatedImportModel::$importCalls, 'A permitted user must still be able to import');
    }

    public function testPermittedUserCanStillExport()
    {
        $this->actAsPermittedUser();
        $controller = $this->makeController(GatedImportExportController::class, [
            'export_columns' => ['secret'],
            'visible_columns' => ['secret' => 1],
        ]);

        $controller->onExport();

        $this->assertNotNull($controller->vars['fileUrl'] ?? null, 'A permitted user must still get a download reference');
        $this->assertCount(1, GatedExportModel::$exportCalls, 'A permitted user must still reach the export sink');
    }

    public function testPermittedUserPassesTheLoadFormGuards()
    {
        $this->actAsPermittedUser();
        $controller = $this->makeController(GatedImportExportController::class, []);

        $this->assertNull($this->statusOf(fn () => $controller->onImportLoadForm()), 'onImportLoadForm must not 403');
        $this->assertNull($this->statusOf(fn () => $controller->onExportLoadForm()), 'onExportLoadForm must not 403');
    }

    /**
     * onImportLoadColumnSampleForm reaches its own validation once past the guard, which
     * is proof the guard let it through rather than short-circuiting.
     */
    public function testPermittedUserPassesTheColumnSampleGuard()
    {
        $this->actAsPermittedUser();
        $controller = $this->makeController(GatedImportExportController::class, []);

        $this->expectException(ApplicationException::class);
        $controller->onImportLoadColumnSampleForm();
    }

    //
    // Default-permissive control -- controllers with no granular config are unaffected
    //

    public function testControllerWithoutGranularPermissionsIsUnaffected()
    {
        $this->actAsDeniedUser();

        $controller = $this->makeController(UngatedImportExportController::class, [
            'column_match' => [0 => ['file_column' => 'secret', 'db_column' => 'secret']],
            'first_row_titles' => 1,
        ]);

        $this->assertTrue($controller->userHasAccess('import'), 'No config means default-permissive');
        $this->assertTrue($controller->userHasAccess('export'), 'No config means default-permissive');

        $controller->onImport();

        $this->assertCount(
            1,
            GatedImportModel::$importCalls,
            'Adding the guards must not break controllers that never configured granular permissions'
        );
    }
}
