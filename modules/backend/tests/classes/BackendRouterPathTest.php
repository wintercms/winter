<?php

namespace Backend\Tests\Classes;

use Backend\Classes\BackendController;
use Backend\Tests\Fixtures\Models\UserFixture;
use RouterProbe\Sample\Controllers\Probe;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Str;

/**
 * Path safety of the backend controller router.
 *
 * Every segment that becomes part of a class name must be a plain PHP identifier.
 */
class BackendRouterPathTest extends PluginTestCase
{
    /**
     * @var string[] Directories to remove after each test.
     */
    protected $tempPaths = [];

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');

        // Route plugin lookups at the fixture plugin, which carries a controller that only
        // findController()'s case-insensitive lookup can resolve.
        $this->app->setPluginsPath(base_path('modules/backend/tests/fixtures/plugins'));
    }

    public function tearDown(): void
    {
        foreach ($this->tempPaths as $path) {
            File::deleteDirectory($path);
        }

        $this->tempPaths = [];

        parent::tearDown();
    }

    //
    // Helpers
    //

    /**
     * Writes a `.php` canary under `storage/`, outside either controller root, and returns the
     * URL segment that reaches it from $controllerDirectory -- joined with $separator -- plus
     * the marker path the canary writes when it executes.
     *
     * @return array{0: string, 1: string}
     */
    protected function seedCanary(string $separator, string $controllerDirectory): array
    {
        $name = 'lfi' . bin2hex(random_bytes(4));
        $directory = base_path('storage/framework/cache/' . $name);

        File::makeDirectory($directory, 0777, true, true);
        $this->tempPaths[] = $directory;

        File::put(
            $directory . '/canary.php',
            '<?php file_put_contents(__DIR__ . \'/included\', \'included\');' . PHP_EOL
        );

        // One `..` for each directory between the project root and the controller directory,
        // so the path lands on the project root before descending into storage/. Normalised
        // first because plugins_path() returns native separators and base_path() does not.
        $relative = Str::after(
            File::normalizePath($controllerDirectory),
            File::normalizePath(base_path())
        );
        $depth = count(explode('/', trim($relative, '/')));

        $segment = implode($separator, array_merge(
            array_fill(0, $depth, '..'),
            ['storage', 'framework', 'cache', $name, 'canary']
        ));

        return [$segment, $directory . '/included'];
    }

    /**
     * Resolves a backend URL the way `BackendController::run()` does, without dispatching.
     */
    protected function requestController(string $url)
    {
        $controller = new BackendController();
        $method = new \ReflectionMethod($controller, 'getRequestedController');
        $method->setAccessible(true);

        return $method->invoke($controller, $url);
    }

    //
    // Blocked: no URL may name a file outside the controller root
    //

    /**
     * The reported request, start to finish and with no session: `%5C` is decoded into a
     * backslash by the time the router sees the URL, and `segmentizeUrl()` keeps it inside
     * one segment.
     */
    public function testEncodedBackslashTraversalIncludesNothing()
    {
        [$segment, $marker] = $this->seedCanary('%5C', base_path('modules/backend/controllers'));

        $response = $this->get('backend/backend/' . $segment);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFileNotExists($marker, 'An unauthenticated request must not execute a file on disk');
    }

    /**
     * The same URL with literal backslashes. Symfony refuses to build such a URI through
     * `Request::create()`, which the test HTTP client uses, so this covers the router itself --
     * a real web server passes the backslash through, as the report demonstrates.
     */
    public function testLiteralBackslashTraversalIncludesNothing()
    {
        [$segment, $marker] = $this->seedCanary('\\', base_path('modules/backend/controllers'));

        $this->assertNull($this->requestController('backend/' . $segment));
        $this->assertFileNotExists($marker, 'A backslash in a URL segment must not reach the filesystem');
    }

    /**
     * A plugin URL carries three class-name segments rather than two, and the third one is
     * where a plugin controller name goes.
     */
    public function testTraversalInAPluginControllerSegmentIncludesNothing()
    {
        [$segment, $marker] = $this->seedCanary('%5C', plugins_path('routerprobe/sample/controllers'));

        $response = $this->get('backend/routerprobe/sample/' . $segment);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFileNotExists($marker, 'An unauthenticated request must not execute a file on disk');
    }

    /**
     * `..` needs no backslash at all: as the module segment it steps the path one level out of
     * `modules/` by itself, landing on `<project root>/controllers/<segment>.php`. That is why
     * a dot is rejected alongside the separator.
     */
    public function testDotModuleSegmentEscapesTheModuleRoot()
    {
        $name = 'lfi' . bin2hex(random_bytes(4));
        $directory = base_path('controllers');
        $canary = $directory . '/' . $name . '.php';
        $marker = $directory . '/' . $name;

        // Only the file is ours; the directory name is fixed by the path findController()
        // builds, so it is removed again only if this test created it.
        $createdDirectory = !File::isDirectory($directory);
        File::makeDirectory($directory, 0777, true, true);

        try {
            File::put($canary, '<?php file_put_contents(\'' . $marker . '\', \'included\');' . PHP_EOL);

            $this->assertNull($this->requestController('../' . $name));
            $this->assertFileNotExists($marker, 'A `..` module segment must not reach outside modules/');
        } finally {
            File::delete([$canary, $marker]);

            if ($createdDirectory) {
                File::deleteDirectory($directory);
            }
        }
    }

    /**
     * The include is reached before `BackendAuth` is consulted, so the guard has to hold for a
     * request that carries no session at all. Asserted against the signed-out state explicitly,
     * because a guard placed in the authenticated path would still pass the tests above.
     */
    public function testTheGuardDoesNotDependOnBeingSignedOut()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        [$segment, $marker] = $this->seedCanary('%5C', base_path('modules/backend/controllers'));

        $this->get('backend/backend/' . $segment);

        $this->assertFileNotExists($marker);
    }

    //
    // Still works: the case-insensitivity workaround and both controller roots
    //

    /**
     * `/backend/backend/users` asks for `\backend\Controllers\users`, a casing no autoloader
     * resolves on a case-sensitive filesystem. `findController()` finding `Users.php` anyway is
     * the workaround the method exists for, and it has to survive.
     */
    public function testLowercaseModuleControllerStillResolves()
    {
        foreach ([
            'backend/users' => \Backend\Controllers\Users::class,
            'backend/userroles' => \Backend\Controllers\UserRoles::class,
            'system/eventlogs' => \System\Controllers\EventLogs::class,
            'cms/themes' => \Cms\Controllers\Themes::class,
        ] as $url => $class) {
            $requested = $this->requestController($url);

            $this->assertIsArray($requested, "'{$url}' must still resolve to a controller");
            $this->assertInstanceOf($class, $requested['controller']);
            $this->assertEquals('index', $requested['action']);
        }
    }

    /** The same lookup, against a plugin root rather than the module root. */
    public function testLowercasePluginControllerStillResolves()
    {
        $requested = $this->requestController('routerprobe/sample/probe');

        $this->assertIsArray($requested, 'A plugin controller must still resolve');
        $this->assertInstanceOf(Probe::class, $requested['controller']);
        $this->assertEquals('index', $requested['action']);
    }

    /** Actions and parameters are not class-name segments, and keep taking any shape. */
    public function testActionsAndParametersAreUnaffected()
    {
        $requested = $this->requestController('backend/users/update/42');

        $this->assertIsArray($requested);
        $this->assertEquals('update', $requested['action']);
        $this->assertEquals(['42'], $requested['params']);

        // A dashed action still normalises, and a parameter may be anything -- it is never
        // used to build a class name or a path.
        $requested = $this->requestController('routerprobe/sample/probe/index/a.b/c\\d');

        $this->assertIsArray($requested);
        $this->assertEquals('index', $requested['action']);
        $this->assertEquals(['a.b', 'c\\d'], $requested['params']);
    }

    /** Dispatch end to end, so a guard that rejects real URLs fails here too. */
    public function testBackendPagesStillDispatch()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        foreach (['backend/backend/users', 'backend/system/eventlogs'] as $url) {
            $this->assertNotEquals(
                404,
                $this->get($url)->getStatusCode(),
                "{$url} must still dispatch"
            );
        }
    }
}
