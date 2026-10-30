<?php

namespace Backend\Tests\Controllers;

use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\Controllers\Files;
use Backend\FormWidgets\FileUpload;
use Backend\Models\User;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use System\Models\File as FileModel;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File as FileHelper;
use Winter\Storm\Support\Str;

/**
 * Covers what `backend/files/get` serves for an attachment whose contents do not match the
 * name it was uploaded under.
 *
 * The upload gate validates the extension the client declared, while the stored content type
 * is derived from the bytes. Every authenticated user can upload their own avatar through My
 * Account, and the code in the URL is derived from values the upload handler returns, so the
 * response for a file of their choosing is reachable without any permission at all.
 */
class FilesOutputTest extends PluginTestCase
{
    protected User $user;
    protected string $workDir;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.backendUri', 'backend');
        Config::set('cms.enableCsrfProtection', false);

        $this->workDir = base_path('storage/framework/cache/files-output-' . Str::random());
        FileHelper::makeDirectory($this->workDir, 0777, true);

        Model::unguard();
        $this->user = User::create([
            'first_name' => 'Mallory',
            'last_name' => 'User',
            'login' => 'mallory',
            'email' => 'mallory@test.test',
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
        ]);
        Model::reguard();

        $this->actingAs($this->user);
    }

    public function tearDown(): void
    {
        FileHelper::deleteDirectory($this->workDir);

        parent::tearDown();
    }

    //
    // The upload gate
    //

    /**
     * The precondition the rest of this file rests on: the avatar field on My Account, which
     * needs no permission, accepts a file whose contents are not an image, because the
     * `extensions` rule only reads the declared name.
     */
    public function testTheAvatarFieldAcceptsAFileWhoseContentsAreNotAnImage(): void
    {
        $response = $this->uploadAvatar('<html><body><p>document</p></body></html>', 'me.jpg');

        $this->assertEquals(200, $response->getStatusCode());

        $file = $this->user->avatar()->withDeferred($this->sessionKey())->first();
        $this->assertNotNull($file, 'The upload was refused.');
        $this->assertEquals('me.jpg', $file->file_name);
        $this->assertEquals('text/html', $file->content_type);
    }

    //
    // What the endpoint serves
    //

    public function testAFileStoredAsHtmlIsNotServedAsADocument(): void
    {
        $file = $this->attachFile('<html><body><p>document</p></body></html>', 'me.jpg');

        $response = $this->get('backend/backend/files/get/' . Files::getUniqueCode($file));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringStartsWith(
            'attachment;',
            $response->headers->get('Content-Disposition')
        );
    }

    public function testAFileStoredAsSvgIsNotServedAsADocument(): void
    {
        $file = $this->attachFile(
            '<svg xmlns="http://www.w3.org/2000/svg"><rect width="1" height="1"/></svg>',
            'me.jpg'
        );

        $response = $this->get('backend/backend/files/get/' . Files::getUniqueCode($file));

        $this->assertEquals('image/svg+xml', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith(
            'attachment;',
            $response->headers->get('Content-Disposition')
        );
    }

    public function testTheEndpointDeclaresItsContentTypeIsAuthoritative(): void
    {
        $file = $this->attachFile($this->pngBytes(), 'avatar.png');

        $response = $this->get('backend/backend/files/get/' . Files::getUniqueCode($file));

        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
    }

    //
    // Legitimate use
    //

    /** An image attachment is still displayed in place -- backend previews rely on it. */
    public function testAnImageIsStillServedForDisplay(): void
    {
        $file = $this->attachFile($this->pngBytes(), 'avatar.png');

        $response = $this->get('backend/backend/files/get/' . Files::getUniqueCode($file));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('image/png', $response->headers->get('Content-Type'));
        $this->assertEquals($this->pngBytes(), $response->getContent());

        $parts = HeaderUtils::split($response->headers->get('Content-Disposition'), ';=');
        $this->assertEquals([['inline'], ['filename', 'avatar.png']], $parts);
    }

    /** And the thumbnail route still returns an image for display. */
    public function testTheThumbnailRouteStillServesAnImageForDisplay(): void
    {
        $file = $this->attachFile($this->pngBytes(), 'avatar.png');

        $response = $this->get(
            'backend/backend/files/thumb/' . Files::getUniqueCode($file) . '/20/20/auto/auto'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('image/png', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('inline;', $response->headers->get('Content-Disposition'));
    }

    /** A name a browser must be told how to decode is still served, not refused. */
    public function testAnAttachmentWithANonAsciiNameIsStillServed(): void
    {
        $file = $this->attachFile($this->pngBytes(), 'Rapport été.png');

        $response = $this->get('backend/backend/files/get/' . Files::getUniqueCode($file));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            [['inline'], ['filename', 'Rapport ete.png'], ['filename*', "utf-8''Rapport%20%C3%A9t%C3%A9.png"]],
            HeaderUtils::split($response->headers->get('Content-Disposition'), ';=')
        );
    }

    //
    // Helpers
    //

    protected function sessionKey(): string
    {
        return 'files-output-test';
    }

    /** Minimal 2x2 PNG, generated rather than committed. */
    protected function pngBytes(): string
    {
        $path = $this->workDir . '/source.png';

        if (!is_file($path)) {
            $image = imagecreatetruecolor(2, 2);
            imagepng($image, $path);
        }

        return file_get_contents($path);
    }

    /**
     * Drives the real avatar upload handler with the real field configuration from
     * modules/backend/models/user/fields.yaml.
     */
    protected function uploadAvatar(string $contents, string $fileName)
    {
        $path = $this->workDir . '/' . Str::random() . '.upload';
        file_put_contents($path, $contents);

        request()->setMethod('POST');
        request()->files->replace([
            'file_data' => new UploadedFile($path, $fileName, null, null, true),
        ]);

        $formField = new FormField('avatar', 'Avatar');
        $formField->valueFrom = 'avatar';

        $widget = new FileUpload(new Controller(), $formField, [
            'model' => $this->user,
            'sessionKey' => $this->sessionKey(),
            'mode' => 'image',
            'imageWidth' => 250,
            'imageHeight' => 250,
        ]);

        return $widget->onUpload();
    }

    /**
     * Stores an attachment on this user's avatar relation directly, for the cases that are
     * about what the delivery endpoint does rather than how the file got there.
     */
    protected function attachFile(string $contents, string $fileName): FileModel
    {
        $path = $this->workDir . '/' . Str::random() . '.upload';
        file_put_contents($path, $contents);

        $file = new FileModel();
        $file->fromFile($path, $fileName);
        $file->save();

        $this->user->avatar()->add($file);

        return $file;
    }
}
