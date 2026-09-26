<?php

namespace Backend\Tests\FormWidgets;

use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\FormWidgets\FileUpload;
use Database\Tester\Models\User as TesterUser;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\UploadedFile;
use System\Models\File as FileModel;
use System\Tests\Bootstrap\PluginTestCase;

/**
 * The FileUpload partials render values taken straight off the attachment record.
 * `file_name` is stored verbatim from the client supplied upload name by
 * Winter\Storm\Database\Attach\File::fromPost(), and its extension reappears in both
 * URLs the widget decorates the record with: the generated `disk_name` of a public
 * attachment, and the thumbnail route of a protected one. So every partial emitting the
 * name or either URL has to encode it itself.
 *
 * These cases hold the document branch of fileupload/partials/_config_form.php, the image
 * branch beside it and the four field partials to the same encoding.
 */
class FileUploadEscapingTest extends PluginTestCase
{
    /**
     * Slash free so that the base name reduction performed by Symfony's UploadedFile does
     * not truncate it, and ending in a valid extension so that upload validation passes.
     */
    protected const UNSAFE_NAME = '<img src=x onerror=window.pwned=1>.pdf';

    /**
     * A name whose extension, emitted verbatim, would close the double quoted attribute it
     * lands in. Backend\Controllers\Files::getThumbUrl() appends that extension to a
     * protected attachment's thumbnail URL, and generateFilenameForDisk() appends it to the
     * disk name that makes up a public attachment's URL. Only one dot, so everything after
     * it is taken as the extension.
     */
    protected const UNSAFE_EXTENSION = 'file.png" onerror="pwned=1';

    protected string $imagePath;

    protected TesterUser $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->imagePath = base_path(
            'modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'
        );

