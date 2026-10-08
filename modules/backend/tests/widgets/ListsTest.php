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

    /**
     * @dataProvider keyOnlyRecordKeysProvider
     */
    public function testRecordKeysAreReadWithoutTheDisplayColumns(string $sortColumn, ?callable $extendQuery)
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $sql = $this->recordKeysQuery($sortColumn, $extendQuery);

        $this->assertStringStartsWith($this->keyOnlySelect(), $sql);
        $this->assertStringNotContainsString('groups_count', $sql);
    }

    public function keyOnlyRecordKeysProvider(): array
    {
        return [
            'sort by a table column' => ['id', null],
            'where and eager load from an extension' => ['id', fn ($query) => $query->where('is_activated', true)->with('groups')],
            'qualified order from an extension' => ['id', fn ($query) => $query->orderBy('backend_users.email')],
        ];
    }

    /**
     * @dataProvider fullSelectRecordKeysProvider
     */
    public function testRecordKeysKeepTheDisplayColumnsWhenTheQueryReliesOnThem(string $sortColumn, ?callable $extendQuery)
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $sql = $this->recordKeysQuery($sortColumn, $extendQuery);

        $this->assertStringStartsNotWith($this->keyOnlySelect(), $sql);
        $this->assertStringContainsString('groups_count', $sql);
    }

    public function fullSelectRecordKeysProvider(): array
    {
        return [
            'sort by a relation count' => ['groups', null],
            'sort by a select alias' => ['full_name', null],
            'having on an alias from an extension' => ['id', fn ($query) => $query->having('groups_count', '>', 0)],
            'order by an alias from an extension' => ['id', fn ($query) => $query->addSelect(Db::raw('1 as weight'))->orderBy('weight')],
            'raw order from an extension' => ['id', fn ($query) => $query->orderByRaw('last_name is null')],
            'group by from an extension' => ['id', fn ($query) => $query->groupBy('backend_users.id')],
            'distinct from an extension' => ['id', fn ($query) => $query->distinct()],
        ];
    }

    public function testRecordKeysKeepTheDisplayColumnsWhenAGlobalScopeReliesOnThem()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $model = new class extends User {
            protected static function booted()
            {
                static::addGlobalScope('distinct', fn ($query) => $query->distinct());
            }
        };

        $sql = $this->recordKeysQuery('id', null, $model);

        $this->assertStringStartsWith('select distinct', $sql);
        $this->assertStringContainsString('groups_count', $sql);
    }

    /**
     * Returns the SQL of the query Lists::getRecordKeys() runs for the given sort,
     * with an optional `list.extendQuery` handler.
     */
    protected function recordKeysQuery(string $sortColumn, ?callable $extendQuery = null, ?User $model = null): string
    {
        $list = new Lists(null, [
            'model' => $model ?? new User,
            'arrayName' => 'array',
            'defaultSort' => ['column' => $sortColumn, 'direction' => 'desc'],
            'columns' => [
                'id' => [
                    'type' => 'text',
                    'label' => 'ID',
                    'sortable' => true
                ],
                'full_name' => [
                    'label' => 'Name',
                    'select' => 'first_name',
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

        if ($extendQuery) {
            $list->bindEvent('list.extendQuery', $extendQuery);
        }

        $queries = Db::connection()->pretend(fn () => $list->getRecordKeys());

        return end($queries)['query'];
    }

    protected function keyOnlySelect(): string
    {
        return 'select ' . Db::connection()->getQueryGrammar()->wrap((new User)->getQualifiedKeyName()) . ' from';
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
