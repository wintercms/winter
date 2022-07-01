# Plugin testing

Individual plugin test cases can be run by running either `artisan winter:test -p MyAuthor.MyPlugin` in the project root or  `../../../vendor/bin/phpunit` in the plugin's base directory (ex. `plugins/acme/demo`).

### Creating plugin tests

Plugins can be tested by creating a file called `phpunit.xml` in the base directory with the following content, for example, in a file **/plugins/acme/blog/phpunit.xml**:

    <?xml version="1.0" encoding="UTF-8"?>
    <phpunit backupGlobals="false"
             backupStaticAttributes="false"
             bootstrap="../../../modules/system/tests/bootstrap/app.php"
             colors="true"
             convertErrorsToExceptions="true"
             convertNoticesToExceptions="true"
             convertWarningsToExceptions="true"
             processIsolation="false"
             stopOnFailure="false"
             syntaxCheck="false"
    >
        <testsuites>
            <testsuite name="MyAuthor.MyPlugin Unit Test Suite">
                <directory>./tests</directory>
            </testsuite>
        </testsuites>
        <php>
            <env name="APP_ENV" value="testing"/>
            <env name="CACHE_DRIVER" value="array"/>
            <env name="SESSION_DRIVER" value="array"/>
        </php>
    </phpunit>

Then a **tests/** directory can be created to contain the test classes. The file structure should mimic the base directory with classes having a `Test` suffix. Using a namespace for the class is also recommended.

```php
namespace Acme\Blog\Tests\Models;

use Acme\Blog\Models\Post;
use System\Tests\Bootstrap\PluginTestCase;

class PostTest extends PluginTestCase
{
    public function testCreateFirstPost()
    {
        $post = Post::create(['title' => 'Hi!']);
        $this->assertEquals(1, $post->id);
    }
}
```

The test class should extend the base class `System\Tests\Boostrap\PluginTestCase` and this is a special class that will set up the Winter database stored in memory, as part of the `setUp` method. It will also refresh the plugin being tested, along with any of the defined dependencies in the plugin registration file. This is the equivalent of running the following before each test:

```bash
php artisan winter:up
php artisan plugin:refresh Acme.Blog
[php artisan plugin:refresh <dependency>, ...]
```

> **Note:** If your plugin uses [configuration files](../plugin/settings#file-configuration), then you will need to run `System\Classes\PluginManager::instance()->registerAll(true);` in the `setUp` method of your tests. Below is an example of a base test case class that should be used if you need to test your plugin working with other plugins instead of in isolation.

```php
use System\Classes\PluginManager;
use System\Tests\Bootstrap\PluginTestCase;

class BaseTestCase extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Get the plugin manager
        $pluginManager = PluginManager::instance();

        // Register the plugins to make features like file configuration available
        $pluginManager->registerAll(true);

        // Boot all the plugins to test with dependencies of this plugin
        $pluginManager->bootAll(true);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Get the plugin manager
        $pluginManager = PluginManager::instance();

        // Ensure that plugins are registered again for the next test
        $pluginManager->unregisterAll();
    }
}
```

#### Changing database engine for plugins tests

By default Winter CMS uses SQLite stored in memory for the plugin testing environment. If you want to override the default behavior you can override the `/config/database.php` file by creating `/config/testing/database.php`. In this case, variables from the latter file will be taken.

## Winter core modules testing

To perform unit testing on the core Winter modules run `artisan winter:test -m system -m backend -m cms`.

### Unit tests

Unit tests can be performed by running `vendor/bin/phpunit` in the root directory of your Winter CMS installation.

### Functional tests

Functional tests can be performed by installing the [Winter.Dusk](https://wintercms.com/plugin/winter-dusk) in your Winter CMS installation. The Winter.Dusk plugin is powered by Laravel Dusk, a comprehensive testing suite for the Laravel framework that is designed to test interactions with a fully operational Winter CMS instance through a virtual browser.

For information on installing and setting up your Winter CMS install to run functional tests, please review the [README](https://github.com/wintercms/wn-dusk-plugin/blob/master/README.md) for the plugin.
