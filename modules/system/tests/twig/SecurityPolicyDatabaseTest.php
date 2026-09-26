<?php

namespace System\Tests\Twig;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Cms\Models\ThemeData;
use Illuminate\Support\Facades\DB;
use System\Tests\Bootstrap\PluginTestCase;
use System\Tests\Fixtures\Twig\SandboxCanary;
use Twig\Sandbox\SecurityNotAllowedMethodError;
use Winter\Storm\Database\QueryBuilder;

/**
 * Covers the parts of the Twig security policy that can only be shown against a live database:
 * that a sandboxed template cannot read or write a table the model does not own, and that the
 * read-only query surface a real theme uses still works.
 *
 * `this.theme.getCustomData()` is the receiver the tests use because it needs no cooperation
 * from the site's developer — every template can reach it.
 */
class SecurityPolicyDatabaseTest extends PluginTestCase
{
    /**
     * @var string Canary value planted in a table cms_theme_data does not own.
     */
    protected const FOREIGN_SECRET = 'CANARY_PASSWORD_HASH';

    protected $theme;

    public function setUp(): void
    {
        parent::setUp();

        DB::table('backend_users')->where('id', 1)->update(['password' => static::FOREIGN_SECRET]);

        ThemeData::flushCache();
        $this->theme = new Theme();
        $this->theme->setDirName('test');
        $this->theme->getCustomData()->save();
    }

    public function tearDown(): void
    {
        ThemeData::flushCache();
        parent::tearDown();
    }

