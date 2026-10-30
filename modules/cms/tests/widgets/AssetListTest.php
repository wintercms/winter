<?php

namespace Cms\Tests\Widgets;

use Backend\Tests\Fixtures\Models\UserFixture;
use Cms\Classes\Theme;
use Cms\Controllers\Index;
use Cms\Widgets\AssetList;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\UploadedFile;
use System\Tests\Bootstrap\PluginTestCase;

class AssetListTest extends PluginTestCase
{
    /**
     * @var string[] Absolute paths to remove once the test has run.
     */
    protected $cleanupPaths = [];

    public function tearDown(): void
    {
        foreach ($this->cleanupPaths as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $this->cleanupPaths = [];

        parent::tearDown();
    }

    //
    // Rendering
    //

    /**
     * The anchor's `data-path` and `href` carry the asset's stored path, which is a file
     * name an asset manager chose. Both must be encoded, like the `data-item-path` and
     * the name on the lines either side of them.
     */
    public function testItemsPartialEncodesTheStoredPathIntoItsLinkAttributes(): void
    {
        $assetList = $this->makeAssetList();

        $html = $assetList->makePartial('items', [
            'items' => [$this->makeItem('/x".js')],
            'nested' => true,
        ]);

        $this->assertStringContainsString('data-path="/x&quot;.js"', $html);
        $this->assertStringNotContainsString('data-path="/x".js"', $html);

        $this->assertStringContainsString('/assets/x&quot;.js"', $html);
        $this->assertStringNotContainsString('/assets/x".js"', $html);
    }

    /**
     * The payload from the report, so the attribute cannot be terminated and a handler
     * started after it.
     */
    public function testItemsPartialDoesNotLetAStoredPathStartAnAttribute(): void
    {
        $assetList = $this->makeAssetList();

        $html = $assetList->makePartial('items', [
            'items' => [$this->makeItem('/x"onmouseover=alert(document.domain)".js')],
            'nested' => true,
        ]);

        // The quote that would close `data-path` and open `onmouseover` as the next
        // attribute of the anchor never reaches the markup as a quote.
        $this->assertStringNotContainsString('"onmouseover=alert(document.domain)"', $html);
        $this->assertStringContainsString(
            'data-path="/x&quot;onmouseover=alert(document.domain)&quot;.js"',
            $html
        );
    }

    /**
     * Invalidation: an ordinary path still renders as itself, with no entities introduced.
     */
    public function testItemsPartialRendersAnOrdinaryPathUnchanged(): void
    {
        $assetList = $this->makeAssetList();

        $html = $assetList->makePartial('items', [
            'items' => [$this->makeItem('/js/script1.js')],
            'nested' => true,
        ]);

        $this->assertStringContainsString('data-path="/js/script1.js"', $html);
        $this->assertStringContainsString('/assets/js/script1.js"', $html);
    }

    //
    // Uploads
    //

    /**
     * `onUpload` took the client's file name as given, so a name that `onApplyName` and
     * `onNewDirectory` both refuse could be written to the theme's asset directory.
     */
    public function testOnUploadRejectsAClientNameOutsideTheAllowedCharacterSet(): void
    {
        $assetList = $this->makeAssetList();
        $fileName = 'x"onmouseover=alert(document.domain)".js';

        $this->uploadFile($assetList, $fileName);

        $this->assertSame(
            Lang::get('cms::lang.asset.error_uploading_file', [
                'name' => $fileName,
                'error' => Lang::get('cms::lang.asset.invalid_name'),
            ]),
            $this->uploadResponseContent($assetList)
        );

        $this->assertFileNotExists($this->assetPath($fileName));
    }

    /**
     * `..` in a name is refused for the same reason it is refused when renaming.
     */
    public function testOnUploadRejectsATraversingClientName(): void
    {
        $assetList = $this->makeAssetList();

        $this->uploadFile($assetList, '..script.js');

        $this->assertStringContainsString(
            Lang::get('cms::lang.asset.invalid_name'),
            $this->uploadResponseContent($assetList)
        );
    }

    /**
     * Invalidation: an ordinary name is still accepted and still lands in the theme's
     * asset directory, so the constraint has not closed the upload path itself.
     */
    public function testOnUploadAcceptsAnOrdinaryClientName(): void
    {
        $assetList = $this->makeAssetList();
        $fileName = 'asset-list-upload test_1.js';

        $this->uploadFile($assetList, $fileName);

        $this->assertSame('success', $this->uploadResponseContent($assetList));
        $this->assertFileExists($this->assetPath($fileName));
        $this->assertSame('/* uploaded */', file_get_contents($this->assetPath($fileName)));
    }

    //
    // Directory navigation
    //

    /**
     * `onOpenDirectory` read a `delay` parameter off the request and passed it to
     * `usleep()` unbounded. Nothing in the product sends that parameter, so it must have
     * no effect on how long the handler takes.
     */
    public function testOnOpenDirectoryIgnoresARequestSuppliedDelay(): void
    {
        $assetList = $this->makeAssetList();

        Request::merge(['path' => '/', 'delay' => 10]);

        $started = microtime(true);
        $result = $assetList->onOpenDirectory();
        $elapsed = microtime(true) - $started;

        // Ten seconds asked for, two allowed: wide enough either side that a slow
        // runner cannot fail it and the sleep cannot pass it.
        $this->assertLessThan(
            2.0,
            $elapsed,
            'onOpenDirectory must not sleep for a duration taken from the request'
        );

        $this->assertSame(['#' . $assetList->getId('asset-list')], array_keys($result));
    }

    //
    // Helpers
    //

    protected function makeAssetList(): AssetList
    {
        $user = (new UserFixture)->withPermission('cms.manage_assets', true);
        $this->actingAs($user);

        $assetList = (new Index)->widget->assetList;
        $this->assertNotNull($assetList, 'assetList widget should be registered');

        return $assetList;
    }

    protected function makeItem(string $path): object
    {
        return (object) [
            'type' => 'file',
            'path' => $path,
            'name' => basename($path),
            'editable' => true,
        ];
    }

    /**
     * Hands `onUpload` an upload whose *client* name is $fileName. The name on disk is
     * deliberately not $fileName: the payloads here are not legal file names on Windows,
     * and the client name is only ever a string in the request.
     */
    protected function uploadFile(AssetList $assetList, string $fileName): void
    {
        $source = tempnam(sys_get_temp_dir(), 'wn-assetlist-');
        file_put_contents($source, '/* uploaded */');

        // The source, and the destination the handler would move it to if it accepted
        // the name — a run without the fix does land the file there.
        $this->cleanupPaths[] = $source;
        $this->cleanupPaths[] = $this->assetPath($fileName);

        Request::merge(['theme' => Theme::getEditTheme()->getDirName()]);
        Request::instance()->files->set('file_data', new UploadedFile(
            $source,
            $fileName,
            'application/javascript',
            null,
            true
        ));

        $assetList->onUpload();
    }

    protected function uploadResponseContent(AssetList $assetList): string
    {
        $controller = self::getProtectedProperty($assetList, 'controller');

        return self::getProtectedProperty($controller, 'responseOverride')->getContent();
    }

    protected function assetPath(string $fileName): string
    {
        return Theme::getEditTheme()->getPath() . '/assets/' . $fileName;
    }
}
