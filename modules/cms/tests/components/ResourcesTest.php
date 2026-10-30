<?php

namespace Cms\Tests\Components;

use Cms\Components\Resources;
use System\Tests\Bootstrap\TestCase;

class ResourcesTest extends TestCase
{
    /**
     * @var Resources
     */
    protected $component;

    public function setUp(): void
    {
        parent::setUp();

        $this->component = new Resources();
    }

    /**
     * Each property names a file in the asset subdirectory the property documents, and the
     * value used to be concatenated onto that directory exactly as written, so a `..` in
     * one named a file outside the theme for the combiner to read.
     */
    public function testAPropertyValueWalkingOutOfItsDirectoryIsDropped(): void
    {
        $this->assertNull($this->prefix('prefixJs', '../../../modules/system/assets/js/framework.js'));
        $this->assertNull($this->prefix('prefixLess', '../../../../../../etc/nginx/placeholder.less'));
        $this->assertNull($this->prefix('prefixCss', '../theme.css'));
        $this->assertNull($this->prefix('prefixSass', 'vendor/../../theme.scss'));
    }

    /**
     * Surrounding whitespace is trimmed before the value is judged, and a Windows
     * separator does not hide the parent reference.
     */
    public function testAWalkingValueIsDroppedWhateverItIsWrittenAs(): void
    {
        $this->assertNull($this->prefix('prefixJs', '  ../app.js  '));
        $this->assertNull($this->prefix('prefixJs', '..\\app.js'));
    }

    /**
     * Invalidation: the ordinary shapes still resolve to the same relative paths the
     * component has always produced, including a file in a subdirectory of the asset
     * directory, which the properties' documentation does not forbid.
     */
    public function testAnOrdinaryPropertyValueIsStillPrefixed(): void
    {
        $this->assertSame('js/app.js', $this->prefix('prefixJs', 'app.js'));
        $this->assertSame('js/vendor/lib.js', $this->prefix('prefixJs', 'vendor/lib.js'));
        $this->assertSame('css/theme.css', $this->prefix('prefixCss', ' theme.css '));
        $this->assertSame('less/theme.less', $this->prefix('prefixLess', 'theme.less'));
        $this->assertSame('sass/_partial.scss', $this->prefix('prefixSass', '_partial.scss'));
    }

    /**
     * A name that merely begins with two dots is not a parent reference.
     */
    public function testANameBeginningWithDotsIsNotTreatedAsTraversal(): void
    {
        $this->assertSame('js/..app.js', $this->prefix('prefixJs', '..app.js'));
    }

    protected function prefix(string $method, string $value): ?string
    {
        return self::callProtectedMethod($this->component, $method, [$value]);
    }
}
