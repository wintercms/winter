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
            'create' => ['create', 'onRelationManageCreate'],
            'update' => ['update', 'onRelationManageUpdate'],
            'delete' => ['delete', 'onRelationManageDelete'],
            'add' => ['add', 'onRelationManageAdd'],
            'add with pivot' => ['add', 'onRelationManagePivotCreate'],
            'link' => ['link', 'onRelationManageAdd'],
            'remove' => ['remove', 'onRelationManageRemove'],
            'unlink' => ['unlink', 'onRelationManageRemove'],
            'delete via button' => ['delete', 'onRelationButtonDelete'],
        ];
    }

    /**
     * @dataProvider guardedHandlerProvider
     */
    public function testHandlerIsDeniedWithoutTheButtonPermissions(string $button, string $handler): void
    {
        $this->actAsUserWith(['acme.blog.manage_comments' => 0]);
        $this->setToolbarButtons([$button => ['permissions' => 'acme.blog.manage_comments']]);

        $this->assertSame(403, $this->statusOf($handler));
    }

    /**
     * @dataProvider guardedHandlerProvider
     */
    public function testHandlerIsAllowedWithTheButtonPermissions(string $button, string $handler): void
    {
        $this->actAsUserWith(['acme.blog.manage_comments' => 1]);
        $this->setToolbarButtons([$button => ['permissions' => 'acme.blog.manage_comments']]);

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
