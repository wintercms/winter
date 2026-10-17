<?php

namespace Backend\Tests\Widgets;

use Backend\Classes\ReportWidgetBase;
use Backend\Classes\WidgetManager;
use Backend\Models\User;
use Backend\Models\UserPreference;
use Backend\Models\UserRole;
use Illuminate\Support\Facades\Cache;
use System\Models\Parameter as SystemParameters;
use System\Classes\SettingsManager;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;

/**
 * A report widget whose registration restricts it to holders of a permission. `WidgetManager::listReportWidgets()` drops it for everyone else.
 */
class RestrictedReportWidget extends ReportWidgetBase
{
    protected $defaultAlias = 'restricted';

    public function render()
    {
        return 'restricted-widget-payload';
    }
}

/**
 * A report widget that is never registered with the WidgetManager at all.
 */
class UnregisteredReportWidget extends ReportWidgetBase
{
    protected $defaultAlias = 'unregistered';

    public function render()
    {
        return 'unregistered-widget-payload';
    }
}

/**
 * A report widget that declares properties of its own, for the sets the container publishes to the inspector.
 */
class PropertiesReportWidget extends ReportWidgetBase
{
    protected $defaultAlias = 'properties';

    public function defineProperties()
    {
        return [
            'label' => ['title' => 'Label', 'type' => 'string', 'default' => 'Widget'],
        ];
    }

    public function render()
    {
        return 'properties-widget-payload';
    }
}

/**
 * A container that publishes one property of its own to the inspector, the way a subclass extends the inspector, and exposes the two protected members this test reads.
 */
class ExtraPropertyContainer extends \Backend\Widgets\ReportContainer
{
    protected function getWidgetPropertyConfig($widget)
    {
        $config = json_decode(parent::getWidgetPropertyConfig($widget), true);

        $config[] = [
            'property' => 'containerNote',
            'title' => 'Container note',
            'type' => 'string',
        ];

        return json_encode($config);
    }

    protected function getWidgetPropertyValues($widget)
    {
        $values = json_decode(parent::getWidgetPropertyValues($widget), true);
        $values['containerNote'] = $widget->property('containerNote');

        return json_encode($values);
    }

    /** The properties this container offers the inspector for the given widget. */
    public function publishedProperties($widget): array
    {
        return array_column(json_decode($this->getWidgetPropertyConfig($widget), true), 'property');
    }

    /** The values the inspector is given, which is what it posts back. */
    public function publishedValues($widget): array
    {
        return json_decode($this->getWidgetPropertyValues($widget), true);
    }

    public function filterProperties($widget, $properties): array
    {
        return $this->filterWidgetProperties($widget, $properties);
    }
}

/**
 * Coverage for the two guarantees `Backend\Widgets\ReportContainer` makes.
 *
 * 1. The widgets a user may put on their dashboard are the ones `WidgetManager::listReportWidgets()` returns for them -- report widgets may declare a `permissions` key, and `onLoadAddPopup()`, `makeReportWidget()` and `onAddWidget()` all work from that same list.
 * 2. The values a dashboard layout carries are rendered as text or as numbers, never as markup -- including a layout published to every other backend user by a holder of `backend.manage_default_dashboard`.
 */
class ReportContainerSecurityTest extends PluginTestCase
{
    protected User $dashboardOnly;
    protected User $privileged;
    protected User $publisher;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        $this->forgetDashboardCaches();

        // registerReportWidgets() is gated behind runningInBackend(), which is false here. The three defaults come from config_dashboard.yaml and have to be registered or makeReportWidget() drops them and there is no default layout to work with.
        WidgetManager::instance()->registerReportWidgets(function ($manager) {
            $manager->registerReportWidget(RestrictedReportWidget::class, [
                'label' => 'Restricted',
                'context' => 'dashboard',
                'permissions' => ['acme.view_restricted'],
            ]);
            $manager->registerReportWidget(\Backend\ReportWidgets\Welcome::class, ['context' => 'dashboard']);
            $manager->registerReportWidget(\System\ReportWidgets\Status::class, ['context' => 'dashboard']);
            $manager->registerReportWidget(\Cms\ReportWidgets\ActiveTheme::class, ['context' => 'dashboard']);
        });