        $this->user = new TesterUser;
        $this->user->name = 'Test User';
        $this->user->email = uniqid('user', true) . '@test.com';
        $this->user->save();
    }

    /**
     * Establishes the source: the widget only validates the extension token, and the
     * storage layer keeps the client supplied file name exactly as sent, so anything
     * rendering it is rendering unfiltered client input.
     */
    public function testUploadStoresClientFileNameVerbatim(): void
    {
        request()->setMethod('POST');
        request()->files->set('file_data', new UploadedFile(
            $this->imagePath,
            self::UNSAFE_NAME,
            'application/pdf',
            null,
            true
        ));

        $response = $this->makeWidget(['mode' => 'file'])->onUpload();

        $this->assertSame(200, $response->getStatusCode(), (string) $response->getContent());
        $this->assertSame(
            self::UNSAFE_NAME,
            FileModel::find($response->getOriginalContent()['id'])->file_name
        );
    }

    /**
     * The document (non image) branch of the config popup.
     */
    public function testConfigFormEscapesFileNameInDocumentMode(): void
    {
        $this->attach(self::UNSAFE_NAME);

        $this->assertNoInjectedMarkup($this->renderConfigForm(['mode' => 'file']));
    }

    /**
     * The sibling image branch, which already escaped. Guards against a regression there.
     */
    public function testConfigFormEscapesFileNameInImageMode(): void
    {
        $this->attach(self::UNSAFE_NAME);

        $this->assertNoInjectedMarkup($this->renderConfigForm(['mode' => 'image']));
    }

    /**
     * The thumbnail URL of a protected attachment ends in the raw file extension, so it is
     * unconstrained too wherever the field does not restrict the extension.
     */
    public function testConfigFormEscapesThumbUrl(): void
    {
        $this->attach(self::UNSAFE_EXTENSION, false);

        $this->assertNoInjectedMarkup($this->renderConfigForm($this->thumbConfig()));
    }

    /**
     * The same URL is rendered by the field itself, which needs no interaction at all to
     * reach: it renders with the record's edit form.
     */
    public function testFieldEscapesThumbUrl(): void
    {
        $this->attach(self::UNSAFE_EXTENSION, false);

        $this->assertNoInjectedMarkup($this->makeWidget($this->thumbConfig())->render());
        $this->assertNoInjectedMarkup(
            $this->makeWidget($this->thumbConfig() + ['mode' => 'image-multi'])->render()
        );
    }

    /**
     * A field that does not restrict the upload extension carries the client extension into
     * the generated disk name, and from there into the public attachment URL that all four
     * field partials render as `data-path` and the popup renders as an `href`.
     */
    public function testFieldEscapesPathUrl(): void
    {
        $single = $this->upload('avatar', self::UNSAFE_EXTENSION, ['mode' => 'file']);

        $this->assertStringContainsString(
            '"',
            $single->getPath(),
            'The unrestricted extension should have reached the attachment URL'
        );

        $this->assertNoInjectedMarkup($this->makeWidget(['mode' => 'file-single'])->render());
        $this->assertNoInjectedMarkup($this->makeWidget(['mode' => 'image-single'])->render());
        $this->assertNoInjectedMarkup($this->renderConfigForm(['mode' => 'file']));

        $this->upload('photos', self::UNSAFE_EXTENSION, ['mode' => 'file']);

        $this->assertNoInjectedMarkup($this->makeWidget(['mode' => 'file-multi'], 'photos')->render());
        $this->assertNoInjectedMarkup($this->makeWidget(['mode' => 'image-multi'], 'photos')->render());
    }

    /**
     * Invalidation case: the field still hands the real attachment URL to the JavaScript
     * that reads `data-path`, entity decoded back to exactly what getPath() returns.
     */
    public function testFieldStillExposesTheAttachmentPath(): void
    {
        $file = $this->upload('avatar', 'Q1 report & notes.pdf', ['mode' => 'file']);

        $xpath = $this->xpath($this->makeWidget(['mode' => 'file-single'])->render());

        $this->assertSame(
            $file->getPath(),
            $xpath->query('//div[@data-path]/@data-path')->item(0)->value
        );
    }

    /**
     * Invalidation case: a legitimate file name must still be displayed as written, and
     * must not be double encoded.
     */
    public function testConfigFormStillDisplaysLegitimateFileName(): void
    {
        $fileName = 'Q1 report (final) & notes.pdf';
        $this->attach($fileName);

        $html = $this->renderConfigForm(['mode' => 'file']);
        $this->assertStringContainsString($fileName, $this->xpath($html)->query('//h4')->item(0)->textContent);

        $html = $this->renderConfigForm(['mode' => 'image']);
        $this->assertStringContainsString($fileName, $this->xpath($html)->query('//img/@title')->item(0)->value);
    }

    /**
     * Invalidation case: a legitimate attachment URL must survive escaping intact, so
     * that the preview image and the "Attachment URL" link still work.
     */
    public function testConfigFormStillLinksToTheAttachment(): void
    {
        $file = $this->attach('photo.png', false);

        $xpath = $this->xpath($this->renderConfigForm($this->thumbConfig()));

        $this->assertSame(
            $file->getPath(),
            $xpath->query('//a[@target="_blank"]/@href')->item(0)->value
        );
        $this->assertSame(
            $file->getThumb(100, 100, ['mode' => 'crop', 'extension' => 'auto']),
            $xpath->query('//img/@src')->item(0)->value
        );
    }

    //
    // Helpers
    //

    /**
     * Attaches a file to the test record under the given name. Protected attachments get
     * their URLs from Backend\Controllers\Files rather than the public storage path.
     */
    protected function attach(string $fileName, bool $isPublic = true): FileModel
    {
        $file = $this->user->avatar()->create(['data' => $this->imagePath]);
        $file->file_name = $fileName;
        $file->is_public = $isPublic;
        $file->save();

        $this->user->reloadRelations();

        request()->setMethod('POST');
        request()->request->replace(['file_id' => $file->id]);

        return $file;
    }

    /**
     * A thumbnail is only generated when the field declares preview dimensions.
     */
    protected function thumbConfig(): array
    {
        return ['mode' => 'image', 'imageWidth' => 100, 'imageHeight' => 100];
    }

    /**
     * Renders the attachment config popup exactly as onLoadAttachmentConfig() would.
     */
    protected function renderConfigForm(array $config): string
    {
        return $this->makeWidget($config)->onLoadAttachmentConfig();
    }

    /**
     * Uploads a file through the widget's own handler, so that the name reaches storage the
     * way a real upload does -- including the generated disk name, which is what the public
     * attachment URL is built from. `fileTypes: *` is what leaves the extension
     * unconstrained.
     */
    protected function upload(string $relation, string $clientName, array $config): FileModel
    {
        request()->setMethod('POST');
        request()->files->set('file_data', new UploadedFile(
            $this->imagePath,
            $clientName,
            'image/png',
            null,
            true
        ));

        $response = $this->makeWidget($config + ['fileTypes' => '*'], $relation)->onUpload();

        $this->assertSame(200, $response->getStatusCode(), (string) $response->getContent());

        request()->files->remove('file_data');
        $this->user->reloadRelations();

        $file = FileModel::find($response->getOriginalContent()['id']);

        request()->request->replace(['file_id' => $file->id]);

        return $file;
    }

    protected function makeWidget(array $config, string $relation = 'avatar'): FileUpload
    {
        $formField = new FormField($relation, ucfirst($relation));
        $formField->valueFrom = $relation;

        return new FileUpload(new Controller, $formField, $config + ['model' => $this->user]);
    }

    protected function xpath(string $html): DOMXPath
    {
        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<html><body>' . $html . '</body></html>');
        libxml_clear_errors();

        return new DOMXPath($doc);
    }

    /**
     * A stored value only becomes markup if the browser parses it as such, so assert on the
     * parsed document rather than on the string: no element may carry an event handler
     * attribute.
     */
    protected function assertNoInjectedMarkup(string $html): void
    {
        $this->assertSame(
            0,
            $this->xpath($html)->query('//@*[starts-with(name(), "on")]')->length,
            'An attachment value was parsed as markup and produced an event handler attribute.'
        );
    }
}
