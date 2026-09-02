<?php

namespace Backend\Tests\Widgets;

use Db;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Exception\ApplicationException;
use Backend\Tests\Fixtures\Models\UserFixture;
use Backend\Models\User;
use Backend\Widgets\Lists;

class ListsTest extends PluginTestCase
{
    public function testRestrictedColumnWithUserWithNoPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user);

        $list = $this->restrictedListsFixture();
        $list->render();

        $this->assertNotNull($list->getColumn('id'));

        // Expect an exception
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('No definition for column email');
        $column = $list->getColumn('email');
    }

    public function testRestrictedColumnWithUserWithWrongPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.wrong_permission', true));

        $list = $this->restrictedListsFixture();
        $list->render();

        $this->assertNotNull($list->getColumn('id'));

        // Expect an exception
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('No definition for column email');
        $column = $list->getColumn('email');
    }

    public function testRestrictedColumnWithUserWithRightPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.access_field', true));

        $list = $this->restrictedListsFixture();
        $list->render();

        $this->assertNotNull($list->getColumn('id'));
        $this->assertNotNull($list->getColumn('email'));
    }

    public function testRestrictedColumnWithUserWithRightWildcardPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.access_field', true));

        $list = new Lists(null, [
            'model' => new User,
            'arrayName' => 'array',
            'columns' => [
                'id' => [
                    'type' => 'text',
                    'label' => 'ID'
                ],
                'email' => [
                    'type' => 'text',
                    'label' => 'Email',
                    'permission' => 'test.*'
                ]
            ]
        ]);
        $list->render();

        $this->assertNotNull($list->getColumn('id'));
        $this->assertNotNull($list->getColumn('email'));
    }

    public function testRestrictedColumnWithSuperuser()
    {
        $user = new UserFixture;
        $this->actingAs($user->asSuperUser());

        $list = $this->restrictedListsFixture();
        $list->render();

        $this->assertNotNull($list->getColumn('id'));
        $this->assertNotNull($list->getColumn('email'));
    }

    public function testRestrictedColumnSinglePermissionWithUserWithWrongPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.wrong_permission', true));

        $list = $this->restrictedListsFixture(true);
        $list->render();

        $this->assertNotNull($list->getColumn('id'));

        // Expect an exception
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('No definition for column email');
        $column = $list->getColumn('email');
    }

    public function testRestrictedColumnSinglePermissionWithUserWithRightPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.access_field', true));

        $list = $this->restrictedListsFixture(true);
        $list->render();

        $this->assertNotNull($list->getColumn('id'));
        $this->assertNotNull($list->getColumn('email'));
    }

    public function testRecordKeysAreReadWithoutTheDisplayColumns()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $sql = $this->recordKeysQuery(['column' => 'id', 'direction' => 'desc']);
        $key = Db::connection()->getQueryGrammar()->wrap((new User)->getQualifiedKeyName());

        $this->assertStringStartsWith('select ' . $key . ' from', $sql);
        $this->assertStringNotContainsString('groups_count', $sql);
    }

    public function testRecordKeysKeepTheDisplayColumnsTheSortResolvesAgainst()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $sql = $this->recordKeysQuery(['column' => 'groups', 'direction' => 'desc']);

        $this->assertStringContainsString('groups_count', $sql);
    }

    /**
     * Returns the SQL of the query Lists::getRecordKeys() runs for the given sort.
     */
    protected function recordKeysQuery(array $defaultSort): string
    {
        $list = new Lists(null, [
            'model' => new User,
            'arrayName' => 'array',
            'defaultSort' => $defaultSort,
            'columns' => [
                'id' => [
                    'type' => 'text',
                    'label' => 'ID',
                    'sortable' => true
                ],
                'groups' => [
                    'label' => 'Groups',
                    'relation' => 'groups',
                    'useRelationCount' => true,
                    'sortable' => true
                ]
            ]
        ]);

        $sql = '';
        Db::listen(function ($query) use (&$sql) {
            $sql = $query->sql;
        });

        $list->getRecordKeys();

        return $sql;
    }

    protected function restrictedListsFixture(bool $singlePermission = false)
    {
        return new Lists(null, [
            'model' => new User,
            'arrayName' => 'array',
            'columns' => [
                'id' => [
                    'type' => 'text',
                    'label' => 'ID'
                ],
                'email' => [
                    'type' => 'text',
                    'label' => 'Email',
                    'permissions' => ($singlePermission) ? 'test.access_field' : [
                        'test.access_field'
                    ]
                ]
            ]
        ]);
    }
}
