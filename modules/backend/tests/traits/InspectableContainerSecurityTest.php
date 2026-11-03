<?php

namespace Backend\Tests\Traits;

use Backend\Classes\ReportWidgetBase;
use Backend\Classes\WidgetManager;
use Backend\Models\User;
use Backend\Models\UserRole;
use Cms\Classes\ComponentBase;
use Cms\Classes\ComponentManager;
use System\Classes\SettingsManager;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;

/**
 * A class using the trait the handler looks for, registered with neither manager. The constructor records that it ran: the response body cannot tell a refusal before construction from a refusal after one.
 */
class UnofferedInspectable
{
    use \System\Traits\PropertyContainer;

    public static bool $constructed = false;

    public function __construct($argument = null)
    {
        static::$constructed = true;
    }

    public function getColourOptions()
    {
        return ['red' => 'Red'];
    }
}

/**
 * A report widget registered with the WidgetManager, which is what the dashboard offers the inspector.
 */
class OfferedReportWidget extends ReportWidgetBase
{
    protected $defaultAlias = 'offeredProbe';

    public function render()
    {
        return '';
    }

    public function getColourOptions()
    {
        return ['blue' => 'Blue'];
    }
}

/**
 * A component registered with the ComponentManager, which is what the CMS index offers the inspector.
 */
class OfferedComponent extends ComponentBase
{
    public function componentDetails()
    {
        return ['name' => 'Offered', 'description' => 'Offered'];
    }

    public function getColourOptions()
    {
        return ['green' => 'Green'];
    }
}

/**
 * A controller mounting the trait, and a subclass of it that publishes an inspectable class of its own.
 */
class InspectableController extends \Backend\Classes\Controller
{
    use \Backend\Traits\InspectableContainer;
}

class ExtraInspectableController extends InspectableController
{
    protected function listInspectableClasses(): array
    {
        return array_merge(parent::listInspectableClasses(), [UnofferedInspectable::class]);
    }
}

/**
 * Coverage for the one guarantee `Backend\Traits\InspectableContainer` makes: the only class names it acts on are the ones the controller offered the inspector in the first place.
 *
 * The handler reaches the named class twice -- `class_uses_recursive()` autoloads it, and the reflection after that constructs it -- so both are observed directly rather than inferred from the response, which reports the same refusal either way.
 */
class InspectableContainerSecurityTest extends PluginTestCase
{
    protected User $dashboardOnly;
    protected User $cmsEditor;

    /** @var callable */
    protected $autoloadProbe;

    protected array $autoloaded = [];

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        // The refusal reason only reaches the error page in debug.
        Config::set('app.debug', true);

        UnofferedInspectable::$constructed = false;

        $this->autoloaded = [];
        $this->autoloadProbe = function ($class) {
            $this->autoloaded[] = $class;
        };

        // Registered last, so it is reached only once every real loader has declined the name -- which is the point: the name was looked up at all.
        spl_autoload_register($this->autoloadProbe);

        Model::unguard();
        $this->dashboardOnly = $this->makeUser('inspectdash', ['backend.access_dashboard' => 1]);
        $this->cmsEditor = $this->makeUser('inspectcms', ['cms.manage_pages' => 1]);
        Model::reguard();

        $this->actingAs($this->dashboardOnly);

        // The memos are loaded first and the probes appended: these managers are singletons for the life of the process, so a callback registered here is never run if an earlier test already loaded them.
        WidgetManager::instance()->listReportWidgets();
        WidgetManager::instance()->registerReportWidget(OfferedReportWidget::class, [
            'label' => 'Offered',
            'context' => 'dashboard',
        ]);

        ComponentManager::instance()->listComponents();
        ComponentManager::instance()->registerComponent(OfferedComponent::class, 'offeredProbeComponent');

