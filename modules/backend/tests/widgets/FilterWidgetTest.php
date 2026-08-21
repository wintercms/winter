<?php

namespace Backend\Tests\Widgets;

use ApplicationException;
use Backend\Models\User;
use Backend\Tests\Fixtures\Models\UserFixture;
use Backend\Widgets\Filter;
use Carbon\Carbon;
use System\Tests\Bootstrap\PluginTestCase;

class FilterWidgetTest extends PluginTestCase
{
    //
    // Permission / scope restriction tests (existing)
    //

    public function testRestrictedScopeWithUserWithNoPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user);

        $filter = $this->restrictedFilterFixture();
        $filter->render();

        $this->assertNotNull($filter->getScope('id'));

        // Expect an exception
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('No definition for scope email');
        $scope = $filter->getScope('email');
    }

    public function testRestrictedScopeWithUserWithWrongPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.wrong_permission', true));

        $filter = $this->restrictedFilterFixture();
        $filter->render();

        $this->assertNotNull($filter->getScope('id'));

        // Expect an exception
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('No definition for scope email');
        $scope = $filter->getScope('email');
    }

    public function testRestrictedScopeWithUserWithRightPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.access_field', true));

        $filter = $this->restrictedFilterFixture();
        $filter->render();

        $this->assertNotNull($filter->getScope('id'));
        $this->assertNotNull($filter->getScope('email'));
    }

    public function testRestrictedScopeWithUserWithRightWildcardPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.access_field', true));

        $filter = new Filter(null, [
            'model' => new User,
            'arrayName' => 'array',
            'scopes' => [
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
        $filter->render();

        $this->assertNotNull($filter->getScope('id'));
        $this->assertNotNull($filter->getScope('email'));
    }

    public function testRestrictedScopeWithSuperuser()
    {
        $user = new UserFixture;
        $this->actingAs($user->asSuperUser());

        $filter = $this->restrictedFilterFixture();
        $filter->render();

        $this->assertNotNull($filter->getScope('id'));
        $this->assertNotNull($filter->getScope('email'));
    }

    public function testRestrictedScopeSinglePermissionWithUserWithWrongPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.wrong_permission', true));

        $filter = $this->restrictedFilterFixture(true);
        $filter->render();

        $this->assertNotNull($filter->getScope('id'));

        // Expect an exception
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('No definition for scope email');
        $scope = $filter->getScope('email');
    }

    public function testRestrictedScopeSinglePermissionWithUserWithRightPermissions()
    {
        $user = new UserFixture;
        $this->actingAs($user->withPermission('test.access_field', true));

        $filter = $this->restrictedFilterFixture(true);
        $filter->render();

        $this->assertNotNull($filter->getScope('id'));
        $this->assertNotNull($filter->getScope('email'));
    }

    //
    // numbersFromAjax() validation tests
    //

    public function testNumbersFromAjaxValidIntegers()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'numberrange', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'numbersFromAjax', [['10', '20']]);
        $this->assertSame([10.0, 20.0], $result);
    }

    public function testNumbersFromAjaxValidFloats()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'numberrange', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'numbersFromAjax', [['10.5', '20.99']]);
        $this->assertSame([10.5, 20.99], $result);
    }

    public function testNumbersFromAjaxValidNegatives()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'numberrange', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'numbersFromAjax', [['-5', '100']]);
        $this->assertSame([-5.0, 100.0], $result);
    }

    public function testNumbersFromAjaxRejectsSqlInjection()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'numberrange', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'numbersFromAjax', [['0', '9999 OR 1=1--']]);
        $this->assertSame([0.0, null], $result);
    }

    public function testNumbersFromAjaxEmptyArray()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'numberrange', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'numbersFromAjax', [[]]);
        $this->assertSame([], $result);
    }

    public function testNumbersFromAjaxScalarNumeric()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'number', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'numbersFromAjax', ['42']);
        $this->assertSame([42.0], $result);
    }

    public function testNumbersFromAjaxScalarInvalid()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'number', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'numbersFromAjax', ['abc']);
        $this->assertSame([], $result);
    }

    //
    // datesFromAjax() validation tests
    //

    public function testDatesFromAjaxValidDates()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'daterange', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'datesFromAjax', [['2024-01-01 00:00:00', '2024-12-31 23:59:59']]);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Carbon::class, $result[0]);
        $this->assertInstanceOf(Carbon::class, $result[1]);
        $this->assertEquals('2024-01-01 00:00:00', $result[0]->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-12-31 23:59:59', $result[1]->format('Y-m-d H:i:s'));
    }

    public function testDatesFromAjaxEmptyBoundaries()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'daterange', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'datesFromAjax', [['', '']]);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Carbon::class, $result[0]);
        $this->assertInstanceOf(Carbon::class, $result[1]);
        $this->assertEquals('0000-01-01 00:00:00', $result[0]->format('Y-m-d H:i:s'));
        $this->assertEquals('2999-12-31 23:59:59', $result[1]->format('Y-m-d H:i:s'));
    }

    public function testDatesFromAjaxRejectsInvalid()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'daterange', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'datesFromAjax', [['not-a-date', '2024-01-01 00:00:00']]);
        $this->assertSame([], $result);
    }

    public function testDatesFromAjaxNull()
    {
        $filter = $this->createFilterWithScope('test', ['type' => 'daterange', 'label' => 'Test']);
        $result = static::callProtectedMethod($filter, 'datesFromAjax', [null]);
        $this->assertSame([], $result);
    }

    //
    // applyScopeToQuery() — numberrange (primary security fix)
    //

    public function testNumberrangeConditionsExecutesQuery()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('id_range', [
            'type' => 'numberrange',
            'label' => 'ID Range',
            'conditions' => 'id >= :min AND id <= :max',
        ]);
        $filter->render();

        $scope = $filter->getScope('id_range');
        $scope->value = [1, 100];

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        $this->assertStringContainsString('id >= ?', $query->toSql());
        $this->assertStringContainsString('id <= ?', $query->toSql());
        $this->assertEquals([1.0, 100.0], $query->getBindings());

        // Actually execute — will throw on binding mismatch
        $this->assertIsInt($query->count());
    }

    public function testNumberrangeConditionsBindingsMatchPlaceholderOrder()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('id_range', [
            'type' => 'numberrange',
            'label' => 'ID Range',
            'conditions' => 'id <= :max AND id >= :min',
        ]);
        $filter->render();

        $scope = $filter->getScope('id_range');
        $scope->value = [1, 100];

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        // :max appears before :min in the conditions, so 100.0 must come first
        $this->assertEquals([100.0, 1.0], $query->getBindings());

        // Actually execute — wrong order would produce incorrect results
        $this->assertIsInt($query->count());
    }

    public function testNumberrangeConditionsWithQuotedPlaceholders()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        // Plugin configs commonly wrap placeholders in SQL quotes: ':min'
        $filter = $this->createFilterWithScope('id_range', [
            'type' => 'numberrange',
            'label' => 'ID Range',
            'conditions' => "id >= ':min' AND id <= ':max'",
        ]);
        $filter->render();

        $scope = $filter->getScope('id_range');
        $scope->value = [1, 100];

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        // Quotes must be stripped so PDO sees ? as a parameter, not '?'
        $this->assertStringContainsString('id >= ?', $query->toSql());
        $this->assertStringNotContainsString("'?'", $query->toSql());
        $this->assertEquals([1.0, 100.0], $query->getBindings());
        $this->assertIsInt($query->count());
    }

    public function testNumberConditionsWithQuotedPlaceholder()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('user_id', [
            'type' => 'number',
            'label' => 'User ID',
            'conditions' => "id = ':filtered'",
        ]);
        $filter->render();

        $scope = $filter->getScope('user_id');
        $scope->value = 1;

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        $this->assertStringContainsString('id = ?', $query->toSql());
        $this->assertStringNotContainsString("'?'", $query->toSql());
        $this->assertEquals([1.0], $query->getBindings());
        $this->assertIsInt($query->count());
    }

    public function testNumberrangeConditionsNullMin()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('id_range', [
            'type' => 'numberrange',
            'label' => 'ID Range',
            'conditions' => 'id >= :min AND id <= :max',
        ]);
        $filter->render();

        $scope = $filter->getScope('id_range');
        $scope->value = [null, 100];

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        $this->assertEquals([-2147483647, 100.0], $query->getBindings());
        $this->assertIsInt($query->count());
    }

    public function testNumberrangeConditionsNullMax()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('id_range', [
            'type' => 'numberrange',
            'label' => 'ID Range',
            'conditions' => 'id >= :min AND id <= :max',
        ]);
        $filter->render();

        $scope = $filter->getScope('id_range');
        $scope->value = [1, null];

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        $this->assertEquals([1.0, 2147483647], $query->getBindings());
        $this->assertIsInt($query->count());
    }

    public function testNumberrangeEmptyValue()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('id_range', [
            'type' => 'numberrange',
            'label' => 'ID Range',
            'conditions' => 'id >= :min AND id <= :max',
        ]);
        $filter->render();

        $scope = $filter->getScope('id_range');
        $scope->value = null;

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        $this->assertEmpty($query->getBindings());
    }

    //
    // applyScopeToQuery() — number (defense-in-depth)
    //

    public function testNumberConditionsExecutesQuery()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('user_id', [
            'type' => 'number',
            'label' => 'User ID',
            'conditions' => 'id = :filtered',
        ]);
        $filter->render();

        $scope = $filter->getScope('user_id');
        $scope->value = 1;

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        $this->assertStringContainsString('id = ?', $query->toSql());
        $this->assertEquals([1.0], $query->getBindings());
        $this->assertIsInt($query->count());
    }

    public function testNumberNonNumericValueIgnored()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('user_id', [
            'type' => 'number',
            'label' => 'User ID',
            'conditions' => 'id = :filtered',
        ]);
        $filter->render();

        $scope = $filter->getScope('user_id');
        $scope->value = 'abc';

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        $this->assertEmpty($query->getBindings());
    }

    //
    // applyScopeToQuery() — date (defense-in-depth)
    //

    public function testDateConditionsExecutesQuery()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('created', [
            'type' => 'date',
            'label' => 'Created',
            'conditions' => 'created_at >= :after AND created_at <= :before',
        ]);
        $filter->render();

        $scope = $filter->getScope('created');
        $scope->value = Carbon::create(2024, 6, 15, 0, 0, 0);

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        $this->assertStringContainsString('created_at >= ?', $query->toSql());
        $this->assertStringContainsString('created_at <= ?', $query->toSql());
        $this->assertCount(2, $query->getBindings());
        $this->assertEquals('2024-06-15 00:00:00', $query->getBindings()[0]);
        $this->assertEquals('2024-06-15 23:59:00', $query->getBindings()[1]);
        $this->assertIsInt($query->count());
    }

    //
    // applyScopeToQuery() — daterange (defense-in-depth)
    //

    public function testDaterangeConditionsExecutesQuery()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('created_range', [
            'type' => 'daterange',
            'label' => 'Created',
            'conditions' => 'created_at >= :after AND created_at <= :before',
        ]);
        $filter->render();

        $scope = $filter->getScope('created_range');
        $scope->value = [
            Carbon::create(2024, 1, 1, 0, 0, 0),
            Carbon::create(2024, 12, 31, 23, 59, 59),
        ];

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        $this->assertStringContainsString('created_at >= ?', $query->toSql());
        $this->assertStringContainsString('created_at <= ?', $query->toSql());
        $this->assertCount(2, $query->getBindings());
        $this->assertEquals('2024-01-01 00:00:00', $query->getBindings()[0]);
        $this->assertEquals('2024-12-31 23:59:59', $query->getBindings()[1]);
        $this->assertIsInt($query->count());
    }

    //
    // applyScopeToQuery() — text (regression test, existing behavior)
    //

    public function testTextConditionsExecutesQuery()
    {
        $this->actingAs((new UserFixture)->asSuperUser());

        $filter = $this->createFilterWithScope('name', [
            'type' => 'text',
            'label' => 'Name',
            'conditions' => 'first_name LIKE :value',
        ]);
        $filter->render();

        $scope = $filter->getScope('name');
        $scope->value = '%test%';

        $query = (new User)->newQuery();
        $filter->applyScopeToQuery($scope, $query);

        // Text scope uses PDO::quote() inline rather than bindings
        $this->assertStringContainsString('first_name LIKE', $query->toSql());
        $this->assertIsInt($query->count());
    }

    //
    // Helpers
    //

    protected function restrictedFilterFixture(bool $singlePermission = false)
    {
        return new Filter(null, [
            'model' => new User,
            'arrayName' => 'array',
            'scopes' => [
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

    protected function createFilterWithScope(string $name, array $scopeConfig): Filter
    {
        return new Filter(null, [
            'model' => new User,
            'arrayName' => 'array',
            'scopes' => [$name => $scopeConfig],
        ]);
    }
}
