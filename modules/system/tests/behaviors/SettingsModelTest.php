<?php

namespace System\Tests\Behaviors;

use System\Behaviors\SettingsModel;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;

/**
 * @testdox Settings model behavior (System\Behaviors\SettingsModel)
 * @author Ben Thomson <git@alfreido.com>
 * @copyright Winter CMS Maintainers
 */
class SettingsModelTest extends PluginTestCase
{
    public function tearDown(): void
    {
        MyShopSettings::instance()->resetDefault();
    }

    public function testCanDetermineIfSettingsModelIsConfigured()
    {
        $settings = MyShopSettings::instance();

        $this->assertFalse($settings->isConfigured());

        $settings->name = 'My shop';
        $settings->description = 'This is my shop';
        $settings->save();

        $this->assertTrue($settings->isConfigured());
    }

    public function testCanInitializeSettingsData()
    {
        // Configure settings
        $settings = MyShopSettings::instance();
        $settings->save();

        MyShopSettings::clearInternalCache();

        // Retrieve settings
        $settings = MyShopSettings::instance();
        $this->assertEquals('Shop name', $settings->name);
        $this->assertEquals('This is a shop', $settings->description);
        $this->assertFalse($settings->is_open);
        $this->assertEquals(10000, $settings->base_price);
        $this->assertEquals(['Food', 'Drinks', 'Clothing', 'Electronics'], $settings->categories);
    }

    public function testCanGetAndSetSettingsData()
    {
        $settings = MyShopSettings::instance();

        // Set settings as model attributes
        $settings->name = 'My shop';
        $settings->save();

        // Set settings through the set() method
        MyShopSettings::set('description', 'This is my shop');
        MyShopSettings::set([
            'is_open' => true,
            'base_price' => 250,
        ]);
        MyShopSettings::set('categories', ['Party Food', 'Party Drinks', 'Party Entertainment']);

        // Retrieve settings
        $settings = MyShopSettings::instance();

        // Retrieve settings through instance
        $this->assertEquals('My shop', $settings->name);
        $this->assertEquals('This is my shop', $settings->description);

        // Retrieve settings through get() method
        $this->assertTrue(MyShopSettings::get('is_open'));
        $this->assertEquals(25000, MyShopSettings::get('base_price'));
        $this->assertEquals(['Party Food', 'Party Drinks', 'Party Entertainment'], MyShopSettings::get('categories'));

        // Retrieve default value for a non-existent setting
        $this->assertEquals('default', MyShopSettings::get('non_existent_setting', 'default'));
    }

    /**
     * This is similar to the `testCanInitializeSettingsData` test, except that it does not commit the settings to the
     * database initially. We should still be able to retrieve the settings defined in `initSettingsData`.
     */
    public function testCanGetDefaultSettings()
    {
        $settings = MyShopSettings::instance();

        // Retrieve settings through instance
        $this->assertEquals('Shop name', $settings->name);
        $this->assertEquals('This is a shop', $settings->description);

        // Retrieve settings through get() method
        $this->assertFalse(MyShopSettings::get('is_open'));
        $this->assertEquals(10000, MyShopSettings::get('base_price'));
        $this->assertEquals(['Food', 'Drinks', 'Clothing', 'Electronics'], MyShopSettings::get('categories'));

        // Retrieve default value for a non-existent setting
        $this->assertEquals('default', MyShopSettings::get('non_existent_setting', 'default'));
    }
}

/**
 * @mixin \System\Behaviors\SettingsModel
 */
class MyShopSettings extends Model
{
    public $implement = [
        SettingsModel::class,
    ];

    public $settingsCode = 'my_shop';

    public $settingsFields = 'fields.yaml';

    public $settingsCacheTtl = 1440;

    public function initSettingsData()
    {
        $this->name = 'Shop name';
        $this->description = 'This is a shop';
        $this->is_open = false;
        $this->base_price = 100;
        $this->categories = ['Food', 'Drinks', 'Clothing', 'Electronics'];
    }

    public function setBasePriceAttribute($value)
    {
        $this->attributes['base_price'] = $value * 100;
    }
}
