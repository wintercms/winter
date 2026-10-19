<?php

namespace Backend\Tests\Behaviors;

use Backend\Facades\BackendAuth;
use Backend\Behaviors\RelationController;
use Backend\Tests\Fixtures\Models\UserFixture;
use System\Tests\Bootstrap\PluginTestCase;

class RelationControllerTest extends PluginTestCase
{
    protected RelationController $behavior;

    public function setUp(): void
    {
        parent::setUp();

        BackendAuth::setUser((new UserFixture)->withPermission('backend.manage_users'));

        $this->behavior = (new \ReflectionClass(RelationController::class))->newInstanceWithoutConstructor();
    }

    public function testDefault(): void
    {
        $this->behavior->setConfig([
            "view" => [
                "toolbarButtons" => "create|delete",
            ],
        ]);

        $expectedText = [
            'create' => "backend::lang.relation.create_name",
            'delete' => "backend::lang.relation.delete",
        ];

        $actualText = static::callProtectedMethod($this->behavior, 'evalToolbarButtons');

        $this->assertEquals($expectedText, $actualText);
    }

    public function testLabels(): void
    {
        $this->behavior->setConfig([
            "view" => [
                "toolbarButtons" => [
                    "create" => "create label",
                    "delete" => "delete label",
                ],
            ],
        ]);

        $expectedText = [
            'create' => "create label",
            'delete' => "delete label",
        ];

        $actualText = static::callProtectedMethod($this->behavior, 'evalToolbarButtons');

        $this->assertEquals($expectedText, $actualText);
    }

    public function testArrayLabels(): void
    {
        $this->behavior->setConfig([
            "view" => [
                "toolbarButtons" => [
                    "create" => [
                        "label" => "create label",
                    ],
                    "delete" => [
                        "label" => "delete label",
                    ],
                ],
            ],
        ]);

        $expectedText = [
            'create' => "create label",
            'delete' => "delete label",
        ];

        $actualText = static::callProtectedMethod($this->behavior, 'evalToolbarButtons');

        $this->assertEquals($expectedText, $actualText);
    }

    public function testMixedLabels(): void
    {
        $this->behavior->setConfig([
            "view" => [
                "toolbarButtons" => [
                    "create" => [
                        "label" => "create label",
                    ],
                    "delete" => "delete label",
                ],
            ],
        ]);

        $expectedText = [
            'create' => "create label",
            'delete' => "delete label",
        ];

        $actualText = static::callProtectedMethod($this->behavior, 'evalToolbarButtons');

        $this->assertEquals($expectedText, $actualText);
    }

    public function testArrayPermissions(): void
    {
        $this->behavior->setConfig([
            "view" => [
                "toolbarButtons" => [
                    "create" => [
                        "permissions" => ["backend.manage_users"],
                    ],
                    "delete" => "delete label",
                ],
            ],
        ]);

        $expectedText = [
            'create' => "backend::lang.relation.create_name",
            'delete' => "delete label",
        ];

        $actualText = static::callProtectedMethod($this->behavior, 'evalToolbarButtons');

        $this->assertEquals($expectedText, $actualText);
    }

    public function testArrayLabelAndPermissions(): void
    {
        $this->behavior->setConfig([
            "view" => [
                "toolbarButtons" => [
                    "create" => [
                        "label" => "create label",
                        "permissions" => ["backend.manage_users"],
                    ],
                    "delete" => "delete label",
                ],
            ],
        ]);

        $expectedText = [
            'create' => "create label",
            'delete' => "delete label",
        ];

        $actualText = static::callProtectedMethod($this->behavior, 'evalToolbarButtons');

        $this->assertEquals($expectedText, $actualText);
    }

    public function testArrayLabelAndMissingPermissions(): void
    {
        $this->behavior->setConfig([
            "view" => [
                "toolbarButtons" => [
                    "create" => [
                        "label" => "create label",
                        "permissions" => ["backend.manage_users"],
                    ],
                    "delete" => [
                        "label" => "delete label",
                        "permissions" => ["some.missing.permission"],
                    ],
                ],
            ],
        ]);

        // delete button is not added since the user does not have the permission
        $expectedText = [
            'create' => "create label",
        ];

        $actualText = static::callProtectedMethod($this->behavior, 'evalToolbarButtons');

        $this->assertEquals($expectedText, $actualText);
    }
}
