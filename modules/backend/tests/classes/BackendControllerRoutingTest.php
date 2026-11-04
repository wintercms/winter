<?php

namespace Backend\Tests\Classes;

use Backend\Classes\BackendController;
use Backend\Tests\Fixtures\Models\UserFixture;
use RouterFixture\Casing\Controllers\Sample;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Str;

/**
 * What a backend URL segment is allowed to be.
 *
 * Every segment that becomes part of a class name must be a plain PHP identifier, which is the
 * only shape a class name can take. These tests fix both halves of that: a segment of any other
 * shape resolves to nothing, and every segment shape a real controller URL uses keeps resolving
 * as it did.
 */
class BackendControllerRoutingTest extends PluginTestCase
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
     * Writes a `.php` file under `storage/`, outside either controller root, and returns the URL
     * segment that addresses it from $controllerDirectory -- joined with $separator -- plus the
     * path of the marker the file writes if it is ever loaded. The marker is what makes the
     * assertions exact: the router either loaded the file or it did not.
     *
     * @return array{0: string, 1: string}
     */
    protected function seedMarkerFile(string $separator, string $controllerDirectory): array
    {
        $name = 'routing' . bin2hex(random_bytes(4));
        $directory = base_path('storage/framework/cache/' . $name);

        File::makeDirectory($directory, 0777, true, true);
        $this->tempPaths[] = $directory;

        File::put(
            $directory . '/marker.php',
            '<?php file_put_contents(__DIR__ . \'/loaded\', \'loaded\');' . PHP_EOL
        );

        // One `..` for each directory between the project root and the controller directory, so
        // the path lands on the project root before descending into storage/. Normalised first
        // because plugins_path() returns native separators and base_path() does not.
        $relative = Str::after(
            File::normalizePath($controllerDirectory),
            File::normalizePath(base_path())
        );
        $depth = count(explode('/', trim($relative, '/')));

        $segment = implode($separator, array_merge(
            array_fill(0, $depth, '..'),
            ['storage', 'framework', 'cache', $name, 'marker']
        ));

        return [$segment, $directory . '/loaded'];
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
    // A segment that cannot name a class resolves to nothing
    //

    /**
     * A controller segment carrying encoded namespace separators, start to finish and with no
     * session. `%5C` is already decoded by the time the router sees the URL, and
     * `segmentizeUrl()` keeps it inside the one segment.
     */
    public function testEncodedSeparatorInAControllerSegmentResolvesToNothing()
    {
        [$segment, $marker] = $this->seedMarkerFile('%5C', base_path('modules/backend/controllers'));

        $response = $this->get('backend/backend/' . $segment);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFileNotExists($marker, 'No file outside the controller root may be loaded');
    }

    /**
     * The same segment with literal separators. Symfony refuses to build such a URI through
     * `Request::create()`, which the test HTTP client uses, so this covers the router directly;
     * a real web server passes the character through.
     */
    public function testLiteralSeparatorInAControllerSegmentResolvesToNothing()
    {
        [$segment, $marker] = $this->seedMarkerFile('\\', base_path('modules/backend/controllers'));

        $this->assertNull($this->requestController('backend/' . $segment));
        $this->assertFileNotExists($marker, 'No file outside the controller root may be loaded');
    }

    /**
     * A plugin URL carries three class-name segments rather than two, and the third one is where
     * a plugin controller name goes, resolved against `plugins_path()` instead.
     */
    public function testNonIdentifierPluginControllerSegmentResolvesToNothing()
    {
        [$segment, $marker] = $this->seedMarkerFile('%5C', plugins_path('routerfixture/casing/controllers'));

        $response = $this->get('backend/routerfixture/casing/' . $segment);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFileNotExists($marker, 'No file outside the controller root may be loaded');
    }

    /**
     * A segment needs no separator in it to leave the controller root: `..` in the module
     * position is already a path component, and the lookup lands on
     * `<project root>/controllers/<segment>.php`. That is why a dot is not an identifier
     * character either.
     */
    public function testDottedModuleSegmentResolvesToNothing()
    {
        $name = 'routing' . bin2hex(random_bytes(4));
        $directory = base_path('controllers');
        $file = $directory . '/' . $name . '.php';
        $marker = $directory . '/' . $name;

        // Only the file is ours; the directory name is fixed by the path findController()
        // builds, so it is removed again only if this test created it.
        $createdDirectory = !File::isDirectory($directory);
        File::makeDirectory($directory, 0777, true, true);

        try {
            File::put($file, '<?php file_put_contents(\'' . $marker . '\', \'loaded\');' . PHP_EOL);

            $this->assertNull($this->requestController('../' . $name));
            $this->assertFileNotExists($marker, 'A `..` module segment must not reach outside modules/');
        } finally {
            File::delete([$file, $marker]);

            if ($createdDirectory) {
                File::deleteDirectory($directory);
            }
        }
    }

    /**
     * Resolution happens before the controller is constructed and before anyone is identified,
     * so the rule has to hold for a request carrying no session at all -- and equally for one
     * carrying the most privileged session there is. Asserted both ways round, because a check
     * placed on only one of those paths would still pass the tests above.
     */
    public function testResolutionDoesNotDependOnTheSignedInUser()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        [$segment, $marker] = $this->seedMarkerFile('%5C', base_path('modules/backend/controllers'));

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
        $requested = $this->requestController('routerfixture/casing/sample');

        $this->assertIsArray($requested, 'A plugin controller must still resolve');
        $this->assertInstanceOf(Sample::class, $requested['controller']);
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
        $requested = $this->requestController('routerfixture/casing/sample/index/a.b/c\\d');

        $this->assertIsArray($requested);
        $this->assertEquals('index', $requested['action']);
        $this->assertEquals(['a.b', 'c\\d'], $requested['params']);
    }

    /** Dispatch end to end, so a check that rejects real URLs fails here too. */
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