    // A join reads every column of the joined table, which is the same outcome as the
    // already-blocked setTable()/from() family.
    public function testCannotReadAForeignTableByJoiningIt()
    {
        $this->expectException(SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {%- for row in this.theme.getCustomData().newQuery()
                .join("backend_users", "backend_users.id", "=", "cms_theme_data.id").get() -%}
                {{- row.login }}:{{ row.password -}}
            {%- endfor -%}
        ');
    }

    public function testCannotReadAForeignTableByCrossJoiningIt()
    {
        $this->expectException(SecurityNotAllowedMethodError::class);
        $this->renderTwigInCmsController('
            {%- for row in this.theme.getCustomData().newQuery().crossJoin("backend_users").get() -%}
                {{- row.password -}}
            {%- endfor -%}
        ');
    }

    // fromQuery() hands its argument to Connection::select(), which executes the statement
    // before fetching, so it covers writes as well as reads.
    public function testCannotRunArbitrarySqlThroughFromQuery()
    {
        $this->assertBlockedAndUnchanged('
            {%- for row in this.theme.getCustomData().newQuery()
                .fromQuery("select \'test\' as theme, password as leaked from backend_users") -%}
                {{- row.leaked -}}
            {%- endfor -%}
        ');
    }

    public function testCannotWriteToAForeignTableThroughFromQuery()
    {
        $this->assertBlockedAndUnchanged('
            {%- set _ = this.theme.getCustomData().newQuery()
                .fromQuery("update backend_users set password = \'PWNED\'") -%}
        ');
    }

    // The where clause array is compiled by its 'type' key, and Twig can write that array.
    public function testCannotInjectRawSqlThroughMergeWheres()
    {
        $this->assertBlockedAndUnchanged('
            {%- for row in this.theme.getCustomData().newQuery().mergeWheres([{
                type: "Raw",
                sql: "(select count(*) from backend_users) > 0",
                boolean: "and"
            }], []).get() -%}{{- row.id -}}{%- endfor -%}
        ');
    }

    // The aggregate function name is emitted verbatim by Grammar::compileAggregate.
    public function testCannotInjectRawSqlThroughAggregate()
    {
        $this->assertBlockedAndUnchanged('
            {{- this.theme.getCustomData().newQuery()
                .aggregate("(select password from backend_users limit 1) || count") -}}
        ');
    }

    // Unlike where(), whereRowValues() never validates its operator.
    public function testCannotInjectRawSqlThroughWhereRowValues()
    {
        $this->assertBlockedAndUnchanged('
            {%- for row in this.theme.getCustomData().newQuery().whereRowValues(
                ["id"], "= 1 or (select count(*) from backend_users) > 0 or ? =", [999]
            ).get() -%}{{- row.id -}}{%- endfor -%}
        ');
    }

    // Storm's selectConcat() wraps a non-identifier part in a quoted literal without escaping it.
    public function testCannotInjectRawSqlThroughSelectConcat()
    {
        $this->assertBlockedAndUnchanged('
            {{- this.theme.getCustomData().newQuery()
                .selectConcat(["x\' || (select password from backend_users limit 1) || \'z"], "leak")
                .first().leak -}}
        ');
    }

    // Storm's search helper interpolates each column name into a raw expression.
    public function testCannotInjectRawSqlThroughSearchWhere()
    {
        $this->assertBlockedAndUnchanged('
            {%- for row in this.theme.getCustomData().newQuery().searchWhere(
                "CANARY", ["(select password from backend_users limit 1)"], "exact"
            ).get() -%}{{- row.id -}}{%- endfor -%}
        ');
    }

    // beforeQuery() takes any callable and applyBeforeQueryCallbacks() invokes it on compile.
    public function testCannotExecuteACallableThroughBeforeQuery()
    {
        SandboxCanary::reset();

        $this->assertBlockedAndUnchanged(sprintf(
            '{%%- set _ = this.theme.getCustomData().newQuery().beforeQuery(\'%s::invoke\').toSql() -%%}',
            SandboxCanary::class
        ));

        $this->assertEquals(0, SandboxCanary::$invocations);
    }

    // A "Class:arg1,arg2" cast is instantiated when the attribute is read.
    public function testCannotInstantiateAnArbitraryClassThroughWithCasts()
    {
        SandboxCanary::reset();

        $this->assertBlockedAndUnchanged(sprintf(
            '{{- this.theme.getCustomData().newQuery().withCasts({ theme: \'%s:a,b\' }).first().theme -}}',
            SandboxCanary::class
        ));

        $this->assertEquals(0, SandboxCanary::$instantiations);
    }

    // The query builder's public macroCall() dispatches on the macro name it is handed, so the
    // policy only ever sees "macrocall" and every registered macro would be reachable through it.
    public function testCannotDispatchAMacroThroughMacroCall()
    {
        SandboxCanary::reset();

        QueryBuilder::macro('securityPolicyTestMacro', function () {
            SandboxCanary::$invocations++;
            return $this;
        });

        try {
            $this->assertBlockedAndUnchanged('
                {%- set _ = this.theme.getCustomData().newQuery().getQuery()
                    .macroCall("securityPolicyTestMacro", []) -%}
            ');
            $this->assertEquals(0, SandboxCanary::$invocations);
        } finally {
            QueryBuilder::flushMacros();
        }
    }

    //
    // Invalidation: the read-only query surface a real theme uses must keep working. In
    // particular count()/sum()/avg() go through the now-blocked aggregate() internally, in PHP,
    // where the policy does not apply.
    //

    public function testReadOnlyQueriesOnTheModelsOwnTableStillWork()
    {
        $result = trim($this->renderTwigInCmsController('
            {{- this.theme.getCustomData().newQuery().count() -}}|
            {{- this.theme.getCustomData().newQuery().where("theme", "test").count() -}}|
            {{- this.theme.getCustomData().newQuery().orderBy("id", "desc").first().theme -}}|
            {{- this.theme.getCustomData().newQuery().sum("id") -}}|
            {{- this.theme.getCustomData().newQuery().pluck("theme").join(",") -}}|
            {{- this.theme.getCustomData().newQuery().where("theme", "nope").exists() ? "y" : "n" -}}
        '));

        $this->assertEquals('1|1|test|1|test|n', $result);
    }

    public function testPaginatingTheModelsOwnTableStillWorks()
    {
        $result = trim($this->renderTwigInCmsController('
            {{- this.theme.getCustomData().newQuery().paginate(10).total() -}}|
            {%- for row in this.theme.getCustomData().newQuery().paginate(10) -%}
                {{- row.theme -}}
            {%- endfor -%}
        '));

        $this->assertEquals('1|test', $result);
    }

    /**
     * Asserts the template is refused by the sandbox and that the foreign table is untouched.
     */
    protected function assertBlockedAndUnchanged(string $source): void
    {
        try {
            $output = $this->renderTwigInCmsController($source);
            $this->fail('The sandbox allowed the call, and rendered: ' . trim($output));
        } catch (SecurityNotAllowedMethodError $e) {
            // Expected
        }

        $this->assertEquals(
            static::FOREIGN_SECRET,
            DB::table('backend_users')->where('id', 1)->value('password')
        );
    }

    protected function renderTwigInCmsController(string $source, array $vars = [])
    {
        $controller = new Controller();
        $twig = $controller->getTwig();
        $template = $twig->createTemplate($source, 'test.case');

        return $twig->render($template, [
            'this' => array_merge($controller->getControllerGlobalVars(), [
                'theme' => $this->theme,
            ]),
        ] + $vars);
    }
}
