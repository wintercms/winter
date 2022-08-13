<?php

namespace System\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use Cms\Classes\Theme;
use System\Classes\CombineAssets;

class CombineAssetsTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        CombineAssets::resetCache();
    }

    //
    // Tests
    //

    public function testCombiner()
    {
        $combiner = CombineAssets::instance();

        /*
         * Supported file extensions should exist
         */
        $jsExt = $cssExt = self::getProtectedProperty($combiner, 'jsExtensions');
        $this->assertIsArray($jsExt);

        $cssExt = self::getProtectedProperty($combiner, 'cssExtensions');
        $this->assertIsArray($cssExt);

        /*
         * Check service methods
         */
        $this->assertTrue(method_exists($combiner, 'combine'));
        $this->assertTrue(method_exists($combiner, 'resetCache'));
    }

    public function testCombine()
    {
        $combiner = CombineAssets::instance();

        $url = $combiner->combine(
            [
                'assets/css/style1.css',
                'assets/css/style2.css'
            ],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $this->assertRegExp('/\w+[-]\d+/i', $url); // Must contain hash-number

        $url = $combiner->combine(
            [
                'assets/js/script1.js',
                'assets/js/script2.js'
            ],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $this->assertRegExp('/\w+[-]\d+/i', $url); // Must contain hash-number
    }

    public function testPutCache()
    {
        $sampleId = md5('testhash');
        $sampleStore = ['version' => 12345678];
        $samplePath = '/tests/fixtures/Cms/themes/test';

        $combiner = CombineAssets::instance();
        $value = self::callProtectedMethod($combiner, 'putCache', [$sampleId, $sampleStore]);

        $this->assertTrue($value);
    }

    public function testGetTargetPath()
    {
        $combiner = CombineAssets::instance();

        $value = self::callProtectedMethod($combiner, 'getTargetPath', ['/combine']);
        $this->assertEquals('combine/', $value);

        $value = self::callProtectedMethod($combiner, 'getTargetPath', ['/index.php/combine']);
        $this->assertEquals('index-php/combine/', $value);
    }

    public function testMakeCacheId()
    {
        $sampleResources = ['assets/css/style1.css', 'assets/css/style2.css'];
        $samplePath = base_path() . '/modules/system/tests/fixtures/cms/themes/test';

        $combiner = CombineAssets::instance();
        self::setProtectedProperty($combiner, 'localPath', $samplePath);

        $value = self::callProtectedMethod($combiner, 'getCacheKey', [$sampleResources]);
        $this->assertEquals(md5($samplePath.implode('|', $sampleResources)), $value);
    }

    public function testResetCache()
    {
        $combiner = CombineAssets::instance();
        $this->assertNull($combiner->resetCache());
    }

    public function testAliasesResolving()
    {
        // Few temporary helpers to avoid typos
        $assertContainsStyle1 = $assertContainsFrameworkCss =
            fn ($response) => $this->assertStringContainsString('.style-1', $response->getContent());
        $assertContainsScript1 = $assertContainsFrameworkJs =
            fn ($response) => $this->assertStringContainsString('script1.js', $response->getContent());
        $assertContainsStyle2 = fn ($response) => $this->assertStringContainsString('.style-2', $response->getContent());
        $assertContainsScript2 = fn ($response) => $this->assertStringContainsString('script2.js', $response->getContent());

        $combiner = CombineAssets::instance();

        // Clear the system registered aliases
        $combiner->resetAliases();

        $combiner->registerAlias('css1', '~/modules/system/tests/fixtures/themes/test/assets/css/style1.css');
        $combiner->registerAlias('css2', '~/modules/system/tests/fixtures/themes/test/assets/css/style2.css');
        $combiner->registerAlias('framework', '~/modules/system/tests/fixtures/themes/test/assets/css/style1.css');
        $combiner->registerAlias('js1', '~/modules/system/tests/fixtures/themes/test/assets/js/script1.js');
        $combiner->registerAlias('js2', '~/modules/system/tests/fixtures/themes/test/assets/js/script2.js');
        $combiner->registerAlias('framework', '~/modules/system/tests/fixtures/themes/test/assets/js/script1.js');

        // 0 aliases
        $url = $combiner->combine(
            ['assets/css/style1.css'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $response = $this->get($url);
        $assertContainsStyle1($response);

        // 1 css
        $url = $combiner->combine(
            ['@css1'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $response = $this->get($url);
        $assertContainsStyle1($response);

        // 1 js
        $url = $combiner->combine(
            ['@js1'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $response = $this->get($url);
        $assertContainsScript1($response);

        // 2 css 1 js
        $url = $combiner->combine(
            ['@css1', '@css2', '@js1'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $response = $this->get($url);
        $assertContainsStyle1($response);
        $assertContainsStyle2($response);
        $this->assertStringNotContainsString('script1.js', $response->getContent());

        // 1 css 2 js
        $url = $combiner->combine(
            ['@css1', '@js1', '@js2'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $response = $this->get($url);
        $assertContainsScript1($response);
        $assertContainsScript2($response);
        $this->assertStringNotContainsString('.style-1', $response->getContent());

        // 2 css 2 js
        $this->expectErrorMessage(trans('system::lang.combiner.cant_guess_extension'));
        $combiner->combine(
            ['@css1', '@css2', '@js1', '@js2'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );

        // 1 combined
        $url = $combiner->combine(
            ['@framework'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $response = $this->get($url);
        $this->expectErrorMessage(trans('system::lang.combiner.cant_guess_extension'));

        // 1 combined 1 css
        $url = $combiner->combine(
            ['@framework', '@css2'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $response = $this->get($url);
        $assertContainsFrameworkCss($response);
        $assertContainsStyle2($response);

        // 1 combined 1 js
        $url = $combiner->combine(
            ['@framework', '@js2'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
        $this->assertNotNull($url);
        $response = $this->get($url);
        $assertContainsFrameworkJs($response);
        $assertContainsScript2($response);

        // 1 combined 1 js 1 css
        $this->expectErrorMessage(trans('system::lang.combiner.cant_guess_extension'));
        $combiner->combine(
            ['@framework', '@js2', '@css2'],
            base_path() . '/modules/system/tests/fixtures/themes/test'
        );
    }
}
