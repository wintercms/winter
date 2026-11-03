<?php

namespace Backend\Tests\Behaviors;

use Backend\Classes\Controller;
use Backend\Models\User;
use Backend\Tests\Fixtures\Models\UserFixture;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * Coverage for which list record navigation walks, and for overriding it.
 *
 * `formGetRecordNavigation()` reads its siblings from the controller's **primary**
 * list. That is the right default, but it leaves navigation unavailable on any
 * controller whose primary list is deliberately a subset -- a queue filtered to
 * pending records, say -- because a record outside that subset has no neighbours
 * in it and the buttons silently disappear.
 *
 * `recordNavigation` therefore also accepts the name of a list definition, and is
 * resolved per form context, so a `preview` context can navigate an archive list
 * while `update` keeps navigating the queue.
 *
 * The other half is that `formRenderRecordNavigation()` resolves the getter through
 * `$this->controller`. Without that a controller cannot override
 * `formGetRecordNavigation()` at all: the behavior calls its own copy, so the
 * override is never reached and the only way to influence navigation is to
 * reimplement the render helper verbatim.
 *
 * @see modules/backend/behaviors/FormController.php
 */
class NavigationController extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
    ];

    public $formConfig = [
        'name' => 'User',
        'modelClass' => User::class,
        'form' => ['fields' => ['login' => ['label' => 'Login']]],
        'update' => ['title' => 'Edit'],
        'preview' => [
            'title' => 'View',
            // The point of the feature: this context navigates a different list
            'recordNavigation' => 'archive',
        ],
    ];

    public $listConfig = [
        'index' => [
            'modelClass' => User::class,
            'list' => ['columns' => ['login' => ['label' => 'Login']]],
        ],
        'archive' => [
            'modelClass' => User::class,
            'list' => ['columns' => ['login' => ['label' => 'Login']]],
        ],
    ];

    /**
     * The primary list is a subset -- superusers only -- while the archive holds
     * everybody. This is the shape that leaves navigation unavailable today.
     */
    public function listExtendQuery($query, $definition)
    {
        if ($definition === 'index') {
            $query->where('is_superuser', true);
        }

        return $query->orderBy('id');
    }
}

/**
 * Disables navigation outright, to prove `false` still wins.
 */
class NavigationDisabledController extends NavigationController
{
    public $formConfig = [
        'name' => 'User',
        'modelClass' => User::class,
        'form' => ['fields' => ['login' => ['label' => 'Login']]],
        'preview' => ['recordNavigation' => false],
    ];
}

/**
 * Overrides the getter, which only takes effect if the render helper resolves it
 * through the controller.
 */
class NavigationOverrideController extends NavigationController
{
    public array $overrideCalls = [];

    public function formGetRecordNavigation($model = null): ?array
    {
        $this->overrideCalls[] = $model ? $model->getKey() : null;

        return ['previous' => 41, 'next' => 43, 'current' => 2, 'total' => 3];
    }
}

class FormControllerRecordNavigationListTest extends PluginTestCase
{
    protected User $inBothLists;

    protected User $archivedOnly;

    public function setUp(): void
    {
        parent::setUp();

        $this->inBothLists = (new UserFixture)->asSuperUser();
        $this->inBothLists->login = 'in-both';
        $this->inBothLists->email = 'in-both@example.com';
        $this->inBothLists->forceSave();

        // In the archive only -- the record with no neighbours in the primary list
        $this->archivedOnly = new UserFixture;
        $this->archivedOnly->login = 'archive-only';
        $this->archivedOnly->email = 'archive-only@example.com';
        $this->archivedOnly->forceSave();

        $this->actingAs((new UserFixture)->asSuperUser());
    }

    public function testThePrimaryListIsUsedByDefault(): void
    {
        $controller = new NavigationController;
        $controller->initForm($this->inBothLists, 'update');

        $navigation = $controller->formGetRecordNavigation($this->inBothLists);

        $this->assertNotNull($navigation);
        $this->assertNotNull($navigation['current'], 'the record should be found in the primary list');
    }

    public function testARecordOutsideThePrimaryListHasNoPositionInIt(): void
    {
        // The behaviour this feature exists to answer: navigation is unavailable
        // because the primary list is a subset that excludes this record
        $controller = new NavigationController;
        $controller->initForm($this->archivedOnly, 'update');

        $navigation = $controller->formGetRecordNavigation($this->archivedOnly);

        $this->assertNull($navigation['current']);
        $this->assertSame('', $controller->formRenderRecordNavigation());
    }

    public function testAContextCanNavigateANamedList(): void
    {
        $controller = new NavigationController;
        $controller->initForm($this->archivedOnly, 'preview');

        $navigation = $controller->formGetRecordNavigation($this->archivedOnly);

        $this->assertNotNull($navigation['current'], 'the archive list contains this record');
        $this->assertSame(
            User::query()->count(),
            $navigation['total'],
            'the total should come from the archive list, not the filtered primary one'
        );
    }

    public function testNavigationCanStillBeDisabled(): void
    {
        $controller = new NavigationDisabledController;
        $controller->initForm($this->inBothLists, 'preview');

        $this->assertNull($controller->formGetRecordNavigation($this->inBothLists));
        $this->assertSame('', $controller->formRenderRecordNavigation());
    }

    public function testTheGetterCanBeOverriddenByTheController(): void
    {
        $controller = new NavigationOverrideController;
        $controller->initForm($this->archivedOnly, 'update');

        // Reached directly ...
        $this->assertSame(2, $controller->formGetRecordNavigation($this->archivedOnly)['current']);

        // ... and, the half that did not work, through the render helper. Without the
        // controller resolving the getter this renders nothing, because the behavior
        // calls its own copy and finds no position in the primary list.
        $this->assertNotSame('', $controller->formRenderRecordNavigation());
        $this->assertNotEmpty($controller->overrideCalls);
    }
}