        // The backend layout renders the settings menu when a handler request runs the page action ahead of the handler.
        SettingsManager::instance()->registerCallback(function ($manager) {
            $manager->registerSettingItems('Winter.Backend', []);
        });
    }

    public function tearDown(): void
    {
        spl_autoload_unregister($this->autoloadProbe);

        parent::tearDown();
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

    //
    // Requests
    //

    /** The dashboard mount, which `backend.access_dashboard` alone reaches. */
    protected function inspect(string $className, string $property = 'colour')
    {
        return $this->getOptions('backend/backend/index', $className, $property);
    }

    /** The CMS index mount, which needs one of the `cms.manage_*` permissions. */
    protected function inspectOnCmsIndex(string $className, string $property = 'colour')
    {
        return $this->getOptions('backend/cms', $className, $property);
    }

    protected function getOptions(string $url, string $className, string $property)
    {
        return $this->post($url, [
            'inspectorProperty' => $property,
            'inspectorClassName' => $className,
        ], [
            'X-WINTER-REQUEST-HANDLER' => 'onInspectableGetOptions',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    /**
     * From the error title, not the body -- the body quotes the surrounding source, messages included.
     */
    protected function refusalMessage($response): string
    {
        preg_match('/<title>(.*?)<\/title>/s', $response->getContent(), $matches);

        $this->assertNotEmpty($matches[1] ?? '', 'The response should report a reason');

        return $matches[1];
    }

    protected function assertWasNotLookedUp(string $className): void
    {
        $this->assertNotContains(
            $className,
            $this->autoloaded,
            'The handler put a request-supplied class name through the autoloader'
        );
    }

    //
    // The class names the handler acts on
    //

    /**
     * The reported request: a class name the controller never offered reaches the autoloader on its way to `class_uses_recursive()`, whether or not a class of that name exists.
     */
    public function testAClassThatIsNotOfferedIsNeverLookedUp(): void
    {
        $this->inspect('Acme\\NoSuchNamespace\\NoSuchClass');

        $this->assertWasNotLookedUp('Acme\\NoSuchNamespace\\NoSuchClass');
    }

    /**
     * And a class name that is not offered but does use the trait the handler looks for is not constructed. Asserted on the constructor rather than on the response: the refusal reads the same whether it came before or after.
     */
    public function testAClassThatIsNotOfferedIsNeverConstructed(): void
    {
        $this->inspect(UnofferedInspectable::class);

        $this->assertFalse(
            UnofferedInspectable::$constructed,
            'The handler constructed a class the controller does not offer'
        );
    }

    /**
     * The list is matched on the normalised class name, so neither a leading separator nor a different casing gets a name past it -- `class_uses_recursive()` and `new` accept both spellings.
     */
    public function testAlternativeSpellingsOfAnUnofferedClassAreRefused(): void
    {
        $this->inspect('\\' . UnofferedInspectable::class);
        $this->inspect(strtolower(UnofferedInspectable::class));

        $this->assertFalse(UnofferedInspectable::$constructed);
    }

    /**
     * Consulted before the name is used at all, so every unoffered name gets the same answer.
     */
    public function testUnofferedClassNamesAreAllAnsweredTheSameWay(): void
    {
        $inspectable = $this->inspect(UnofferedInspectable::class);
        $plain = $this->inspect(\Backend\Classes\Controller::class);
        $absent = $this->inspect('Acme\\NoSuchNamespace\\NoSuchClass');

        $this->assertSame($inspectable->getStatusCode(), $plain->getStatusCode());
        $this->assertSame($inspectable->getStatusCode(), $absent->getStatusCode());

        $message = $this->refusalMessage($inspectable);
        $this->assertStringContainsString('options cannot be loaded', $message);
        $this->assertSame($message, $this->refusalMessage($plain));
        $this->assertSame($message, $this->refusalMessage($absent));
    }

    /**
     * The same holds on the other mount. The CMS index offers components, so a report widget is not one of its own, but both mounts answer from the same list and neither constructs what it will not answer for.
     */
    public function testTheCmsMountAlsoRefusesAClassItDoesNotOffer(): void
    {
        $this->actingAs($this->cmsEditor);

        $this->inspectOnCmsIndex(UnofferedInspectable::class);
        $this->inspectOnCmsIndex('Acme\\NoSuchNamespace\\NoSuchClass');

        $this->assertFalse(UnofferedInspectable::$constructed);
        $this->assertWasNotLookedUp('Acme\\NoSuchNamespace\\NoSuchClass');
    }

    //
    // Legitimate use
    //

    /** A registered report widget is still answered for, which is what the dashboard inspects. */
    public function testARegisteredReportWidgetIsStillAnsweredFor(): void
    {
        $response = $this->inspect(OfferedReportWidget::class);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(
            [['value' => 'blue', 'title' => 'Blue']],
            json_decode($response->getContent(), true)['options']
        );
    }

    /** A registered component is still answered for, which is what the CMS index and the Pages snippet editor inspect. */
    public function testARegisteredComponentIsStillAnsweredFor(): void
    {
        $this->actingAs($this->cmsEditor);

        $response = $this->inspectOnCmsIndex(OfferedComponent::class);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(
            [['value' => 'green', 'title' => 'Green']],
            json_decode($response->getContent(), true)['options']
        );
    }

    /**
     * Both mounts answer from the same list, so the dashboard still answers for a component the way it did before -- the backend index is where the Pages snippet inspector's sibling handler lives.
     */
    public function testTheDashboardStillAnswersForARegisteredComponent(): void
    {
        $response = $this->inspect(OfferedComponent::class);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(
            [['value' => 'green', 'title' => 'Green']],
            json_decode($response->getContent(), true)['options']
        );
    }

    /** A controller that publishes an inspectable class of its own adds it to the list and is answered for it. */
    public function testAControllerCanOfferAnInspectableClassOfItsOwn(): void
    {
        app('request')->merge([
            'inspectorProperty' => 'colour',
            'inspectorClassName' => UnofferedInspectable::class,
        ]);

        $options = (new ExtraInspectableController)->onInspectableGetOptions();

        $this->assertTrue(UnofferedInspectable::$constructed);
        $this->assertSame([['value' => 'red', 'title' => 'Red']], $options['options']);
    }
}
