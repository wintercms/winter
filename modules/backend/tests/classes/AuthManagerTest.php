<?php

namespace Backend\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Exception\SystemException;
use Backend\Classes\AuthManager;

class AuthManagerTest extends TestCase
{
    protected AuthManager $instance;
    protected $existingPermissions = [];

    public function setUp(): void
    {
        $this->createApplication();

        $this->instance = AuthManager::instance();

        $this->existingPermissions = $this->instance->listPermissions();

        $this->instance->registerPermissions('Winter.TestCase', [
            'test.permission_one' => [
                'label' => 'Test Permission 1',
                'tab' => 'Test',
                'order' => 200
            ],
            'test.permission_two' => [
                'label' => 'Test Permission 2',
                'tab' => 'Test',
                'order' => 300
            ]
        ]);
    }

    protected function listNewPermissions()
    {
        $existing = collect($this->existingPermissions)->pluck('code')->toArray();
        $allPermissions = collect($this->instance->listPermissions());

        return $allPermissions->whereNotIn('code', $existing)->pluck('code')->toArray();
    }

    public function tearDown(): void
    {
        AuthManager::forgetInstance();
    }

    public function testListPermissions()
    {
        $permissions = $this->listNewPermissions();
        $this->assertCount(2, $permissions);
        $this->assertEquals([
            'test.permission_one',
            'test.permission_two'
        ], $permissions);
    }

    public function testRegisterPermissions()
    {
        $this->instance->registerPermissions('Winter.TestCase', [
            'test.permission_three' => [
                'label' => 'Test Permission 3',
                'tab' => 'Test',
                'order' => 100
            ]
        ]);

        $permissions = $this->listNewPermissions();
        $this->assertCount(3, $permissions);
        $this->assertEquals([
            'test.permission_three',
            'test.permission_one',
            'test.permission_two'
        ], $permissions);
    }

    public function testAliasesPermissions()
    {
        $this->instance->registerPermissionOwnerAlias('Winter.TestCase', 'Aliased.TestCase');

        $permissions = $this->listNewPermissions();
        $this->assertCount(2, $permissions);

        $this->instance->removePermission('Aliased.TestCase', 'test.permission_one');

        $permissions = $this->listNewPermissions();
        $this->assertCount(1, $permissions);
        $this->assertEquals([
            'test.permission_two'
        ], $permissions);
    }

    public function testRegisterPermissionsThroughCallbacks()
    {
        // Callback one
        $this->instance->registerCallback(function ($manager) {
            $manager->registerPermissions('Winter.TestCase', [
                'test.permission_three' => [
                    'label' => 'Test Permission 3',
                    'tab' => 'Test',
                    'order' => 100
                ]
            ]);
        });

        // Callback two
        $this->instance->registerCallback(function ($manager) {
            $manager->registerPermissions('Winter.TestCase', [
                'test.permission_four' => [
                    'label' => 'Test Permission 4',
                    'tab' => 'Test',
                    'order' => 400
                ]
            ]);
        });

        $permissions = $this->listNewPermissions();
        $this->assertCount(4, $permissions);
        $this->assertEquals([
            'test.permission_three',
            'test.permission_one',
            'test.permission_two',
            'test.permission_four'
        ], $permissions);
    }

    public function testRegisterAdditionalTab()
    {
        $this->instance->registerPermissions('Winter.TestCase', [
            'test.permission_three' => [
                'label' => 'Test Permission 3',
                'tab' => 'Test 2',
                'order' => 100
            ]
        ]);

        $this->instance->registerCallback(function ($manager) {
            $manager->registerPermissions('Winter.TestCase', [
                'test.permission_four' => [
                    'label' => 'Test Permission 4',
                    'tab' => 'Test 2',
                    'order' => 400
                ]
            ]);
        });

        $tabs = $this->instance->listTabbedPermissions();

        // Remove the core tabs
        unset($tabs['cms::lang.permissions.name']);
        unset($tabs['system::lang.permissions.name']);

        $this->assertCount(2, $tabs);
        $this->assertEquals([
            'Test 2',
            'Test'
        ], array_keys($tabs));
        $this->assertEquals([
            'test.permission_three',
            'test.permission_four'
        ], collect($tabs['Test 2'])->pluck('code')->toArray());
        $this->assertEquals([
            'test.permission_one',
            'test.permission_two',
        ], collect($tabs['Test'])->pluck('code')->toArray());
    }

    public function testRemovePermission()
    {
        $this->instance->removePermission('Winter.TestCase', 'test.permission_one');

        $permissions = $this->listNewPermissions();
        $this->assertCount(1, $permissions);
        $this->assertEquals([
            'test.permission_two'
        ], $permissions);
    }

    public function testCannotRemovePermissionsBeforeLoaded()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Unable to remove permissions before they are loaded.');

        AuthManager::forgetInstance();
        $this->instance = AuthManager::instance();
        $this->instance->removePermission('Winter.TestCase', 'test.permission_one');
    }
}
