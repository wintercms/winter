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
 * `file_name` is stored verbatim from the client supplied upload name, and its extension reaches both URLs the widget
 * decorates the record with: the `disk_name` of a public attachment, and the thumbnail route of a protected one.
 */
class FileUploadEscapingTest extends PluginTestCase
{
    /** Slash free so UploadedFile's base name reduction does not truncate it, and a valid extension so upload validation passes. */
    protected const UNSAFE_NAME = '<img src=x onerror=window.pwned=1>.pdf';

    /**
     * Only one dot, so everything after it is taken as the extension -- which getThumbUrl() appends to a protected
     * attachment's thumbnail URL and generateFilenameForDisk() appends to a public one's disk name.
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
     * Establishes the source for every case below: only the extension token is validated, the rest is kept as sent.
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

    public function testConfigFormEscapesFileNameInDocumentMode(): void
    {
        $this->attach(self::UNSAFE_NAME);

        $this->assertNoInjectedMarkup($this->renderConfigForm(['mode' => 'file']));
    }

    /**
     * The sibling image branch, which already escaped: a regression guard rather than a case the fix changed.
     */
    public function testConfigFormEscapesFileNameInImageMode(): void
    {
        $this->attach(self::UNSAFE_NAME);

        $this->assertNoInjectedMarkup($this->renderConfigForm(['mode' => 'image']));
    }

    public function testConfigFormEscapesThumbUrl(): void
    {
        $this->attach(self::UNSAFE_EXTENSION, false);

        $this->assertNoInjectedMarkup($this->renderConfigForm($this->thumbConfig()));
    }

    public function testFieldEscapesThumbUrl(): void
    {
        $this->attach(self::UNSAFE_EXTENSION, false);

        $this->assertNoInjectedMarkup($this->makeWidget($this->thumbConfig())->render());

        // array_replace, not a union: thumbConfig() already names a mode, which a union would keep.
        $multi = $this->makeWidget(array_replace($this->thumbConfig(), ['mode' => 'image-multi']))->render();
        $xpath = $this->xpath($multi);

        // _image_multi.php is a separate partial, and it has to have rendered the attachment for the assertion to mean anything.
        $this->assertSame(1, $xpath->query('//div[contains(@class, "style-image-multi")]')->length);
        $this->assertSame(1, $xpath->query('//div[contains(@class, "upload-object")]/@data-path')->length);
        $this->assertNoInjectedMarkup($multi);
    }

    /**
     * An unrestricted extension reaches the generated disk name, and from there the attachment URL that all four field
     * partials render as `data-path` and the popup renders as an `href`.
     */
    public function testFieldEscapesPathUrl(): void
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped(
                'Windows rejects a file name containing a quote, so this upload cannot be stored there and '
                . 'the URL it asserts on cannot arise. testFieldEscapesThumbUrl covers the same partials '
                . 'from a stored record on every platform.'
            );
        }

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
     * Invalidation case: `data-path` still decodes back to exactly what getPath() returns, for the JavaScript reading it.
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
     * Invalidation case: a legitimate file name is still displayed as written, and not double encoded.
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
     * Invalidation case: the preview image and the "Attachment URL" link still resolve to the attachment.
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
     * A protected attachment gets its URLs from Backend\Controllers\Files rather than the public storage path.
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

    protected function renderConfigForm(array $config): string
    {
        return $this->makeWidget($config)->onLoadAttachmentConfig();
    }

    /**
     * Uploads through the widget's own handler so the name reaches storage the way a real upload does, including the
     * generated disk name the public URL is built from. `fileTypes: *` is what leaves the extension unconstrained.
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
     * Asserted against the parsed document rather than the string, since a value is only markup if a parser says so.
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
