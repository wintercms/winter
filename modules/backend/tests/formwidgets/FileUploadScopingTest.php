<?php

namespace Backend\Tests\FormWidgets;

use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\FormWidgets\FileUpload;
use Database\Tester\Models\User as TesterUser;
use System\Models\File as FileModel;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;
use Winter\Storm\Exception\ApplicationException;

/**
 * Ensures FileUpload::getFileRecord() only ever resolves a posted `file_id` that
 * belongs to the widget's own relation (including the current deferred-binding
 * session), and never an arbitrary System\Models\File row from another record.
 *
 * Regression test for GHSA-3277-h8g9-qj5f (IDOR via attacker-controlled file_id).
 */
class FileUploadScopingTest extends PluginTestCase
{
    protected string $imagePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->imagePath = base_path(
            'modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'
        );
    }

    /**
     * The legitimate case: a file already attached to this record (parent saved)
     * must still resolve, otherwise editing existing attachments breaks.
     */
    public function testResolvesOwnAttachedFile(): void
    {
        $user = $this->makeUser();
        $file = $user->avatar()->create(['data' => $this->imagePath]);
        $user->reloadRelations();

        $widget = $this->makeWidget($user);

        $this->postFileId($file->id);
        $this->assertSameFile($file, $widget->exposedGetFileRecord());
    }

    /**
     * The legitimate upload-in-progress case: a freshly uploaded file that is only
     * bound to the parent via the deferred-binding session (parent not yet saved)
     * must still resolve. This guards against a fix that scopes to the relation but
     * forgets ->withDeferred($sessionKey), which would break the upload workflow.
     */
    public function testResolvesOwnDeferredFile(): void
    {
        $sessionKey = 'fileupload-scoping-deferred';

        $user = $this->makeUser();

        $file = new FileModel;
        $file->data = $this->imagePath;
        $file->save();
        $user->avatar()->add($file, $sessionKey);

        $widget = $this->makeWidget($user, $sessionKey);

        $this->postFileId($file->id);
        $this->assertSameFile($file, $widget->exposedGetFileRecord());
    }

    /**
     * The security property: a file_id belonging to a DIFFERENT record must never
     * resolve, even though it is a valid row in the global system_files table.
     */
    public function testDoesNotResolveAnotherRecordsFile(): void
    {
        $owner = $this->makeUser();
        $attacker = $this->makeUser();

        $victimFile = $owner->avatar()->create(['data' => $this->imagePath]);
        $owner->reloadRelations();

        // Widget is bound to the attacker's record, but posts the victim's file id.
        $widget = $this->makeWidget($attacker);
        $this->postFileId($victimFile->id);

        $this->assertFalse(
            $widget->exposedGetFileRecord(),
            'A file_id from another record must not resolve through this widget.'
        );
    }

    /**
     * A non-existent / empty file_id must resolve to false, not throw.
     */
    public function testDoesNotResolveMissingOrEmptyFileId(): void
    {
        $widget = $this->makeWidget($this->makeUser());

        $this->postFileId(999999);
        $this->assertFalse($widget->exposedGetFileRecord());

        $this->postFileId(null);
        $this->assertFalse($widget->exposedGetFileRecord());
    }

    /**
     * End-to-end assertion mirroring the advisory PoC at the widget level:
     * saving the attachment config while posting another record's file_id must
     * NOT mutate that file's metadata.
     */
    public function testSaveConfigCannotMutateAnotherRecordsFile(): void
    {
        $owner = $this->makeUser();
        $attacker = $this->makeUser();

        $victimFile = $owner->avatar()->create([
            'data' => $this->imagePath,
            'title' => 'original title',
            'description' => 'original description',
        ]);
        $owner->reloadRelations();

        // Mirror a real AJAX request: the POST payload (including file_id) is
        // present before the widget is constructed for the request.
        $this->postData([
            'file_id' => $victimFile->id,
            'avatar' => [
                'title' => 'hijacked title',
                'description' => 'hijacked description',
            ],
        ]);

        $widget = $this->makeWidget($attacker);

        // Either the handler rejects the out-of-scope file, or it silently no-ops;
        // either way the victim's metadata must be untouched.
        try {
            $widget->onSaveAttachmentConfig();
        } catch (ApplicationException $ex) {
            // Acceptable: handler refused to find the out-of-scope file.
        }

        $victimFile = FileModel::find($victimFile->id);
        $this->assertSame('original title', $victimFile->title);
        $this->assertSame('original description', $victimFile->description);
    }

    /**
     * The legitimate case: reordering files that belong to this record's own
     * relation must update their sort_order.
     */
    public function testSortReordersOwnFiles(): void
    {
        $user = $this->makeUser();
        $first = $user->photos()->create(['data' => $this->imagePath]);
        $second = $user->photos()->create(['data' => $this->imagePath]);
        $user->reloadRelations();

        $widget = $this->makeWidget($user, null, 'photos');

        // Swap their order.
        $this->postData(['sortOrder' => [
            $first->id => 20,
            $second->id => 10,
        ]]);
        $widget->onSortAttachments();

        $this->assertEquals(20, FileModel::find($first->id)->sort_order);
        $this->assertEquals(10, FileModel::find($second->id)->sort_order);
    }

    /**
     * The security property: onSortAttachments must not write sort_order to a
     * file that belongs to a different record, even with a valid file id.
     */
    public function testSortIgnoresAnotherRecordsFile(): void
    {
        $owner = $this->makeUser();
        $attacker = $this->makeUser();

        $victimFile = $owner->photos()->create(['data' => $this->imagePath]);
        $owner->reloadRelations();
        $originalOrder = FileModel::find($victimFile->id)->sort_order;

        // Attacker drives their own photos widget but posts the victim's file id.
        $widget = $this->makeWidget($attacker, null, 'photos');
        $this->postData(['sortOrder' => [
            $victimFile->id => $originalOrder + 9999,
        ]]);
        $widget->onSortAttachments();

        $this->assertEquals(
            $originalOrder,
            FileModel::find($victimFile->id)->sort_order,
            'Sort order of another record\'s file must not change.'
        );
    }

    //
    // Helpers
    //

    protected function makeUser(): TesterUser
    {
        $user = new TesterUser;
        $user->name = 'Test User';
        $user->email = uniqid('user', true) . '@test.com';
        $user->save();

        return $user;
    }

    protected function makeWidget(Model $model, ?string $sessionKey = null, string $relation = 'avatar'): FileUploadTestable
    {
        $formField = new FormField($relation, ucfirst($relation));
        $formField->valueFrom = $relation;

        return new FileUploadTestable(new Controller, $formField, [
            'model' => $model,
            'sessionKey' => $sessionKey,
        ]);
    }

    protected function postFileId($id): void
    {
        $this->postData(['file_id' => $id]);
    }

    protected function postData(array $data): void
    {
        request()->setMethod('POST');
        request()->request->replace($data);
    }

    protected function assertSameFile($expected, $actual): void
    {
        $this->assertInstanceOf(FileModel::class, $actual);
        $this->assertEquals($expected->id, $actual->id);
    }
}

/**
 * Exposes the protected getFileRecord() so the scoping boundary can be asserted
 * directly without rendering partials.
 */
class FileUploadTestable extends FileUpload
{
    public function exposedGetFileRecord()
    {
        return $this->getFileRecord();
    }
}
