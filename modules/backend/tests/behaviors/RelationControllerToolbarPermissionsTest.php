<?php

namespace Backend\Tests\Behaviors;

use Backend\Behaviors\RelationController;
use Backend\Tests\Fixtures\Models\UserFixture;
use Symfony\Component\HttpKernel\Exception\HttpException;
use System\Tests\Bootstrap\PluginTestCase;

class RelationControllerToolbarPermissionsTest extends PluginTestCase
{
    protected RelationController $behavior;

    public function setUp(): void
    {
        parent::setUp();

        $this->behavior = (new \ReflectionClass(RelationController::class))->newInstanceWithoutConstructor();

        // Skip the controller bootstrapping in beforeAjax(), the guards only need the config
        $initialized = new \ReflectionProperty(RelationController::class, 'initialized');
        $initialized->setAccessible(true);
        $initialized->setValue($this->behavior, true);
    }

    protected function setToolbarButtons($buttons): void
    {
        $config = new \ReflectionProperty(RelationController::class, 'config');
        $config->setAccessible(true);
        $config->setValue($this->behavior, (object) ['view' => ['toolbarButtons' => $buttons]]);
    }

    protected function setViewMode(string $viewMode): void
    {
        $property = new \ReflectionProperty(RelationController::class, 'viewMode');
        $property->setAccessible(true);
        $property->setValue($this->behavior, $viewMode);
    }

    protected function actAsUserWith(array $permissions): void
    {
        $this->actingAs((new UserFixture)->withPermission($permissions));
    }

    protected function evalToolbarButtons(): ?array
    {
        return static::callProtectedMethod($this->behavior, 'evalToolbarButtons');
    }

    protected function statusOf(string $handler): ?int
    {
        try {
            $this->behavior->$handler();
        } catch (HttpException $ex) {
            return $ex->getStatusCode();
        } catch (\Throwable $ex) {
            // Past the guard, the handler fails on the widgets this test does not build
            return null;
        }

        return null;
    }

    public function testButtonsWithoutPermissionsAreUnaffected(): void
    {
        $this->actAsUserWith(['acme.blog.delete_comments' => 0]);
        $this->setToolbarButtons('create|delete');

        $this->assertSame(
            ['create' => 'backend::lang.relation.create_name', 'delete' => 'backend::lang.relation.delete'],
            $this->evalToolbarButtons()
        );
    }

    public function testButtonsAcceptArrayConfig(): void
    {
        $this->setToolbarButtons([
            'create' => ['label' => 'Add comment'],
            'update' => [],
            'delete' => 'Delete comments',
            'add',
        ]);

        $this->assertSame(
            [
                'create' => 'Add comment',
                'update' => 'backend::lang.relation.update_name',
                'delete' => 'Delete comments',
                'add' => 'backend::lang.relation.add_name',
            ],
            $this->evalToolbarButtons()
        );
    }

    public function testCustomButtonTypeFallsBackToItsName(): void
    {
        $this->actAsUserWith(['acme.blog.export' => 1]);
        $this->setToolbarButtons(['create', 'export', 'publish' => ['permissions' => 'acme.blog.export']]);

        $this->assertSame(
            ['create' => 'backend::lang.relation.create_name', 'export' => 'export', 'publish' => 'publish'],
            $this->evalToolbarButtons()
        );
    }

    public function testButtonIsHiddenWithoutItsPermissions(): void
    {
        $this->actAsUserWith(['acme.blog.delete_comments' => 0]);
        $this->setToolbarButtons([
            'create' => 'Add comment',
            'delete' => ['permissions' => 'acme.blog.delete_comments'],
        ]);

        $this->assertSame(['create' => 'Add comment'], $this->evalToolbarButtons());
    }

    public function testButtonIsShownWithAnyOfItsPermissions(): void
    {
        $this->actAsUserWith(['acme.blog.moderate' => 1]);
        $this->setToolbarButtons([
            'delete' => [
                'label' => 'Delete comments',
                'permissions' => ['acme.blog.delete_comments', 'acme.blog.moderate'],
            ],
            'remove' => ['permissions' => ['acme.blog.moderate']],
        ]);

        $this->assertSame(
            ['delete' => 'Delete comments', 'remove' => 'backend::lang.relation.remove'],
            $this->evalToolbarButtons()
        );
    }

    public static function guardedHandlerProvider(): array
    {
        return [
            'create' => ['create', 'onRelationManageCreate', 'multi'],
            'update' => ['update', 'onRelationManageUpdate', 'multi'],
            'delete' => ['delete', 'onRelationManageDelete', 'multi'],
            'add' => ['add', 'onRelationManageAdd', 'multi'],
            'add with pivot' => ['add', 'onRelationManagePivotCreate', 'multi'],
            'link' => ['link', 'onRelationManageAdd', 'single'],
            'remove' => ['remove', 'onRelationManageRemove', 'multi'],
            'unlink' => ['unlink', 'onRelationManageRemove', 'single'],
            'delete via button' => ['delete', 'onRelationButtonDelete', 'multi'],
        ];
    }

    /**
     * @dataProvider guardedHandlerProvider
     */
    public function testHandlerIsDeniedWithoutTheButtonPermissions(string $button, string $handler, string $viewMode): void
    {
        $this->actAsUserWith(['acme.blog.manage_comments' => 0]);
        $this->setToolbarButtons([$button => ['permissions' => 'acme.blog.manage_comments']]);
        $this->setViewMode($viewMode);

        $this->assertSame(403, $this->statusOf($handler));
    }

    /**
     * @dataProvider guardedHandlerProvider
     */
    public function testHandlerIsAllowedWithTheButtonPermissions(string $button, string $handler, string $viewMode): void
    {
        $this->actAsUserWith(['acme.blog.manage_comments' => 1]);
        $this->setToolbarButtons([$button => ['permissions' => 'acme.blog.manage_comments']]);
        $this->setViewMode($viewMode);

        $this->assertNull($this->statusOf($handler));
    }

    public static function otherViewModeProvider(): array
    {
        return [
            'add does not guard link' => ['add', 'onRelationManageAdd', 'single'],
            'link does not guard add' => ['link', 'onRelationManageAdd', 'multi'],
            'remove does not guard unlink' => ['remove', 'onRelationManageRemove', 'single'],
            'unlink does not guard remove' => ['unlink', 'onRelationManageRemove', 'multi'],
        ];
    }

    /**
     * @dataProvider otherViewModeProvider
     */
    public function testHandlerIsAllowedWhenTheOtherViewModeButtonIsRestricted(string $button, string $handler, string $viewMode): void
    {
        $this->actAsUserWith(['acme.blog.manage_comments' => 0]);
        $this->setToolbarButtons([$button => ['permissions' => 'acme.blog.manage_comments']]);
        $this->setViewMode($viewMode);

        $this->assertNull($this->statusOf($handler));
    }

    public function testHandlerIsAllowedWhenOtherButtonsAreRestricted(): void
    {
        $this->actAsUserWith(['acme.blog.delete_comments' => 0]);
        $this->setToolbarButtons(['create', 'delete' => ['permissions' => 'acme.blog.delete_comments']]);

        $this->assertNull($this->statusOf('onRelationManageCreate'));
        $this->assertSame(403, $this->statusOf('onRelationManageDelete'));
    }
}
