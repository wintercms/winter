<?php

namespace Cms\Tests\Controllers;

use Backend\Tests\Fixtures\Models\UserFixture;
use Cms\Controllers\Index;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Auth\AuthorizationException;

class IndexPermissionTest extends PluginTestCase
{
    protected Index $controller;

    protected static array $allTypes = ['page', 'partial', 'layout', 'content', 'asset'];

    protected static array $permissionMap = [
        'page'    => 'cms.manage_pages',
        'partial' => 'cms.manage_partials',
        'layout'  => 'cms.manage_layouts',
        'content' => 'cms.manage_content',
        'asset'   => 'cms.manage_assets',
    ];

    //
    // getRelevantPermissionForType mapping
    //

    /**
     * @dataProvider permissionMappingProvider
     */
    public function testGetRelevantPermissionForType(string $type, string $expectedPermission): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        $controller = new Index;

        $result = self::callProtectedMethod($controller, 'getRelevantPermissionForType', [$type]);
        $this->assertEquals($expectedPermission, $result);
    }

    public static function permissionMappingProvider(): array
    {
        return [
            'page maps to cms.manage_pages'       => ['page', 'cms.manage_pages'],
            'partial maps to cms.manage_partials'  => ['partial', 'cms.manage_partials'],
            'layout maps to cms.manage_layouts'    => ['layout', 'cms.manage_layouts'],
            'content maps to cms.manage_content'   => ['content', 'cms.manage_content'],
            'asset maps to cms.manage_assets'      => ['asset', 'cms.manage_assets'],
        ];
    }

    //
    // validateRequestType — allowed access
    //

    /**
     * @dataProvider allowedAccessProvider
     */
    public function testValidateRequestTypeAllowed(string $type, array $permissions): void
    {
        $user = new UserFixture;
        foreach ($permissions as $permission) {
            $user->withPermission($permission, true);
        }
        $this->actingAs($user);
        $controller = new Index;

        // Should not throw
        self::callProtectedMethod($controller, 'validateRequestType', [$type]);
        $this->assertTrue(true);
    }

    public static function allowedAccessProvider(): array
    {
        return [
            'pages user can access page'      => ['page', ['cms.manage_pages']],
            'partials user can access partial' => ['partial', ['cms.manage_partials']],
            'layouts user can access layout'   => ['layout', ['cms.manage_layouts']],
            'content user can access content'  => ['content', ['cms.manage_content']],
            'assets user can access asset'     => ['asset', ['cms.manage_assets']],
            'all permissions can access page'  => ['page', [
                'cms.manage_pages', 'cms.manage_partials', 'cms.manage_layouts',
                'cms.manage_content', 'cms.manage_assets',
            ]],
            'all permissions can access asset' => ['asset', [
                'cms.manage_pages', 'cms.manage_partials', 'cms.manage_layouts',
                'cms.manage_content', 'cms.manage_assets',
            ]],
        ];
    }

    public function testSuperuserCanAccessAllTypes(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        $controller = new Index;

        foreach (self::$allTypes as $type) {
            self::callProtectedMethod($controller, 'validateRequestType', [$type]);
        }
        $this->assertTrue(true);
    }

    //
    // validateRequestType — denied access
    //

    /**
     * @dataProvider deniedAccessProvider
     */
    public function testValidateRequestTypeDenied(string $grantedPermission, string $deniedType): void
    {
        $user = (new UserFixture)->withPermission($grantedPermission, true);
        $this->actingAs($user);
        $controller = new Index;

        $this->expectException(AuthorizationException::class);
        self::callProtectedMethod($controller, 'validateRequestType', [$deniedType]);
    }

    public static function deniedAccessProvider(): array
    {
        $cases = [];
        foreach (self::$permissionMap as $grantedType => $grantedPermission) {
            foreach (self::$allTypes as $targetType) {
                if ($targetType === $grantedType) {
                    continue;
                }
                $cases["$grantedType user denied $targetType"] = [$grantedPermission, $targetType];
            }
        }
        return $cases;
    }

    //
    // Constructor widget registration
    //

    public function testSuperuserGetsAllWidgets(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        $controller = new Index;

        $this->assertNotNull($controller->widget->pageList ?? null, 'pageList should be registered');
        $this->assertNotNull($controller->widget->partialList ?? null, 'partialList should be registered');
        $this->assertNotNull($controller->widget->layoutList ?? null, 'layoutList should be registered');
        $this->assertNotNull($controller->widget->contentList ?? null, 'contentList should be registered');
        $this->assertNotNull($controller->widget->assetList ?? null, 'assetList should be registered');
        $this->assertNotNull($controller->widget->componentList ?? null, 'componentList should be registered');
    }

    public function testPagesOnlyUserGetsOnlyPageList(): void
    {
        $user = (new UserFixture)->withPermission('cms.manage_pages', true);
        $this->actingAs($user);
        $controller = new Index;

        $this->assertNotNull($controller->widget->pageList ?? null, 'pageList should be registered');
        $this->assertNull($controller->widget->partialList ?? null, 'partialList should not be registered');
        $this->assertNull($controller->widget->layoutList ?? null, 'layoutList should not be registered');
        $this->assertNull($controller->widget->contentList ?? null, 'contentList should not be registered');
        $this->assertNull($controller->widget->assetList ?? null, 'assetList should not be registered');
        // Pages user should get componentList since components are usable in pages
        $this->assertNotNull($controller->widget->componentList ?? null, 'componentList should be registered for pages user');
    }

    public function testAssetsOnlyUserGetsOnlyAssetList(): void
    {
        $user = (new UserFixture)->withPermission('cms.manage_assets', true);
        $this->actingAs($user);
        $controller = new Index;

        $this->assertNull($controller->widget->pageList ?? null, 'pageList should not be registered');
        $this->assertNull($controller->widget->partialList ?? null, 'partialList should not be registered');
        $this->assertNull($controller->widget->layoutList ?? null, 'layoutList should not be registered');
        $this->assertNull($controller->widget->contentList ?? null, 'contentList should not be registered');
        $this->assertNotNull($controller->widget->assetList ?? null, 'assetList should be registered');
        $this->assertNull($controller->widget->componentList ?? null, 'componentList should not be registered for assets-only user');
    }

    public function testPagesAndLayoutsUserGetsComponentList(): void
    {
        $user = (new UserFixture)
            ->withPermission('cms.manage_pages', true)
            ->withPermission('cms.manage_layouts', true);
        $this->actingAs($user);
        $controller = new Index;

        $this->assertNotNull($controller->widget->pageList ?? null, 'pageList should be registered');
        $this->assertNull($controller->widget->partialList ?? null, 'partialList should not be registered');
        $this->assertNotNull($controller->widget->layoutList ?? null, 'layoutList should be registered');
        $this->assertNull($controller->widget->contentList ?? null, 'contentList should not be registered');
        $this->assertNull($controller->widget->assetList ?? null, 'assetList should not be registered');
        $this->assertNotNull($controller->widget->componentList ?? null, 'componentList should be registered');
    }

    public function testContentOnlyUserDoesNotGetComponentList(): void
    {
        $user = (new UserFixture)->withPermission('cms.manage_content', true);
        $this->actingAs($user);
        $controller = new Index;

        $this->assertNotNull($controller->widget->contentList ?? null, 'contentList should be registered');
        $this->assertNull($controller->widget->componentList ?? null, 'componentList should not be registered for content-only user');
    }

    //
    // makeTemplateFormWidget — permission checks
    //

    /**
     * @dataProvider deniedAccessProvider
     */
    public function testMakeTemplateFormWidgetDenied(string $grantedPermission, string $deniedType): void
    {
        $user = (new UserFixture)->withPermission($grantedPermission, true);
        $this->actingAs($user);
        $controller = new Index;

        $template = self::callProtectedMethod($controller, 'createTemplate', [$deniedType]);

        $this->expectException(AuthorizationException::class);
        self::callProtectedMethod($controller, 'makeTemplateFormWidget', [$deniedType, $template]);
    }

    /**
     * @dataProvider allowedAccessProvider
     */
    public function testMakeTemplateFormWidgetAllowed(string $type, array $permissions): void
    {
        $user = new UserFixture;
        foreach ($permissions as $permission) {
            $user->withPermission($permission, true);
        }
        $this->actingAs($user);
        $controller = new Index;

        $template = self::callProtectedMethod($controller, 'createTemplate', [$type]);
        $widget = self::callProtectedMethod($controller, 'makeTemplateFormWidget', [$type, $template]);

        $this->assertInstanceOf(\Backend\Widgets\Form::class, $widget);
    }

    public function testMakeTemplateFormWidgetSuperuserAllTypes(): void
    {
        $this->actingAs((new UserFixture)->asSuperUser());
        $controller = new Index;

        foreach (self::$allTypes as $type) {
            $template = self::callProtectedMethod($controller, 'createTemplate', [$type]);
            $widget = self::callProtectedMethod($controller, 'makeTemplateFormWidget', [$type, $template]);
            $this->assertInstanceOf(\Backend\Widgets\Form::class, $widget, "Superuser should be able to create form widget for $type");
        }
    }
}