        // Likewise for the settings items, which the backend layout renders when a `widget::handler` request runs the page action ahead of the handler.
        SettingsManager::instance()->registerCallback(function ($manager) {
            $manager->registerSettingItems('Winter.Backend', []);
        });

        Model::unguard();
        $this->dashboardOnly = $this->makeUser('dashonly', ['backend.access_dashboard' => 1]);
        $this->privileged = $this->makeUser('privileged', [
            'backend.access_dashboard' => 1,
            'acme.view_restricted' => 1,
        ]);
        $this->publisher = $this->makeUser('layoutadmin', [
            'backend.access_dashboard' => 1,
            'backend.manage_default_dashboard' => 1,
        ]);
        Model::reguard();
    }

    /**
     * UserPreference and Parameter memoise in a protected static $cache, and Parameter's lookup is additionally query-cached. None of that is reset between tests in the same process, and user ids repeat, so without this a later test reads an earlier test's dashboard instead of the one it just published.
     */
    protected function forgetDashboardCaches(): void
    {
        foreach ([UserPreference::class, SystemParameters::class] as $model) {
            $cache = new \ReflectionProperty($model, 'cache');
            $cache->setAccessible(true);
            $cache->setValue(null, []);
        }

        Cache::flush();
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

    protected function addWidget(string $className, $size = 4)
    {
        return $this->reportContainerRequest('onAddWidget', [
            'className' => $className,
            'size' => $size,
        ]);
    }

    protected function updateWidget(string $alias, array $fields)
    {
        return $this->reportContainerRequest('onUpdateWidget', [
            'alias' => $alias,
            'fields' => json_encode($fields),
        ]);
    }

    protected function setWidgetOrders(string $aliases, string $orders)
    {
        return $this->reportContainerRequest('onSetWidgetOrders', [
            'aliases' => $aliases,
            'orders' => $orders,
        ]);
    }

    protected function removeWidget(string $alias)
    {
        return $this->reportContainerRequest('onRemoveWidget', ['alias' => $alias]);
    }

    protected function publishDefaultLayout()
    {
        return $this->reportContainerRequest('onMakeLayoutDefault');
    }

    protected function reportContainerRequest(string $handler, array $data = [])
    {
        return $this->post('backend/backend/index', $data, [
            'X-WINTER-REQUEST-HANDLER' => 'reportContainer::' . $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    /**
     * The container markup the acting user is served.
     */
    protected function renderDashboard(): string
    {
        $response = $this->post('backend/backend/index', [], [
            'X-WINTER-REQUEST-HANDLER' => 'onInitReportContainer',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(200, $response->getStatusCode(), 'The dashboard must render');

        $contents = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('#dashReportContainer', $contents);

        return $contents['#dashReportContainer'];
    }

    /** The container markup a second user with no dashboard of their own is served. */
    protected function otherUsersDashboard(): string
    {
        $this->actingAs($this->dashboardOnly);

        return $this->renderDashboard();
    }

    //
    // Assertions
    //

    /**
     * Asserts on the parsed document rather than the source text: the value also appears, correctly escaped, inside the inspector's JSON attributes, so a substring search cannot tell an inert copy from a live one. What matters is whether any element ends up carrying the attributes the value spells out.
     */
    protected function assertNoLiveMarkup(string $html): void
    {
        $document = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<!DOCTYPE html><html><body>' . $html . '</body></html>');
        libxml_use_internal_errors($previous);

        $injected = (new \DOMXPath($document))->query('//*[@onmouseover] | //*[@data-injected]');

        $this->assertSame(0, $injected->length, 'Markup from a stored value reached the rendered dashboard');
    }

    /**
     * The message a refused request reports, as the error response titles it. Taken from the title rather than the whole body because the body also quotes the surrounding source, messages included.
     */
    protected function refusalMessage($response): string
    {
        preg_match('/<title>(.*?)<\/title>/s', $response->getContent(), $matches);

        $this->assertNotEmpty($matches[1] ?? '', 'The response should report a reason');

        return $matches[1];
    }

    /**
     * The class names stored on the acting user's dashboard. Read back through the store the handler writes to, so the assertion reflects what happened rather than what the response body happened to contain.
     */
    protected function storedWidgetClasses(): array
    {
        return array_column($this->storedWidgets(), 'class');
    }

    /**
     * The widget layout stored for the acting user.
     *
     * The memoised reads are dropped first: Preferences::get() caches the default it was handed when there is no record, and the container hands it the default layout, so a read taken after a request would otherwise report that default layout as though it had been stored.
     */
    protected function storedWidgets(): array
    {
        $this->forgetDashboardCaches();

        return (array) UserPreference::forUser()->get('backend::reportwidgets.dashboard', []);
    }

    //
    // The list of widgets a user may add
    //

    /**
     * The handler constructs the widget and returns its rendered output, so it applies the same `permissions` filter the rest of the container does.
     */
    public function testRestrictedWidgetCannotBeAddedWithoutItsPermission(): void
    {
        $this->actingAs($this->dashboardOnly);

        $this->addWidget(RestrictedReportWidget::class);

        $this->assertNotContains(RestrictedReportWidget::class, $this->storedWidgetClasses());
    }

    /**
     * The registry check runs before the class is constructed -- `new $className()` on a request-supplied class name is itself a side effect the handler must not have.
     */
    public function testUnregisteredClassCannotBeAdded(): void
    {
        $this->actingAs($this->privileged);

        $this->addWidget(UnregisteredReportWidget::class);

        $this->assertNotContains(UnregisteredReportWidget::class, $this->storedWidgetClasses());
    }

    /**
     * The class name is matched against the list exactly as the list is keyed. `class_exists()` is case insensitive and tolerates a leading separator, so both spellings reach the check and both are turned away -- the same way makeReportWidget() matches when the container renders.
     */
    public function testAlternativeClassNameSpellingsAreRefused(): void
    {
        $this->actingAs($this->privileged);

        $this->addWidget('\\' . RestrictedReportWidget::class);
        $this->addWidget(strtolower(RestrictedReportWidget::class));

        $this->assertSame([], $this->storedWidgetClasses(), 'No layout should have been written');
    }

    /**
     * The list is consulted before the class name is used for anything else, so a name that is not on it is answered identically whether or not a class of that name exists.
     */
    public function testClassNamesThatAreNotOfferedAreAllRefusedTheSameWay(): void
    {
        $this->actingAs($this->privileged);

        $existing = $this->refusalMessage($this->addWidget(UnregisteredReportWidget::class));
        $absent = $this->refusalMessage($this->addWidget('Acme\\NoSuchNamespace\\NoSuchWidget'));

        $this->assertStringContainsString('is not a report widget', $existing);
        $this->assertSame($existing, $absent, 'Both names must be answered the same way');

        $this->assertSame([], $this->storedWidgetClasses(), 'No layout should have been written');
    }

    /** Nothing legitimate regressed: a holder of the permission can still add it. */
    public function testPermittedUserCanStillAddTheWidget(): void
    {
        $this->actingAs($this->privileged);

        $response = $this->addWidget(RestrictedReportWidget::class);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('restricted-widget-payload', $response->getContent());
        $this->assertContains(RestrictedReportWidget::class, $this->storedWidgetClasses());
    }

    //
    // Values a layout carries into the markup
    //
    // backend.manage_default_dashboard is documented as publishing a layout to every other backend user, so the cross-user reach is by design. The values carried across are request-supplied strings, and the container renders two of them into attributes of its own, so they are typed on the way in and neutralised on the way out.
    //

    protected const INJECTED_MARKUP = '7" data-injected="1" onmouseover="window.touched=1';

    /** A width set through the inspector. */
    public function testWidgetWidthCannotCarryMarkupIntoAnotherUsersDashboard(): void
    {
        $this->actingAs($this->publisher);
        $this->updateWidget('systemStatus', ['ocWidgetWidth' => self::INJECTED_MARKUP]);
        $this->publishDefaultLayout();

        $this->assertNoLiveMarkup($this->otherUsersDashboard());
    }

    /** The same value arriving as onAddWidget's `size` rather than as inspector fields. */
    public function testAddWidgetSizeCannotCarryMarkupIntoAnotherUsersDashboard(): void
    {
        $this->actingAs($this->publisher);
        $this->addWidget(\System\ReportWidgets\Status::class, self::INJECTED_MARKUP);
        $this->publishDefaultLayout();

        $this->assertNoLiveMarkup($this->otherUsersDashboard());
    }

    /**
     * The sort order is emitted into a value="" attribute and is equally unvalidated input.
     *
     * It only reaches the markup on a single-widget layout: with two or more widgets defineReportWidgets() sorts them with `$a['sortOrder'] - $b['sortOrder']`, and a non-numeric sort order raises there before anything renders. So the other widgets are removed first -- which is a supported thing to do.
     */
    public function testSortOrderCannotCarryMarkupIntoAnotherUsersDashboard(): void
    {
        $this->actingAs($this->publisher);

        $this->removeWidget('welcome');
        $this->removeWidget('activeTheme');
        $this->setWidgetOrders('systemStatus', self::INJECTED_MARKUP);
        $this->publishDefaultLayout();

        $this->assertNoLiveMarkup($this->otherUsersDashboard());
    }

    /**
     * setProperties() stores whatever the decoded JSON holds. No current partial renders an undeclared property, but the stored layout is a structure later markup may read, so the handler keeps only the properties it publishes.
     */
    public function testUndeclaredPropertiesAreNotStored(): void
    {
        $this->actingAs($this->publisher);
        $this->updateWidget('systemStatus', [
            'ocWidgetWidth' => 5,
            'notAProperty' => self::INJECTED_MARKUP,
        ]);

        $configuration = $this->storedWidgets()['systemStatus']['configuration'];

        $this->assertSame(5, $configuration['ocWidgetWidth'], 'The properties the container publishes are still stored');
        $this->assertArrayNotHasKey('notAProperty', $configuration);
    }

    /**
     * The container and the toolbar it includes both echo the container's own alias, one as a value and the other inside the handler names it builds, so both have to escape it. The alias is developer-set rather than posted, which is why this is a unit-level check of the two partials rather than a request.
     */
    public function testTheContainerAndItsToolbarEscapeTheContainerAlias(): void
    {
        $this->actingAs($this->publisher);

        $container = new \Backend\Widgets\ReportContainer(new \Backend\Controllers\Index());
        $container->alias = 'probe" data-injected="1';

        $this->assertNoLiveMarkup($container->render());
    }

    /**
     * trans() resolves a key naming a translation group to an array, which cannot be rendered. A title is stored configuration, and a published layout is served to every user who has none of their own, so one stored title of that shape would otherwise make every such dashboard unrenderable - including the toolbar that would let it be reset.
     */
    public function testATitleNamingATranslationGroupStillRendersTheDashboard(): void
    {
        SystemParameters::set('backend::reportwidgets.default.dashboard', [
            'systemStatus' => [
                'class' => \System\ReportWidgets\Status::class,
                'sortOrder' => 1,
                'configuration' => ['ocWidgetWidth' => 4, 'title' => 'backend::lang.dashboard'],
            ],
        ]);

        $markup = $this->otherUsersDashboard();

        $this->assertStringContainsString('backend::lang.dashboard', $markup, 'the configured value is rendered as written');
        $this->assertStringContainsString('class="report-widget"', $markup, 'the widget still renders rather than failing the whole dashboard');
    }

    /**
     * The widget manager is a singleton for the life of the process, and the handlers now authorise against the list it returns, so the permission filter must not be applied to the memo the next caller reads.
     */
    public function testTheWidgetListIsNotHiddenFromALaterUserInTheSameProcess(): void
    {
        $manager = WidgetManager::instance();

        $this->actingAs($this->publisher);
        $this->assertArrayHasKey(\System\ReportWidgets\Status::class, $manager->listReportWidgets());

        $this->actingAs($this->dashboardOnly);
        $manager->listReportWidgets();

        $this->actingAs($this->publisher);
        $this->assertArrayHasKey(
            \System\ReportWidgets\Status::class,
            $manager->listReportWidgets(),
            'the permission filter poisoned the memo for a later caller'
        );
    }

    /**
     * Both write paths cast the column count, so what is stored is a number whatever the request spelled. Asserted on the store rather than on the markup, which casts again at the sink.
     */
    public function testTheStoredWidgetWidthIsAnInteger(): void
    {
        $this->actingAs($this->publisher);

        $this->addWidget(\System\ReportWidgets\Status::class, '7 columns wide');
        $this->assertSame(7, $this->storedWidgets()['systemStatus']['configuration']['ocWidgetWidth']);

        // A numeric string, which is what the inspector posts; a value the property type refuses is
        // rejected before it reaches the cast and leaves the stored width alone.
        $this->updateWidget('systemStatus', ['ocWidgetWidth' => '5']);
        $this->assertSame(5, $this->storedWidgets()['systemStatus']['configuration']['ocWidgetWidth']);
    }

    /**
     * Publishes a default layout of `$count` widgets that all carry `$value` as their sort order and as their column count.
     *
     * The widget count is a parameter because the layout is sorted before a single widget is rendered, and the comparator only runs at all once there are two entries to compare -- so the rendering path is only fully exercised by a layout with several widgets on it.
     */
    protected function publishStoredLayout($value, int $count): void
    {
        $classes = [
            'systemStatus' => \System\ReportWidgets\Status::class,
            'welcome' => \Backend\ReportWidgets\Welcome::class,
            'activeTheme' => \Cms\ReportWidgets\ActiveTheme::class,
        ];

        $layout = [];
        foreach (array_slice($classes, 0, $count) as $alias => $class) {
            $layout[$alias] = [
                'class' => $class,
                'sortOrder' => $value,
                'configuration' => ['ocWidgetWidth' => $value],
            ];
        }

        SystemParameters::set('backend::reportwidgets.default.dashboard', $layout);
    }

    public function widgetCountProvider(): array
    {
        return [
            'one widget' => [1],
            'three widgets' => [3],
        ];
    }

    /**
     * Every shape a `fields` payload can arrive in that does not name a single property the container publishes. JSON decoding turns several of them into arrays -- a list, a list of sets, an empty object -- so a shape check on the decoded value is not enough on its own; what the handler applies has to be a set that still holds a value after filtering.
     */
    public function unusablePayloadProvider(): array
    {
        return [
            'plain string' => ['not a set of values'],
            'empty string' => [''],
            'json null' => ['null'],
            'json number' => ['5'],
            'json boolean' => ['true'],
            'json empty object' => ['{}'],
            'json empty list' => ['[]'],
            'json list' => ['[1,2,3]'],
            'json list of sets' => ['[{"ocWidgetWidth":9}]'],
            'set of other keys' => ['{"a":{"b":{"c":1}}}'],
        ];
    }

    /**
     * A payload that is not a usable set of property values is refused outright, and reports why. Reading it as an empty set instead would store that empty set, replacing the configuration the widget already had with its declared defaults.
     *
     * @dataProvider unusablePayloadProvider
     */
    public function testAPropertyPayloadThatIsNotASetOfValuesChangesNothing(string $payload): void
    {
        $this->actingAs($this->publisher);

        $this->updateWidget('systemStatus', ['ocWidgetWidth' => 7]);
        $before = $this->storedWidgets()['systemStatus']['configuration'];
        $this->assertSame(7, $before['ocWidgetWidth']);

        $response = $this->reportContainerRequest('onUpdateWidget', [
            'alias' => 'systemStatus',
            'fields' => $payload,
        ]);

        $this->assertSame($before, $this->storedWidgets()['systemStatus']['configuration'], 'The stored configuration must be left alone');
        $this->assertNotEquals(200, $response->getStatusCode(), 'The request must be refused');
        $this->assertStringContainsString('Invalid widget properties posted.', $this->refusalMessage($response), 'The refusal must report the reason the container gives');
    }

    /**
     * A property the container publishes to the inspector is a property the inspector posts back, so the filter is computed from the same set rather than from the widget's declared properties alone. Those two sets are the same set for the container itself; a subclass extending the inspector is where they part.
     */
    public function testAPropertyTheContainerPublishesSurvivesItsFilter(): void
    {
        $this->actingAs($this->publisher);

        $controller = new \Backend\Controllers\Index();
        $container = new ExtraPropertyContainer($controller);
        $widget = new PropertiesReportWidget($controller);
        $widget->setProperty('containerNote', 'kept');

        $published = $container->publishedProperties($widget);
        $this->assertContains('containerNote', $published, 'The subclass must publish the extra property');

        $filtered = $container->filterProperties($widget, $container->publishedValues($widget));

        $this->assertArrayHasKey('containerNote', $filtered, 'A property the container publishes must survive its own filter');
        $this->assertSame('kept', $filtered['containerNote']);
        $this->assertArrayHasKey('label', $filtered, 'A property the widget declares must survive it too');
    }

    /**
     * Sort orders a layout was stored with before this change are outside the container's control, but the order it derives from them for a newly added widget is not: it is stored, so it is stored as an integer.
     *
     * @dataProvider untypedOrderProvider
     */
    public function testAddingAWidgetToALayoutWithAnUntypedOrderStoresAnInteger($value): void
    {
        $this->publishStoredLayout($value, 3);

        $this->actingAs($this->privileged);
        $response = $this->addWidget(RestrictedReportWidget::class, 4);

        $this->assertEquals(200, $response->getStatusCode(), 'The widget must still be added');

        $stored = $this->storedWidgets();
        $added = array_diff(array_keys($stored), ['systemStatus', 'welcome', 'activeTheme']);
        $this->assertCount(1, $added, 'The widget must be stored');

        $this->assertIsInt($stored[reset($added)]['sortOrder'], 'The sort order the container derives must be an integer');
    }

    public function untypedOrderProvider(): array
    {
        return [
            'markup' => [self::INJECTED_MARKUP],
            'non-numeric string' => ['abc'],
            'set' => [['x' => 1]],
            'null' => [null],
        ];
    }

    /**
     * A layout stored before this change is beyond the reach of any constraint on the way in, so the rendering path is what has to neutralise it, and is tested on its own.
     *
     * @dataProvider widgetCountProvider
     */
    public function testAnAlreadyStoredValueIsNeutralisedOnRender(int $count): void
    {
        $this->publishStoredLayout(self::INJECTED_MARKUP, $count);

        $this->assertNoLiveMarkup($this->otherUsersDashboard());
    }

    /**
     * ...and it stays renderable: a stored value the rendering path can neither turn into a string nor compare as a number, which the store has always accepted, must not take the dashboard down with it.
     *
     * @dataProvider widgetCountProvider
     */
    public function testAnAlreadyStoredNonScalarValueStillRenders(int $count): void
    {
        $this->publishStoredLayout(['nested' => 1], $count);

        $this->assertStringContainsString('report-container', $this->otherUsersDashboard());
    }

    //
    // Legitimate use
    //

    /** A real width still reaches another user's markup. */
    public function testALegitimateWidthStillReachesOtherUsers(): void
    {
        $this->actingAs($this->publisher);
        $this->updateWidget('systemStatus', ['ocWidgetWidth' => 9]);
        $this->publishDefaultLayout();

        $this->assertStringContainsString('width-9', $this->otherUsersDashboard());
    }

    /**
     * The set of values the container hands the inspector is the set the inspector posts back, so posting it verbatim is accepted and changes nothing. This is the shape the dashboard's own save takes, and it is what keeps the refusal above off the path a user travels.
     */
    public function testThePayloadTheInspectorIsGivenIsAlwaysAccepted(): void
    {
        $this->actingAs($this->publisher);

        // Leaves one widget on the dashboard, so the values read back below are that widget's.
        $this->removeWidget('welcome');
        $this->removeWidget('activeTheme');
        $this->updateWidget('systemStatus', ['ocWidgetWidth' => 8]);
        $before = $this->storedWidgets()['systemStatus']['configuration'];

        preg_match('/data-inspector-values value="([^"]*)"/', $this->renderDashboard(), $matches);
        $published = html_entity_decode($matches[1] ?? '', ENT_QUOTES);
        $this->assertArrayHasKey('ocWidgetWidth', (array) json_decode($published, true), 'The inspector is given the properties the container renders');

        $response = $this->reportContainerRequest('onUpdateWidget', [
            'alias' => 'systemStatus',
            'fields' => $published,
        ]);

        $after = $this->storedWidgets()['systemStatus']['configuration'];

        $this->assertEquals(200, $response->getStatusCode(), 'The payload the inspector is given must be accepted');
        $this->assertSame(8, $after['ocWidgetWidth'], 'and must keep the column count the widget was configured with');
        $this->assertEmpty(array_diff(array_keys($before), array_keys($after)), 'and must not drop a property the widget had');
    }

    /** A declared property of the widget itself still round-trips. */
    public function testADeclaredPropertyStillRoundTrips(): void
    {
        $this->actingAs($this->publisher);
        $this->updateWidget('welcome', ['title' => 'My dashboard']);

        $this->assertStringContainsString('My dashboard', $this->renderDashboard());
    }

    /** So does the container's own new-row property. */
    public function testTheNewRowPropertyStillRoundTrips(): void
    {
        $this->actingAs($this->publisher);
        $this->updateWidget('systemStatus', ['ocWidgetWidth' => 6, 'ocWidgetNewRow' => 1]);

        $markup = $this->renderDashboard();

        $this->assertStringContainsString('new-line', $markup);
        $this->assertStringContainsString('width-6', $markup);
    }

    /** Widgets can still be added and removed, and the layout reflects it. */
    public function testWidgetsCanStillBeAddedAndRemoved(): void
    {
        $this->actingAs($this->privileged);

        $this->removeWidget('welcome');
        $this->assertNotContains(\Backend\ReportWidgets\Welcome::class, $this->storedWidgetClasses());

        $this->addWidget(\Backend\ReportWidgets\Welcome::class, 6);
        $this->assertContains(\Backend\ReportWidgets\Welcome::class, $this->storedWidgetClasses());
    }

    /** Reordering still persists, and the stored orders render back into the markup. */
    public function testWidgetOrdersStillPersistAndRenderBack(): void
    {
        $this->actingAs($this->privileged);

        $this->setWidgetOrders('welcome,systemStatus,activeTheme', '3,2,1');

        $stored = $this->storedWidgets();
        $this->assertSame(3, $stored['welcome']['sortOrder']);
        $this->assertSame(2, $stored['systemStatus']['sortOrder']);
        $this->assertSame(1, $stored['activeTheme']['sortOrder']);

        $markup = $this->renderDashboard();

        preg_match_all('/data-widget-order name="widgetSortOrders\[\]" value="([^"]*)"/', $markup, $matches);
        $this->assertSame(['1', '2', '3'], $matches[1]);
    }
}
